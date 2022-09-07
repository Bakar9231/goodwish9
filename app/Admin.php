<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
	use HasApiTokens;
	protected $table = 'tbl_admin';
	public $primaryKey = 'id';
	public $timestamps = true;
	public $incrementing = false;

	protected $fillable = [
        'name', 'username', 'email', 'mobile', 'role', 'password', 'referral_code', 'referral_income_percentage', 'referral_link', 'profile_image', 'unique_key'
    ];
	
	public static function verify_request_base($headers)
	{
	
		
		$unique_key = $headers['unique-key'][0];
		if($unique_key){
			$admin = Admin::where('unique_key', '=', $unique_key)->count();
	
			if($admin <= 0)
			{
				$status = 401;
				$response = array('status' => $status, 'errors' => 'Unauthorized Access!');
				return $response;
				// return $ci->response($response, 401);
				exit();
			}
			else
			{
				$status = 200;
				$response = array('status' => $status, 'errors' => 'Authorized Access!');
				return $response;
				// return $ci->response($response, 200);
				exit();
			}
		}else
		{
			$status = 401;
			$response = array('status' => $status, 'errors' => 'Unauthorized Access!');
			return $response;
			// return $ci->response($response, 200);
			exit();
		}
       
	}
}
