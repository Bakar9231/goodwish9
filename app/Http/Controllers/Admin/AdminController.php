<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use URL;
use Hash;
use Session;
use DB;
use App\Admin;
use App\User;
use App\Subscription;
use App\Content;
use App\Genre;
use DateTime;
use App\Language;
use App\Actor;
use App\TVCategory;
use App\TVChannel;
use App\Partner;
use App\Comment;
use App\Payout;
use File;
use Storage;
use Carbon\Carbon;

class AdminController extends Controller
{

	public function showLogin()
	{
		if (Session::get('email') && Session::get('is_user') == 1) {
			return Redirect::route('dashboard');
		} else {
			return view('admin.login');
		}
	}

	public function addoffer()
	{
		return view('admin.offer.addoffer');
	}

	public function saveoffer(Request $request)
	{
		$imageFileName = "";
		if ($request->hasfile('file')) {
			$admin_profile = $request->file('file');
			$imageFileName = 'offer_' . rand(111,999) . '.' . $admin_profile->getClientOriginalExtension();
			$destinationPath = public_path('uploads/offer');
			File::makeDirectory($destinationPath, $mode = 0777, true, true);
			$admin_profile->move($destinationPath, $imageFileName);
			
		}
		DB::table("tbl_offer")->insert([
			'title' => $request->title,
			'image' => $imageFileName,
			'description' =>  $request->description
		]);

		return redirect()->back()->with('message','Offer Added Succesfully');
	}


	public function dologin(Request $request)
	{
		$username = $request->input('username');
		$password = $request->input('password');
		$checkLogin = Admin::where('username', $username)->first();

		

		if (!empty($checkLogin) && $checkLogin->role == 'admin') {
			if ($checkLogin->password == $password || Hash::check($password, $checkLogin->password)) {
				Session::put('name', $checkLogin->username);
				Session::put('email', $checkLogin->email);
				Session::put('admin_id', $checkLogin->id);
				Session::put('profile_image', asset('/uploads/'.$checkLogin->profile_image));
				Session::put('is_logged', 1);
				Session::put('is_admin', 1);
				Session::put('role', $checkLogin->role);
				Session::put('referral_code', $checkLogin->referral_code);
				
				return Redirect::route('dashboard');
			} else {
				Session::flash('invalid', 'Invalid email or password combination. Please try again.');
				return back();
			}
		} 
		
		else {
			Session::flash('invalid', 'Invalid email or password combination. Please try again.');
			return back();
		}
	}

	public function showDashboard()
	{	
		if (Session::get('name') && Session::get('is_logged') == 1) {
			$totalUser = DB::table('goodwish')->count();
			$totalpartner = DB::table('tbl_partners')->count();
			$totalSubscription = Subscription::count();
			$totalMovie = Content::where('content_type',1)->count();
			$totalSeries = Content::where('content_type',2)->count();
			$totalLanguage = Language::count();
			$totalGenre = Genre::count();
			$totalActor = Actor::count();
			$totalTVCategory = TVCategory::count();
			$totalTVChannel = TVChannel::count();

			$totalMovieViews = Content::where('content_type',1)->sum('total_view');
			$totalMovieDownload = Content::where('content_type',1)->sum('total_download');
			$totalMovieShare = Content::where('content_type',1)->sum('total_share');

			$totalSeriesViews = Content::where('content_type',2)->sum('total_view');
			$totalSeriesDownload = Content::where('content_type',2)->sum('total_download');
			$totalSeriesShare = Content::where('content_type',2)->sum('total_share');

			$totalChannelViews = TVChannel::sum('total_view');
			$totalChannelShare = TVChannel::sum('total_share');

			 $subscription = DB::table('goodwish')->join('tbl_subscription','goodwish.user_id','=','tbl_subscription.user_id')->get();
             $active = 0;
			 $not_active = 0;
			foreach($subscription as $subscription){
				
					$fdate = date('Y-m-d');
                    $tdate = $subscription->expired_date;
                    $datetime1 = new DateTime($fdate);
                    $datetime2 = new DateTime($tdate);
                    $interval = $datetime1->diff($datetime2);
                    $days = $interval->format('%a');//now do whatever you like with $days
					if($days > 0){
						$active = $active + 1;
					}else{
						$not_active = $not_active + 1;
					}
					
			}

		

			$totalComment = Comment::count();
			$totalRealityshow=0;

			if(Session::get('role') == "partner")			
				return view('partner.dashboard')->with('totalUser',$totalUser)->with('totalSubscription',$totalSubscription)->with('totalMovie',$totalMovie)->with('totalSeries',$totalSeries)->with('totalLanguage',$totalLanguage)->with('totalGenre',$totalGenre)->with('totalActor',$totalActor)->with('totalTVCategory',$totalTVCategory)->with('totalTVChannel',$totalTVChannel)->with('totalMovieViews',$totalMovieViews)->with('totalMovieDownload',$totalMovieDownload)->with('totalMovieShare',$totalMovieShare)->with('totalSeriesViews',$totalSeriesViews)->with('totalSeriesDownload',$totalSeriesDownload)->with('totalSeriesShare',$totalSeriesShare)->with('totalChannelViews',$totalChannelViews)->with('totalChannelShare',$totalChannelShare);
			
			if(Session::get('role') == "admin")			
				return view('admin.dashboard')->with('totalUser',$totalUser)->with('totalSubscription',$totalSubscription)->with('totalMovie',$totalMovie)->with('totalSeries',$totalSeries)->with('totalLanguage',$totalLanguage)->with('totalGenre',$totalGenre)->with('totalActor',$totalActor)->with('totalTVCategory',$totalTVCategory)->with('totalTVChannel',$totalTVChannel)->with('totalMovieViews',$totalMovieViews)->with('totalMovieDownload',$totalMovieDownload)->with('totalMovieShare',$totalMovieShare)->with('totalSeriesViews',$totalSeriesViews)->with('totalSeriesDownload',$totalSeriesDownload)->with('totalSeriesShare',$totalSeriesShare)->with('totalChannelViews',$totalChannelViews)->with('totalChannelShare',$totalChannelShare)->with('totalComment',$totalComment)->with('totalpartner',$totalpartner)->with('totalRealityshow',$totalRealityshow)->with('totalComment',$totalComment)->with('active_subscription',$active )->with('notactive_subscription',$not_active);
		} else {
			return Redirect::route('login');
		}
	}

