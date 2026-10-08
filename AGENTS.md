# Working Guide

Read `README.md` before changing this repository. It records the module map, runtime, roles, shipment/invoice state flow, and operational caveats.

## Make changes in the established shape

- Put domain work in the owning module. Keep the root `app/` layer thin.
- For CRUD features, follow the nearest module's controller + `ModelService` + Eloquent entity + Blade/translation pattern.
- Controllers coordinate HTTP concerns and transactions; services own validation, permission-aware queries, and create/update behavior; entities own relationships and query scopes.
- Preserve the existing public spelling `Shippment` and `Transportar` in code, routes, translation keys, and database names.
- Add schema changes as new migrations under the owning module. Do not edit an old migration that may already be deployed.
- Keep Arabic and English translation files aligned when adding user-visible text.

## Check the behavior around the edit

- Trace authorization at all three relevant levels: controller middleware, `ModelService::guard()` abilities, and role/owner query scoping.
- Wrap related database writes in `DB::transaction`, matching neighboring code.
- For shipment/invoice changes, account for every affected status and for queued follow-up jobs; do not assume `QUEUE_CONNECTION=sync` models delayed production behavior.
- For uploaded files, reuse the existing `UploadedFile` and `GraphManager`/`S3GraphManager` conventions.
- Do not invoke the legacy test/message routes in root `routes/web.php`; some have external side effects.

## Verify proportionally

Start with syntax checks for changed PHP files, then targeted tests or `php artisan test` and `php artisan route:list` when dependencies and an isolated test database are available. The repository currently has very little automated coverage, so add a focused regression test for changed business rules when practical and report anything that could not be exercised.

Do not run broad formatting or unrelated cleanup. Existing code has mixed legacy patterns; consistency with the touched feature is safer than a repository-wide modernization.
