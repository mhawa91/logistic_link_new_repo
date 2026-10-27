@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'items' => [
            [
                'title' => __('ui::strings.dashboard'),
                'link'  => route('AdminDashboardController@index')
            ],
        ],
    ]
])

<div class="nk-block-head-content">
    <div class="toggle-wrap nk-block-tools-toggle">
        <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
            <em class="icon ni ni-menu-alt-r"></em>
        </a>
        <div class="toggle-expand-content" data-content="pageMenu">
            <ul class="nk-block-tools g-3">
                @if (auth()->check() && auth()->user()->type == 'DEFAULT')
                   <li>
                        <a href="{{ route('ShippmentsController@create') }}" class="btn btn-white btn-outline-light">
                            <em class="icon ni ni-plus"></em><span>{{ __('contenttypes::dashboard.add_new_order') }}</span>
                        </a>
                    </li> 
                @endif
                <li>
                    <a href="{{ route('ShippmentsController@index') }}" class="btn btn-white btn-outline-light">
                        <em class="icon ni ni-eye"></em><span>{{ __('contenttypes::dashboard.view_order') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('TransportersController@index') }}" class="btn btn-danger">
                        <em class="icon ni ni-edit"></em><span>{{ __('contenttypes::dashboard.congigure_your_contracts') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
    
@endcomponent

@push('head')
    <style>
        .dashboard-overview__heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }
        .dashboard-overview__heading h4 {
            margin-bottom: 4px;
            color: #25396f;
            font-size: 20px;
        }
        .dashboard-overview__heading p {
            margin: 0;
            color: #8094ae;
            font-size: 13px;
        }
        .dashboard-metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
        }
        .dashboard-metric {
            --metric-color: #6576ff;
            --metric-soft: rgba(101, 118, 255, .12);
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid #e5e9f2;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(31, 43, 74, .05);
            color: #364a63;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .dashboard-metric::before {
            position: absolute;
            inset-block: 0;
            inset-inline-start: 0;
            width: 3px;
            background: var(--metric-color);
            content: '';
        }
        .dashboard-metric:hover {
            border-color: var(--metric-color);
            box-shadow: 0 9px 24px rgba(31, 43, 74, .1);
            color: #364a63;
            transform: translateY(-2px);
        }
        .dashboard-metric__inner {
            padding: 18px;
        }
        .dashboard-metric__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .dashboard-metric__icon {
            display: inline-flex;
            flex: 0 0 42px;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--metric-soft);
            color: var(--metric-color);
            font-size: 21px;
        }
        .dashboard-metric__value {
            overflow: hidden;
            color: #25396f;
            font-size: 29px;
            font-weight: 700;
            line-height: 1;
            text-overflow: ellipsis;
        }
        .dashboard-metric__label {
            display: block;
            min-height: 40px;
            margin-top: 14px;
            color: #526484;
            font-size: 13px;
            font-weight: 600;
        }
        .dashboard-metric__link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            color: var(--metric-color);
            font-size: 12px;
            font-weight: 600;
        }
        .dashboard-metric--contracts { --metric-color: #6576ff; --metric-soft: rgba(101, 118, 255, .12); }
        .dashboard-metric--shipments { --metric-color: #3b82f6; --metric-soft: rgba(59, 130, 246, .11); }
        .dashboard-metric--delivered { --metric-color: #1c9a83; --metric-soft: rgba(28, 154, 131, .11); }
        .dashboard-metric--progress { --metric-color: #d99a00; --metric-soft: rgba(217, 154, 0, .12); }
        .dashboard-metric--invoices { --metric-color: #e85347; --metric-soft: rgba(232, 83, 71, .11); }
        .dashboard-activity-card {
            border-radius: 10px;
            box-shadow: 0 5px 18px rgba(31, 43, 74, .05);
        }
        .dashboard-activity-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
        }
        .dashboard-activity-card__header h5 {
            margin-bottom: 4px;
            color: #25396f;
        }
        .dashboard-activity-card__header p {
            margin: 0;
            color: #8094ae;
            font-size: 12px;
        }
        .dashboard-period-switch {
            display: inline-flex;
            flex: 0 0 auto;
            gap: 4px;
            padding: 4px;
            border: 1px solid #e5e9f2;
            border-radius: 8px;
            background: #f5f6fa;
        }
        .dashboard-period-switch button {
            padding: 7px 12px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #8094ae;
            font-size: 12px;
            font-weight: 600;
        }
        .dashboard-period-switch button.is-active {
            background: #fff;
            box-shadow: 0 2px 7px rgba(31, 43, 74, .1);
            color: #1c9a83;
        }
        .dashboard-period-total {
            display: flex;
            align-items: baseline;
            gap: 7px;
            margin-top: 18px;
            color: #8094ae;
            font-size: 12px;
        }
        .dashboard-period-total strong {
            color: #25396f;
            font-size: 22px;
        }
        .dashboard-chart-wrap {
            position: relative;
            height: 285px;
            margin-top: 8px;
        }
        .pending-request-alert {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(101, 118, 255, 0.28);
            border-radius: 10px;
            background: linear-gradient(110deg, rgba(101, 118, 255, 0.12), rgba(255, 255, 255, 0.96) 58%);
            box-shadow: 0 10px 28px rgba(31, 43, 74, 0.07);
        }
        .pending-request-alert::before {
            position: absolute;
            inset-block: 0;
            inset-inline-start: 0;
            width: 4px;
            background: #6576ff;
            content: '';
        }
        .pending-request-alert__content {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 20px 24px;
        }
        .pending-request-alert__icon {
            position: relative;
            display: inline-flex;
            flex: 0 0 54px;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #6576ff;
            color: #fff;
            box-shadow: 0 8px 18px rgba(101, 118, 255, 0.28);
            font-size: 25px;
        }
        .pending-request-alert__pulse {
            position: absolute;
            inset-block-start: 0;
            inset-inline-end: 1px;
            width: 12px;
            height: 12px;
            border: 2px solid #fff;
            border-radius: 50%;
            background: #f4bd0e;
            box-shadow: 0 0 0 4px rgba(244, 189, 14, 0.18);
        }
        .pending-request-alert__body {
            flex: 1 1 auto;
            min-width: 0;
        }
        .pending-request-alert__label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 5px;
            color: #6576ff;
            font-size: 12px;
            font-weight: 700;
        }
        .pending-request-alert__count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 12px;
            background: #6576ff;
            color: #fff;
            font-size: 12px;
        }
        .pending-request-alert__title {
            margin: 0 0 4px;
            color: #25396f;
            font-size: 19px;
        }
        .pending-request-alert__description {
            margin: 0;
            color: #667085;
            font-size: 13px;
        }
        .pending-request-alert__action {
            flex: 0 0 auto;
            white-space: nowrap;
        }
        body.dark-mode .pending-request-alert {
            background: linear-gradient(110deg, rgba(101, 118, 255, 0.2), rgba(17, 23, 31, 0.96) 58%);
        }
        body.dark-mode .pending-request-alert__title {
            color: #e5e9f2;
        }
        body.dark-mode .pending-request-alert__description {
            color: #b6c6e3;
        }
        body.dark-mode .dashboard-metric,
        body.dark-mode .dashboard-activity-card,
        body.dark-mode .dashboard-period-switch button.is-active {
            background: #18212d;
        }
        body.dark-mode .dashboard-overview__heading h4,
        body.dark-mode .dashboard-metric__value,
        body.dark-mode .dashboard-activity-card__header h5,
        body.dark-mode .dashboard-period-total strong {
            color: #e5e9f2;
        }
        body.dark-mode .dashboard-metric__label {
            color: #b6c6e3;
        }
        body.dark-mode .dashboard-period-switch {
            background: #101924;
        }
        @media (max-width: 1199px) {
            .dashboard-metrics-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 767px) {
            .dashboard-metrics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .dashboard-activity-card__header {
                flex-direction: column;
            }
            .pending-request-alert__content {
                align-items: flex-start;
                flex-wrap: wrap;
                padding: 18px;
            }
            .pending-request-alert__body {
                flex-basis: calc(100% - 76px);
            }
            .pending-request-alert__action {
                width: 100%;
                margin-inline-start: 72px;
            }
            .pending-request-alert__action .btn {
                width: 100%;
                justify-content: center;
            }
        }
        @media (max-width: 420px) {
            .dashboard-metrics-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-period-switch {
                width: 100%;
            }
            .dashboard-period-switch button {
                flex: 1 1 50%;
            }
            .pending-request-alert__action {
                margin-inline-start: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div id="vue-dashboard">
        @if(auth()->check() && auth()->user()->type == 'TRANSPORTER' && $pendingTransporterRequestsCount > 0)
            <div class="pending-request-alert mb-4" role="status" aria-live="polite">
                <div class="pending-request-alert__content">
                    <div class="pending-request-alert__icon" aria-hidden="true">
                        <em class="icon ni ni-bell-fill"></em>
                        <span class="pending-request-alert__pulse"></span>
                    </div>
                    <div class="pending-request-alert__body">
                        <div class="pending-request-alert__label">
                            <span>{{ __('contenttypes::dashboard.new_requests') }}</span>
                            <span class="pending-request-alert__count">{{ $pendingTransporterRequestsCount }}</span>
                        </div>
                        <h4 class="pending-request-alert__title">
                            {{ __('contenttypes::dashboard.pending_requests_title') }}
                        </h4>
                        <p class="pending-request-alert__description">
                            {{ trans_choice('contenttypes::dashboard.pending_requests_description', $pendingTransporterRequestsCount, ['count' => $pendingTransporterRequestsCount]) }}
                        </p>
                    </div>
                    <div class="pending-request-alert__action">
                        <a href="{{ route('ShippmentsController@index') }}" class="btn btn-primary">
                            <em class="icon ni ni-inbox-in"></em>
                            <span>{{ __('contenttypes::dashboard.review_pending_requests') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @php
            $showLegacyDashboardCards = (int) $dashboardStatistics['total_contracts'] === 0
                && (int) $dashboardStatistics['total_shippments'] === 0;

            $dashboardMetricCards = [
                [
                    'key'   => 'total_contracts',
                    'label' => __('contenttypes::dashboard.total_contracts'),
                    'icon'  => 'ni-file-docs',
                    'class' => 'contracts',
                    'link'  => route('TransportersController@index'),
                ],
                [
                    'key'   => 'total_shippments',
                    'label' => __('contenttypes::dashboard.total_shippments'),
                    'icon'  => 'ni-truck',
                    'class' => 'shipments',
                    'link'  => route('ShippmentsController@index'),
                ],
                [
                    'key'   => 'delivered_shippments',
                    'label' => __('contenttypes::dashboard.delivered_shippments'),
                    'icon'  => 'ni-check-circle',
                    'class' => 'delivered',
                    'link'  => route('ShippmentsController@index', ['dashboard_filter' => 'delivered']),
                ],
                [
                    'key'   => 'in_progress',
                    'label' => __('contenttypes::dashboard.in_progress_shippments'),
                    'icon'  => 'ni-loader',
                    'class' => 'progress',
                    'link'  => route('ShippmentsController@index', ['dashboard_filter' => 'in_progress']),
                ],
                [
                    'key'   => 'open_invoices',
                    'label' => __('contenttypes::dashboard.open_invoices'),
                    'icon'  => 'ni-reports-alt',
                    'class' => 'invoices',
                    'link'  => route('InvoicesController@index', ['dashboard_filter' => 'open']),
                ],
            ];
        @endphp

        <section class="dashboard-overview mb-4" aria-labelledby="dashboard-statistics-title">
            <div class="dashboard-overview__heading">
                <div>
                    <h4 id="dashboard-statistics-title">{{ __('contenttypes::dashboard.statistics_title') }}</h4>
                    <p>{{ __('contenttypes::dashboard.statistics_description') }}</p>
                </div>
            </div>

            <div class="dashboard-metrics-grid">
                @foreach($dashboardMetricCards as $metric)
                    <a href="{{ $metric['link'] }}" class="dashboard-metric dashboard-metric--{{ $metric['class'] }}">
                        <div class="dashboard-metric__inner">
                            <div class="dashboard-metric__top">
                                <span class="dashboard-metric__icon" aria-hidden="true">
                                    <em class="icon ni {{ $metric['icon'] }}"></em>
                                </span>
                                <strong class="dashboard-metric__value">{{ number_format($dashboardStatistics[$metric['key']]) }}</strong>
                            </div>
                            <span class="dashboard-metric__label">{{ $metric['label'] }}</span>
                            <span class="dashboard-metric__link">
                                {{ __('contenttypes::dashboard.view_details') }}
                                <em class="icon ni ni-arrow-long-left"></em>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="card card-bordered dashboard-activity-card mt-3">
                <div class="card-inner">
                    <div class="dashboard-activity-card__header">
                        <div>
                            <h5>{{ __('contenttypes::dashboard.shippments_activity') }}</h5>
                            <p>{{ __('contenttypes::dashboard.shippments_activity_description') }}</p>
                        </div>
                        <div class="dashboard-period-switch" role="group" aria-label="{{ __('contenttypes::dashboard.shippments_activity') }}">
                            <button type="button" class="is-active" data-dashboard-period="last_7_days" aria-pressed="true">
                                {{ __('contenttypes::dashboard.last_7_days') }}
                            </button>
                            <button type="button" data-dashboard-period="last_30_days" aria-pressed="false">
                                {{ __('contenttypes::dashboard.last_30_days') }}
                            </button>
                        </div>
                    </div>
                    <div class="dashboard-period-total">
                        <span>{{ __('contenttypes::dashboard.period_total') }}</span>
                        <strong id="dashboard-period-total">{{ number_format($dashboardStatistics['chart']['last_7_days']['total']) }}</strong>
                    </div>
                    <div class="dashboard-chart-wrap">
                        <canvas id="shippmentsActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        @if($showLegacyDashboardCards)
        <div class="row g-gs">
            <div class="col-lg-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner p-4">
                        <h4>
                            <img src="{{ asset('images/icons/user.svg') }}" alt="">
                            {{ __('contenttypes::dashboard.company_profile') }}
                        </h4>
                        <div class="card-title-group">
                            <div class="d-stastic-block1">
                                {{ __('contenttypes::dashboard.company_profile_desc') }}
                            </div>
                            <div class="d-stastic-block2" style="width: 25%">
                                {{-- <img width="59%" src="{{ asset('images/icons/test_pie.png') }}" alt=""> --}}
                                <canvas id="progressChart" width="100" height="100"></canvas>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('UsersController@profile') }}">
                                <button class="btn btn-primary">
                                    {{ __('contenttypes::dashboard.create_your_profile') }}
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-bordered h-100">
                    <div class="card-inner p-4">
                        <h4>
                            <img src="{{ asset('images/icons/company_contracts.svg') }}" alt="">
                            {{ __('contenttypes::dashboard.company_contracts') }}
                        </h4>
                        <div class="card-title-group">
                            @if($clientContractsAll == 0)
                                <div class="d-stastic-block1">
                                    {{ __('contenttypes::dashboard.company_contracts_desc') }}
                                </div>
                            @else
                                <div class="nk-sale-data-group flex-md-nowrap g-4">
                                    <div class="nk-sale-data">
                                        <span class="amount">
                                            {{ $HelpersCLass->moneyFormat($clientContracts, 0)->abs }}
                                        </span>
                                        <span class="sub-title">
                                            {{ __('contenttypes::dashboard.active_contract') }}
                                        </span>
                                    </div>
                                    <div class="nk-sale-data">
                                        <span class="amount text-danger">
                                            {{ $HelpersCLass->moneyFormat($clientContractsNotActive, 0)->abs }}
                                        </span>
                                        <span class="sub-title">
                                            {{ __('contenttypes::dashboard.not_active_contract') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <div class="d-stastic-block2">
                                <img width="55%" src="{{ asset('images/icons/company_contracts_1.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="mt-3">
                            @if($clientContractsAll == 0)
                                <a href="{{ route('TransportersController@index') }}">
                                    <button class="btn btn-primary">
                                        {{ __('contenttypes::dashboard.add_your_first_transporter') }}
                                    </button>
                                </a>
                            @else
                                <a href="{{ route('TransportersController@index') }}">
                                    <button class="btn btn-primary">
                                        {{ __('contenttypes::dashboard.add_more_transporters') }}
                                    </button>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                 <div class="card card-bordered h-100">
                    <div class="card-inner company-shipments p-4">
                        <h4>
                            <img src="{{ asset('images/icons/truck_2.svg') }}" alt="">
                            {{ __('contenttypes::dashboard.company_shipments') }}
                        </h4>
                        <div class="card-title-group">
                            <div class="d-stastic-block1">
                                {{ __('contenttypes::dashboard.company_shipments_desc') }}
                            </div>
                            <div class="d-stastic-block2">
                                <img width="65%" src="{{ asset('images/icons/truck_1.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="mt-5">
                            <a href="{{ route('ShippmentsController@index') }}">
                                <button class="btn btn-primary">
                                    {{ __('contenttypes::dashboard.view_order') }}
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card card-full mt-4 how-it-works">
        <div class="card-inner">
            <div class="card-title-group">
                <div class="card-title py-3 px-5">
                    <h4 class="title">{{ __('contenttypes::dashboard.how_it_works') }}</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <div class="text-block pe-xl-5">
                            <h2 class="title mb-3">
                                {!! \Illuminate\Support\Str::of($Sections->byKey('how-it-works', 'title'))->replaceMatches('/\[(.*?)\]/u', '<span class="text-green">$1</span>') !!}
                            </h2>
                            <p>
                                {!! \Illuminate\Support\Str::of($Sections->byKey('how-it-works', 'content'))->replaceMatches('/\[(.*?)\]/u', '<span class="text-green">$1</span>') !!}
                            </p>
                            <ul class="list list-lg list-success list-checked-circle outlined">
                                <li>
                                    {!! \Illuminate\Support\Str::of($Sections->byKey('how-it-works', 'section-1'))->replaceMatches('/\[(.*?)\]/u', '<strong class="text-green">$1</strong>') !!}
                                </li>
                                <li>
                                    {!! \Illuminate\Support\Str::of($Sections->byKey('how-it-works', 'section-2'))->replaceMatches('/\[(.*?)\]/u', '<strong class="text-green">$1</strong>') !!}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="img-block img-block-s1 left">
                            <img src="{{ asset('landing') }}/images/gfx/a.png" alt="Dashlite">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const DashboardApp = new Vue({
            el    : '#vue-dashboard',
            data  : {
            },
            methods: {
            },
            created: function() {
                this.$nextTick(() => {
                })
            }
        });
    </script>

    <script>
        const dashboardShippmentSeries = @json($dashboardStatistics['chart']);
        const shippmentsActivityCanvas = document.getElementById('shippmentsActivityChart');
        const dashboardPeriodTotal = document.getElementById('dashboard-period-total');

        if (shippmentsActivityCanvas && typeof Chart !== 'undefined') {
            const initialSeries = dashboardShippmentSeries.last_7_days;
            const shippmentsActivityChart = new Chart(shippmentsActivityCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: initialSeries.labels,
                    datasets: [{
                        label: @json(__('contenttypes::dashboard.shippments_count')),
                        data: initialSeries.values,
                        borderColor: '#24bda6',
                        backgroundColor: 'rgba(36, 189, 166, .12)',
                        pointBackgroundColor: '#24bda6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        borderWidth: 2,
                        tension: .35,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: '#8094ae',
                                maxTicksLimit: 10,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(128, 148, 174, .14)',
                            },
                            ticks: {
                                color: '#8094ae',
                                precision: 0,
                            },
                        },
                    },
                },
            });

            document.querySelectorAll('[data-dashboard-period]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const period = button.getAttribute('data-dashboard-period');
                    const series = dashboardShippmentSeries[period];

                    if (! series) {
                        return;
                    }

                    document.querySelectorAll('[data-dashboard-period]').forEach(function (periodButton) {
                        const isActive = periodButton === button;
                        periodButton.classList.toggle('is-active', isActive);
                        periodButton.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    shippmentsActivityChart.data.labels = series.labels;
                    shippmentsActivityChart.data.datasets[0].data = series.values;
                    shippmentsActivityChart.update();
                    dashboardPeriodTotal.textContent = Number(series.total).toLocaleString(document.documentElement.lang || undefined);
                });
            });
        }
    </script>

    <script>
        const progressChartCanvas = document.getElementById('progressChart');
        const progressValue = Number('{{ $progress }}');

        if (progressChartCanvas && typeof Chart !== 'undefined') {
        new Chart(progressChartCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Progress', 'Remaining'],
                datasets: [{
                    label: 'Progress',
                    data: [progressValue, 100 - progressValue],
                    backgroundColor: ['#FF4C29', '#E0E0E0'],
                    borderWidth: 5
                }]
            },
            options: {
                cutout: '1%',
                responsive: false,
                plugins: {
                    tooltip: false,
                    legend: false,
                    annotation: {
                        annotations: {
                            label: {
                                type: 'label',
                                content: `${progressValue}%`,
                                position: 'center'
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: (chart) => {
                    const {width, height, ctx} = chart;
                    ctx.save();
                    ctx.font = 'bold 15px sans-serif';
                    ctx.fillStyle = '#002244';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(`${progressValue}%`, width / 2, height / 1.9);
                    ctx.restore();
                }
            }]
        });
        }
    </script>

@endpush