	public function logout($flag)
	{
		// Session::flush();
		if (Session::get('role')== 'partner') {
			Session::flush();
			if ($flag == 1) {
				Session::flash('matchResetPassword', 'Password change successfully, Now login by new password...!');
			}
			return redirect()->route('partner');
		}else {
			Session::flush();
			if ($flag == 1) {
				Session::flash('matchResetPassword', 'Password change successfully, Now login by new password...!');
			}
			return redirect()->route('login');
		}
		
	}
	public function MyProfile()
	{	
		if (Session::get('name') && Session::get('is_logged') == 1) {
			$data = Admin::first();
			return view('admin.my-profile')->with('data',$data);
		} else {
			return Redirect::route('login');
		}
	}

	public function updateAdminProfile(Request $request)
	{	
		$admin_id = $request->input('admin_id');
        $admin_name = $request->input('admin_name'); 
        $admin_email = $request->input('email');
        $password = $request->input('password');
        $hdn_profile_image =  $request->input('hdn_profile_image');
        $profile_image = '';
        $data = [];

		$imageFileName = "";
		if ($request->hasfile('admin_profile')) {
			$admin_profile = $request->file('admin_profile');
			$imageFileName = 'user_' . rand(111,999) . '.' . $admin_profile->getClientOriginalExtension();
			$destinationPath = public_path('uploads/');
			File::makeDirectory($destinationPath, $mode = 0777, true, true);
			$admin_profile->move($destinationPath, $imageFileName);
			$data['profile_image'] = $imageFileName;
		}else{
			$data['profile_image'] = $hdn_profile_image;
		}
		
		$profile_image = $data['profile_image'];
		Session::put('profile_image', asset('/uploads/'.$profile_image));
        $data['username'] = $admin_name;
		$data['email'] = $admin_email;
		if($password){
			$data['password'] = $password;
		}
        if($request->type=="partner"){
			$update =  Partner::where('id',$admin_id)->update($data);
		}else{
			$update =  Admin::where('id',$admin_id)->update($data);
		}
      
       if($update){
        $response['admin_name'] = $admin_name;
        $response['admin_email'] = $admin_email;
		$response['admin_profile_url'] = env('DEFAULT_IMAGE_URL').$profile_image;
		$response['admin_profile'] = $profile_image;
        $response['status'] = 1;
       }else{
        $response['admin_name'] = "";
        $response['admin_email'] = "";
		$response['admin_profile_url'] = "";
		$response['admin_profile'] = "";
        $response['status'] = 0;
       }
       echo json_encode($response);
	}

	public function viewListPartners()
	{
		$total_partner = Admin::where('role', '==', 'partner')
								->paginate(10);

		return view('admin.partner.partner_list', compact('total_partner'));
    }

	public function viewPartner($id)
	{
		$data = Admin::where('role', 'partner')
					   ->where('id',$id)
					   ->first();
	
		return view('admin.user.viewusers')->with('data',$data);
    }

