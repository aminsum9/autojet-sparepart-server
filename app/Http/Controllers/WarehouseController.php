<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Barang;
use App\Models\SupplierBarang;
use App\Models\BarangUser;
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

    public function get_warehouses(Request $request)
    {
        // $paging = $request->input('paging');

        $warehouses = Warehouse::with('barang','user','supplier')->orderBy('created_at','DESC')->get();

        return json_encode([
            'success' => true,
            'message' => 'Data warehouse ditemukan.',
            'data'    => $warehouses
        ]);
    }

    public function add_warehouse(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];
        
        $qty         = $request->input('qty');
        $barang_id   = $request->input('barang_id');
        $supplier_id = $request->input('supplier_id');
        $notes       = $request->input('notes');

        if (!empty($qty) && !empty($barang_id) && !empty($user_id) && !empty($supplier_id)) {

            $barang = Barang::where('id','=',$barang_id)->first();
            if(!empty($barang)){
                $new_qty = $barang->qty + $qty;
                Barang::where('id','=',$barang_id)->update(['qty' => $new_qty]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ]);
            }

            $warehouse = new Warehouse();
            
            $warehouse->qty          = $qty;
            $warehouse->barang_id    = $barang_id;
            $warehouse->user_id      = $user_id;
            $warehouse->supplier_id  = $supplier_id;
            $warehouse->notes        = $notes;

            //add to table supplier_barang
            $supplier_barang = SupplierBarang::where('barang_id', '=', $barang_id)->get();
            
            $fill_supplier_barang = false;

            foreach ($supplier_barang as $item) {
                if($item['supplier_id'] == $supplier_id){
                    $fill_supplier_barang = true;
                }
            };

            if(!$fill_supplier_barang){
                $barang_supplier = new SupplierBarang();
                
                $barang_supplier->supplier_id = $supplier_id;
                $barang_supplier->barang_id = $barang_id;
    
                $barang_supplier->save();
            }
            //add to table barang_user
            $supplier_barang = BarangUser::where('barang_id', '=', $barang_id)->get();
            
            $fill_barang_user = false;

            foreach ($supplier_barang as $item) {
                if($item['user_id'] == $user_id){
                    $fill_barang_user = true;
                }
            };

            if(!$fill_barang_user){
                $barang_user = new BarangUser();
                
                $barang_user->user_id = $user_id;
                $barang_user->barang_id = $barang_id;
    
                $barang_user->save();
            }

            if ($warehouse->save()) {

                $response = ([
                    'id'          => $warehouse->id,
                    'qty'         => $qty,
                    'barang_id'   => $barang_id,
                    'user_id'     => $user_id,
                    'supplier_id' => $supplier_id,
                    'notes'       => $notes,
                ]);

                return ([
                    'success' => true,
                    'message' => 'Berhasil menambah data warehouse',
                    'data'    => $response
                ]);
            } else {
                return ([
                    'success' => false,
                    'message' => 'Gagal menambah data warehouse'
                ]);
            }
        } else {
            return ([
                'success' => false,
                'message' => 'Mohon lengkapi data yang diminta'
            ]);
        }
    }

    public function update_warehouse(Request $request)
    {
        $auth_data = $request->get('auth');
        $user_id = $auth_data[0]['id'];
        $ws_id = $request->input('id');
        $qty = $request->input('qty');
        $barang_id = $request->input('barang_id');
        $supplier_id = $request->input('supplier_id');
        $notes = $request->input('notes');

        if (!empty($qty) && !empty($barang_id) && !empty($user_id) && !empty($supplier_id)) {

            $find_ws = Warehouse::where('id', '=', $ws_id)->get();

            if (count($find_ws) != 1) {
                return ([
                    'success' => false,
                    'message' => 'Data Warehouse tidak ditemukan.'
                ]);
            }

            $update_ws = Warehouse::where('id', '=', $ws_id)->update([
                'qty'         => $qty,
                'barang_id'   => $barang_id,
                'user_id'     => $user_id,
                'supplier_id' => $supplier_id,
                'notes'       => $notes,
            ]);

            if ($update_ws) {
                $response = ([
                    'qty'         => $qty,
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