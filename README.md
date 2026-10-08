# Logistic Link Backend

Laravel 8 application for connecting client companies with transporters, managing transporter contracts and pricing, moving shipments through delivery, and settling them through invoices. The application is primarily a localized, server-rendered admin/client portal; the API surface is currently small.

## Stack and runtime

- PHP `^7.4|^8.0`; deployment workflows currently use PHP 8.1.
- Laravel 8, MySQL, Blade, Laravel Mix, and `nwidart/laravel-modules` 8.
- Bouncer roles/abilities for authorization and Laravel queues for shipment matching, delayed transporter responses, and mail.
- Arabic and English localization. Module web routes are locale-prefixed, for example `/ar/admin/...` and `/en/admin/...`.
- Uploaded images/files use the custom `GraphManager`/`S3GraphManager` bindings configured from the `DO_*` variables.

MySQL is an application dependency rather than an interchangeable default: models contain MySQL-specific expressions and `SystemCore` installs database functions for distance/location calculations.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the MySQL, mail, object-storage, map, and Firebase values needed by the feature under development, then initialize the application:

```bash
php artisan migrate
php artisan db:seed
php artisan module:seed
php artisan serve
```

`module:seed` matters: the root `DatabaseSeeder` is intentionally almost empty, while module seeders create roles, abilities, root/admin users, countries/cities, and module-specific reference data. Configure `ROOT_*` and `ADMIN_*` environment values before seeding; the defaults in `modules/SystemCore/Config/config.php` are development defaults only.

For real shipment matching behavior, use an asynchronous queue connection and run a worker:

```bash
php artisan queue:work --tries=3 --timeout=90
```

The `.env.example` uses `QUEUE_CONNECTION=sync`. That is convenient for basic local work, but it does not preserve the intended 15-minute delay before moving an unanswered shipment offer to the next transporter.

Frontend source at the repository root can be built with `npm install` and `npm run dev`. Most UI assets are already committed under `public/`; several module Mix files are inherited scaffolding and currently reference source files that do not exist, so do not assume every module can be built independently.

## Project structure

```text
app/                         Thin Laravel host: HTTP kernel, console kernel, User model
config/                      Application and package configuration
database/                    Host migrations; module migrations are loaded automatically
modules/
  SystemCore/                Shared runtime, ModelService, auth/permissions, geography, config
  Ui/                        Shared Blade components, layouts, menus, and frontend helpers
  Admin/                     Session auth, users/roles, dashboard, system configuration
  ContentManager/            Translatable categories, content, tags, and page sections
  ContentTypes/              Logistics domain: companies, contracts, shipments, RFQs, invoices
  Address/                   Google/OpenStreetMap geocoding and map integration
  Clients/                   Client administration screens/services
  ContactUs/                 Contact request administration
  Website/                   Public site, contact form, and social login
  ModuleManager/             Local scaffolding/generator commands
modules_statuses.json        Enabled modules; changing it changes booted routes/providers
routes/                      Host routes; most product routes live inside their modules
public/graph/                Custom local/CDN file and image handling implementation
```

Each module follows the same broad layout: `module.json`, a service provider, `Routes/`, `Entities/`, `Http/Controllers/`, `Resources/`, and optionally `Database/`, `Jobs/`, or mail classes. Providers merge module config, load translations/views/migrations, register routes, and add menus/settings or container bindings.

### Request and CRUD pattern

The established implementation pattern is:

1. A module route targets a module controller.
2. The controller extends `SystemCoreHttpController`, declares auth/ability middleware, prepares views, and wraps multi-record writes in `DB::transaction`.
3. An entity service extending `Modules\SystemCore\System\Services\ModelService` owns validation, role-scoped queries, DataTables/select/export behavior, permission names, and create/update logic.
4. Eloquent entities own relations and reusable query scopes; Blade views use module translation namespaces such as `contenttypes::...`.
5. `ActionResponse` supplies the conventional response/redirect/error shape.

When adding ordinary CRUD behavior, start from a nearby controller/service/entity in the same module instead of introducing a new repository or request layer. The older `EloquentBaseService` plus contract pattern still exists in a few shared services; follow the local pattern of the feature being changed.

### Authentication and authorization

Web authentication is session based through `HttpAuth`; APIs can use JWT through `ApiAuth`. `NeedPermissions` checks abilities generated from the fully qualified entity name, such as `Modules_ContentTypes_Entities_Shippment@read`. Important seeded roles are:

