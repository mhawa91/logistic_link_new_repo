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

@section('content')
    <div id="vue-dashboard">
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
                            <div class="d-stastic-block1">
                                {{ __('contenttypes::dashboard.company_contracts_desc') }}
                                {{-- <div class="nk-sale-data">
                                    <span class="amount">
                                        14,299.59 
                                        <span class="sub-title">
                                            This Month
                                        </span>
                                    </span>
                                </div> --}}
                            </div>
                            <div class="d-stastic-block2">
                                <img width="55%" src="{{ asset('images/icons/company_contracts_1.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('TransportersController@index') }}">
                                <button class="btn btn-primary">
                                    {{ __('contenttypes::dashboard.add_your_first_transporter') }}
                                </button>
                            </a>
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