<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class WarehouseController extends Controller
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

    public function get_warehouse_id(Request $request)
    {
        $ws_id = $request->input('id');

        $warehouse = Warehouse::where('id','=',$ws_id)->first();

        return ([
            'success' => true,
            'message' => 'Data warehouse ditemukan.',
            'data'    => $warehouse
        ]);
    }

    public function update_warehouse(Request $request)
    {
        
        $ws_id = $request->input('id');
        $barang_id = $request->input('barang_id');
        $user_id = $request->input('user_id');
        $supplier_id = $request->input('supplier_id');
        $notes = $request->input('notes');

        if (!empty($barang_id) && !empty($user_id) && !empty($supplier_id)) {

            $find_ws = Warehouse::where('id', '=', $ws_id)->get();

            if (count($find_ws) != 1) {
                return ([
                    'success' => false,
                    'message' => 'Data Warehouse tidak ditemukan.'
                ]);
            }

            $update_ws = Warehouse::where('id', '=', $ws_id)->update([
                'barang_id'   => $barang_id,
                'user_id'     => $user_id,
                'supplier_id' => $supplier_id,
                'notes'       => $notes,
            ]);

            if ($update_ws) {
                $response = ([
                    'barang_id'   => $barang_id,
                    'user_id'     => $user_id,
                    'supplier_id' => $supplier_id,
                    'notes'       => $notes,
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil update data warehouse',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal update data warehouse'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta'
            ]);
        }
    }

    public function delete_warehouse(Request $request)
    {

        $ws_id = $request->input('id');

        $delete_warehouse = Warehouse::where(['id' => $ws_id])->delete();

        if($delete_warehouse){
            return ([
                'success' => true,
                'message' => 'Berhasil menghapus data warehouse',
            ]);
        } else {
            return ([
                'success' => true,
                'message' => 'Gagal menghapus data warehouse',
            ]);
        }
    }
}