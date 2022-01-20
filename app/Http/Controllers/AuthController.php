<?php
namespace App\Http\Controllers;
use App\Http\Controllers\AuthController; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    private function getUserPrivate($token)
    {
        $user = User::where('token',$token)->where('is_confirm',1)->first();
        if(!empty($user)){
            return $user;
        }
        else{
            return false;
        }
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::where('email', $email)->first();
        if(!empty($user)){
            if (Hash::check($password, $user->password)) {
                $token = $this->getToken(48)."".$user->id;
                $otp = rand(10000,100000);
                $user->otp = $otp;
                $user->otp_limit = 0;
                $user->token = $token;
                $user->save();
                $this->sendOTP($otp,$user);
                return response()->json([
                    'status' => '2xx',
                    'message' => 'berhasil login!',
                    'token' => $token
                ]);
            } 
            else{
                return response()->json([
                    'status' => '4xx',
                    'message' => 'data anda salah!'
                ]);
            }
        }
        else{
            return response()->json([
                'status' => '4xx',
                'message' => 'data anda salah!'
            ]);
        }
    }

    public function confirmOTP(Request $request)
    {
        $otp = $request->input('otp');
        $token = $request->input('token');
        $user = User::where('token',$token)->first();
        if(!empty($user)){
            if($user->otp_limit > 3){
                return response()->json([
                    'status' => '4xx',
                    'message' => 'batas confirmasi otp sudah 3 kali, silakan login lagi!'
                ]);
            }
            else{
                if($otp == $user->otp){
                    $user->is_confirm = 1;
                    $user->update();
                    return response()->json([
                        'status' => '2xx',
                        'message' => 'successfully!'
                    ]);
                }
                else{
                    $user->otp_limit += 1;
                    $user->update();
                    return response()->json([
                        'status' => '4xx',
                        'message' => 'otp salah!'
                    ]);
                }
            }
        }
        else{
            return response()->json([
                'status' => '4xx',
                'message' => 'token not valid'
            ]);
        }
    }

    public function logout($token)
    {
        $user = User::where('token',$token)->first();
        if(empty($user)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        else{
            $user->is_confirm = 0;
            $user->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'successfully'
                ]
            );
        }
    }

    public function getToken($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
    
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
    
        return $randomString;
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $username = $request->input('username');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));
        if($email && $name && $password){
            $if_email_exist = User::where('username',$username)->first();
            $if_username_exist = User::where('email',$email)->first();
            if(!empty($if_email_exist) || !empty($if_username_exist)){
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'data sudah ada!'
                    ]
                );   
            }
            $register = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]); 
            if($register){
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'register berhasil'
                    ]
                );
            }
            else{
               return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'gagal daftar'
                    ]
                ); 
            }
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'gagal'
                ]
            ); 
        }
    }

    public function sendOTP($otp,$user)
    {

    }

    public function editUser(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            );
        }
        $name = $request->input('name');
        $username = $request->input('username');
        $limit_monthly = $request->input('limit_monthly');
        $password = $request->input('password');
        $user->name = $name;
        $user->username = $username;
        $user->limit_monthly = $limit_monthly;
        $user->password = Hash::make($password);
        $user->save();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully!',
                'data' => $user
            ]
        );
    
    }

      
}

    