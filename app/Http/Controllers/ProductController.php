<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ProductController extends Controller
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

    public function get_product_by_id(Request $request)
    {
        $product_id = $request->input('product_id');

        $products = Produk::where('id','=',$product_id)->first();

        return ([
            'success' => true,
            'message' => 'Data produk ditemukan.',
            'data'    => $products
        ]);
    }

    public function get_products(Request $request)
    {
        $paging = $request->input('paging');



        $products = Produk::orderBy('created_at','DESC')->paginate($paging);

        return ([
            'success' => true,
            'message' => 'Data produk ditemukan.',
            'data'    => $products
        ]);
    }

    public function add_product(Request $request)
    {
        $name = $request->input('name');
        $image = $request->input('image');
        $price = $request->input('price');
        $discount = $request->input('discount');
        $desc = $request->input('desc');

        $responseError = ([
            'id' => 0,
            'name' =>  "",
            'alias' =>  "",
            'image' =>  "",
            'price' => "",
            'discount' => "",
            'desc' => "",
        ]);

        if (!empty($name) && !empty($price)) {

            $alias = Str::slug($name).'-'.rand(111111, 999999);

            $find_user = Produk::where('alias', '=', $alias)->get();

            if (count($find_user) == 1) {
                return ([
                    'success' => false,
                    'message' => 'Produk sudah ada.'
                ]);
            }
            $api_key = sha1(time());

            $add_product = Produk::create([
                'name'     => $name,
                'alias'    => $alias,
                'image'    => $image,
                'price'    => $price,
                'discount' => $discount,
                'desc'     => $desc,
            ]);

            if ($add_product) {
                $response = ([
                    'id'  => $add_product['id'],
                    'name'  => $add_product['name'],
                    'alias' => $add_product['alias'],
                    'image' => $add_product['image'],
                    'price' => $add_product['price'],
                    'discount' => $add_product['discount'],
                    'desc' => $add_product['desc'],
                ]);

                return ([
                    'success' => true,
                    'api_key' => $api_key,
                    'message' => 'Berhasil menambah produk',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'api_key' => '',
                    'message' => 'Gagal menambah produk',
                    'data'    => $responseError
                ]);
            }
        } else {
            return ([
                'success' => false,
                'api_key' => '',
                'message' => 'Mohon lengkapi data yang diminta',
                'data'    => $responseError
            ]);
        }
    }

    public function update_product(Request $request)
    {
        
        $product_id = $request->input('id');
        $name = $request->input('name');
        $price = $request->input('price');
        $discount = $request->input('discount');
        $desc = $request->input('desc');

        if (!empty($product_id) && !empty($name) && !empty($price)) {

            $alias = Str::slug($name).'-'.rand(111111, 999999);

            $find_user = Produk::where('id', '=', $product_id)->get();
            if (count($find_user) != 1) {
                return ([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan.'
                ]);
            }

            $update_product = Produk::where('id', '=', $product_id)->update([
                'name'     => $name,
                'alias'    => $alias,
                'price'    => $price,
                'discount' => $discount,
                'desc'     => $desc,
            ]);

            if ($update_product) {
                $response = ([
                    'name'  => $name,
                    'alias' => $alias,
                    'price' => $price,
                    'discount' => $discount,
                    'image' => $desc
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil update data produk',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal update data produk'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta'
            ]);
        }
    }

    public function delete_product(Request $request)
    {

        $product_id = $request->input('id');

        $delete_product_data = Produk::where(['id' => $product_id])->delete();

        if($delete_product_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data produk',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data produk',
            ]);
        }
    }
}