<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
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

    public function createToko(Request $request)
    {
        $token = $request->input('token');
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $logo = $request->input('logo');

        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }

        $is_avail = Toko::where('user_id',$user->id)->first();
        if(!empty($is_avail)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Toko sudah dibuat!',
                ]
            );
        }

        $toko = Toko::create(
            [
                'user_id' => $user->id,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'logo' => $logo
            ]
        );
        if($toko){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'successfully!',
                    'data' => $toko
                ]
            );
        }

    }

    public function editToko(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $logo = $request->input('logo');

        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }

        $toko = Toko::where('user_id',$user->id)->where('id',$id)->first();
        if(empty($toko)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Toko tidak ada!',
                ]
            );
        }

        $toko->nama = $nama;
        $toko->deskripsi = $deskripsi;
        $toko->logo = $logo;
        $toko->update();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully!',
                'data' => $toko
            ]
        );


    }

    public function getKategoriProduct()
    {
        $kategori = KategoriProduk::all();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $kategori
            ]
        );
    }

    public function getProduct(Request $request)
    {
        $token = $request->input('token');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
        $produks = Produk::where('user_id',$user->id)
        ->skip($offset)
        ->take($limit)
        ->get();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $produks
            ]
        );

    }

    public function getProductByFilter(Request $request)
    {
        $token = $request->input('token');
        $kategori = $request->input('kategori');
        $order_by = $request->input('order_by');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
        $produks = Produk::where('user_id',$user->id)
        ->where('kategori',$kategori)
        ->orderBy('terjual',$order_by)
        ->skip($offset)
        ->take($limit)
        ->get();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $produks
            ]
        );
    }

    public function addProduct(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
    }

    public function editProduct(Request $request)
    {

    }

    public function deleteProduct(Request $request)
    {

    }




    public function setTransaksiPenjualan(Request $request)
    {

    }

    public function getTransaksiPenjualan(Request $request)
    {

    }

    public function getTransaksiPenjualanByFilter(Request $request)
    {

    }

    public function transaksiKredit(Request $request)
    {

    }




    
    public function statistikPenjualan(Request $request)
    {

    }

    public function rekeomendasiProduct(Request $request)
    {

    }

}