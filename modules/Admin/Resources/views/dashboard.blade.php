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
        @media (max-width: 767px) {
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
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressValue = Number('{{ $progress }}');

        new Chart(ctx, {
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
    </script>

@endpush
