@extends('admin::emails_v2.layout')

@section('content')
    <table style="width:1200px;max-width:1200px;margin:0 auto;background-color:#ffffff;">
        <tbody>
            <tr>
                <td style="text-align:left;padding: 30px 30px 15px 30px;">
                    <h2 style="font-size: 18px; color: #344357; font-weight: 600; margin: 0;">
                        Shipment Request
                    </h2>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;padding: 0 30px 20px">
                    <p style="margin-bottom: 10px;">Dear Transporter {{ ucwords($transportarUser->name) }},</p>
                    <p style="margin-bottom: 10px;">Shipment requested and waiting your approval.</p>
                    <p style="color: #FB4F28">If you are capable to meet the order, please click on “Confirm” to continue to next step</p>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;padding: 0 30px 20px">
                    <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #e0e0e0;">
                        <thead style="background-color: #f7f8fc;">
                            <tr style="font-weight: bold; font-size: 14px;">
                                <td style="padding: 10px 10px;">Client</td>
                                <td style="padding: 10px 10px;">From</td>
                                <td style="padding: 10px 10px;">To</td>
                                <td style="padding: 10px 10px;">Truck Type</td>
                                <td style="padding: 10px 10px;">Commodity type</td>
                                <td style="padding: 10px 10px;">Load Value</td>
                                <td style="padding: 10px 10px;">Contract Rate</td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Repeat as needed -->
                            @foreach ($shippments as $shippment)
                                <tr style="font-size: 14px;">
                                    <td style="padding: 10px 10px;">
                                        <span>{{ ucfirst($shippment->companyUser->company->company_name) }}</span>
                                        <br />
                                        <span style="font-size: 12px; color: #24bda6;">{{ ucfirst($shippment->companyUser->company->contact_name) }}</span>
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        {{ ucwords($shippment->fromCity->name_en) }}
                                        <br />
                                        <span style="font-size: 12px; color: #24bda6;">
                                            {{ ucwords($shippment->fromCountry->name_en) }}
                                        </span>
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        {{ ucwords($shippment->toCity->name_en) }}
                                        <br />
                                        <span style="font-size: 12px; color: #24bda6;">
                                            {{ ucwords($shippment->toCountry->name_en) }}
                                        </span>
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        <strong>{{ ucwords($shippment->truckType->__('title', 'en')) }}</strong>
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        <span style="background-color: #e6fff5; color: #24bda6; padding: 5px 10px; border-radius: 4px; display: inline-block;">
                                            {{ $shippment->commodities->map(function ($item) {
                                                return $item->commodity->__('title', 'en');
                                            })->implode(', ') }}
                                        </span>
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        {{ $helper->moneyFormat($shippment->decalred_value, 2)->abs }} SAR
                                    </td>
                                    <td style="padding: 10px 10px;">
                                        {{ $shippment->matchedTransportar ? $helper->moneyFormat($shippment->matchedTransportar->total_price, 2)->abs : '0.00' }} SAR
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Copy above <tr> block as needed for each shipment -->
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 20px 30px 40px">
                    <p>If you think you got this email by mistake, please ignore this email.</p>
                    <p style="margin-bottom: 15px;">We’re excited to have you join us!</p>
                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">
                        This is an automatically generated email please do not reply to this email. If you face any issues, please contact us at {{ config('mail_theme.appContactEmail') }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
@endsection