- `ROOT`: bypasses ordinary ability checks.
- `SYSTEM_ADMIN`: administration role populated by module seeders.
- `DEFAULT`: client/company user.
- `TRANSPORTER`: transporter user.
- `CONTENT_EDITOR`: content-management role.

Do not rely only on menu visibility. Controllers apply middleware, while service queries also scope records by the authenticated role and ownership.

## Core logistics flow

`ContentTypes` is the main business module. The spelling `Shippment` and `Transportar` is used in class names, routes, translations, jobs, and database identifiers. Preserve it in existing interfaces; correcting isolated occurrences would break conventions or compatibility.

### Contract-priced shipment

```text
DRAFT (optional)
  -> MATCHING
  -> PENDING_TRANSPORTER_APPROVAL
     -> PENDING_PICKUP
     -> TRANSPORTER_APPROVED_WITH_PRICE_CHANGE -> client accept -> PENDING_PICKUP
     -> next matched transporter on reject/timeout
  -> PENDING_DELIVERY
  -> DELIVERED_PENDING_PAYMENT
  -> INVOICED
  -> COMPLETED
```

`ShippmentMatchTransportar` finds active current contract-instance prices matching client, trip type, truck type, origin, and destination. It records all candidates, chooses the first ordered match, and dispatches `ShippmentSendToTransportar`. That job sends the offer and schedules `ShippmentCheckTransportaAcceptance` after 15 minutes. The flow can also end in `NO_MATCHING_CONTRACTS`, `CANCELED_DUE_DELAYED_TRANSPORTER_APPROVAL`, or `CANCELLED`.

Pickup/delivery progress is stored per address. Assigning driver/waybill and marking locations delivered moves the shipment from `PENDING_PICKUP` to `PENDING_DELIVERY`, then to `DELIVERED_PENDING_PAYMENT` when all required locations are complete.

### RFQ and invoicing

RFQ shipments use `RFQ_PENDING_TRANSPORTER_SELECTION` and `RFQ_PENDING_TRANSPORTER_OFFER`, with candidate offers in `ShippmentRfqTransporter`; an accepted offer moves the shipment to `PENDING_PICKUP`.

Invoices group delivered shipments between a client and transporter. Creating an invoice moves included shipments to `INVOICED`. The implemented invoice state sequence is `PENDING` -> `PENDING_SUPPLIER_REVIEW` -> `PAID`; the final transition moves its shipments to `COMPLETED`.

## Where to change common behavior

| Change | Primary locations |
| --- | --- |
| Shipment rules, ownership filters, persistence | `modules/ContentTypes/Entities/ShippmentService.php` |
| Shipment relationships/query projections | `modules/ContentTypes/Entities/Shippment.php` |
| Shipment actions and transitions | `modules/ContentTypes/Http/Controllers/ShippmentsController.php` |
| Automatic matching/timeout | `modules/ContentTypes/Jobs/` |
| Contracts/transporter onboarding | `TransporterService.php` and contract entities in `ContentTypes/Entities/` |
| Invoice creation/status transitions | `InvoiceService.php`, `Invoice.php`, `InvoicesController.php` |
| Roles and abilities | relevant module seeder plus controller middleware/service `guard()` |
| Admin navigation/settings | module service provider `registerEvents()`/`registerSettings()` |
| Labels/form copy | both `Resources/lang/ar/` and `Resources/lang/en/` in the owning module |
| Schema | a new timestamped migration under the owning module |

## Verification and operational cautions

Run the smallest relevant checks, followed by the suite when dependencies and a test database are available:

```bash
php -l path/to/changed.php
php artisan route:list
php artisan test
```

The current test suite contains only Laravel's example feature test and is not meaningful regression coverage. Tests are configured to use the environment's database (SQLite is commented out), so use an isolated test database and never point tests at shared data.

Before changing shipment, invoice, contract, role, or upload behavior, verify both role scoping and the full state transition. Keep multi-table writes transactional and remember that queued jobs and emails may run immediately when the queue is `sync`.

The host `routes/web.php` contains unauthenticated legacy `fcm-test`, `test`, `test-mail`, `preview-mail`, and `test-sms` routes. Some reference absent modules or trigger external messages. Do not call them during verification, and do not copy their style into product routes.

Deployment workflows for `main` and `staging` are under `.github/workflows/`; they install production Composer dependencies and run `migrate`, `db:seed`, and `module:seed`. Treat committed schema and seed changes as deployment-sensitive.
