<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Category;
use App\Models\ProviderDocument;
use App\Models\AppSetting;
use App\Models\Setting;
use App\Models\ProviderPayout;
use App\Models\HandymanPayout;
use App\Models\ServiceAddon;
use App\Models\AppDownload;
use App\Models\FrontendSetting;
use App\Models\PaymentGateway;
use App\Models\SubCategory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\BookingRating;
//jabu
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        //jabu 
        // session()->put('backid', "00");
        //endjabu
        if (request()->ajax()) {
            $start = (!empty($_GET["start"])) ? date('Y-m-d', strtotime($_GET["start"])) : ('');
            $end = (!empty($_GET["end"])) ? date('Y-m-d', strtotime($_GET["end"])) : ('');
            $data =  Booking::myBooking()->where('status', 'pending')->whereDate('date', '>=', $start)->whereDate('date',   '<=', $end)->with('service')->get();
            return response()->json($data);
        }

        $data['dashboard'] = [
            'count_total_booking'               => Booking::myBooking()->count(),
            'count_total_service'               => Service::myService()->count(),
            'count_total_provider'              => User::myUsers('get_provider')->count(),
            'new_customer'                      => User::myUsers('get_customer')->orderBy('id', 'DESC')->take(5)->get(),
            'new_provider'                      => User::myUsers('get_provider')->with('getServiceRating')->orderBy('id', 'DESC')->take(5)->get(),
            'upcomming_booking'                 => Booking::myBooking()->with('customer')->where('status', 'pending')->orderBy('id', 'DESC')->take(5)->get(),
            'top_services_list'                 => Booking::myBooking()->showServiceCount()->take(5)->get(),
            'count_handyman_pending_booking'    => Booking::myBooking()->where('status', 'pending')->count(),
            'count_handyman_complete_booking'   => Booking::myBooking()->where('status', 'completed')->count(),
            'count_handyman_cancelled_booking'  => Booking::myBooking()->where('status', 'cancelled')->count()
        ];

        $data['category_chart'] = [
            'chartdata'     => Booking::myBooking()->showServiceCount()->take(4)->get()->pluck('count_pid'),
            'chartlabel'    => Booking::myBooking()->showServiceCount()->take(4)->get()->pluck('service.category.name')
        ];

        $total_revenue  = Payment::where('payment_status', 'paid');
        if (auth()->user()->hasAnyRole(['admin', 'demo_admin'])) {
            $data['revenueData']    =  adminEarning();
        }
        if ($user->hasRole('provider')) {
            $revenuedata = ProviderPayout::selectRaw('sum(amount) as total , DATE_FORMAT(created_at , "%m") as month')
                ->where('provider_id', auth()->user()->id)
                ->whereYear('created_at', date('Y'))
                ->groupBy('month');
            $revenuedata = $revenuedata->get()->toArray();
            $data['revenueData']    =    [];
            $data['revenuelableData']    =    [];
            for ($i = 1; $i <= 12; $i++) {
                $revenueData = 0;

                foreach ($revenuedata as $revenue) {
                    if ((int)$revenue['month'] == $i) {
                        $data['revenueData'][] = (int)$revenue['total'];
                        $revenueData++;

                    }
                }
                if ($revenueData == 0) {
                    $data['revenueData'][] = 0;
                }
            }

            $data['currency_data']=currency_data();
        }


        $data['total_revenue']  =    $total_revenue->sum('total_amount');
        if ($user->hasRole('provider')) {
            $total_revenue  = ProviderPayout::where('provider_id', $user->id)->sum('amount') ?? 0;

            $data['total_revenue']=getPriceFormat($total_revenue);
        }
        if ($user->hasRole('handyman')) {
            $data['total_revenue']  = HandymanPayout::where('handyman_id', $user->id)->sum('amount') ?? 0;


        }
        //jabu dashboards
        if($user->hasRole('Neopreneur')){
            $total_downline = DB::table('users')->where('sp_neo_id', $user->id)->where('user_type', 'provider')
            ->join('bookings', 'users.id', '=', 'bookings.provider_id')
            ->count();

            $total_downline_services = DB::table('users')->where('sp_neo_id', $user->id)->where('user_type', 'provider')
            ->count();

            $total_downline_commission = DB::table('earnings_neo')->where('neo_id', $user->id)->sum('neo_comm');

            $total_sp_rev = DB::table('users')->where('sp_neo_id', $user->id)->where('user_type', 'provider')
            ->join('earnings_service_provider', 'users.id', '=', 'earnings_service_provider.sp_id')
            ->sum('sp_comm');


            $data['neo_total_booking'] = $total_downline;
            $data['neo_total_services'] = $total_downline_services;
            $data['total_downline_commission'] = $total_downline_commission;
            $data['total_sp_rev'] = $total_sp_rev;
            $data['wallet_id'] = $user->id;
        }
        if ($user->hasRole('Depot'))
        {
            $data['wallet_id'] = $user->id;
       
            // $total_service_provider = DB::table('users')->where('city_id', $user->city_id )->where('user_type','provider')->count();
            $depotArea = explode(',',$user->area);
            // $total_service_provider = 0;
            // foreach($depotArea as $area){
            //     $userCount = DB::table('users')->where('city_id', $area)->where('user_type','provider')->where('status','=','1')->count();
            //     $total_service_provider += $userCount;
            // }
            // $area_manager = explode(',',$user->area);
            // $depot_total_booking = 0;
            // foreach ($area_manager as $area)
            // {
            //     $getspId = DB::table('users')->where('city_id', $area )->where('user_type','provider')->get();
                
            //     $data['checker'] = $area;
            //     foreach ($getspId as $sp) 
            //     {
            //         $depot_total_booking = DB::table('bookings')->where('provider_id',$sp->id)->count();
                    
            //         $getTotalComs = DB::table('earnings_city_manager')->where('sp_id', $sp->id )->get();
            //         if($getTotalComs){
            //             $total_commission = 0;
            //             foreach($getTotalComs as $getTotalSp)
            //             {
            //                 $total_commission+= (float)$getTotalSp->city_comm;
            //             }
            //             $data['depot_total_commission']  = $total_commission;
            //         }
            //         $getTotalSps = DB::table('earnings_service_provider')->where('sp_id', $sp->id )->get();
            //         if($getTotalSps){
            //             $total_sp_rev = 0;
            //             foreach($getTotalSps as $getTotalSp)
            //             {
            //                 $total_sp_rev+= (float)$getTotalSp->sp_comm;
            //             }
            //             $data['depot_total_sp_rev'] = $total_sp_rev;
            //         }
            //     }
            // }
            // jabu earnings_service_provider
            $city_comission = 0;
            $service_provider_rev = 0;
            $total_sp = 0;
            $total_bookings = 0;
            foreach($depotArea as $area){
                $data['checker'] = $area;
                $depo_earnings = DB::table('earnings_city_manager')->where('city_id', $area)->sum('city_comm');
                $city_comission += (float)$depo_earnings;
                $serviceprovider = DB::table('users')->where('city_id', $area)->where('user_type', 'provider')->get();
                foreach($serviceprovider as $provider){
                    $total_sp += 1;
                    $sp_rev = DB::table('earnings_service_provider')->where('sp_id', $provider->id)->get();
                    $total_bookings += DB::table('bookings')->where('provider_id', $provider->id)->count();
                    foreach($sp_rev as $revenue){
                        $service_provider_rev += $revenue->sp_comm;
                    }
                    
                }
                
            }
            $data['depot_total_commission']  = (float)$city_comission;
            $data['depot_total_sp_rev']      = (float)$service_provider_rev;
            $data['depot_total_booking']     = $total_bookings;
            $data['depot_totaL_sp']          = $total_sp;
            $data['depot_area']              = $user->area;
        }
        // end
        if (auth()->user()->hasAnyRole(['admin', 'demo_admin'])) {
            return $this->adminDashboard($data);
        } else if (auth()->user()->hasAnyRole('provider')) {
            return $this->providerDashboard($data);
        } else if (auth()->user()->hasAnyRole('handyman')) {
            return $this->handymanDashboard($data);
        } else if (auth()->user()->hasAnyRole('Neopreneur')) {
            return $this->neopreneurDashboard($data);
        } else if (auth()->user()->hasAnyRole('Depot')){
            return $this->depotDashboard($data);
        } else {
            return $this->userDashboard($data);
        }
    }

    /**
     * Admin Dashboard
     *
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    //jabu login as provider
    public function login_as($id){
        // session()->put('back_id', "1");
    //   session()->get('back_id')
        $user = auth()->user();
        if(session()->get('backid')){
            session()->put('backid', "");
        }else{
            session()->put('backid', $user->id);
        }
        Auth::loginUsingId($id);
        return redirect(route('home'));
    }
    //end jabu login as
    // jabu dashboard function
    public function depotDashboard($data)
    {   
        	$wallet = DB::table('wallets')->where('user_id', $data['wallet_id'])->first();
       
        return view('dashboard.depot-dashboard', compact('data','wallet'));
    } 
    public function neopreneurDashboard($data)
    {
        $show = "false";
        $dashboard_setting = Setting::where('type', 'dashboard_setting')->first();

        if ($dashboard_setting == null) {
            $show = "true";
        }
        $wallet = DB::table('wallets')->where('user_id', $data['wallet_id'])->first();     
        return view('dashboard.neo-dashboard-new', compact('data', 'show', 'wallet'));
    }
    // end jabu dashboard
    //jabu encashment
    public function encashment_index(Request $request)
    {
        $user = auth()->user();
        $data['wallet_id'] = $user->id;
        
        return $this->encashment($data);
    }
    public function encashment($data)
    {   
        $wallet = DB::table('wallets')->where('user_id', $data['wallet_id'])->first();
        return view('encashment.index', compact('data','wallet'));
    }
    public function encashment_table(DataTables $datatable, Request $request){
        $query = User::query();
        $user = Booking::query();
        $filter = $request->filter;
        $getUser = auth()->user();
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        if($request->list_status == 'all'){
            $query = $query->whereNotIn('user_type',['admin','demo_admin']);
        }else{
        //   $query = [];
          $userInfo = auth()->user();
          if($userInfo->user_type == "admin"){
              
              $query = DB::table('encashment_history')
              ->join('users', 'users.id', '=', 'encashment_history.user_id')
              ->select('encashment_history.*', 'users.display_name', 'users.user_type')
              ->get();
          }else{
              
              $query = DB::table('encashment_history')->where('user_id', $userInfo->id)
              ->join('users', 'users.id', '=', 'encashment_history.user_id')
              ->select('encashment_history.*', 'users.display_name', 'users.user_type')
              ->get();
          }
       
        }   
        return Datatables::of($query)
            ->editColumn('transaction_id', function($query){
                return $query->transaction_id;
            })
            ->editColumn('display_name', function($query){
                return '<a class="btn-link btn-link-hover">'.$query->display_name.'</a>';
            })
            ->editColumn('user_type', function($query){
                return '<a class="btn-link btn-link-hover">'.$query->user_type.'</a>';
            })
            ->editColumn('amount', function($query) {
                return "₱ ". $query->amount.".00";
            })
            ->editColumn('status', function($query) {
                if($query->status == "Pending"){
                    $status = "<span class='text-info'>".$query->status."</span>";
                }elseif($query->status == "Canceled"){
                    $status = "<span class='text-danger'>".$query->status."</span>";
                }elseif($query->status == "Success"){
                    $status = "<span class='text-success'>".$query->status."</span>";
                }
                elseif($query->status == "Approve"){
                    $status = "<span class='text-success'>".$query->status."</span>";
                }
                return $status;
            })
            // ->filterColumn('status',function($query,$keyword){
            //     $query->where('status','like','%'.$keyword.'%');
            // })
            ->editColumn('type', function($query) {
                return $query->type;
            })
            ->editColumn('updated_at', function($query) {
                return $query->updated_at;
            })
            ->editColumn('created_at', function($query) {
                return $query->created_at;
            })
            ->editColumn('action', function($query) {
                $data = $query;
                $stat = $query->status;
                
                return view('encashment.action',compact('data','stat'))->render();
               // return '<button type="button" class="btn btn-danger text-white delbtn" data-data-transid="'.$query['id'].'">'.$query['action'].'</button>';
            })
            ->addIndexColumn()
            ->rawColumns(['display_name','action','status','user_type'])
            ->toJson();
    }
    public function depot_encashment(Request $request)
    {
        $amount = $request->amount;
        $type = $request->type;
        $user = auth()->user();
        
        $user_wallet = DB::table('wallets')->where('user_id', $user->id)->first();
        if($amount > $user_wallet->amount || $amount == 0){
            return response()->json(['suc'=> "Wallet must be higher than requested amount"]);
        }
        $encash_pending = DB::table('encashment_history')->where('user_id', $user->id)->where('status', 'Pending')->sum('amount');
        if($encash_pending >= $user_wallet->amount){
            return response()->json(['suc'=> "Still have pending transaction"]);
        }
        $natira = $user_wallet->amount - $encash_pending ;
        if($natira < $amount){
            return response()->json(['suc'=> "Still have pending transaction"]);
        }
        
        $now = Carbon::now();
        $characters = 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $randomString = '';
        $charLength = strlen($characters);
    
        for ($i = 0; $i < 5; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        
        $trans_id = $user->id."-".$randomString."-".$now->year.$now->month.$now->dayOfWeek;
        $data = [
            'transaction_id' => $trans_id,
            'user_id'        => $user->id,
            'amount'         => $amount,
            'type'           => $type,
            'status'         => "Pending",
            'created_at'     => $now->toDateTimeString()
            ];
      $insert = DB::table('encashment_history')->insert([
            'transaction_id' => $data['transaction_id'],
            'user_id'        => $data['user_id'],
            'amount'         => $data['amount'],
            'type'           => $data['type'],
            'status'         => $data['status'],
            'created_at'     => $data['created_at']
            ]);
        if($insert){
            return response()->json(['suc'=> "success"]);
        }else{
            return response()->json(['suc'=> "error"]);
        }
        
    }
    public function admin_encashment(Request $request)
    {
        $id = $request->id;
        $status = $request->status;
        $user_id = $request->userid;
        $now = Carbon::now();
        
        $user_wallet = DB::table('wallets')->where('user_id', $user_id)->first();
        $encashment = DB::table('encashment_history')->where('id', $id)->first();
        if($encashment->status == "Approve"){
            $newWallet = $encashment->amount + $user_wallet->amount;
            DB::table('encashment_history')->where('id', $id)->update(['status' => $status, 'updated_at' => $now->toDateTimeString()]);
            DB::table('wallets')->where('user_id', $user_id)->update(['amount' => $newWallet]);
            return response()->json(['suc'=> "success"]);
        }else{
            if($status == "Approve"){
                $newWallet = $user_wallet->amount - $encashment->amount;
                DB::table('encashment_history')->where('id', $id)->update(['status' => $status, 'updated_at' => $now->toDateTimeString()]);
                DB::table('wallets')->where('user_id', $user_id)->update(['amount' => $newWallet]);
                return response()->json(['suc'=> "success"]);
            }else{
                DB::table('encashment_history')->where('id', $id)->update(['status' => $status, 'updated_at' => $now->toDateTimeString()]);
                return response()->json(['suc'=> "success"]);
            }
           
            
        }
        return response()->json(['suc'=> "error", 'er' => "Error"]); 
        
    }
    public function encashment_delete($id)
    {
        $user = auth()->user();
        if ($user->hasRole('Depot'))
        {
            $data['wallet_id'] = $user->id;
        }
        $wallet = DB::table('wallets')->where('user_id', $data['wallet_id'])->first();
        $encashment = DB::table('encashment_history')->where('id', $id)->first();
        if($encashment){
            DB::table('encashment_history')->where('id', $id)->delete();
            $msg = "success";
        }else{
            $msg = "failed";
        }
        return view('encashment.index', compact('data', 'wallet', 'msg'));
    }
    //end jabu encashment
    //jabu earning history
    public function earning_history_index(Request $request)
    {
        $user = auth()->user();
        $data['wallet_id'] = $user->id;
        
        return $this->earning_history($data);
    }
    public function earning_history($data)
    {   
        $wallet = DB::table('wallets')->where('user_id', $data['wallet_id'])->first();
        return view('earning_history.index', compact('data','wallet'));
    }
    public function earning_history_table(DataTables $datatable, Request $request){
        $query = User::query();
        $user = Booking::query();
        $filter = $request->filter;
        $getUser = auth()->user();
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        if($request->list_status == 'all'){
            $query = $query->whereNotIn('user_type',['admin','demo_admin']);
        }else{
        //   $query = [];
          $userInfo = auth()->user();
          if($userInfo->user_type == "Neopreneur"){
             $query = DB::table('earnings_neo')->where('neo_id', $userInfo->id)
                ->join('bookings', 'bookings.id', '=', 'earnings_neo.booking_id')
                ->join('users', 'users.id', '=', 'bookings.provider_id')
                ->select('earnings_neo.*', 'bookings.amount as amount', 'users.display_name as display_name')
                ->get();
          }else{
             return $this->earning_history_depo();
          }
       
        }   
        return Datatables::of($query)
            ->editColumn('booking_id', function($query){
                return $query->booking_id;
            })
            ->editColumn('display_name', function($query){
                return $query->display_name;
            })
            ->editColumn('amount', function($query) {
                return "₱ ".$query->amount.".00";
            })
            ->editColumn('comm', function($query) {
                // if($query->status == "Pending"){
                //     $status = "<span class='text-info'>".$query->status."</span>";
                // }elseif($query->status == "Canceled"){
                //     $status = "<span class='text-danger'>".$query->status."</span>";
                // }elseif($query->status == "Success"){
                //     $status = "<span class='text-success'>".$query->status."</span>";
                // }
                // elseif($query->status == "Approve"){
                //     $status = "<span class='text-success'>".$query->status."</span>";
                // }
                $commision = $query->neo_comm;
                return "₱ ".$commision;
            })
           
            ->addIndexColumn()
            ->rawColumns(['amount','status'])
            ->toJson();
    }
    public function earning_history_depo(){
        $getUser = auth()->user();
        $allArea = explode(",", auth()->user()->area);
        $query = [];
        
        foreach($allArea as $area){
            $poviders = DB::table('users')->where('user_type', 'provider')->where('city_id', $area)->get();
            foreach($poviders as $provider){
                $providerBooking = DB::table('bookings')->where('provider_id', $provider->id)->where('status', 'completed')->get();
                foreach($providerBooking as $booking){
                    $depoEarnings = DB::table('earnings_city_manager')->where('booking_id', $booking->id)->first();
                    if($depoEarnings){
                        $ar_data = array(
                            'user_id' => $provider->id,
                            'booking_id' => $booking->id,
                            'display_name' => $provider->display_name,
                            'amount' => $booking->amount,
                            'sp_comm' => $depoEarnings->city_comm
                            
                        );
                        array_push($query, $ar_data);
                    }
                    
                }
                
                
            }
        }
        
        
         return Datatables::of($query)
            ->editColumn('booking_id', function($query){
                return $query['booking_id'];
            })
            ->editColumn('display_name', function($query){
                return $query['display_name'];
            })
            ->editColumn('amount', function($query) {
                return "₱ ".$query['amount'].".00";
            })
            ->editColumn('comm', function($query) {
                $commision = $query['sp_comm'];
                return "₱ ".$commision;
            })
           
            ->addIndexColumn()
            ->rawColumns(['amount','status'])
            ->toJson();
    }
    //end jabu earning history
    //jabu dashboards tables
    public function depot_table(DataTables $datatable, Request $request){
        $query = User::query();
        $user = Booking::query();
        $filter = $request->filter;
        $getUser = auth()->user();
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        if($request->list_status == 'all'){
            $query = $query->whereNotIn('user_type',['admin','demo_admin']);
        }else{
          $query = [];
          $allArea = explode(",", auth()->user()->area);
          $text = "";
          foreach($allArea as $newArea){
            $provi = DB::table('users')->where('user_type','provider')->where('city_id', $newArea)->get();
            foreach($provi as $provider){
                if($provider){
                  array_push($query, [
                        'id' => $provider->id,
                        'display_name' => $provider->display_name
                    ]);  
                }
            }
            
          }
         // $query = DB::table('users')->where('user_type','provider')->where('city_id', 32229)->get();
        }   
       
        
        return Datatables::of($query)
            ->editColumn('display_name', function($query){
                return '<a class="btn-link btn-link-hover" >'.$query['display_name'].'</a>';
            })
            ->editColumn('total_booking', function($query) {
                $totalbooking = DB::table('bookings')->where('provider_id', $query['id'])->count();
                return isset($totalbooking) ? $totalbooking : 0;
            })
            ->editColumn('sp_comm', function($query) {
                $totalkomi = DB::table('earnings_service_provider')->where('sp_id', $query['id'])->sum('sp_comm');
                return isset($totalkomi) ? $totalkomi : 0;
            })
            ->editColumn('neo_comm', function($query) {
                $neoComms = DB::table('earnings_city_manager')
                            ->where('sp_id', $query['id'])
                            ->sum('city_comm');
                return isset($neoComms) ? $neoComms : 0.00;
            })
            ->editColumn('comm_persent', function($query) {
                $getCom = DB::table('commission')->first();
                $getComInt = (int)$getCom->city_manager;
                return $getComInt.'%';
            })
            ->editColumn('total_completed', function($query) {
                $totalCompleted = DB::table('bookings')->where('provider_id', $query['id'])->where('status', 'completed')->count();
                return $totalCompleted;
            })
            ->editColumn('total_rejected', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query['id'])->where('status', 'rejected')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_cancelled', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query['id'])->where('status', 'cancelled')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_failed', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query['id'])->where('status', 'failed')->count();
                return isset($total) ? $total : 0;
            })
            ->addIndexColumn()
            ->rawColumns(['display_name'])
            ->toJson();
    }
    public function neo_tag_history(DataTables $datatable, Request $request){
        // $user = User::query();
        // $query = Booking::query();
        
        $query = User::query();
        $user = Booking::query();

        
        $filter = $request->filter;
        $getUser = auth()->user();
        
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        if($request->list_status == 'all'){
            $query = $query->whereNotIn('user_type',['admin','demo_admin']);
        }else{
            $query = $query->where('user_type','provider')->where('sp_neo_id', $getUser->id);
        }   
        return $datatable->eloquent($query)
            ->editColumn('display_name', function($query){
                return '<a class="btn-link btn-link-hover" >'.$query->display_name.'</a>';
            })
            ->filterColumn('display_name',function($query,$keyword){
                $query->where('display_name','like','%'.$keyword.'%');
            })
            ->editColumn('total_booking', function($query) {
                $totalbooking = DB::table('bookings')->where('provider_id', $query->id)->count();
                return $totalbooking;
            })
            ->editColumn('sp_comm', function($query) {
                $totalkomi = DB::table('earnings_service_provider')->where('sp_id', $query->id)->sum('sp_comm');
                return isset($totalkomi) ? $totalkomi : 0;
            })
            ->editColumn('neo_comm', function($query) {
                $neoComms = DB::table('earnings_neo')
                            ->where('neo_id', auth()->user()->id)
                            ->where('sp_id', $query->id)
                            ->sum('neo_comm');
                //$getU = DB::table('users')->where('id', 2597)->join('bookings', 'users.id', '=', 'bookings.provider_id')->join('earnings_neo', 'bookings.id', '=', 'earnings_neo.booking_id')->select('earnings_neo.neo_comm as ye')->sum('ye');
                //->select('*', 'bookings.id AS booking_new_id', 'bookings.status AS booking_status')
                //->join('bookings', 'users.id', '=', 'bookings.provider_id')->join('earnings_neo', 'bookings.id', '=', 'earnings_neo.booking_id')->select('earnings_neo.neo_comm as ye')
                //$getU = DB::table('users')->where('id', '=', $query->id)->join('bookings', 'users.id', '=', 'bookings.provider_id')->join('earnings_neo', 'bookings.id', '=', 'earnings_neo.booking_id')->select('users.first_name as uge')->first();
                // $querye = User::query();
                // $querye = $querye->where('user_type','provider')->where('upline', $getUser->referal_code)->where('id', 2597)
                // ->rightJoin('bookings', 'users.id', '=', 'bookings.provider_id')
                // ->rightJoin('earnings_neo', 'bookings.id', '=', 'earnings_neo.booking_id')
                // ->select('*', 'bookings.id AS booking_new_id', 'bookings.status AS booking_status');
                return $neoComms;
            })
            ->editColumn('comm_persent', function($query) {
                $getCom = DB::table('commission')->first();
                $getComInt = (int)$getCom->neopreneur;
                return $getComInt.'%';
            })
            ->editColumn('total_completed', function($query) {
                $totalCompleted = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'completed')->count();
                return $totalCompleted;
            })
            ->editColumn('total_rejected', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'rejected')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_cancelled', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'cancelled')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_failed', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'failed')->count();
                return isset($total) ? $total : 0;
            })
            // ->filterColumn('sp_comm',function($query,$keyword){
            //     $query->where('sp_comm','like','%'.$keyword.'%');
            // })
            ->addIndexColumn()
            ->rawColumns(['display_name'])
            ->toJson();
    }
    public function neo_tag_upline_history(DataTables $datatable, Request $request){
        
        $getUser = auth()->user();
        $query = [];
        $getNeo = DB::table('users')->where('user_type', 'Neopreneur')->where('upline', auth()->user()->referal_code)->get();

        foreach($getNeo as $val){
            $getProvider = DB::table('users')->where('user_type', 'provider')->where('upline', $val->referal_code)->get();
            foreach($getProvider as $provVal){
                array_push($query, [
                    'id' => $provVal->id,
                    'display_name' => $provVal->first_name
                ]);
            }
        }
        $query = DB::table('users')->where('user_type', 'provider')->where('sp_upline_id', auth()->user()->id)->get();
        
        return Datatables::of($query)
            ->editColumn('display_name', function($query){
                return '<a class="btn-link btn-link-hover" >'.$query->display_name.'</a>';
            })
            ->editColumn('total_booking', function($query) {
                $totalbooking = DB::table('bookings')->where('provider_id', $query->id)->count();
                return $totalbooking;
            })
            ->editColumn('sp_comm', function($query) {
                $totalkomi = DB::table('earnings_service_provider')->where('sp_id', $query->id)->sum('sp_comm');
                return isset($totalkomi) ? $totalkomi : 0;
            })
            ->editColumn('comm_persent', function($query) {
                $getCom = DB::table('commission')->first();
                $getComInt = (int)$getCom->upline;
                return $getComInt.'%';
            })
            ->editColumn('earnings_upline', function($query) {
                $neoComms = DB::table('earnings_upline')
                            ->where('upline_id', auth()->user()->id)
                            ->where('sp_id', $query->id)
                            ->sum('upline_comm');
                
                return $neoComms;
            })->editColumn('total_completed', function($query) {
                $totalCompleted = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'completed')->count();
                return $totalCompleted;
            })
            ->editColumn('total_rejected', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'rejected')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_cancelled', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'cancelled')->count();
                return isset($total) ? $total : 0;
            })
            ->editColumn('total_failed', function($query) {
                $total = DB::table('bookings')->where('provider_id', $query->id)->where('status', 'failed')->count();
                return isset($total) ? $total : 0;
            })
            ->addIndexColumn()
            ->rawColumns(['display_name'])
            ->toJson();
    }
    //end jabu dashboard tables
    public function adminDashboard($data)
    {

        $rezorpayX_details=PaymentGateway::where('type','razorPayX')->where('status',1)->first();
        return view('dashboard.dashboard', compact('data','rezorpayX_details'));
    }
    public function providerDashboard($data)
    {

        return view('dashboard.provider-dashboard', compact('data'));
    }
    public function handymanDashboard($data)
    {

        return view('dashboard.handyman-dashboard', compact('data'));
    }
    public function userDashboard($data)
    {
        return view('dashboard.user-dashboard', compact('data'));
    }
    public function changeStatus(Request $request)
    {
        if (demoUserPermission()) {
            $message = __('messages.demo_permission_denied');
            $response = [
                'status'    => false,
                'message'   => $message
            ];

            return comman_custom_response($response);
        }
        $type = $request->type;
        $message_form = __('messages.item');
        $message = trans('messages.update_form', ['form' => trans('messages.status')]);
        switch ($type) {
            case 'role':
                $role = \App\Models\Role::find($request->id);
                $role->status = $request->status;
                $role->save();
                break;
            case 'category_status':
                $category = \App\Models\Category::find($request->id);
                $category->status = $request->status;
                $category->save();
                break;
            case 'category_featured':
                $message_form = __('messages.category');
                $category = \App\Models\Category::find($request->id);
                $category->is_featured = $request->status;
                $category->save();
                break;
            case 'service_status':
                $service = \App\Models\Service::find($request->id);
                $service->status = $request->status;
                $service->save();
                break;
            case 'coupon_status':
                $coupon = \App\Models\Coupon::find($request->id);
                $coupon->status = $request->status;
                $coupon->save();
                break;
            case 'document_status':
                $document = \App\Models\Documents::find($request->id);
                $document->status = $request->status;
                $document->save();
                break;
            case 'document_required':
                $message_form = __('messages.document');
                $document = \App\Models\Documents::find($request->id);
                $document->is_required = $request->status;
                $document->save();
                break;
            case 'provider_is_verified':
                $message_form = __('messages.providerdocument');
                $document = \App\Models\ProviderDocument::find($request->id);
                $document->is_verified = $request->status;
                $document->save();
                break;
            case 'tax_status':
                $tax = \App\Models\Tax::find($request->id);
                $tax->status = $request->status;
                $tax->save();
                break;
            case 'provideraddress_status':
                $provideraddress = \App\Models\ProviderAddressMapping::find($request->id);
                $provideraddress->status = $request->status;
                $provideraddress->save();
                break;
            case 'slider_status':
                $slider = \App\Models\Slider::find($request->id);
                $slider->status = $request->status;
                $slider->save();
                break;
            case 'servicefaq_status':
                $servicefaq = \App\Models\ServiceFaq::find($request->id);
                $servicefaq->status = $request->status;
                $servicefaq->save();
                break;
            case 'wallet_status':
                $wallet = \App\Models\Wallet::find($request->id);
                $wallet->status = $request->status;
                $wallet->save();
                break;
            case 'subcategory_status':
                $subcategory = \App\Models\SubCategory::find($request->id);
                $subcategory->status = $request->status;
                $subcategory->save();
                break;
            case 'subcategory_featured':
                $message_form = __('messages.subcategory');
                $subcategory = \App\Models\SubCategory::find($request->id);
                $subcategory->is_featured = $request->status;
                $subcategory->save();
                break;
            case 'plan_status':
                $plans = \App\Models\Plans::find($request->id);
                $plans->status = $request->status;
                $plans->save();
                break;
            case 'bank_status':
                $banks = \App\Models\Bank::find($request->id);
                $banks->status = $request->status;
                $banks->save();
                break;
            case 'blog_status':
                $blog = \App\Models\Blog::find($request->id);
                $blog->status = $request->status;
                $blog->save();
                break;
            case 'servicepackage_status':
                $servicepackage = \App\Models\ServicePackage::find($request->id);
                $servicepackage->status = $request->status;
                $servicepackage->save();
                break;
            case 'notificationtemplate_status':
                $notificationtemplate = \App\Models\NotificationTemplate::find($request->id);
                $notificationtemplate->status = $request->status;
                $notificationtemplate->save();
            case 'serviceaddon_status':
                $serviceaddon = \App\Models\ServiceAddon::find($request->id);
                $serviceaddon->status = $request->status;
                $serviceaddon->save();
                break;
            case 'user_verify_email':
                $user = \App\Models\User::find($request->id);
                $user->is_email_verified = $request->status;
                $user->save();
                break;
            case 'user_service_status':
                $userService = \App\Models\Service::find($request->id);
                $userService->status = $request->status;
                $userService->save();
                break;
            case 'handyman_type_status':
                $handyman_type_status = \App\Models\HandymanType::find($request->id);
                $handyman_type_status->status = $request->status;
                $handyman_type_status->save();
                break;
            case 'providertype_status':
                $providertype_status = \App\Models\ProviderType::find($request->id);
                $providertype_status->status = $request->status;
                $providertype_status->save();
                break;

            default:
                $message = 'error';
                break;
        }
        if ($request->has('is_email_verified') && $request->is_email_verified == 'is_email_verified') {
            $message =  __('messages.user_verified', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('is_featured') && $request->is_featured == 'is_featured') {
            $message =  __('messages.added_form', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('is_required') && $request->is_required == 'is_required') {
            $message =  __('messages.added_form', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('provider_is_verified') && $request->provider_is_verified == 'provider_is_verified') {
            $message =  __('messages.is_verify', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form_verify', ['form' => $message_form]);
            }
        }
        return comman_custom_response(['message' => $message, 'status' => true]);
    }

    public function getAjaxList(Request $request)
    {
        $items = array();
        $value = $request->q;

        $auth_user = authSession();
        switch ($request->type) {
            case 'permission':
                $items = \App\Models\Permission::select('id', 'name as text')->whereNull('parent_id');
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'category':
                $items = \App\Models\Category::select('id', 'name as text')->where('status', 1);
                if (isset($request->is_featured)) {
                    $items->where('is_featured', $request->is_featured);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }

                $items = $items->get();
                break;
            case 'subcategory':
                $items = \App\Models\SubCategory::select('id', 'name as text')->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }

                $items = $items->get();
                break;
            case 'provider':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'provider')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;

            case 'user':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'user')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;

                case 'provider-user':
                    // old
                    $items = \App\Models\User::select('id', 'display_name as text')
                        ->where('user_type', 'provider')->orWhere('user_type','user')
                        ->where('status', 1);
                    //jabu
                    //  $items = \App\Models\User::select('id', 'display_name as text')
                    //     ->where('user_type', 'provider')
                    //     ->where('status', 1);
                    //end jabuu
                    if ($value != '') {
                        $items->where('display_name', 'LIKE', $value . '%');
                    }
                    
                    $items = $items->get();
                    break;
                //jabu    
                case 'provider-wallet-search':
                  
                    //jabu
                     $items = \App\Models\User::select('id', 'display_name as text')
                        ->where('user_type', 'provider')
                        ->where('status', 1);
                    //end jabuu
                    if ($value != '') {
                        $items->where('display_name', 'LIKE', $value . '%');
                    }
                    
                    $items = $items->get();
                    break;
                //end jabu
            case 'handyman':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'handyman')
                    ->where('status', 1);

                if (isset($request->provider_id)) {
                    $items->where('provider_id', $request->provider_id);
                }

                if (isset($request->booking_id)) {
                    $booking_data = Booking::find($request->booking_id);

                    $service_address = $booking_data->handymanByAddress;
                    if ($service_address != null) {
                        $items->where('service_address_id', $service_address->id);
                    }
                }

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            case 'service':
                $items = \App\Models\Service::select('id', 'name as text')->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                if (isset($request->provider_id)) {
                    $items->where('provider_id', $request->provider_id);
                }

                if (isset($request->top_rated)) {
                    $minRating = $request->top_rated['min'] ?? 0;
                    $maxRating = $request->top_rated['max'] ?? 5;

                    $topRatedServiceIds = BookingRating::select('service_id', \DB::raw('COALESCE(AVG(rating), 0) as avg_rating'))
                        ->groupBy('service_id')
                        ->havingRaw('avg_rating >= ?', [$minRating])
                        ->havingRaw('avg_rating <= ?', [$maxRating])
                        ->orderByDesc('avg_rating')
                        ->pluck('service_id')
                        ->toArray();

                    $items->whereIn('id', $topRatedServiceIds)
                        ->orderByRaw(\DB::raw("FIELD(id, " . implode(',', $topRatedServiceIds) . ")"));
                }

                if(isset($request->is_featured)){
                    $items->where('is_featured', 1);
                }


                $items = $items->get();


                break;
            case 'service-list':
                    $items = \App\Models\Service::select('id', 'name as text')->where('status', 1)->where('service_type','service');

                    if ($value != '') {
                        $items->where('name', 'LIKE', '%' . $value . '%');
                    }
                    if (isset($request->provider_id)) {
                        $items->where('provider_id', $request->provider_id);
                    }

                    $items = $items->get();
                    break;
            case 'providertype':
                $items = \App\Models\ProviderType::select('id', 'name as text')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            case 'coupon':
                $items = \App\Models\Coupon::select('id', 'code as text')->where('status', 1);

                if ($value != '') {
                    $items->where('code', 'LIKE', '%' . $value . '%');
                }

                $items = $items->where('status',1)->get();
                break;

                case 'bank':
                    $items = \App\Models\Bank::select('id', 'bank_name as text')->where('provider_id',$request->provider_id)->where('status',1);

                    if ($value != '') {
                        $items->where('name', 'LIKE', $value . '%');
                    }
                    $items = $items->get();
                    break;

            case 'country':
                $items = \App\Models\Country::select('id', 'name as text');

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'religion-list':
                $items = \App\Models\Religion::select('id', 'name as text');
                if ($value != '') {
                   $items->where('name', 'LIKE', '%' . $value . '%');
                }
                $items = $items->get();
                break;
            case 'state':
                $items = \App\Models\State::select('id', 'name as text');
                if (isset($request->country_id)) {
                    $items->where('country_id', $request->country_id);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'city':
                $items = \App\Models\City::select('id', 'name as text');
                if (isset($request->state_id)) {
                    $items->where('state_id', $request->state_id);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'booking_status':
                $items = \App\Models\BookingStatus::select('id', 'label as text');

                if ($value != '') {
                    $items->where('label', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'currency':
                $items = \DB::table('countries')->select(\DB::raw('id id,CONCAT(name , " ( " , symbol ," ) ") text'));

                $items->whereNotNull('symbol')->where('symbol', '!=', '');
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%')->orWhere('currency_code', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'country_code':
                $items = \DB::table('countries')->select(\DB::raw('code id,name text'));
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%')->orWhere('code', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;

            case 'time_zone':
                $items = timeZoneList();

                foreach ($items as $k => $v) {

                    if ($value != '') {
                        if (strpos($v, $value) !== false) {
                        } else {
                            unset($items[$k]);
                        }
                    }
                }

                $data = [];
                $i = 0;
                foreach ($items as $key => $row) {
                    $data[$i] = [
                        'id'    => $key,
                        'text'  => $row,
                    ];
                    $i++;
                }
                $items = $data;
                break;
            case 'provider_address':
                $provider_id = !empty($request->provider_id) ? $request->provider_id : $auth_user->id;
                $items = \App\Models\ProviderAddressMapping::select('id', 'address as text', 'latitude', 'longitude', 'status')->where('provider_id', $provider_id)->where('status', 1);
                $items = $items->get();
                break;

            case 'provider_tax':
                $provider_id = !empty($request->provider_id) ? $request->provider_id : $auth_user->id;
                $items = \App\Models\Tax::select('id', 'title as text')->where('status', 1);
                $items = $items->get();
                break;

            case 'documents':
                $items = \App\Models\Documents::select('id', 'name', 'status', 'is_required', \DB::raw('(CASE WHEN is_required = 1 THEN CONCAT(name," * ") ELSE CONCAT(name,"") END) AS text'))->where('status', 1);
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'handymantype':
                $items = \App\Models\HandymanType::select('id', 'name as text')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            case 'subcategory_list':
                $category_id = !empty($request->category_id) ? $request->category_id : '';
                $items = \App\Models\SubCategory::select('id', 'name as text')->where('category_id', $category_id)->where('status', 1);
                $items = $items->get();
                break;
            case 'service_package':
                $service_id = !empty($request->service_id) ? $request->service_id : $auth_user->id;
                $items = \App\Models\ServicePackage::select('id', 'description as text', 'status')->where('provider_id', $service_id)->where('status', 1);
                $items = $items->get();
                break;
            case 'all_user':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            default:
                break;
        }
        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function removeFile(Request $request)
    {
        if (demoUserPermission()) {
            $message = __('messages.demo_permission_denied');
            $response = [
                'status'    => false,
                'message'   => $message
            ];

            return comman_custom_response($response);
        }

        $type = $request->type;
        $data = null;
        
        switch ($type) {
            case 'slider_image':
                $data = Slider::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.slider')]);
                break;
            case 'profile_image':
                $data = User::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.profile_image')]);
                break;
            case 'service_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'category_image':
                $data = Category::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.category')]);
                break;
            case 'provider_document':
                $data = ProviderDocument::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.providerdocument')]);
                break;
            case 'booking_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'bank_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'app_image':
                $data = AppDownload::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'app_image_full':
                $data = AppDownload::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'package_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'blog_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'serviceaddon_image':
                $data = ServiceAddon::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.service_addon')]);
                break;
            case 'section5_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'main_image':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.main_image')]);
                break;
            case 'google_play':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.google_image')]);
                break;
            case 'app_store':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'vimage':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'login_register_image':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'logo':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'favicon':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'footer_logo':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'loader':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;

             case 'subcategory_image':
                 $data = SubCategory::find($request->id);
                 $message = __('messages.msg_removed', ['name' => __('messages.subcategory')]);
                 break;    


            default:
                $data = AppSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.image')]);
                break;
        }

        if ($data != null) {
            $data->clearMediaCollection($type);
        }

        $response = [
            'status'    => true,
            'image'     => getSingleMedia($data, $type),
            'id'        => $request->id,
            'preview'   => $type . "_preview",
            'message'   => $message
        ];

        return comman_custom_response($response);
    }

    public function lang($locale)
    {
        \App::setLocale($locale);
        session()->put('locale', $locale);
        \Artisan::call('cache:clear');
        $dir = 'ltr';
        if (in_array($locale, ['ar', 'dv', 'ff', 'ur', 'he', 'ku', 'fa'])) {
            $dir = 'rtl';
        }

        session()->put('dir',  $dir);
        if (auth()->check()) {
            $user = auth()->user();
            $user->language_option = $locale;
            $user->save();
        }
        return redirect()->back();
    }

    function authLogin()
    {
        return view('auth.login');
    }
    function authRegister()
    {
        return view('auth.register');
    }

    function authRecoverPassword()
    {
        return view('auth.forgot-password');
    }

    function authConfirmEmail()
    {
        return view('auth.verify-email');
    }
    function getAjaxServiceList(Request $request){
        $items = \App\Models\Service::select('id', 'name as text')->where('status', 1)->where('type', 'fixed');

        $provider_id = !empty($request->provider_id) ? $request->provider_id : auth()->user()->id;
        $items->where('provider_id', $provider_id );
        if (isset($request->category_id)) {
            $items->where('category_id', $request->category_id);
        }
        if (isset($request->subcategory_id)) {
            $items->where('subcategory_id', $request->subcategory_id);
        }
        $items = $items->get();
        return response()->json(['status' => 'true', 'results' => $items]);
    }
}
