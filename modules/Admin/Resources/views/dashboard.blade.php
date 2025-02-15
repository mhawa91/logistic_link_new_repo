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
    
@endcomponent

@section('content')
    <div id="vue-dashboard" class="row g-gs">

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
@endpush