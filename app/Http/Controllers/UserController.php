<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class UserController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function get_user_by_id(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];

        $barangs = User::where('id','=', $user_id)->first();

        return json_encode([
            'success' => true,
            'message' => 'Data user ditemukan.',
            'data'    => $barangs
        ]);
    }

    public function get_users(Request $request)
    {
        $paging = $request->input('paging');

        $barangs = User::orderBy('created_at','DESC')->paginate($paging);

        return json_encode([
            'success' => true,
            'message' => 'Data user ditemukan.',
            'data'    => $barangs
        ]);
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $password = $request->input('password');

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'email' => "",
            'image' => "",
            'phone' => "",
            'address' => "",
            'is_verify' =>  "",
            'api_key' => "",
        ]);

        if (!empty($name) && !empty($email) && !empty($password)) {

            $find_user = User::where('email', '=', $email)->get();
            if (count($find_user) == 1) {
                return ([
                    'success' => false,
                    'message' => 'Email sudah digunakan.'
                ]);
            }
            $api_key = sha1(time());

            $add_user = User::create([
                'name'     => $name,
                'email'    => $email,
                'address'  => $address,
                'phone'    => $phone,
                'password' => Hash::make($password),
                'image'    => '',
                'is_verify' => '0',
                'api_key'  => $api_key
            ]);

            if ($add_user) {
                $response = ([
                    'id'  => $add_user['id'],
                    'name'  => $add_user['name'],
                    'email' => $add_user['email'],
                    'image' => $add_user['image'],
                    'phone' => $add_user['phone'],
                    'address' => $add_user['address'],
                    'is_verify' =>  $add_user['is_verify'],
                    'api_key' => $api_key,
                ]);

                return ([
                    'success' => true,
                    'api_key' => $api_key,
                    'message' => 'Anda berhasil melakukan registrasi',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'api_key' => '',
                    'message' => 'Anda gagal melakukan registrasi',
                    'data'    => $responseError
                ]);
            }
        } else {
            return ([
                'success' => false,
                'api_key' => '',
                'message' => 'Mohon lengkapi data yang dibutuhkan',
                'data'    => $responseError
            ]);
        }
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $hasher = app()->make('hash');

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'email' => "",
            'image' => "",
            'phone' => "",
            'address' => "",
            'image' => "",
            'is_verify' =>  "",
            'api_key' => "",
        ]);

        if (!empty($email) && !empty($password)) {
            $find_user = User::where('email', '=', $email)->get();

            if (count($find_user) == 0) {
                return ([
                    'success' => false,
                    'message' => 'Email yang anda masukkan salah.'
                ]);
            }

            $check_password = $hasher->check($password, $find_user[0]['password']);
            $api_key = sha1(time());

            $update_token = User::where('email', '=', $email)->update([
                'api_key' => $api_key
            ]);

            $response = ([
                'id' => $find_user[0]['id'],
                'name'  => $find_user[0]['name'],
                'email' => $find_user[0]['email'],
                'image' => $find_user[0]['image'],
                'phone' => $find_user[0]['phone'],
                'address' => $find_user[0]['address'],
                'image' => $find_user[0]['image'],
                'is_verify' => $find_user[0]['is_verify'],
                'api_key' => $api_key,
            ]);

            if ($check_password && $update_token) {
                return ([
                    'success' => true,
                    'api_key' => $api_key,
                    'message' => 'Anda berhasil melakukan login.',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Password yang anda masukkan salah.',
                    'api_key' => "-",
                    'data'    => $responseError
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang dibutuhkan.',
                'api_key' => "-",
                'data'    => $responseError
            ]);
        }
    }

    public function check_login(Request $request)
    {
        $api_key = $request->input('token');

        $find_user = User::where('api_key', '=', $api_key)->first();

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'email' => "",
            'image' => "",
            'phone' => "",
            'address' => "",
            'image' => "",
            'is_verify' =>  "",
            'api_key' => "",
        ]);

        if (!empty($find_user)) {
            return ([
                'success' => true,
                'api_key' => $find_user->api_key,
                'message' => 'Anda sudah melakukan login.',
                'data'    => $find_user
            ]);
        } else {
            return ([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.',
                'api_key' => "",
                'data'    => $responseError
            ]);
        }
    }

    public function change_password(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];
        //
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        $hasher = app()->make('hash');

        if (!empty($old_password) && !empty($new_password)) {
            $find_user = User::where('id', '=', $user_id)->get();

            $check_password = $hasher->check($old_password, $find_user[0]['password']);

            if (!$check_password) {
                return ([
                    'success' => false,
                    'message' => 'Password lama yang anda masukkan salah.'
                ]);
            }

            if(strlen($new_password) < 8){
                return ([
                    'success' => false,
                    'message' => 'Password minimal 8 karakter.'
                ]);
            }

            $update_password = User::where('id', '=', $user_id)->update([
                'password' => Hash::make($new_password)
            ]);

            if ($update_password) {
                return ([
                    'success' => true,
                    'message' => 'Anda berhasil melakukan update password.',
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Anda gagal melakukan update password.'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang dibutuhkan.'
            ]);
        }
    }

    public function update(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];
        
        $name = $request->input('name');
        $email = $request->input('email');
        $address = $request->input('address');
        $phone = $request->input('phone');

        if (!empty($user_id) && !empty($name) && !empty($email) && !empty($phone)) {

            $find_user = User::where('email', '=', $email)->get();
            if (count($find_user) != 1) {
                return ([
                    'success' => false,
                    'message' => 'User tidak ditemukan.'
                ]);
            }

            $update_user = User::where('id', '=', $user_id)->update([
                'name'     => $name,
                'email'    => $email,
                'address'  => $address,
                'phone'    => $phone,
                'image'    => '',
            ]);

            if ($update_user) {
                $response = ([
                    'name'  => $name,
                    'email' =>  $email,
                    'address' => $address,
                    'phone' =>  $phone,
                    'image' => ''
                ]);

                return ([
                    'success' => true,
                    'message' => 'Anda berhasil melakukan update data user',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Anda gagal melakukan update data user'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang dibutuhkan'
            ]);
        }
    }

    public function delete_user(Request $request)
    {

        $user_id = $request->input('id');

        $delete_user_data = User::where(['id' => $user_id])->delete();

        if($delete_user_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data user',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data user',
            ]);
        }
    }
}