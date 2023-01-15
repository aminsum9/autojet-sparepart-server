<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TransaksiController extends Controller
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

    public function get_transaksi_by_id(Request $request)
    {
        $trans_id = $request->input('id');

        $transaksi = Transaksi::where('id','=',$trans_id)->first();

        return ([
            'success' => true,
            'message' => 'Data transaksi ditemukan.',
            'data'    => $transaksi
        ]);
    }

    public function get_transaksis(Request $request)
    {
        // $paging = $request->input('paging');

        $transaksis = Transaksi::with('detail_transaksi.barang.suppliers','created_by')->orderBy('created_at','DESC')->get();
        
        return json_encode([
            'success' => true,
            'message' => 'Data transaksi ditemukan.',
            'data'    => $transaksis
        ]);
    }

    public function generate_trx_id($trx_id){
        $find_trans = Transaksi::where('trx_id','=',$trx_id)->first();
        if($find_trans){
            return $this->generate_trx_id(rand(111111, 999999));
        } else {
            return $trx_id;
        }
        //"ABCDEFGHIJKLMNOPQRSTUVWXYZ"
    }

    public function create_transaksi(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];
        
        $discount = $request->input('discount');
        $notes = $request->input('notes');
        $detail_transaksi = $request->input('detail_transaksi');
        
        $responseError = ([
            'trx_id' => 0,
            'subtotal' =>  "",
            'discount' => "",
            'detail_transaksi' => "",
        ]);

        $inputs = $request->all();
        $rules = [
            'detail_transaksi'         => 'required',
        ];

        $messages = [
            'detail_transaksi.required'       => 'detail_transaksi harus diisi!',
        ];

        if($detail_transaksi == "[]"){
            $res['success'] = false;
            $res['message'] = "Detail transaksi tidak boleh kosong!";
            return collect($res)->toJson();
        }

        // if(count($detail_transaksi) == 0){
        //     $res['success'] = false;
        //     $res['message'] = "Detail transaksi tidak boleh kosong!";
        //     return collect($res)->toJson();
        // }

        $validator = Validator::make($inputs,$rules,$messages);

        if ($validator->fails()) {
            $res['success'] = false;
            $res['message'] = $validator->errors()->all();
            return collect($res)->toJson();
        }

        //check stock barang
        $is_stock_empty = false;
        $subtotal = 0;
        if(gettype($detail_transaksi) === 'array'){
            foreach ($detail_transaksi as $item) { 
                $find_barang = Barang::where('id', '=', $item['id'])->first();
                $subtotal = $subtotal + $find_barang['price'] * $item['qty'];

                if($find_barang['qty'] <= 0 || ($find_barang['qty'] - $item['qty'] < 0)){
                    $is_stock_empty = true;
                }
            }
        } else {
            foreach (json_decode($detail_transaksi) as $item) {
                $find_barang = Barang::where('id', '=', $item->id)->first();
                $subtotal = $subtotal + $find_barang['price'] * $item->qty;

                if($find_barang['qty'] <= 0 || ($find_barang['qty'] - $item->qty < 0)){
                    $is_stock_empty = true;
                }
            }
        }

        if ($is_stock_empty === true) {
            return ([
                'success' => false,
                'message' => 'Stok produk kurang mencukupi, silahkan ganti produk.'
            ]);
        }

        $trx_id = $this->generate_trx_id(rand(111111, 999999));

        //count grand total
        $grand_total = $subtotal - $discount;
       
        $add_transaksi = Transaksi::create([
            'trx_id'        => $trx_id,
            'user_id'       => $user_id,
            'status'        => 'new',
            'discount'      => $discount || 0,
            'subtotal'      => $subtotal,
            'grand_total'   => $grand_total,
            'notes'         => $notes,
        ]);

        if ($add_transaksi) {
            //add detail transaksi
            foreach (json_decode($detail_transaksi) as $item) {
                $find_barang = Barang::where('id', '=', $item->id)->first();

                $add_d_trans = new DetailTransaksi();

                $add_d_trans->trans_id  = $add_transaksi->id;
                $add_d_trans->barang_id = $item->id;
                $add_d_trans->qty       = $item->qty;
                $add_d_trans->subtotal  = $item->qty * $find_barang->price;
                $add_d_trans->discount  = $item->qty * $find_barang->discount;
                $add_d_trans->grand_total = $item->qty * $find_barang->price;
                $add_d_trans->notes     = $item->notes;

                $add_d_trans->save();
            }
            
            //update stock barang
            foreach (json_decode($detail_transaksi) as $item) {
                $barang = Barang::where('id', '=', $item->id)->first();

                Barang::where('id', '=', $item->id)->update([
                    'qty'          => $barang->qty - $item->id,
                ]);

            }
            
            $response = ([
                'trx_id'      => $add_transaksi['trx_id'],
                'user_id'     => $add_transaksi['user_id'],
                'status'      => $add_transaksi['status'],
                'discount'    => $add_transaksi['discount'],
                'subtotal'    => $add_transaksi['subtotal'],
                'grand_total' => $add_transaksi['grand_total'],
                'notes'       => $add_transaksi['notes'],
            ]);

            return ([
                'success' => true,
                'message' => 'Berhasil menambah transaksi',
                'data'    => $response
            ]);
        } else {
            return ([
                'success' => false,
                'message' => 'Gagal menambah transaksi',
                'data'    => $responseError
            ]);
        }

    }

    public function update_transaksi(Request $request)
    {
        
        $trans_id = $request->input('id');
        $id_trx = $request->input('id_trx');
        $user_id = $request->input('user_id');
        $status = $request->input('status');
        $subtotal = $request->input('subtotal');
        $discount = $request->input('discount');
        $grand_total = $request->input('grand_total');
        $notes = $request->input('notes');

        $inputs = $request->all();
        $responseError = ([
            'trans_id' => 0,
            'id_trx' => "",
            'user_id' =>  "",
            'status' =>  "",
            'subtotal' =>  "",
            'discount' => "",
            'grand_total' => "",
            'notes' => "",
        ]);

        $inputs = $request->all();
        $rules = [
            'trans_id'         => 'required',
            'id_trx'           => 'required',
            'user_id'          => 'required',
            'status'           => 'required',
            'subtotal'         => 'required',
            'discount'         => 'required',
            'grand_total'      => 'required',
            'notes'            => 'required',
        ];

        $messages = [
            'trans_id.required'     => 'trans_id harus diisi!',
            'id_trx.required'       => 'id_trx harus diisi!',
            'user_id.required'      => 'user_id harus diisi!',
            'status.required'       => 'status harus diisi!',
            'discount.required'     => 'discount harus diisi!',
            'subtotal.required'     => 'subtotal harus diisi!',
            'grand_total.required'  => 'grand_total harus diisi!',
            'notes.required'        => 'notes harus diisi!',
        ];

        $validator = Validator::make($inputs,$rules,$messages);

        if ($validator->fails()) {
            $res['success'] = false;
            $res['message'] = $validator->errors()->all();
            return collect($res)->toJson();
        }

        $find_transaksi = Transaksi::where('id', '=', $trans_id)->get();
        if (count($find_transaksi) != 1) {
            return ([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ]);
        }

        $update_transaksi = Transaksi::where('id', '=', $trans_id)->update([
            'id'           => $trans_id,
            'id_trx'       => $id_trx,
            'user_id'      => $user_id,
            'status'       => $status,
            'discount'     => $discount,
            'subtotal'     => $subtotal,
            'grand_total'  => $grand_total,
            'notes'        => $notes,
        ]);

        if ($update_transaksi) {
            $response = ([
                'trans_id'     => $trans_id,
                'id_trx'       => $id_trx,
                'user_id'      => $user_id,
                'status'       => $status,
                'discount'     => $discount,
                'subtotal'     => $subtotal,
                'grand_total'  => $grand_total,
                'notes'        => $notes,
            ]);

            return ([
                'success' => true,
                'message' => 'Berhasil update data transaksi',
                'data'    => $response
            ]);
        } else {
            return ([
                'success' => false,
                'message' => 'Gagal update data transaksi'
            ]);
        }
        
    }

    public function delete_transaksi(Request $request)
    {

        $trans_id = $request->input('id');

        $delete_trans_data = Transaksi::where(['id' => $trans_id])->delete();

        if($delete_trans_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data transaksi',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data transaksi',
            ]);
        }
    }
}