@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contentmanager::main.contract_details'),
        'items' => [
            [
                'title' => __('contentmanager::main.transporters'),
                'link'  => 'a'            ],
            [
                'title' => __('contentmanager::main.contract_details'),
                'link'  => 'b'
            ]
        ],
    ]
])
    
@endcomponent

@section('content')

<div class="order-details">
    <div class="container-fluid mt-4 order-info">
        <div class="row">
            <div class="col-lg-12">
                <div class="nk-content-inner">
                    <div class="card card-bordered h-100 border-unset-shadow">
                        <div class="card-inner">
                            <div class="card-title-group align-start gx-3 mb-3 main-title">
                                <div class="card-title">
                                    <h6 class="custom-title">{{__('contentmanager::main.contract_details')}}</h6>
                                    <p>{{__('contentmanager::main.details_of_the_contract')}}</p>
                                </div>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-custom-1 mt-3">{{__('contentmanager::main.active')}}</a>
                                </div>
                            </div>
                            <div class="row general-info">
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::orders.general_information')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/date.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.start_date')}}</span>
                                                <p class="value">March 01, 2024</p>
                                            </div>
                                            <div class="col-lg-6 mb-4">
                                                <img src="{{ asset('images/icons/date.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.end_date')}}</span>
                                                <p class="value">March 01, 2024</p>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/Commodity.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.payment_terms')}}</span>
                                                <p class="custom-p">
                                                    All invoices must be paid within 30 days of the invoice date unless otherwise agreed in writing.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <h4>
                                        {{__('contentmanager::main.trucks_and_commodity')}}
                                    </h4>
                                    <div class="box">
                                        <div class="row">
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/number_of_trucks.svg') }}" alt="">
                                                <span class="name">{{__('contentmanager::main.type_of_trucks')}}</span>
                                                <p class="value">
                                                    <a href="#" class="btn btn-custom-1 mt-3">Box Truck</a>
                                                    <a href="#" class="btn btn-custom-1 mt-3">Flatbed Truck</a>
                                                </p>
                                            </div>
                                            <div class="col-lg-12 mb-4">
                                                <img src="{{ asset('images/icons/number_of_pallets.svg') }}" class="modify-icon-1">
                                                <span class="name">{{__('contentmanager::orders.number_of_pallets')}}</span>
                                                <p class="value">
                                                    <a href="#" class="btn btn-custom-1 mt-3">Food and Beverages</a>
                                                    <a href="#" class="btn btn-custom-1 mt-3">Agricultural Products</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row general-info mt-6">
                                <div class="col-lg-12">
                                    <h4>
                                        {{__('contentmanager::main.destination')}}
                                    </h4>
                                </div>
                                <div class="col-lg-12">
                                    <table class="table custom-table mt-3">
                                        <thead>
                                            <tr>
                                                <th>{{__('contentmanager::main.destination_rate')}}</th>
                                                <th>{{__('contentmanager::main.loading_type')}}</th>
                                                <th>{{__('contentmanager::main.from')}}</th>
                                                <th>{{__('contentmanager::main.to')}}</th>
                                                <th>{{__('contentmanager::main.price')}}</th>
                                                <th>{{__('contentmanager::main.location_price_discount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Truck 1</td>
                                                <td>Box Truck</td>
                                                <td>123456</td>
                                                <td>2000 kg</td>
                                                <td>2000 kg</td>
                                                <td>2000 kg</td>
                                            </tr>
                                            <tr>
                                                <td>Truck 2</td>
                                                <td>Flatbed Truck</td>
                                                <td>654321</td>
                                                <td>5000 kg</td>
                                                <td>5000 kg</td>
                                                <td>5000 kg</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row general-info custom-tabs">
                                <div class="col-lg-12">
                                    <div class="card card-bordered card-preview border-unset-shadow">
                                        <div class="card-inner">
                                            <ul class="nav nav-tabs mt-n3" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabItem5" aria-selected="true" role="tab">
                                                        <img src="{{ asset('images/icons/conditions_and_terms.svg') }}" alt="">&nbsp;&nbsp;
                                                        <span>{{__('contentmanager::main.conditions_and_terms')}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tabItem6" aria-selected="true" role="tab">
                                                        <img src="{{ asset('images/icons/insurance_details.svg') }}" alt="">&nbsp;&nbsp;
                                                        <span>{{__('contentmanager::main.insurance_details')}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tabItem7" aria-selected="true" role="tab">
                                                        <img src="{{ asset('images/icons/panelties.svg') }}" alt="">&nbsp;&nbsp;
                                                        <span>{{__('contentmanager::main.panelties')}}</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tabItem8" aria-selected="true" role="tab">
                                                        <img src="{{ asset('images/icons/attachements.svg') }}" alt="">&nbsp;&nbsp;
                                                        <span>{{__('contentmanager::main.attachements')}}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tabItem5" role="tabpanel">
                                                    <p>Cillum ad ut irure tempor velit nostrud occaecat ullamco aliqua anim Lorem sint. Veniam sint duis incididunt do esse magna mollit excepteur laborum qui. Id id reprehenderit sit est eu aliqua occaecat quis et velit excepteur laborum mollit dolore eiusmod. Ipsum dolor in occaecat commodo et voluptate minim reprehenderit mollit pariatur. Deserunt non laborum enim et cillum eu deserunt excepteur ea incid.</p>
                                                </div>
                                                <div class="tab-pane" id="tabItem6" role="tabpanel">
                                                    <p>Culpa dolor voluptate do laboris laboris irure reprehenderit id incididunt duis pariatur mollit aute magna pariatur consectetur. Eu veniam duis non ut dolor deserunt commodo et minim in quis laboris ipsum velit id veniam. Quis ut consectetur adipisicing officia excepteur non sit. Ut et elit aliquip labore Lorem enim eu. Ullamco mollit occaecat dolore ipsum id officia mollit qui esse anim eiusmod do sint minim consectetur qui.</p>
                                                </div>
                                                <div class="tab-pane" id="tabItem7" role="tabpanel">
                                                    <p>Fugiat id quis dolor culpa eiusmod anim velit excepteur proident dolor aute qui magna. Ad proident laboris ullamco esse anim Lorem Lorem veniam quis Lorem irure occaecat velit nostrud magna nulla. Velit et et proident Lorem do ea tempor officia dolor. Reprehenderit Lorem aliquip labore est magna commodo est ea veniam consectetur.</p>
                                                </div>
                                                <div class="tab-pane" id="tabItem8" role="tabpanel">
                                                    <p>Eu dolore ea ullamco dolore Lorem id cupidatat excepteur reprehenderit consectetur elit id dolor proident in cupidatat officia. Voluptate excepteur commodo labore nisi cillum duis aliqua do. Aliqua amet qui mollit consectetur nulla mollit velit aliqua veniam nisi id do Lorem deserunt amet. Culpa ullamco sit adipisicing labore officia magna elit nisi in aute tempor commodo eiusmod.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        
    </script>
@endpush