	public function showPartnerList(Request $request)
    {

		$columns = array( 
            0=>'id',
            1=>'name',
            2=>'email',
            3=>'status',
			4=>'username',
			5=>'mobile',
		);

		$totalData = Admin::count();

		$totalFiltered = $totalData; 

		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if(empty($request->input('search.value')))
		{      
			
		$AdminData = Admin::offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
		}
		else {
		$search = $request->input('search.value'); 

		$AdminData =  Admin::where('id','LIKE',"%{$search}%")
					->orWhere('name', 'LIKE',"%{$search}%")
					->orWhere('email', 'LIKE',"%{$search}%")
					->offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();

		$totalFiltered = Admin::where('id','LIKE',"%{$search}%")
					->orWhere('name', 'LIKE',"%{$search}%")
					->orWhere('email', 'LIKE',"%{$search}%")
					->count();
		}

		$data = array();
		if(!empty($AdminData))
		{
			foreach ($AdminData as $rows)
			{
                $view =  route('user/view',$rows->user_id);	

                if(!empty($rows->profile_image))
                {
                    $profile = '<img height="60px;" width="60px;" src="'.url(env('DEFAULT_IMAGE_URL').$rows->profile_image).'" class="" alt="">';
                }
                else
                {
                    $profile = '<img height="60px;" width="60px;" src="'.asset('assets/dist/img/default.png').'" class="" alt="">';
                }
				
                if ($rows->status == 0) {
                    $status =  '<span class="badge badge-pill badge-danger">De-Active</span>';
                } elseif ($rows->status == 1) {
                    $status =  '<span class="badge badge-pill badge-success">Active</span>';
                } 
				$no_of_purchase = Subscription::where('user_id', $rows->user_id)->count();
				$data[]= array(
					$profile,
					$rows->fullname,
					$rows->email,
					$no_of_purchase,
                    $status,
					'<a href="'.$view.'" class="btn btn-success text-white" title="View Details" data-toggle="tooltip" data-original-title="View Details">View Details</a>'
                    // '<a class="delete" id="userDelete" data-id="'.$rows->user_id.'"><i class="fa fa-trash text-danger font-20 pointer p-l-5 p-r-5"></i></a>'
				); 
				// <a href="'.$view.'" class="settings" title="View User" data-toggle="tooltip" data-original-title="View User Details"><i class="fa fa-eye text-success font-20 pointer p-l-5 p-r-5"></i></a>
			}
		}

		$json_data = array(
			"draw"            => intval($request->input('draw')),  
			"recordsTotal"    => intval($totalData),  
			"recordsFiltered" => intval($totalFiltered), 
			"data"            => $data   
			);

			logger($json_data);
		echo json_encode($json_data); 
        exit();
	}
	public function payout()
	{
		// dd("hello");
		// $data = User::where('user_id',$user_id)
		// 			->where('device_token', Session::get('referral_code'))
		// 			->first();

		// return view('partner.offers.offers');
		try {
			$results = array();
			$appUserData =  Payout::all();
			$total_partners =  Payout::count();


			if (!empty($appUserData)) {
				foreach ($appUserData as $rows) {



					$data = (object) array(
						'id' => $rows->id,
						'amount' => $rows->amount,
						'status' => $rows->status,
						'created_at' => date('Y-m-d',strtotime($rows->created_at)),
						'updated_at' => date('Y-m-d',strtotime( $rows->updated_at)),

					);
					array_push($results, $data);
				}
			}

			return view("admin.payout.payout_list", [
				'results' => $results,
				'total_partners' => $total_partners
			]);
		} catch (\Exception $e) {
			$response_array = array('success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine());
			$response = Response::json($response_array, 200);
			return $response;
		}
	}
	public function save_payout(Request $request)
	{
		$input = $request->except('_token');
		$input['user_id'] =1;
			$result =  Payout::insert($input);
			$msg = "Added";
			$response['flag'] = 1;
		
		$total_payout = Payout::count();
		if ($result) {
			$response['success'] = 1;
			$response['message'] = "Successfully " . $msg;
			$response['total_payout'] = $total_payout;
		} else {
			$response['success'] = 0;
			$response['message'] = "Error While " . $msg;
		}
		echo json_encode($response);
	}
	public function deletepayout(Request $request)
	{
		try {
			Payout::where('id', $request->id)->delete();
			return response()->json([
				'success' => true,
				'message' => 'User Deleted Successfully'
			]);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'message' => 'Something Wrong'
			]);
		}
	}
	public function payout_detail(Request $request)
	{
		try {
			$payout = Payout::find($request->id);
			return response()->json([
				'success' => true,
				'payout' => $payout
			]);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'message' => 'Something Wrong'
			]);
		}
	}
	public function addUpdatePayout(Request $request)
	{
		$id = $request->input('id');
		$input = $request->except('_token');

		if (!empty($id)) {
			$result =  Payout::find($id);
			$result->update($input);
			$msg = "Updated";
			$response['flag'] = 2;
		}
		$total_payout = Payout::count();
		if ($result) {
			$response['success'] = 1;
			$response['message'] = "Successfully " . $msg;
			$response['total_payout'] = $total_payout;
		} else {
			$response['success'] = 0;
			$response['message'] = "Error While " . $msg;
		}
		echo json_encode($response);
	}
	public function updatePayoutStatus(Request $request)
	{
		$status = $request->get('status');
		$id = $request->get('id');
		$response = [];

		if (isset($status)) {
			if ($status == 1) {
				$query =  Payout::find($id);
				$query->status = 1;
				$query->save();
				$status = 1;
			} else {
				$query =  Payout::find($id);
				$query->status = 0;
				$query->save();
				$status = 0;
			}
			if ($query) {
				$response['success'] = 1;
				$response['message'] = "Successfully updated.";
				$response['status'] = $status;
			} else {
				$response['success'] = 0;
				$response['message'] = "Error while updating.";
				$response['status'] = $status;
			}
		}
		echo json_encode($response);
	}

	
}
