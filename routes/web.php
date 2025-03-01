<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     if (\Module::installedAndEnabled('Admin')) {
//         return redirect()->route('AuthController@login');
//     }
//     return view('welcome');
// });

Route::get('/', function () {
    if (\Module::installedAndEnabled('Website')) {
        return redirect()->route('WebsiteController@index');
    }
    return view('welcome');
});

Route::get('fcm-test', function () {

    // $NotificationListForUser = \Modules\Notifications\Entities\NotificationRecipient::ofUser(auth()->user())
    // ->with('Notification.translations', 'Data')
    // ->orderBy('created_at', 'DESC')
    // ->get();
    // dd($NotificationListForUser->toArray());

    $Notification = new \Modules\Notifications\Services\Notification;
    $Notification->type('TEST_NOTIFICATION');
    $Notification->locale('en');
    $Notification->title('Hello There!');
    $Notification->body('This is a test notification [notification_code]');
    $Notification->data('notification_code', '#000001');
    $Notification->saveAsTemplate();
    $Notification->locale('ar');
    $Notification->title('مرحبا بك!');
    $Notification->body('هذا الاشعار للتجربة [notification_code]');
    $Notification->saveAsTemplate();

    $Notification = new \Modules\Notifications\Services\Notification;
    $Notification->template('TEST_NOTIFICATION', 'ar');
    $Notification->data('notification_code', '#000001');

    $FCM = \Modules\Notifications\Services\Manager::firebase();
    $Log = $FCM->notification($Notification)->toAll()->send();
    dd($Log);

    // $Factory   = (new \Kreait\Firebase\Factory)->withServiceAccount(public_path('../' . config('notifications.providers.firebase.SERVER_KEY')));
    // $Messaging = $Factory->createMessaging();
    // $message   = \Kreait\Firebase\Messaging\CloudMessage::fromArray([
    //     'notification' => [
    //         'title' => 'Test Title',
    //         'body'  => 'Test Body Lorem Ipsum',
    //     ],
    //     'data' => [
    //         'k1' => 'v1',
    //         'k2' => 'v2',
    //     ],
    // ]);
    // $Token = 'cd2a51oPEDtdA9QYumojQP:APA91bHMuAEvDjuGqpGb3JJhc-_viCCwGWmelgHnzuS41agw8RcrTeDA2zvddETzx5uH6yxzLRTjIysq7Qh3rInGVl4HEtrFqnOh6QbOGIWiOKehNs5Uw0f7qhyxyRluC1mxhjg1SJkh';
    // $sendReport = $Messaging->sendMulticast($message, [
    //     $Token
    // ]);
    // dd($sendReport);
});

Route::get('test', function () {
    $Helpers = app(\Modules\SystemCore\System\Classes\Helpers::class);
    $Data = app(\Modules\Finance\Entities\CostCenterService::class)->withStructuredChildrens()->find(1)->data();
    // dd($Data->toArray());
    dd( $Helpers->treeCollapse('childrens', [$Data->toArray()])->pluck('id') );
});

Route::get('test-mail', function () {
    \Mail::to('mu-omar@outlook.com')
    ->send(new \Modules\Admin\Emails\ResetPasswordRequest(
        \App\Models\User::first()
    ));
    // \Mail::to('mu-omar@outlook.com')
    // ->send(new \Modules\Admin\Emails\EmailVerification(
    //     \App\Models\User::first()
    // ));
});

Route::get('preview-mail', function () {
    return new \Modules\Admin\Emails\ResetPasswordRequest(\App\Models\User::first());
    return new \Modules\Admin\Emails\EmailVerification(\App\Models\User::first());
});

Route::get('test-sms', function () {
    $user = \App\Models\User::where('phone_number', '+905050999888')->firstOrFail();
    $user->notify(
        new \Modules\Admin\Notifications\SmsNotification('Test Sms')
    );
});