<?php

return [
    'global' => [
        'Pre Defined Lists' => 'Predefined Data Lists',
    ],
    'privacy-policy' => [
        'title'       => 'Privacy Policy',
        'description' => 'Full text of the privacy policy',
    ],
    'terms-conditions' => [
        'title'       => 'Terms and Conditions',
        'description' => 'Full text of the terms and conditions',
    ],

    'profile-type' => [
        'menu'   => 'Account Types',
        'list'   => 'List of Account Types',
        'create' => 'Add New Account Type',
        'update' => 'Edit Account Type',
    ],
    'registration-type' => [
        'menu'   => 'Registration Types',
        'list'   => 'List of Registration Types',
        'create' => 'Add New Registration Type',
        'update' => 'Edit Registration Type',
    ],
    'payment-term' => [
        'menu'   => 'Contract Payment Terms',
        'list'   => 'List of Contract Payment Terms',
        'create' => 'Add New Payment Term',
        'update' => 'Edit Payment Term',
    ],
    'commodity' => [
        'menu'   => 'Commodity List',
        'list'   => 'Commodity List',
        'create' => 'Add New Commodity',
        'update' => 'Edit Commodity',
    ],
    'truck-type' => [
        'menu'   => 'Truck Types',
        'list'   => 'List of Truck Types',
        'create' => 'Add New Truck Type',
        'update' => 'Edit Truck Type',
    ],
    'load-size' => [
        'menu'   => 'Load Sizes',
        'list'   => 'List of Load Sizes',
        'create' => 'Add New Load Size',
        'update' => 'Edit Load Size',
    ],
    'load-type' => [
        'menu'   => 'Load Types',
        'list'   => 'List of Load Types',
        'create' => 'Add New Load Type',
        'update' => 'Edit Load Type',
    ],
    'orders' => [
        'menu'   => 'Orders',
        'list'   => 'Order List',
        'create' => 'Add New Order',
        'update' => 'Edit Order',
    ],
    'transporters' => [
        'menu'   => 'Transporters',
        'list'   => 'Transporter List',
        'create' => 'Add New Transporter',
        'update' => 'Edit Transporter',
    ],
    'contracts' => [
        'menu'   => 'Transport Contracts',
        'list'   => 'List of Transport Contracts',
        'create' => 'Add New Transport Contract',
        'update' => 'Edit Contract Details',
    ],
    'billing' => [
        'menu'   => 'Invoices',
        'list'   => 'Invoice List',
        'create' => 'Add New Invoice',
        'update' => 'Edit Invoice',
    ],
    'reports' => [
        'menu'   => 'Reports',
        'list'   => 'Report List',
        'create' => 'Add New Report',
        'update' => 'Edit Report',
    ],
    'settings' => [
        'menu'   => 'Settings',
        'list'   => 'Settings List',
        'create' => 'Add New Setting',
        'update' => 'Edit Setting',
    ],
    'contract_items' => [
        'header'           => 'Add / Edit Destination',
        'no_packages'      => 'No Destinations',
        'no_packages_desc' => 'No destinations or sources in the list. Add a new one to the contract by clicking the button below.',
        'add_new'          => 'Add New',
        'add'              => 'Add Destination',
        'save'             => 'Save Data',
    ],
    'shippments' => [
        'header' => 'Shipments',
        'menu'   => 'Shipments',
        'list'   => 'Shipment List',
        'create' => 'Add New Shipment',
        'update' => 'Edit Shipment',
        'steps'  => [
            'step1'      => 'Shipment Information',
            'step1_desc' => 'Fill in basic shipment information',
            'step2'      => 'Address Information',
            'step2_desc' => 'Fill in address information',
        ],
        'loading_address'                   => 'Loading Address',
        'offloading_address'                => 'Delivery Address(es)',
        'add_offloading_address'            => 'Add Address',
        'remove_offloading_address'         => 'Remove Address',
        'remove_offloading_address_confirm' => 'Are you sure you want to remove this address?',
        'save_as_draft'                     => 'Save as Draft',
        'save_and_send'                     => 'Save and Send to Appropriate Shipping Company',
        'unspecified_yet'                   => 'Not Specified Yet',
        'destination'                       => 'Destination',
        'save'                              => 'Save Shipment',
        'save_and_send_rfq'                 => 'Save and Send RFQ',
    ],
    'messages' => [
        'transportarApprove' => [
            'title'       => 'Shipment Approved',
            'description' => 'The shipment was approved successfully and the client has been notified',
        ],
        'transportarApproveWithPriceChange' => [
            'title'       => 'Shipment Approved with Price Change',
            'description' => 'The shipment was approved successfully and the client has been notified of the price change',
        ],
        'transportarReject' => [
            'title'       => 'Shipment Rejected',
            'description' => 'The shipment was rejected successfully and the client has been notified',
        ],
        'pricing_not_available' => [
            'title'       => 'No Pricing Available',
            'description' => 'No pricing available for the shipment. Please check the shipment',
        ],
        'shippment_not_available' => [
            'title'       => 'Shipment Not Available',
            'description' => 'Shipment not available. Please check the shipment',
        ],
        'clientApprovePriceChange' => [
            'title'       => 'Shipment Approved at New Price',
            'description' => 'The shipment was approved successfully and the transporter has been notified of the price acceptance',
        ],
        'clientRejectPriceChange' => [
            'title'       => 'Shipment Rejected Due to Price',
            'description' => 'The shipment was rejected successfully and the transporter has been notified of the price rejection',
        ],
        'postDriverAndWaybill' => [
            'title'       => 'Driver and Waybill Number Added',
            'description' => 'Driver assigned and waybill number added to the shipment successfully',
        ],
        'shippment_need_driver_and_waybill' => [
            'title'       => 'Shipment Needs Driver and Waybill Number',
            'description' => 'Shipment needs driver and waybill number. Please assign them',
        ],
        'shippment_delivered_to_location' => [
            'title'       => 'Shipment Delivered/Received at Location',
            'description' => 'Shipment was successfully delivered/received at the location',
        ],
        'select_one_transportar_at_least' => [
            'title'       => 'Select At Least One Transporter',
            'description' => 'Please select at least one transporter from the list to continue',
        ],
        'rfqs_sent' => [
            'title'       => 'RFQ Sent',
            'description' => 'RFQ sent successfully to the selected transporters',
        ],
        'clientApproveRfq' => [
            'title'       => 'RFQ Approved',
            'description' => 'RFQ approved successfully and the transporter has been notified',
        ],
        'clientRejectRfq' => [
            'title'       => 'RFQ Rejected',
            'description' => 'RFQ rejected successfully and the transporter has been notified',
        ],
    ],
    'public-transportars' => [
        'menu'   => 'Transport Companies',
        'list'   => 'List of Transport Companies',
        'create' => 'Add New Transport Company',
        'update' => 'Edit Transport Company Details',
    ],
    'shippments_rfqs' => [
        'menu'   => 'RFQ Requests',
        'list'   => 'List of RFQ Requests',
        'create' => 'Add New RFQ',
        'update' => 'Edit RFQ',
    ],
    'company_profile'       => 'Company Profile',
    'all_transporters'      => 'All Platform Transporters',
    'send_rfq_for_selected' => 'Send RFQ',
];
