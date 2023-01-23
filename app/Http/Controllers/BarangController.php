<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BarangController extends Controller
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

    public function get_barang_by_id(Request $request)
    {
        $barang_id = $request->input('id');

        $barangs = Barang::where('id','=',$barang_id)->first();

        return ([
            'success' => true,
            'message' => 'Data barang ditemukan.',
            'data'    => $barangs
        ]);
    }

    public function get_barangs(Request $request)
    {
        $keyword = $request->input('keyword');
        // $paging = $request->input('paging');

        $barangs = Barang::with('suppliers','input_by')->orderBy('created_at','DESC');

        if($keyword){
            $barangs = $barangs->whereRaw('name LIKE ?','%'.$keyword.'%');
        }

        $barangs = $barangs->get();

        return json_encode([
            'success' => true,
            'message' => 'Data barang ditemukan.',
            'data'    => $barangs
        ]);
    }

    public function add_barang(Request $request)
    {
        $name = $request->input('name');
        $image = $request->file('image');
        $price = $request->input('price');
        $qty = $request->input('qty');
        $discount = $request->input('discount');
        $desc = $request->input('desc');

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'alias' =>  "",
            'image' =>  "",
            'price' => "",
            'qty' => "",
            'discount' => "",
            'desc' => "",
        ]);

        if (!empty($name) && !empty($price)) {

            $alias = Str::slug($name);

            $find_barang = Barang::where('alias', '=', $alias)->get();

            if (count($find_barang) == 1) {
                return ([
                    'success' => false,
                    'message' => 'Barang sudah ada.'
                ]);
            }

            $imageName = "";

            if($request->file('image') !== null && $request->file('image') !== ""){
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/barang'), $imageName);
            }

            $add_barang = Barang::create([
                'name'     => $name,
                'alias'    => $alias,
                'image'    => $imageName,
                'price'    => $price,
                'qty'      => $qty,
                'discount' => $discount,
                'desc'     => $desc,
            ]);

            if ($add_barang) {

                $response = ([
                    'id'    => $add_barang['id'],
                    'name'  => $add_barang['name'],
                    'alias' => $add_barang['alias'],
                    'image' => $add_barang['image'],
                    'price' => $add_barang['price'],
                    'qty'   => $add_barang['qty'],
                    'discount' => $add_barang['discount'],
                    'desc' => $add_barang['desc'],
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil menambah barang',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal menambah barang',
                    'data'    => $responseError
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta',
                'data'    => $responseError
            ]);
        }
    }

    public function update_barang(Request $request)
    {
        
        $barang_id = $request->input('id');
        $name = $request->input('name');
        $qty = $request->input('name');
        $price = $request->input('price');
        $qty = $request->input('qty');
        $discount = $request->input('discount');
        $desc = $request->input('desc');

        if (!empty($barang_id) && !empty($name) && !empty($price)) {

            $alias = Str::slug($name);

            $find_barang = Barang::where('id', '=', $barang_id)->first();
            if (!$find_barang) {
                return ([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan.'
                ]);
            }

            if($find_barang['name'] !== $name){
                $find_barang_alias = Barang::where('alias', '=', $alias)->get();
                if (count($find_barang_alias) > 0) {
                    return ([
                        'success' => false,
                        'message' => 'Barang dengan nama '.$name.' sudah ada.'
                    ]);
                }
            }

            $update_barang = Barang::where('id', '=', $barang_id)->update([
                'name'     => $name,
                'qty'      => $qty,
                'alias'    => $alias,
                'price'    => $price,
                'qty'      => $qty,
                'discount' => $discount,
                'desc'     => $desc,
            ]);

            if ($update_barang) {
                $response = ([
                    'name'  => $name,
                    'alias' => $alias,
                    'price' => $price,
                    'qty'   => $qty,
                    'discount' => $discount,
                    'image' => $desc
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil update data barang',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal update data barang'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta'
            ]);
        }
    }

    public function delete_barang(Request $request)
    {

        $barang_id = $request->input('id');

        $delete_barang_data = Barang::where(['id' => $barang_id])->delete();

        if($delete_barang_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data barang',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data barang',
            ]);
        }
    }
}