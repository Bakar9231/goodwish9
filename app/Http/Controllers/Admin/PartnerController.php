<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use URL;
use Hash;
use File;
use Session;
use DB;
use App\Admin;
use App\Actor;
use App\MovieCast;
use App\User;
use App\Subscription;
use App\Content;
use App\Genre;
use App\Language;
use App\TVCategory;
use App\TVChannel;
use App\TbiOffer;
use App\Payout;
use App\Comment;
use App\Models\TbiOffer as ModelsTbiOffer;
use App\Partner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PartnerController extends Controller
{
	public function viewListPartners()
	{

		try {
			$results = array();
			$PartnerData =  Partner::all();
			$total_partners = $PartnerData->count();
			if (!empty($PartnerData)) {
				foreach ($PartnerData as $rows) {

					if (!empty($rows->profile_image)) {
						$profile = "/uploads/" . $rows->profile_image;
					} else {
						$profile = "/assets/dist/img/default.png";
					}

					$referredUsers =   DB::table('goodwish')->where('referalcode', $rows->referral_code)->count();

					$data = (object) array(
						'profileImg' => $profile,
						'id' => $rows->id,
						'name' => $rows->name,
						'email' => $rows->email,
						'mobile' => $rows->mobile,
						'percentage' => $rows->referral_income_percentage,
						'referral_code' => $rows->referral_code,
						'referral_link' => $rows->referral_link,
						'status' => $rows->status,
						'last_login' => $rows->updated_at,
						'referredUsers' => $referredUsers,

					);
					array_push($results, $data);
				}
			}

			return view("admin.partner.partner_list", [
				'partnerDetails' => $results,
				'total_partners' => $total_partners
			]);
		} catch (\Exception $e) {
			$response_array = array('success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine());
			$response = Response::json($response_array, 200);
			return $response;
		}
	}

	public function CheckExistPartner(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');

		if (!empty($id)) {
			$checkPartner = Partner::selectRaw('*')->where('name', $name)->where('id', '!=', $id)->first();
		} else {
			$checkPartner = Partner::selectRaw('*')->where('name', $name)->first();
		}

		if (!empty($checkPartner)) {
			return json_encode(FALSE);
		} else {
			return json_encode(TRUE);
		}
	}


	public function addUpdatePartner(Request $request)
	{
		$id = $request->input('id');
		$input = $request->all();
		$imageFileName = "";
		if ($request->hasfile('profile_image')) {
			$profile_image = $request->file('profile_image');
			$imageFileName = 'partner' . rand(111, 999) . '.' . $profile_image->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/');
			File::makeDirectory($destinationPath, $mode = 0777, true, true);
			$profile_image->move($destinationPath, $imageFileName);
			$input['profile_image'] = $imageFileName;
		}
		unset($input['_token']);
		// $data['name'] = $name;

		if (!empty($id)) {

			if (!empty($input['password']))
			$input['password'] = Hash::make($input['password']);
		else
			unset($input['password']);
			
			$result =  Partner::find($id);

			$result->update($input);

			$msg = "Updated";
			$response['flag'] = 2;
		} else {
			$input['password'] = Hash::make($request->password);
			$result =  Partner::insert($input);
			$msg = "Added";
			$response['flag'] = 1;
		}
		$total_partners = Partner::count();
		if ($result) {
			$response['success'] = 1;
			$response['message'] = "Successfully " . $msg;
			$response['total_partners'] = $total_partners;
		} else {
			$response['success'] = 0;
			$response['message'] = "Error While " . $msg;
		}
		echo json_encode($response);
	}

	public function updateAppUser(Request $request)
	{
		$id = $request->input('id');
		$input = $request->all();
		$imageFileName = "";

		unset($input['_token']);
		// $data['name'] = $name;
        unset($input['unique_key']);
	    unset($input['id']);

		if ($request->has('firstname')) {
			$input['firstname'] = $request->firstname;
		}

		if ($request->has('lastname')) {
			$input['lastname'] = $request->lastname;
		}

		if ($request->has('email')) {
			$input['email'] = $request->email;
		}

		if ($request->has('phone')) {
			$input['phone'] = $request->phone;
		}

		if (!empty($input['password']))
                $input['password'] = Hash::make($input['password']);
            else
                unset($input['password']);
				

		$result =  DB::table('goodwish')->where('user_id', $id)->update($input);



		$msg = "Updated";
		$response['flag'] = 2;

		if ($result) {
			$response['success'] = 1;
			$response['message'] = "Successfully " . $msg;
		} else {
			$response['success'] = 0;
			$response['message'] = "Error While " . $msg;
		}
		return response()->json($response);
	}

	public function deletepartner(Request $request)
	{

		$id = $request->input('id');
		$PartnerData = Partner::where('id', $id)->first();
		if ($PartnerData && $PartnerData->profile_image && file_exists(public_path('/uploads/') . $PartnerData->profile_image)) {
			unlink(public_path('/uploads/') . $PartnerData->profile_image);
		}

		$result = Partner::where('id', $id)->delete();
		$total_partners = Partner::count();
		//MovieCast::where('id',$id)->delete();

		if ($result) {
			$response['success'] = 1;
			$response['total_partners'] = $total_partners;
		} else {
			$response['success'] = 0;
			$response['message'] = "Error While Deleting";
		}
		echo json_encode($response);
	}

	public function partnerdetail(Request $request)
	{
		try {
			$partner = Partner::find($request->id);
			return response()->json([
				'success' => true,
				'partner' => $partner
			]);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'message' => 'Something Wrong'
			]);
		}
	}

	public function updatePartnerStatus(Request $request)
	{
		$status = $request->get('status');
		$id = $request->get('id');
		$response = [];

		if (isset($status)) {
			if ($status == 1) {
				$query =  Partner::find($id);
				$query->status = 1;
				$query->save();
				$status = 1;
			} else {
				$query =  Partner::find($id);
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

	public function viewListUser()
	{


		try {
			$results = array();
			$appUserData =  DB::table('goodwish')
				->where('referalcode', Session::get('referral_code'))->get();
			$total_users =  DB::table('goodwish')->where('referalcode', Session::get('referral_code'))->count();


			if (!empty($appUserData)) {
				foreach ($appUserData as $rows) {
					$days  =  DB::table('tbl_subscription')->select('days')->where('user_id', $rows->user_id)->first();

					$profile = "/assets/dist/img/default.png";


					$data = (object) array(
						'profileImg' => $profile,
						'id' => $rows->user_id,
						'fname' => $rows->firstname,
						'lname' => $rows->lastname,
						'email' => $rows->email,
						'referral_code' => $rows->referalcode,
						'date' => $rows->login_date,
						'pack' => isset($days) ? $days->days : '',
						'mobile' => $rows->phone,

					);
					array_push($results, $data);
				}
			}

			return view("partner.user.user_list", [
				'appUser' => $results,
				'total_user' => $total_users
			]);
		} catch (\Exception $e) {
			$response_array = array('success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine());
			$response = Response::json($response_array, 200);
			return $response;
		}
	}

	// public function deleteUser(Request $request){
	// 	$user_id = $request->input('user_id');
	// 	$result =  User::where('user_id',$user_id)->delete();
	// 	$total_user = User::count();
	// 	if ($result) {
	// 		$response['success'] = 1;
	// 		$response['total_user'] = $total_user;
	// 	} else {
	// 		$response['success'] = 0;
	// 		$response['total_user'] = 0;
	// 	}
	// 	echo json_encode($response);
	// }

	public function viewUser($user_id)
	{
		$data = User::where('user_id', $user_id)
			->where('device_token', Session::get('referral_code'))
			->first();

		return view('partner.user.viewusers')->with('data', $data);
	}

	public function updateUserProfile(Request $request)
	{
		$user_id = $request->input('user_id');
		$hdn_profile_image =  $request->input('hdn_profile_image');
		$profile_image = '';
		$data = [];

		// $s3 = Storage::disk('s3');
		// if ($request->hasfile('profile_image')) {
		// 	$file = $request->file('profile_image');
		// 	$imageFileName='profile_' . rand(111,999) . '.' . $file->getClientOriginalExtension();
		// 	$destinationPath = '/uploads/';
		// 	// File::makeDirectory($destinationPath, $mode = 0777, true, true);
		// 	$filePath = $destinationPath . $imageFileName;
		// 	if ($s3->put($filePath, file_get_contents($file)) ){
		// 		$data['profile_image'] = $imageFileName;
		// 	}
		// }else{
		// 	$data['profile_image'] = $hdn_profile_image;
		// }
		$imageFileName = "";
		if ($request->hasfile('profile_image')) {
			$profile_image = $request->file('profile_image');
			$imageFileName = 'user_' . rand(111, 999) . '.' . $profile_image->getClientOriginalExtension();
			$destinationPath = public_path('uploads/');
			File::makeDirectory($destinationPath, $mode = 0777, true, true);
			$profile_image->move($destinationPath, $imageFileName);
			$data['profile_image'] = $imageFileName;
		} else {
			$data['profile_image'] = $hdn_profile_image;
		}
		$profile_image = $data['profile_image'];
		Session::put('profile_image', asset('/uploads/' . $profile_image));
		$update =  User::where('user_id', $user_id)->update($data);
		if ($update) {
			$response['user_profile_url'] = url(env('DEFAULT_IMAGE_URL') . $profile_image);
			$response['status'] = 1;
		} else {
			$response['user_profile_url'] = "";
			$response['status'] = 0;
		}
		echo json_encode($response);
	}

	public function showUserList(Request $request)
	{

		$columns = array(
			0 => 'user_id',
			1 => 'fullname',
			2 => 'email',
			3 => 'status',
		);

		$totalData = User::where('device_token', Session::get('referral_code'))
			->count();

		$totalFiltered = $totalData;

		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if (empty($request->input('search.value'))) {

			$UserData = User::where('device_token', Session::get('referral_code'))
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();
		} else {
			$search = $request->input('search.value');

			$UserData =  User::where('device_token', Session::get('referral_code'))
				->where('id', 'LIKE', "%{$search}%")
				->orWhere('fullname', 'LIKE', "%{$search}%")
				->orWhere('email', 'LIKE', "%{$search}%")
				->offset($start)
				->limit($limit)
				->orderBy($order, $dir)
				->get();

			$totalFiltered = User::where('device_token', Session::get('referral_code'))
				->where('id', 'LIKE', "%{$search}%")
				->orWhere('fullname', 'LIKE', "%{$search}%")
				->orWhere('email', 'LIKE', "%{$search}%")
				->count();
		}

		$data = array();
		if (!empty($UserData)) {
			foreach ($UserData as $rows) {

				$view =  route('partner/user/view', $rows->user_id);
				logger($view);
				if (!empty($rows->profile_image)) {
					$profile = '<img height="60px;" width="60px;" src="' . url(env('DEFAULT_IMAGE_URL') . $rows->profile_image) . '" class="" alt="">';
				} else {
					$profile = '<img height="60px;" width="60px;" src="' . asset('assets/dist/img/default.png') . '" class="" alt="">';
				}

				if ($rows->status == 0) {
					$status =  '<span class="badge badge-pill badge-danger">De-Active</span>';
				} elseif ($rows->status == 1) {
					$status =  '<span class="badge badge-pill badge-success">Active</span>';
				}
				$no_of_purchase = Subscription::where('user_id', $rows->user_id)->count();
				$data[] = array(
					$profile,
					$rows->fullname,
					$rows->email,
					$no_of_purchase,
					$status,
					'<a href="' . $view . '" class="btn btn-success text-white" title="View Details" data-toggle="tooltip" data-original-title="View Details">View Details</a>'
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

		echo json_encode($json_data);
		exit();
	}

	public function MyProfile()
	{
		if (Session::get('role') == "partner" && Session::get('is_logged') == 1) {
			$data = Partner::where('id', Session::get('admin_id'))->first();
			return view('partner.user.my-profile')->with('data', $data);
		} else {
			return Redirect::route('login');
		}
	}

	public function showDashboard()
	{
		if (Session::get('name') && Session::get('is_logged') == 1) {
			$totalUser =  DB::table('goodwish')->where('referalcode', Session::get('referral_code'))->count();
			$income =  DB::table('tbl_partners')->select('income')->where('id', Session::get('admin_id'))->first();
			// $totalSubscription = Subscription::count();
			// $totalMovie = Content::where('content_type',1)->count();
			// $totalSeries = Content::where('content_type',2)->count();
			// $totalLanguage = Language::count();
			// $totalGenre = Genre::count();
			// $totalActor = Actor::count();
			// $totalTVCategory = TVCategory::count();
			// $totalTVChannel = TVChannel::count();

			// $totalMovieViews = Content::where('content_type',1)->sum('total_view');
			// $totalMovieDownload = Content::where('content_type',1)->sum('total_download');
			// $totalMovieShare = Content::where('content_type',1)->sum('total_share');

			// $totalSeriesViews = Content::where('content_type',2)->sum('total_view');
			// $totalSeriesDownload = Content::where('content_type',2)->sum('total_download');
			// $totalSeriesShare = Content::where('content_type',2)->sum('total_share');

			// $totalChannelViews = TVChannel::sum('total_view');
			// $totalChannelShare = TVChannel::sum('total_share');

			// $totalComment = Comment::count();

			if (Session::get('role') == "partner")
				return view('partner.dashboard')->with('totalUser', $totalUser)
					->with('income', $income->income);
		} else {
			return Redirect::route('login');
		}
	}

	public function showPartnerLogin()
	{
		if (Session::get('email') && Session::get('is_user') == 1) {
			return Redirect::route('partner/dashboard');
		} else {
			return view('partner.login');
		}
	}

	public function doPartnerlogin(Request $request)
	{
		$username = $request->input('username');
		$password = $request->input('password');
		$checkLogin = Partner::where('email', $username)->first();


		if (!empty($checkLogin) && $checkLogin->role == 'partner') {
			if ($checkLogin->status == 0) {
				Session::flash('invalid', 'You account is inactive.Please contact admin');
				return back();
			}
			if (Hash::check($password, $checkLogin->password)) {

				Session::put('name', $checkLogin->name);
				Session::put('email', $checkLogin->email);
				Session::put('available_income', $checkLogin->available_income);
				Session::put('admin_id', $checkLogin->id);
				Session::put('profile_image', asset('/uploads/' . $checkLogin->profile_image));
				Session::put('is_logged', 1);
				Session::put('is_admin', 1);
				Session::put('role', $checkLogin->role);
				Session::put('referral_code', $checkLogin->referral_code);

				return  redirect()->route('partner/dashboard');
			} else {
				Session::flash('invalid', 'Invalid email or password combination. Please try again.');
				return back();
			}
		} else {
			Session::flash('invalid', 'Invalid email or password combination. Please try again.');
			return back();
		}
	}


	public function viewListReports()
	{
		// $data = User::where('user_id',$user_id)
		// 			->where('device_token', Session::get('referral_code'))
		// 			->first();

		return view('partner.incomeReports.income');
	}

	public function viewListOffer()
	{
		// dd("hello");
		// $data = User::where('user_id',$user_id)
		// 			->where('device_token', Session::get('referral_code'))
		// 			->first();

		// return view('partner.offers.offers');
		try {
			$results = array();
			$appUserData =  TbiOffer::all();
			$total_users =  TbiOffer::count();


			if (!empty($appUserData)) {
				foreach ($appUserData as $rows) {

					$image = "/public/uploads/offer/$rows->image";


					$data = (object) array(
						'id' => $rows->user_id,
						'title' => $rows->title,
						'image' => $image,
						'description' => $rows->description,

					);
					array_push($results, $data);
				}
			}

			return view("partner.offers.offers", [
				'appUser' => $results,
				'total_user' => $total_users
			]);
		} catch (\Exception $e) {
			$response_array = array('success' => false, 'error' => $e->getMessage(), 'line' => $e->getLine());
			$response = Response::json($response_array, 200);
			return $response;
		}
	}

	public function deleteuser(Request $request)
	{
		try {
			DB::table('goodwish')->where('user_id', $request->id)->delete();
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

	public function getuserdetail(Request $request)
	{
		try {
			$partner = DB::table('goodwish')->where('user_id', $request->id)->first();
			return response()->json([
				'success' => true,
				'partner' => $partner
			]);
		} catch (\Throwable $th) {
			return response()->json([
				'success' => false,
				'message' => 'Something Wrong'
			]);
		}
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
            $net_income = DB::table('tbl_partners')->select('income')->where('id',Session::get('admin_id'))->first();
			$widrhdraw = DB::table('tbl_partners')->select('widthdraw_income')->where('id',Session::get('admin_id'))->first();
			$pending = DB::table('tbl_partners')->select('pending_income')->where('id',Session::get('admin_id'))->first();
			$available = DB::table('tbl_partners')->select('available_income')->where('id',Session::get('admin_id'))->first();
			

			if (!empty($appUserData)) {
				foreach ($appUserData as $rows) {



					$data = (object) array(
						'id' => $rows->id,
						'amount' => $rows->amount,
						'status' => $rows->status,
						'created_at' => $rows->created_at,
						'updated_at' => $rows->updated_at,

					);
					array_push($results, $data);
				}
			}

			return view("partner.payout.payout_list", [
				'results' => $results,
				'total_partners' => $total_partners,
				'netincome'=> number_format($net_income->income, 2),
				'widthdraw'=> number_format($widrhdraw->widthdraw_income, 2),
				'pending'=> number_format($pending->pending_income, 2),
				'available'=> number_format($available->available_income, 2),
				
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
	// 	$details = [
	// 		'template'  => 'new-payout-email',
	// 		'subject'   => 'New Payout Request',
	// 		// 'email'     =>  $email,
	// 		];
	//  Mail::to('mabubakar9231@gmail.com')->send(new  \App\Mail\SendPassword($details));
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
}
