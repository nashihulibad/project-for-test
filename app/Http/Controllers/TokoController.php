<?php
namespace App\Http\Controllers;
use App\Models\User;

use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\TransaksiPenjualan;
use App\Models\TransaksiKredit;  
 
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
     
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }

        $user->toko_toko = $nama;
        $user->deskripsi_toko = $deskripsi;
        $user->update();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully!',
                'data' => [
                    'nama_toko' => $user->nama_toko,
                    'deskripsi_toko' => $user->deskripsi_toko
                ]
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

    public function getAllProduct(Request $request)
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

    public function getProductById(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
        $produk = Produk::where('user_id',$user->id)
        ->where('id',$id)
        ->get();

        if(empty($produk)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found!',
                ]
            );
        }

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $produk
            ]
        );
    }

    public function getProductByFilter(Request $request)
    {
        $token = $request->input('token');
        $kategori = $request->input('kategori');
        $nama = $request->input('nama');
        $order_by = $request->input('order_by'); //asc or desc
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
        ->where('nama','like','%'.$nama.'%')
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
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $kategori = $request->input('token');
        $harga_jual = $request->input('token');
        $harga_beli = $request->input('token');
        $stok = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 

        $produk = Produk::create(
            [
                'user_id' => $user->id,
                'nama' => $nama,
                'deskripsi' => $deskripsi,
                'kategori' => $kategori,
                'harga_jual' => $harga_jual,
                'harga_beli' => $harga_beli,
                'stok' => $stok,
            ]
        );

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Token salah!',
                'data' => $produk
            ]
        );
    }

    public function editProduct(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $kategori = $request->input('token');
        $harga_jual = $request->input('token');
        $harga_beli = $request->input('token');
        $stok = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
        $produk = Produk::where('user_id',$user->id)->where('id',$id)->first();
        if(empty($produk)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found!',
                ]
            );
        }

        $produk->nama = $nama;
        $produk->deskripsi = $deskripsi;
        $produk->kategori = $kategori;
        $produk->harga_jual = $harga_jual;
        $produk->harga_beli = $harga_beli;
        $produk->stok = $stok;
        $produk->update();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Token salah!',
                'data' => $produk
            ]
        );
    }

    public function deleteProduct(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 

        $produk = Produk::where('user_id',$user->id)->where('id',$id)->first();
        if(empty($produk)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found!',
                ]
            );
        }
        $produk->is_delete = 1;
        $produk->update();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfuly!',
            ]
        );
    }

  
    
    public function setTransaksiPenjualan(Request $request)
    {
        $token = $request->input('token');

        $date = $request->input('date') ? $request->input('date') : date('d');
        $month = $request->input('month') ? $request->input('month') : date('m');
        $year = $request->input('year') ? $request->input('year') : date('Y');
        $produk_id = $request->input('produk_id');
        $qty = $request->input('qty');
        $harga = $request->input('harga');
        $total_harga = $request->input('total_harga');
        $is_kredit = $request->input('is_kredit');
        $keterangan = $request->input('keterangan');
 
        $year_month_date = date($year."-".$month."-".$date);

        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 

        $produk = Produk::where('user_id',$user->id)->where('id',$produk_id)->first();
        if(empty($produk)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Produk tidak ada!',
                ]
            );
        }

        $transaksi_penjualan = [
                'user_id' => $user->id,
                'produk_id' => $produk_id,
                'qty' => $qty,
                'harga' => $harga,
                'total_harga' => $total_harga,
                'is_kredit' => $is_kredit,
                'keterangan' => $keterangan,
                'year_month_date' => $year_month_date
            ];
        

        DB::transaction(
            function() use($produk,$transaksi_penjualan) 
            { 
               TransaksiPenjualan::create($transaksi_penjualan);
               $produk->stok = $produk->stok - $transaksi_penjualan->qty;
               $produk->terjual = $produk->terjual + $transaksi_penjualan->qty;
               $produk->update();
            }
        );

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $transaksi_penjualan
            ]
        );
       
    }

    public function getTransaksiPenjualan(Request $request)
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

        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->skip($offset)
        ->take($limit)
        ->get();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $transaksi_penjualan
            ]
        );
        
    }

    public function getTransaksiPenjualanByProduk(Request $request)
    {
        $token = $request->input('token');
        $produk = $request->input('produk');
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

        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->where('produk_id',$produk_id)
        ->skip($offset)
        ->take($limit)
        ->get();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $transaksi_penjualan
            ]
        );
    }

    public function getTransaksiPenjualanByFilter(Request $request)
    {
        $token = $request->input('token');
        $year1 = $request->input('year1') == 0 ? date('Y') : $request->input('year1');
        $year2 = $request->input('year2') == 0 ? date('Y') : $request->input('year2');
        $month1 = $request->input('month1') == 0 ? date('m') : $request->input('month1');
        $month2 = $request->input('month2') == 0 ? date('m') : $request->input('month2');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');
        $from = date($year1."-".$month1."-".$date1);
        $to = date($year2."-".$month2."-".$date2);
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

        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->whereBetween('year_month_date', [$from, $to])
        ->skip($offset)
        ->take($limit)
        ->get();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $transaksi_penjualan
            ]
        );
    }

    public function getTransaksiPenjualanById(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }

        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->where('id',$id)
        ->first();

        if(empty($transaksi_penjualan)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found!',
                ]
            );
        }

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => $transaksi_penjualan
            ]
        );
    }

    public function getTransaksiHasilPenjualan(Request $request)
    {
        $token = $request->input('token');
        $year1 = $request->input('year1') == 0 ? date('Y') : $request->input('year1');
        $year2 = $request->input('year2') == 0 ? date('Y') : $request->input('year2');
        $month1 = $request->input('month1') == 0 ? date('m') : $request->input('month1');
        $month2 = $request->input('month2') == 0 ? date('m') : $request->input('month2');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 

        $rows_hasil_penjualan =  TransaksiPenjualan::where('user_id',$user->id)
        ->whereBetween('year_month_date', [$from, $to])
        ->count();

        if($rows_hasil_penjualan > 10000){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Data terlalu banyak, silakan sederhanakan filter pencarian!',
                ]
            );
        }

        $hasil_penjualan =  TransaksiPenjualan::where('user_id',$user->id)
        ->whereBetween('year_month_date', [$from, $to])
        ->get();

        $arr_hasil_penjualan = [];
        $total_hasil_penjualan = 0;
        $modal = 0;
        foreach($hasil_penjualan as $h){
            $arr_hasil_penjualan[] = array(
                [
                    'produk' => $h->produk->nama,
                    'modal' =>  $h->qty * $h->produk->harga_beli,
                    'qty' => $h->qty,
                    'total_harga' => $h->total_harga,
                ]
            );
            $modal += $h->qty * $h->produk->harga_beli;
            $total_hasil_penjualan += $h->total_harga;
        }

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
                'data' => [
                    'hasil_penjualan' => $arr_hasil_penjualan,
                    'total_hasil_penjualan' => $total_hasil_penjualan,
                    'modal' => $modal
                ]
            ]
        );
    }

    public function deleteTransaksiPenjualan(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }

        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->where('id',$id)
        ->first();

        if(empty($transaksi_penjualan)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Transaksi tidak ada!',
                ]
            );   
        }

        $transaksi_penjualan->is_delete = 1;
        $transaksi_penjualan->update();

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!'
            ]
        ); 
    }




    public function setTransaksiKredit(Request $request)
    {
        $token = $request->input('token');
        $transaksi_penjualan_id = $request->input('transaksi_penjualan_id');
        $banyak_angsuran = $request->input('banyak_angsuran');
        $suku_bunga = $request->input('suku_bunga');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 
 
        $transaksi_penjualan = TransaksiPenjualan::where('user_id',$user->id)
        ->where('is_kredit',1)
        ->where('id',$transaksi_penjualan_id)
        ->first();

        if(empty($transaksi_penjualan)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Transaksi Penjualan kredit tidak ada!',
                ]
            );
        }

        $transaksi_kredit = TransaksiKredit::create(
            [
                'user_id' => $user->id,
                'transaks_penjualan_id' => $transaksi_penjualan_id,
                'banyak_angsuran' => $banyak_angsuran,
                'suku_bunga' => $suku_bunga
            ]
        );

        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Not found!',
                'data' => $transaksi_kredit
            ]
        );

    }

    public function getTransaksiKredit(Request $request)
    {
        $token = $request->input('token');
        $transaksi_penjualan_id = $request->input('transaksi_penjualan_id'); 

        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        } 

        $transaksi_kredit = TransaksiKredit::where('user_id',$user->id)
        ->where('transaksi_penjualan_id',$transaksi_penjualan_id)
        ->first();

        if(empty($transaksi_kredit)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found',
                ]
            );
        }

        $transaksi_penjualan = $transaksi_kredit->transaksi_penjualan;

        $total_pinjaman = $transaksi_penjualan->total_harga;
        $total_pinjaman_dengan_bunga = $total_pinjaman + ($transaksi_kredit->suku_bunga/100) * $total_pinjaman;    
        $total_angsuran = ceil($total_pinjaman_dengan_bunga/$transaksi_kredit->banyak_angsuran);

        $angsuran = [];
        for($i = 1; $i<=$transaksi_kredit->banyak_angsuran; $i++){
            if($i <= $transaksi_kredit->banyak_angsuran_terbayar){
                $angsuran[] = array(
                    [
                        'angsuran' => $total_angsuran,
                        'angsuran_ke' => $i,
                        'status' => 'Sudah Terbayar'
                    ]
                );
            }
            else{
                $angsuran[] = array(
                    [
                        'angsuran' => $total_angsuran,
                        'angsuran_ke' => $i,
                        'status' => 'Sudah Terbayar'
                    ]
                );
            }
        }
    }

    public function bayarAngsuranKredit(Request $request)
    {

    }




    public function statistikPenjualan(Request $request)
    {

    }

    public function rekeomendasiProduct(Request $request)
    {

    }

}