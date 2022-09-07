<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use URL;
use Hash;
use Session;
use DB;
use File;
use App\Admin;
use App\User;
use App\Subscription;
use Illuminate\Support\Facades\Response;
class UserController extends Controller
{

    public function viewListUser()
	{
		
		try {
			$results = array();
			$appUserData =  DB::table('goodwish')
			->get();
			$total_users =  DB::table('goodwish')->count();
			if(!empty($appUserData))
			{
				foreach ($appUserData as $rows)
				{

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

            return view("admin.user.user_list", [
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
		$data = User::where('user_id',$user_id)->first();
	
		return view('admin.user.viewusers')->with('data',$data);
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
			$imageFileName = 'user_' . rand(111,999) . '.' . $profile_image->getClientOriginalExtension();
			$destinationPath = public_path('uploads/');
			File::makeDirectory($destinationPath, $mode = 0777, true, true);
			$profile_image->move($destinationPath, $imageFileName);
			$data['profile_image'] = $imageFileName;
		}else{
			$data['profile_image'] = $hdn_profile_image;
		}
		$profile_image = $data['profile_image'];
       	$update =  User::where('user_id',$user_id)->update($data);
       if($update){
		$response['user_profile_url'] = url(env('DEFAULT_IMAGE_URL').$profile_image);
        $response['status'] = 1;
       }else{
		$response['user_profile_url'] = "";
        $response['status'] = 0;
       }
       echo json_encode($response);
	}

	public function showUserList(Request $request)
    {

		$columns = array( 
            0=>'user_id',
            1=>'fullname',
            2=>'email',
            3=>'status',
		);

		$totalData = User::count();

		$totalFiltered = $totalData; 

		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		if(empty($request->input('search.value')))
		{      
			
		$UserData = User::offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
		}
		else {
		$search = $request->input('search.value'); 

		$UserData =  User::where('id','LIKE',"%{$search}%")
					->orWhere('fullname', 'LIKE',"%{$search}%")
					->orWhere('email', 'LIKE',"%{$search}%")
					->offset($start)
					->limit($limit)
					->orderBy($order,$dir)
					->get();

		$totalFiltered = User::where('id','LIKE',"%{$search}%")
					->orWhere('fullname', 'LIKE',"%{$search}%")
					->orWhere('email', 'LIKE',"%{$search}%")
					->count();
		}

		$data = array();
		if(!empty($UserData))
		{
			foreach ($UserData as $rows)
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

		echo json_encode($json_data); 
        exit();
	}
}
