<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProviderTypeController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\HandymanController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProviderAddressMappingController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\ProviderDocumentController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\ProviderPayoutController;
use App\Http\Controllers\HandymanPayoutController;
use App\Http\Controllers\HandymanTypeController;
use App\Http\Controllers\ServiceFaqController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ReligionController;
use App\Http\Controllers\PostJobRequestController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingRatingController;
use App\Http\Controllers\HandymanRatingController;
use App\Http\Controllers\UserServiceListController;
use App\Http\Controllers\ProviderSlotController;
use App\Http\Controllers\NotificationTemplatesController;
use App\Http\Controllers\ServiceAddonController;
use App\Http\Controllers\FrontendSettingController;
use App\Http\Controllers\MailTemplatesController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Artisan;
//jabu
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Setting;
//owel
use App\Http\Controllers\DepotWalletController;
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



require __DIR__.'/auth.php';
require __DIR__.'/frontend.php';

Route::get('/create-storage-link', function () {
   // Execute the storage:link command
   Artisan::call('storage:link');
 
   // Redirect back or return a success message
   return 'Storage link created successfully!';
});
//jabu
Route::get('/manual-wallet', function () {
    $wallet = "";
    // $wallet = Wallet::create([
    //         'title' => "tohamie taher",
    //         'user_id' => 11160,
    //         'amount' => 0,
    // ]);
    
    if($wallet){
        echo " d";
    }
});
Route::get('/jabu-lat-long', [ProviderAddressMappingController::class, 'jabugetLatLong'])->name('jabugetLatLong');
Route::get('/get-long-lat', function () {
    $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
    $sitesetupdata = json_decode($sitesetup->value);
    $googleMapKey = $sitesetupdata->google_map_keys;
    config(['geocoder.providers.Geocoder\Provider\Chain\Chain.Geocoder\Provider\GoogleMaps\GoogleMaps' => [
        env('GOOGLE_MAPS_LOCALE', 'us'),
        $googleMapKey,
    ]]);
    $result =  app('geocoder')->geocode('16-18, Argyle Street, Camden, London, WC1H 8EG, United Kingdom')->get();
    $lat = null;
    $long = null;
    if ($result->isNotEmpty()) {
        $coordinates = $result[0]->first()->getCoordinates();
        $lat = $coordinates->getLatitude();
        $long = $coordinates->getLongitude();
    }
    echo $long;
        
});
Route::get('/get-sp-location', function () {
    $frommanila = DB::table('users')->where('state_id', 2852)->where('user_type', 'provider')->get();
    foreach($frommanila as $user){
        $state = DB::table('states')->where('id', $user->state_id)->first();
        $city  = DB::table('cities')->where('id', $user->city_id)->first();
        $s = isset($state->name) ? $state->name : "None";
        $c = isset($city->name) ? $city->name : "None";
        $a = isset($user->address) ? $user->address : "wala";
        echo "___________________________________________ <br><br>";
        echo "Provider Name : ".$user->display_name." <br>";
        echo "Provider address : ".$s." ".$c." <br>";
        echo "jabu : ".$a." <br>";
    }

});
Route::get('/testt', function () {
    $nueva = DB::table('users')->where('id', 9971)->first();
    $areas = explode(',', $nueva->area);
    foreach($areas as $area){
        $providers = DB::table('users')->where('city_id',$area)->where('user_type', 'provider')->get();
        foreach($providers as $provider){
            echo $provider->display_name."<br>";
        }
    }
    
});
Route::get('/get-service-cloc', function () {
    $info = [];
    $sp = DB::table('users')->where('user_type', 'provider')->get();
    foreach($sp as $provider){
        $service_array = [];
        $sp_service = DB::table('services')->where('provider_id', $provider->id)->get();
        if($sp_service){
           foreach($sp_service as $service){
               $check_addres = DB::table('provider_service_address_mappings')->where('service_id', $service->id)->first();
               if($check_addres){
                   $service_data = array(
                        'service_id' => $service->id,
                        'provider_id' => $service->provider_id,
                        'service_name' => $service->name,
                        'user_address' => $provider->address,
                        'address' => 'Meron'.' - '. $check_addres->id
                    );
               }else{
                   $service_data = array(
                        'service_id' => $service->id,
                        'provider_id' => $service->provider_id,
                        'service_name' => $service->name,
                        'user_address' => $provider->address,
                        'address' => 'Wala'
                    );
                    // $availablle_address = DB::table('provider_address_mappings')->where('provider_id', $service->provider_id)->first();
                    // if($availablle_address){
                    //     $update = DB::table('provider_service_address_mappings')->insert(['service_id' => $service->id, 'provider_address_id' => $availablle_address->id]);
                    // }
               }
                array_push($service_array, $service_data);
            }
            $data = array(
                'provider_id' => $provider->id,
                'provider_name' => $provider->display_name,
                'provider_email' => $provider->email,
                'provider_services' => $service_array
            );
            array_push($info, $data);
        }
        
    }
    $var2 = "";
    foreach($info as $sp_info){
        echo $sp_info['provider_name'].'PERSPTA ID: '.$sp_info['provider_id'].'<br>';
        $var = 'Service : [ <br> <br>';
        foreach($sp_info['provider_services'] as $servicessp){
            $var .= '________________________________________________<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;service ID: '.$servicessp['service_id'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;provider ID: '.$servicessp['provider_id'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;service name: '.$servicessp['service_name'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;service address id: '.$servicessp['address'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;user address: '.$servicessp['user_address'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;addresses : [ <br>';
            if($servicessp['address'] == 'Wala'){
                $var2 .= $sp_info['provider_name'].'<br>';
                $var2 .= $sp_info['provider_email'].'<br>';
                $var2 .= "----------------------------------<br>";
                $availablle_address = DB::table('provider_address_mappings')->where('provider_id', $servicessp['provider_id'])->get();
                if($availablle_address){
                    foreach($availablle_address as $newadd){
                        $var .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SERVICE ID: '.$servicessp['service_id'].'// PROVIDER_ADDRESS ID: '.$newadd->id.'<br>';
                       
                        $var .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;addresses: '.$newadd->address.'<br>';
                    }
                }
            }
            
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;]';
            $var .= ' <br>';
        }
        $var .= '] <br>________________________________________________<br><br>';
        echo $var;
    }
    echo "mga wala <br>";
    echo $var2;
    // return $info;
});
Route::get('/jabu-get-loc', function () {
    $getLocDepo = DB::table('users')->where('id', 9971)->first();
    $areas = explode(",",$getLocDepo->area);
    $arr = [];
    
    foreach($areas as $area){
        $providers = DB::table('users')->where('user_type', 'provider')->where('city_id', $area)->get();
        foreach($providers as $provider){
            $prov_name = $provider->display_name;
            $prov_id = $provider->id;
            $total_depot_earn = 0;
            $arrBook = [];
            $providerBookings = DB::table('bookings')->where('provider_id',$provider->id)->where('status', 'completed')->get();
            foreach($providerBookings as $bookings){
                $getService = DB::table('services')->where('id', $bookings->service_id)->first();
                $depoEarning = DB::table('earnings_city_manager')->where('booking_id',$bookings->id)->first();
                if($depoEarning){
                    $ifMeron = $depoEarning->id;
                    $depo_earn = $depoEarning->city_comm;
                    $total_depot_earn += $depo_earn;
                }else{
                    $ifMeron = 'wala';
                    $depo_earn = 0;
                    $total_depot_earn += $depo_earn;
                }
                
                $arr_booking = array(
                    'booking_id'=>$bookings->id,
                    'provider_id'=>$bookings->provider_id,
                    'service_name' => $getService->name,
                    'area'=>$area,
                    'amount' => $bookings->amount,
                    // 'earnings' => $ifMeron,
                    // 'depo_earn' => $depo_earn,
                    'date' => $bookings->date
                );
                
                
                array_push($arrBook, $arr_booking);
            }
            $pr = array(
                'provider_id'=>$prov_id,
                'Provider_name'=>$prov_name,
                // 'area'=>$area,
                // 'depo_earn_total' => $total_depot_earn,
                'bookings' => $arrBook
            );
            array_push($arr, $pr);
        }
    }
    //  dd($arr);
    foreach($arr as $arrey){
        // echo $arrey['provider_id'].'<br>';
        echo $arrey['Provider_name'].'/ ID: '.$arrey['provider_id'].'<br>';
        $var = 'Bookings : [ <br> <br>';
        foreach($arrey['bookings'] as $booking){
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;Booking ID: '.$booking['booking_id'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;Service Name: '.$booking['service_name'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;Amount: '.$booking['amount'].'<br>';
            $var .= '&nbsp;&nbsp;&nbsp;&nbsp;Date: '.$booking['date'].'<br>';
            $var .= ' <br>';
        }
        $var .= '] <br>________________________________________________<br><br>';
        echo $var;
        
    }
//   return 'Storage link created successfully!';
});
//end jabu

Route::group(['prefix' => 'auth'], function() {
    Route::get('login', [HomeController::class, 'authLogin'])->name('auth.login');
    Route::get('register', [HomeController::class, 'authRegister'])->name('auth.register');
    Route::get('recover-password', [HomeController::class, 'authRecoverPassword'])->name('auth.recover-password');
    Route::get('confirm-email', [HomeController::class, 'authConfirmEmail'])->name('auth.confirm-email');
    Route::get('lock-screen', [HomeController::class, 'authlockScreen'])->name('auth.lock-screen');
});

Route::get('lang/{locale}', [HomeController::class,'lang'])->name('switch-language');
Route::get('/verify/{id}', [VerificationController::class, 'verify'])->name('verify');

Route::group(['middleware' => ['auth', 'verified']], function()
{
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::group(['namespace' => '', 'middleware' => ['permission:permission list']], function () {
        Route::resource('permission',PermissionController::class);
        Route::get('permission/add/{type}',[PermissionController::class,'addPermission'])->name('permission.add');
        Route::post('permission/save',[PermissionController::class,'savePermission'])->name('permission.save');

    });
    //jabu login as provider
    Route::get('loginas/{id}',[HomeController::class,'login_as'])->name('login.as');
    //end jabu
    //jabu dashboards
    Route::get('neo-tag-history',[HomeController::class,'neo_tag_history'])->name('neo_tag_history');
    Route::get('neo-tag-upline-history',[HomeController::class,'neo_tag_upline_history'])->name('neo_tag_upline_history');
    //end jabu dashboards
    //jabu encashment
    Route::get('depot-table',[HomeController::class,'depot_table'])->name('depot_table');
    Route::get('encashment-table',[HomeController::class,'encashment_table'])->name('encashment_table');
    Route::get('/encashment', [HomeController::class, 'encashment_index'])->name('encashment');
    Route::post('depot-encashment',[HomeController::class,'depot_encashment'])->name('depot_encashment');
    Route::post('admin-encashment',[HomeController::class,'admin_encashment'])->name('admin_encashment');
    Route::get('encashment/delete/{id}', [HomeController::class, 'encashment_delete'])->name('encashment_delete');
    //end jabu encashment
    //jabu commission
    Route::group(['middleware' => ['permission:Commission']], function () {
        Route::get('commission', [BookingController::class, 'commission'])->name('commission.com');
        Route::get('commission/update', [BookingController::class, 'commission_update'])->name('commission.update');
    });
    //end jabu commission
    
    //jabu earning history
    Route::get('/earning-history', [HomeController::class, 'earning_history_index'])->name('earning_history');
    Route::get('earning-history-table',[HomeController::class,'earning_history_table'])->name('earning_history_table');
    //edn jabu earning history
    
    Route::group(['middleware' => ['permission:role list']], function () {
        Route::resource('role', RoleController::class);
        Route::get('role-index-data',[RoleController::class,'index_data'])->name('role.index_data');
        Route::post('role-bulk-action', [RoleController::class, 'bulk_action'])->name('role.bulk-action');
    });

    Route::get('changeStatus', [ HomeController::class, 'changeStatus'])->name('changeStatus');

    Route::group(['middleware' => ['permission:category list']], function () {
        Route::resource('category', CategoryController::class);
        Route::get('index_data',[CategoryController::class,'index_data'])->name('category.index_data');
        Route::post('category-bulk-action', [CategoryController::class, 'bulk_action'])->name('category.bulk-action');
        Route::post('category-action',[CategoryController::class, 'action'])->name('category.action');
        Route::post('category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
        Route::post('check-in-trash', [CategoryController::class, 'check_in_trash'])->name('check-in-trash');

    });

    Route::group(['middleware' => ['permission:service list']], function () {
        Route::resource('service', ServiceController::class);
        Route::get('service-index-data',[ServiceController::class,'index_data'])->name('service.service-index-data');
        Route::post('service-bulk-action', [ServiceController::class, 'bulk_action'])->name('service.bulk-action');
        Route::get('user-service-list',[ServiceController::class,'getUserServiceList'])->name('service.user-service-list');
        Route::post('service-action',[ServiceController::class, 'action'])->name('service.action');
        Route::post('service/{id}', [ServiceController::class, 'destroy'])->name('service.destroy');
        Route::get('user-service-index-data',[UserServiceListController::class,'index_data'])->name('service.user-index-data');

    });
    Route::get('provider-change-password', [ ProviderController::class , 'getChangePassword'])->name('provider.getchangepassword');
    Route::post('provider-change-password', [ ProviderController::class , 'changePassword'])->name('provider.changepassword');
    Route::get('provider-time-slot/{id}',[ProviderController::class,'getProviderTimeSlot'])->name('provider.time-slot');
    Route::get('provider-edit-time-slot',[ProviderController::class,'editProviderTimeSlot'])->name('provider.edit-time-slot');
    Route::post('provider-save-slot', [ProviderSlotController::class, 'store'] )->name('providerslot.store');
    Route::group(['middleware' => ['permission:provider list']], function () {
        Route::resource('provider', ProviderController::class);
        Route::get('provider/list/{status?}', [ProviderController::class,'index'])->name('provider.pending');
        Route::get('provider-index-data',[ProviderController::class,'index_data'])->name('provider.index_data');
        Route::get('provider/approve/{id}',[ProviderController::class, 'approve'])->name('provider.approve');
        Route::post('provider-action',[ProviderController::class, 'action'])->name('provider.action');
        Route::post('provider/{id}', [ProviderController::class, 'destroy'])->name('provider.destroy');
        Route::post('provider-bulk-action', [ProviderController::class, 'bulk_action'])->name('provider.bulk-action');
    });

    Route::group(['middleware' => ['permission:provideraddress list']], function () {
        Route::resource('provideraddress', ProviderAddressMappingController::class);
        Route::get('provideraddress-index-data',[ProviderAddressMappingController::class,'index_data'])->name('provideraddress.index_data');
        Route::post('provideraddress-bulk-action', [ProviderAddressMappingController::class, 'bulk_action'])->name('provideraddress.bulk-action');
        Route::post('provideraddress/{id}', [ProviderAddressMappingController::class, 'destroy'])->name('provideraddress.destroy');
        Route::post('/get-lat-long', [ProviderAddressMappingController::class, 'getLatLong'])->name('getLatLong');
    });

    Route::group(['middleware' => ['permission:providertype list']], function () {
        Route::resource('providertype', ProviderTypeController::class);
        Route::get('providertype-index-data',[ProviderTypeController::class,'index_data'])->name('providertype.index_data');
        Route::post('providertype-bulk-action', [ProviderTypeController::class, 'bulk_action'])->name('providertype.bulk-action');
        Route::post('providertype-action',[ProviderTypeController::class, 'action'])->name('providertype.action');
        Route::post('providertype/{id}', [ProviderTypeController::class, 'destroy'])->name('providertype.destroy');
    });
    Route::get('handyman-change-password', [ HandymanController::class , 'getChangePassword'])->name('handyman.getchangepassword');
    Route::post('handyman-change-password', [ HandymanController::class , 'changePassword'])->name('handyman.changepassword');
    Route::group(['middleware' => ['permission:handyman list']], function () {
        Route::resource('handyman', HandymanController::class);
        Route::get('handyman/list/{status?}', [HandymanController::class,'index'])->name('handyman.pending');
        Route::get('handyman-index-data',[HandymanController::class,'index_data'])->name('handyman.index_data');
        Route::post('handyman-bulk-action', [HandymanController::class, 'bulk_action'])->name('handyman.bulk-action');
        Route::get('handyman/approve/{id}',[ProviderController::class, 'approve'])->name('handyman.approve');
        Route::post('handyman-action',[HandymanController::class, 'action'])->name('handyman.action');
        Route::post('handyman/{id}', [HandymanController::class, 'destroy'])->name('handyman.destroy');
        Route::post('assign-provider', [HandymanController::class, 'updateProvider'])->name('handyman.updateProvider');
        Route::get('handymandetail/{id}', [HandymanController::class, 'handyman_detail'])->name('handyman.detail');
    });

    Route::group(['middleware' => ['permission:coupon list']], function () {
        Route::resource('coupon', CouponController::class);
        Route::get('coupon-index_data',[CouponController::class,'index_data'])->name('coupon.index_data');
        Route::post('coupon-bulk-action', [CouponController::class, 'bulk_action'])->name('coupon.bulk-action');
        Route::post('coupons-action',[CouponController::class, 'action'])->name('coupon.action');
        Route::post('coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.destroy');
    });

    Route::group(['middleware' => ['permission:booking list']], function () {
        Route::resource('booking', BookingController::class);
        Route::get('booking-index-data',[BookingController::class,'index_data'])->name('booking.index_data');
        Route::post('booking-bulk-action', [BookingController::class, 'bulk_action'])->name('booking.bulk-action');
        Route::post('booking-status-update',[ BookingController::class,'updateStatus'])->name('bookingStatus.update');
        Route::post('booking-save', [ App\Http\Controllers\BookingController::class, 'store' ] )->name('booking.save');
        Route::post('booking-action',[BookingController::class, 'action'])->name('booking.action');
        Route::post('booking/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
        //jabu
        Route::get('jabubooking',[BookingController::class,'jabu'])->name('booking.jabu');
    });

    Route::group(['middleware' => ['permission:slider list']], function () {
        Route::resource('slider', SliderController::class);
        Route::get('slider-index-data',[SliderController::class,'index_data'])->name('slider.index_data');
        Route::post('slider-bulk-action', [SliderController::class, 'bulk_action'])->name('slider.bulk-action');
        Route::post('slider-action',[SliderController::class, 'action'])->name('slider.action');
        Route::post('slider/{id}', [SliderController::class, 'destroy'])->name('slider.destroy');
    });

    Route::resource('payment', PaymentController::class);
    Route::get('cash-payment-list', [PaymentController::class,'cashDatatable'])->name('cash.list');
    Route::get('cash-index-data', [PaymentController::class,'cash_index_data'])->name('cash.index_data');
    Route::get('payment-index-data',[PaymentController::class,'index_data'])->name('payment.index_data');
    Route::post('payment-bulk-action', [PaymentController::class, 'bulk_action'])->name('payment.bulk-action');
    Route::get('cash/history/{id?}', [PaymentController::class,'cashIndex'])->name('cash.index');
    Route::get('paymenthistory-index-data/{id}', [PaymentController::class,'paymenthistory_index_data'])->name('paymenthistory.index_data');
    Route::get('cash/approve/{id}',[PaymentController::class, 'cashApprove'])->name('cash.approve');

    Route::post('save-payment',[App\Http\Controllers\API\PaymentController::class, 'savePayment'])->name('payment.save');
    Route::get('save-stripe-payment/{id}',[App\Http\Controllers\BookingController::class, 'saveStripePayment']);

    

    Route::get('user-change-password', [ CustomerController::class , 'getChangePassword'])->name('user.getchangepassword');
    Route::post('user-change-password', [ CustomerController::class , 'changePassword'])->name('user.changepassword');
    //jabu reset pass
    Route::get('user-reset-password', [ CustomerController::class , 'userResetPassword'])->name('user.userResetPassword'); 
    //end reset pass
    Route::group(['middleware' => ['permission:user list']], function () {
        Route::resource('user', CustomerController::class);
        Route::get('user/list/{status?}', [CustomerController::class,'index'])->name('user.all');
        Route::get('user-index-data',[CustomerController::class,'index_data'])->name('user.index_data');
        Route::post('user-bulk-action', [CustomerController::class, 'bulk_action'])->name('user.bulk-action');
        Route::post('user-action',[CustomerController::class, 'action'])->name('user.action');
        Route::post('user/{id}', [CustomerController::class, 'destroy'])->name('user.destroy');
        //jabu
        Route::get('tag/list/{status?}', [CustomerController::class,'index_tagging'])->name('tag.all');
        Route::get('tag-data-table',[CustomerController::class,'tag_data_table'])->name('tag.tag_data_table');
        Route::get('tag-update-page',[CustomerController::class,'update_tag'])->name('tag.tag_update_page');
        //jabu neo search
        Route::get('ajax-search-neo',[CustomerController::class,'search_neo'])->name('tag.search_neo');
        Route::get('ajax-add-neo',[CustomerController::class,'add_neo'])->name('tag.add_neo');
        Route::get('ajax-remove-neo',[CustomerController::class,'remove_neo'])->name('tag.remove_neo');
    });

    Route::get('booking-assign-form/{id}',[BookingController::class,'bookingAssignForm'])->name('booking.assign_form');
    Route::get('booking/details/{id}',[BookingController::class,'bookingDetails'])->name('booking.details');
    Route::post('booking-assigned',[BookingController::class,'bookingAssigned'])->name('booking.assigned');
    Route::get('comission/{id}',[SettingController::class,'comission'])->name('setting.comission');
    Route::get('details/{id}',[BookingController::class,'bookingDetailsData'])->name('booking.detailsdata');


    // Setting
    Route::get('setting/{page?}',[ SettingController::class, 'settings'])->name('setting.index');
    Route::post('/layout-page',[ SettingController::class, 'layoutPage'])->name('layout_page');

    // Route::post('settings/save',[ SettingController::class , 'settingsUpdates'])->name('settingsUpdates');
    // Route::post('dashboard-setting',[ SettingController::class , 'dashboardtogglesetting'])->name('togglesetting');
    // Route::post('provider-dashboard-setting',[ SettingController::class , 'providerdashboardtogglesetting'])->name('providertogglesetting');
    // Route::post('handyman-dashboard-setting',[ SettingController::class , 'handymandashboardtogglesetting'])->name('handymantogglesetting');
    // Route::post('config-save',[ SettingController::class , 'configUpdate'])->name('configUpdate');


    Route::post('env-setting', [ SettingController::class , 'envChanges'])->name('envSetting');
    Route::post('update-profile', [ SettingController::class , 'updateProfile'])->name('updateProfile');
    Route::post('change-password', [ SettingController::class , 'changePassword'])->name('changePassword');


    //Frontend Setting

    Route::middleware(['auth', 'role:admin|demo_admin'])->group(function () {
        Route::get('frontend-setting/{page?}', [FrontendSettingController::class, 'frontendSettings'])->name('frontend_setting.index');
        Route::post('/layout-frontend-page', [FrontendSettingController::class, 'layoutPage'])->name('layout_frontend_page');
        Route::post('/landing-page-settings-updates', [FrontendSettingController::class, 'landingpagesettingsUpdates'])->name('landing_page_settings_updates');
        Route::post('/landing-layout-page', [FrontendSettingController::class, 'landingLayoutPage'])->name('landing_layout_page');
        Route::post('/get-landing-layout-page-config', [FrontendSettingController::class , 'getLandingLayoutPageConfig'])->name('getLandingLayoutPageConfig');
        Route::post('/header-page-settings', [FrontendSettingController::class, 'headingpagesettings'])->name('heading_page_settings');
        Route::post('/footer-page-settings', [FrontendSettingController::class, 'footerpagesettings'])->name('footer_page_settings');
        Route::post('/login-register-page-settings', [FrontendSettingController::class, 'loginregisterpagesettings'])->name('login_register_page_settings');
    });

    Route::get('notification-list',[ NotificationController::class ,'notificationList'])->name('notification.list');
    Route::get('notification-counts',[ NotificationController::class ,'notificationCounts'])->name('notification.counts');
    Route::get('notification',[ NotificationController::class ,'index'])->name('notification.index');
    Route::get('notification-index-data',[ NotificationController::class ,'index_data'])->name('notification.index_data');

    Route::post('remove-file', [ App\Http\Controllers\HomeController::class, 'removeFile' ] )->name('remove.file');
    Route::post('get-lang-file', [ App\Http\Controllers\LanguageController::class, 'getFile' ] )->name('getLangFile');
    Route::post('save-lang-file', [ App\Http\Controllers\LanguageController::class, 'saveFileContent' ] )->name('saveLangContent');

    Route::group(['middleware' => ['permission:terms condition']], function () {
        Route::get('pages/term-condition',[ SettingController::class, 'termAndCondition'])->name('term-condition');
        Route::post('term-condition-save',[ SettingController::class, 'saveTermAndCondition'])->name('term-condition-save');
    });

    Route::group(['middleware' => ['permission:privacy policy']], function () {
        Route::get('pages/privacy-policy',[ SettingController::class, 'privacyPolicy'])->name('privacy-policy');
        Route::post('privacy-policy-save',[ SettingController::class, 'savePrivacyPolicy'])->name('privacy-policy-save');
    });

    Route::get('pages/data-deletion-request',[ SettingController::class, 'dataDeletion'])->name('data-deletion-request');
    Route::post('data-deletion-request-save',[ SettingController::class, 'saveDataDeletion'])->name('data-deletion-request-save');

    Route::get('pages/help-support',[ SettingController::class, 'helpAndSupport'])->name('help-support');
    Route::post('help-support-save',[ SettingController::class, 'saveHelpAndSupport'])->name('help-support-save');

    Route::get('pages/refund-cancellation-policy',[ SettingController::class, 'refundCancellationPolicy'])->name('refund-cancellation-policy');
    Route::post('refund-cancellation-policy-save',[ SettingController::class, 'saveRefundCancellationPolicy'])->name('refund-cancellation-policy-save');

    Route::post('general-setting-save',[ SettingController::class, 'generalSetting'])->name('generalsetting');
    Route::post('theme-setup-save',[ SettingController::class, 'themeSetup'])->name('themesetup');
    Route::post('site-setup-save',[ SettingController::class, 'siteSetup'])->name('sitesetup');
    Route::post('service-config-save',[ SettingController::class, 'serviceConfig'])->name('serviceConfig');
    Route::post('social-media-save',[ SettingController::class, 'socialMedia'])->name('socialMedia');
    route::post('role-permission',[RoleController::class,'rolePermission'])->name('role_layout_page');
    Route::post('cookie-setup-save',[ SettingController::class, 'cookieSetup'])->name('cookiesetup');

    Route::group(['middleware' => ['permission:document list|providerdocument list']], function () {
        Route::resource('document', DocumentsController::class);
        Route::get('document-index-data',[DocumentsController::class,'index_data'])->name('document.index_data');
        Route::post('document-bulk-action', [DocumentsController::class, 'bulk_action'])->name('document.bulk-action');
        Route::post('document-action',[DocumentsController::class, 'action'])->name('document.action');
        Route::post('document/{id}', [DocumentsController::class, 'destroy'])->name('document.destroy');
    });

    Route::group(['middleware' => ['permission:providerdocument list']], function () {
        Route::resource('providerdocument', ProviderDocumentController::class);
        Route::get('providerdocument-index-data',[ProviderDocumentController::class,'index_data'])->name('providerdocument.index_data');
        Route::post('providerdocument-bulk-action', [ProviderDocumentController::class, 'bulk_action'])->name('providerdocument.bulk-action');
        Route::post('providerdocument-action',[ProviderDocumentController::class, 'action'])->name('providerdocument.action');
        Route::post('providerdocument/{id}', [ProviderDocumentController::class, 'destroy'])->name('providerdocument.destroy');
    });

    Route::resource('ratingreview', RatingReviewController::class);
    Route::post('ratingreview-action',[RatingReviewController::class, 'action'])->name('ratingreview.action');
    Route::get('ratingreview-index-data',[RatingReviewController::class,'index_data'])->name('ratingreview.index_data');

    Route::resource('booking-rating', BookingRatingController::class);
    Route::get('booking-rating-index-data',[BookingRatingController::class,'index_data'])->name('booking-rating.index_data');
    Route::post('booking-rating-bulk-action', [BookingRatingController::class, 'bulk_action'])->name('booking-rating.bulk-action');
    Route::post('booking-rating/{id}', [BookingController::class, 'destroy'])->name('booking-rating.destroy');
    Route::post('booking-rating-action',[CouponController::class, 'action'])->name('booking-rating.action');

    Route::resource('handyman-rating', HandymanRatingController::class);
    Route::get('handyman-rating-index-data',[HandymanRatingController::class,'index_data'])->name('handyman-rating.index_data');
    Route::post('handyman-rating-bulk-action', [HandymanRatingController::class, 'bulk_action'])->name('handyman-rating.bulk-action');
    Route::post('handyman-rating/{id}', [HandymanController::class, 'destroy'])->name('handyman-rating.destroy');

    Route::post('/payment-layout-page',[ PaymentGatewayController::class, 'paymentPage'])->name('payment_layout_page');
    Route::post('payment-settings/save',[ PaymentGatewayController::class , 'paymentsettingsUpdates'])->name('paymentsettingsUpdates');
    Route::post('get_payment_config',[ PaymentGatewayController::class , 'getPaymentConfig'])->name('getPaymentConfig');

    Route::post('/razorpay-layout-page',[ PaymentGatewayController::class, 'rezorpaypaymentPage'])->name('razorpay_layout_page');

    Route::resource('tax', TaxController::class);
    Route::get('tax-index_data',[TaxController::class,'index_data'])->name('tax.index_data');
    Route::post('tax-bulk-action', [TaxController::class, 'bulk_action'])->name('tax.bulk-action');
    Route::post('tax/{id}', [TaxController::class, 'destroy'])->name('tax.destroy');
    Route::get('earning',[EarningController::class,'index'])->name('earning');
    Route::get('earning-data',[EarningController::class,'setEarningData'])->name('earningData');
    Route::post('earning/{id}', [EarningController::class, 'destroy'])->name('earning.destroy');
    Route::get('earning/{id}', [EarningController::class, 'show'])->name('earning.show');

    Route::get('handyman-earning',[EarningController::class,'handymanEarning'])->name('handymanEarning');
    Route::get('handyman-earning-data',[EarningController::class,'handymanEarningData'])->name('handymanEarningData');

    Route::resource('providerpayout', ProviderPayoutController::class);
    Route::get('providerpayout-index-data',[ProviderPayoutController::class,'index_data'])->name('providerpayout.index_data');
    Route::post('providerpayout-bulk-action', [ProviderPayoutController::class, 'bulk_action'])->name('providerpayout.bulk-action');
    Route::get('providerpayout/create/{id}', [ProviderPayoutController::class,'create'])->name('providerpayout.create');
    Route::get('provider-payout-index-data/{id}',[ProviderPayoutController::class,'ProviderPayout_index_data'])->name('providerpayout.ProviderPayout_index_data');

    Route::get('review/{id}',[ProviderController::class,'review'])->name('provider.review');
    Route::post('sidebar-reorder-save',[ SettingController::class, 'sequenceSave'])->name('reorderSave');

    Route::resource('handymanpayout', HandymanPayoutController::class);
    Route::get('handymanpayout-index-data',[HandymanPayoutController::class,'index_data'])->name('handymanpayout.index_data');
    Route::post('handymanpayout-bulk-action', [HandymanPayoutController::class, 'bulk_action'])->name('handymanpayout.bulk-action');
    Route::get('handymanpayout/create/{id}', [HandymanPayoutController::class,'create'])->name('handymanpayout.create');
    Route::get('handymanpayoutcreate/create/{id}', [HandymanPayoutController::class,'handymanpayoutcreate'])->name('handymanpayoutcreate.create');
   

    Route::group(['middleware' => ['permission:handymantype list']], function () {
        Route::resource('handymantype', HandymanTypeController::class);
        Route::get('handyman-index_data',[HandymanTypeController::class,'index_data'])->name('handymantype.index_data');
        Route::post('handymantype-bulk-action', [HandymanTypeController::class, 'bulk_action'])->name('handymantype.bulk-action');
        Route::post('handymantype-action',[HandymanTypeController::class, 'action'])->name('handymantype.action');
        Route::post('handymantype/{id}', [HandymanTypeController::class, 'destroy'])->name('handymantype.destroy');
    });

    Route::group(['middleware' => ['permission:servicefaq list']], function () {
        Route::resource('servicefaq', ServiceFaqController::class);
        Route::get('servicefaq-index-data',[ServiceFaqController::class,'index_data'])->name('servicefaq.index_data');
    });
    Route::match(['get', 'post'], '/push-notification', [SettingController::class, 'PushNotification'])->name('pushNotification.index');
    Route::post('send-push-notification', [ SettingController::class , 'sendPushNotification'])->name('sendPushNotification');
    Route::post('save-earning-setting', [ SettingController::class , 'saveEarningTypeSetting'])->name('saveEarningTypeSetting');
    Route::post('save-userdashboard-setting', [ SettingController::class , 'saveUserDashboardTypeSetting'])->name('saveUserDashboardTypeSetting');
    // Route::post('advance-earning-setting' , [ SettingController::class , 'advanceEarningSetting'])->name('advanceEarningSetting');
    Route::post('other-setting' , [ SettingController::class , 'otherSetting'])->name('otherSetting');

    // Route::post('enable-user-wallet', [SettingController::class, 'enableUserWallet'])->name('enableUserWallet');
    //Depot Wallet -owel
    Route::resource('walletdepot', DepotWalletController::class);
    Route::get('wallet-depot-history',[DepotWalletController::class,'walletdepothistory_index'])->name('walletdepot.history_index');
    Route::get('wallet-depot-history-index-data',[DepotWalletController::class,'walletdepothistoryindex_data'])->name('walletdepothistory.index_data');
    Route::get('wallet-depot-index-data',[DepotWalletController::class,'index_data'])->name('walletdepot.index_data');
    Route::post('wallet-depot-bulk-action', [DepotWalletController::class, 'bulk_action'])->name('walletdepot.bulk-action');
    Route::post('wallet-depot/{id}', [DepotWalletController::class, 'destroy'])->name('walletdepot.destroy');
    
    //Religion -owel
    Route::resource('religion', ReligionController::class);
    Route::get('religion-index_data',[ReligionController::class,'index_data'])->name('religion.index_data');
    Route::post('religion-bulk-action', [ReligionController::class, 'bulk_action'])->name('religion.bulk-action');
    Route::post('religion/{id}', [ReligionController::class, 'destroy'])->name('religion.destroy');
    
    Route::resource('wallet', WalletController::class);
    Route::get('wallet-index-data',[WalletController::class,'index_data'])->name('wallet.index_data');
    Route::post('wallet-bulk-action', [WalletController::class, 'bulk_action'])->name('wallet.bulk-action');
    Route::post('wallet/{id}', [WalletController::class, 'destroy'])->name('wallet.destroy');
    Route::get('wallet-history-index-data/{id}',[WalletController::class,'wallethistory_index_data'])->name('wallethistory.index_data');

    Route::get('withdrawal-request',[WalletController::class,'wallet_transaction_index'])->name('wallet_transaction');
    Route::get('withdrawal-request-index-data',[WalletController::class,'wallet_transaction_index_data'])->name('wallet_transaction.index_data');
    Route::get('withdrawal-request-payout/{id}', [WalletController::class, 'wallet_transaction_payout'])->name('wallet.wallet_transaction_payout');
    

    Route::group(['middleware' => ['permission:subcategory list']], function () {
        Route::resource('subcategory', SubCategoryController::class);
        Route::get('sub-index-data',[SubCategoryController::class,'index_data'])->name('subcategory.sub-index-data');
        Route::post('sub-bulk-action', [SubCategoryController::class, 'bulk_action'])->name('sub-bulk-action');
        Route::post('subcategory-action',[SubCategoryController::class, 'action'])->name('subcategory.action');
        Route::post('subcategory/{id}', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');
    });

    Route::resource('plans', PlanController::class);
    Route::get('plans-index-data',[PlanController::class,'index_data'])->name('plans.index_data');
    Route::post('plans-bulk-action', [PlanController::class, 'bulk_action'])->name('plans.bulk-action');
    Route::post('plans/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');

    Route::resource('bank',BankController::class);
    Route::get('bank-index-data',[BankController::class, 'index_data'])->name('bank.index_data');
    Route::post('bank-bulk-action',[BankController::class,'bulk_action'])->name('bank.bulk_action');
    Route::post('bank-action',[BankController::class, 'action'])->name('bank.action');
    Route::get('bank/create/', [BankController::class,'create'])->name('bank.create');
    Route::get('bank/edit/{id}', [BankController::class,'edit'])->name('bank.edit');
    Route::get('bank-list/{user_id}',[BankController::class, 'banklist'])->name('bank.list');


    Route::get('/provider-detail-page',[ ProviderController::class, 'providerDetail'])->name('provider_detail_pages');
    Route::post('/provider-detail-page',[ ProviderController::class, 'providerDetail'])->name('provider_detail_pages');
    Route::post('/booking-layout-page/{id}',[ BookingController::class, 'bookingstatus'])->name('booking_layout_page');
    Route::get('/invoice_pdf/{id}', [BookingController::class, 'createPDF'])->name('invoice_pdf');

    Route::group(['middleware' => ['permission:postjob list']], function () {
        Route::resource('post-job-request', PostJobRequestController::class);
        Route::get('post-job-index-data',[PostJobRequestController::class,'index_data'])->name('post-job.index_data');
        Route::post('post-job-bulk-action', [PostJobRequestController::class, 'bulk_action'])->name('post-job.bulk-action');
        Route::get('post-job-service/list/{postjobid?}', [ServiceController::class, 'index'])->name('postjobrequest.service');
        Route::get('postrequest-index-data/{id}', [PostJobRequestController::class,'postrequest_index_data'])->name('postrequest.index_data');
    });

    Route::group(['middleware' => ['permission:servicepackage list']], function () {
        Route::resource('servicepackage', ServicePackageController::class);
        Route::get('servicepackage/list/{packageid?}', [ServiceController::class,'index'])->name('servicepackage.service');
        Route::get('servicepackage-index-data',[ServicePackageController::class,'index_data'])->name('servicepackage.index-data');
        Route::post('servicepackage-bulk-action', [ServicePackageController::class, 'bulk_action'])->name('servicepackage.bulk-action');
        Route::post('servicepackage-action',[ServicePackageController::class, 'action'])->name('servicepackage.action');
    });

    Route::group(['middleware' => ['permission:blog list']], function () {
        Route::resource('blog', BlogController::class);
        Route::get('blog-index-data',[BlogController::class,'index_data'])->name('blog.index_data');
        Route::post('blog-bulk-action', [BlogController::class, 'bulk_action'])->name('blog.bulk-action');
        Route::post('blog-action',[BlogController::class, 'action'])->name('blog.action');
        Route::post('blog/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');
    });

    Route::group(['middleware' => ['permission:service list']], function () {
        Route::resource('serviceaddon', ServiceAddonController::class);
        Route::get('serviceaddon-index-data',[ServiceAddonController::class,'index_data'])->name('serviceaddon.index-data');
        Route::post('serviceaddon-bulk-action', [ServiceAddonController::class, 'bulk_action'])->name('serviceaddon.bulk-action');
    });

    Route::group(['prefix' => 'notifications-templates', 'as' => 'notificationtemplates.'], function () {
        Route::get('index_list', [NotificationTemplatesController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [NotificationTemplatesController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [NotificationTemplatesController::class, 'trashed'])->name('trashed');
        Route::patch('trashed/{id}', [NotificationTemplatesController::class, 'restore'])->name('restore');
        Route::get('ajax-list', [NotificationTemplatesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('notification-buttons', [NotificationTemplatesController::class, 'notificationButton'])->name('notification-buttons');
        Route::get('notification-template', [NotificationTemplatesController::class, 'notificationTemplate'])->name('notification-template');
        Route::post('channels-update', [NotificationTemplatesController::class, 'updateChanels'])->name('settings.update');
        Route::post('update-status/{id}', [NotificationTemplatesController::class, 'update_status'])->name('update_status');
        Route::get('fetchnotification_data', [NotificationTemplatesController::class, 'fetchNotificationData'])->name('fetchnotification_data');
       
    });
    Route::post('notification-template-bulk-action', [NotificationTemplatesController::class, 'bulk_action'])->name('notificationtemplate.bulk_action');
    Route::resource('notification-templates', NotificationTemplatesController::class, ['names' => 'notification-templates']);
    

    Route::group(['prefix' => 'mail-templates', 'as' => 'mailtemplates.'], function () {
        Route::get('index_list', [MailTemplatesController::class, 'index_list'])->name('index_list');
        Route::get('index_data', [MailTemplatesController::class, 'index_data'])->name('index_data');
        Route::get('trashed', [MailTemplatesController::class, 'trashed'])->name('trashed');
        Route::patch('trashed/{id}', [MailTemplatesController::class, 'restore'])->name('restore');
        Route::get('ajax-list', [MailTemplatesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('notification-buttons', [MailTemplatesController::class, 'mailButton'])->name('notification-buttons');
        Route::get('notification-template', [MailTemplatesController::class, 'mailTemplate'])->name('notification-template');
        Route::post('channels-update', [MailTemplatesController::class, 'updateChanels'])->name('settings.update');
        Route::post('update-status/{id}', [MailTemplatesController::class, 'update_status'])->name('update_status');
        Route::get('fetch_data', [MailTemplatesController::class, 'fetchData'])->name('fetch_data');

       
    });
    Route::post('mail-template-bulk-action', [MailTemplatesController::class, 'bulk_action'])->name('mailtemplate.bulk_action');
    Route::resource('mail-templates', MailTemplatesController::class, ['names' => 'mail-templates']);


    Route::get('save-wallet-stripe-payment/{id}',[App\Http\Controllers\WalletController::class, 'saveWalletStripePayment']);

});
Route::get('/ajax-list',[HomeController::class, 'getAjaxList'])->name('ajax-list');
Route::post('/service-list',[HomeController::class, 'getAjaxServiceList'])->name('service-list');







