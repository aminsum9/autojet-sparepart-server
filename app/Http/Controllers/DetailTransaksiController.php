<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class DetailTransaksiController extends Controller
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

    public function get_detail_trans_by_id(Request $request)
    {
        $d_trans_id = $request->input('id');

        $detail_transaksi = DetailTransaksi::with('barang')->where('id','=',$d_trans_id)->first();

        return json_encode([
            'success' => true,
            'message' => 'Data detail transaksi ditemukan.',
            'data'    => $detail_transaksi
        ]);
    }

    public function update_detail_trans(Request $request)
    {
        
        $detail_trans_id = $request->input('id');
        $trans_id = $request->input('trans_id');
        $barang_id = $request->input('barang_id');
        $qty = $request->input('qty');
        $subtotal = $request->input('subtotal');
        $discount = $request->input('discount');
        $grand_total = $request->input('grand_total');
        $notes = $request->input('notes');

        if (!empty($detail_trans_id) && !empty($barang_id) && !empty($trans_id) && !empty($qty) && !empty($subtotal) && !empty($discount) && !empty($grand_total)) {

            $find_d_trans = DetailTransaksi::where('id', '=', $detail_trans_id)->get();

            if (count($find_d_trans) != 1) {
                return ([
                    'success' => false,
                    'message' => 'Detail transaksi tidak ditemukan.'
                ]);
            }

            $update_d_trans = DetailTransaksi::where('id', '=', $detail_trans_id)->update([
                'trans_id' => $trans_id,
                'barang_id' => $barang_id,
                'qty'      => $qty,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'grand_total'     => $grand_total,
                'notes'    => $notes,
            ]);

            if ($update_d_trans) {
                $response = ([
                    'trans_id' => $trans_id,
                    'barang_id' => $barang_id,
                    'qty'      => $qty,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'grand_total'     => $grand_total,
                    'notes'    => $notes,
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil update data detail transaksi',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal update data detail transaksi'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta'
            ]);
        }
    }

    public function delete_detail_trans(Request $request)
    {

        $d_trans_id = $request->input('id');

        $delete_d_trans_data = DetailTransaksi::where(['id' => $d_trans_id])->delete();

        if($delete_d_trans_data){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data detail transaksi',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data detail transaksi',
            ]);
        }
    }
}