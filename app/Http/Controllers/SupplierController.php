<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SupplierController extends Controller
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

    public function get_supplier_by_id(Request $request)
    {
        $supplier_id = $request->input('id');

        $supplier = Supplier::where('id','=',$supplier_id)->first();

        return ([
            'success' => true,
            'message' => 'Data supplier ditemukan.',
            'data'    => $supplier
        ]);
    }

    public function get_suppliers(Request $request)
    {
        $paging = $request->input('paging');

        $suppliers = Supplier::orderBy('created_at','DESC')->paginate($paging);

        return ([
            'success' => true,
            'message' => 'Data supplier ditemukan.',
            'data'    => $suppliers
        ]);
    }

    public function add_supplier(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $image = $request->input('image');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $desc = $request->input('desc');

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'email' =>  "",
            'image' =>  "",
            'address' => "",
            'phone' => "",
            'desc' => "",
        ]);

        $inputs = $request->all();
        $rules = [
            'name'           => 'required',
            'email'          => 'email',
            'address'        => 'required',
            'phone'          => 'required',
        ];

        $messages = [
            'name.required'       => 'nama supplier harus diisi!',
            'email.required'      => 'email harus diisi!',
            'address.required'    => 'addres harus diisi!',
            'phone.required'      => 'phone harus diisi!',
        ];
        $validator = Validator::make($inputs,$rules,$messages);

        if ($validator->fails()) {
            $res['success'] = false;
            $res['message'] = $validator->errors()->all();
            return collect($res)->toJson();
        }

        $find_supplier = Supplier::where('email', '=', $email)->get();

        if (count($find_supplier) == 1) {
            return ([
                'success' => false,
                'message' => 'Supplier sudah ada.'
            ]);
        }
        $api_key = sha1(time());

        $add_supplier = Supplier::create([
            'name'     => $name,
            'email'    => $email,
            'image'    => $image,
            'address'  => $address,
            'phone'    => $phone,
            'desc'     => $desc,
        ]);

        if ($add_supplier) {
            $response = ([
                'id'      => $add_supplier['id'],
                'email'   => $add_supplier['email'],
                'name'    => $add_supplier['name'],
                'image'   => $add_supplier['image'],
                'address' => $add_supplier['address'],
                'phone'   => $add_supplier['phone'],
                'desc'    => $add_supplier['desc'],
            ]);

            return ([
                'success' => true,
                'api_key' => $api_key,
                'message' => 'Berhasil menambah supplier',
                'data'    => $response
            ]);
        } else {
            return ([
                'success' => false,
                'api_key' => '',
                'message' => 'Gagal menambah supplier',
                'data'    => $responseError
            ]);
        }

    }

    public function update_supplier(Request $request)
    {
        
        $supplier_id = $request->input('id');
        $name        = $request->input('name');
        $email       = $request->input('email');
        $image       = $request->input('image');
        $address     = $request->input('address');
        $phone       = $request->input('phone');
        $desc        = $request->input('desc');

        $inputs = $request->all();
        $rules = [
            'name'           => 'required',
            'email'          => 'required',
            'address'        => 'required',
            'phone'          => 'required',
        ];

        $messages = [
            'name.required'       => 'nama supplier harus diisi!',
            'email.required'      => 'email supplier harus diisi!',
            'address.required'    => 'addres harus diisi!',
            'phone.required'      => 'phone harus diisi!',
        ];
        $validator = Validator::make($inputs,$rules,$messages);

        if ($validator->fails()) {
            $res['success'] = false;
            $res['message'] = $validator->errors()->all();
            return collect($res)->toJson();
        }

        $find_supplier = Supplier::where('id', '=', $supplier_id)->get();
        if (count($find_supplier) != 1) {
            return ([
                'success' => false,
                'message' => 'Supplier tidak ditemukan.'
            ]);
        }

        $update_supplier = Supplier::where('id', '=', $supplier_id)->update([
            'name'     => $name,
            'email'    => $email,
            'image'    => $image,
            'address'  => $address,
            'phone'    => $phone,
            'desc'     => $desc,
        ]);

        if ($update_supplier) {
            $response = ([
                'name'    => $name,
                'email'   => $email,
                'image'   => $image,
                'address' => $address,
                'phone'   => $phone,
                'desc'    => $desc
            ]);

            return ([
                'success' => true,
                'message' => 'Berhasil update data supplier',
                'data'    => $response
            ]);
        } else {
            return ([
                'success' => false,
                'message' => 'Gagal update data supplier'
            ]);
        }
        
    }

    public function delete_supplier(Request $request)
    {

        $supplier_id = $request->input('id');

        $delete_supplier_data = Supplier::where(['id' => $supplier_id])->delete();

        if($delete_supplier_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data supplier',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data supplier',
            ]);
        }
    }
}