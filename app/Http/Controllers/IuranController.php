<?php

namespace App\Http\Controllers;
use App\Http\Controllers\MainController; 
use App\Models\User;
use App\Models\Komunitas;
use App\Models\Anggota;
use App\Models\Iuran;
use App\Models\Message;
use App\Models\Invitation; 
use App\Models\Transaksi; 
use Illuminate\Http\Request;
use DB;
 
class IuranController extends Controller
{ 
    
    private function getUserPrivate($token)
    {
        $user = User::where('token',$token)->where('is_confirm',1)->first();
        if(empty($user)){
            return false;
        }
        else{
            return $user;
        }
    }

    private function isAdmin($token,$komunitas_id)
    {
        $admin = $this->getUserPrivate($token);
        if($admin == false){
            return false;
        }
        $anggota = Anggota::where('user_id',$admin->id)->where('komunitas_id',$komunitas_id)->first();
        if(empty($anggota)){
            return false;
        }
        else if($anggota->is_admin == 0){
            return false;
        }
        else{
            return $anggota;
        }
    } 
    
    public function getIsAdmin(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $is_admin = $this->isAdmin($token,$komunitas_id);
        if($is_admin){
            $is_admin = true;
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully!',
                'data' => $is_admin
            ]
        );
    }

    public function findUser(Request $request)
    {
        $token = $request->input('token');
        $username =  $request->input('username');
        $user = $this->getUserPrivate($token);
        if(!$user){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $user = User::where('username',$username)->where('is_confirm',1)->first();
        if(!empty($user)){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfuly!',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                    ]
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found!',
                ]
            );
        }
    }


    //List komunitas
    public function getListKomunitas(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token not valid'
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user->id)->get();
        $data = [];
        foreach($anggota as $a){
            $data[] = array(
                'id_komunitas' => $a->komunitas->id,
                'nama' => $a->komunitas->nama
            );
        } 
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'success',
                'data' => $data
            ]
        );
    }

    public function getDetailKomunitas(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$komunitas_id)->first();
        if(!empty($anggota)){
            $komunitas = Komunitas::find($komunitas_id);
            $is_admin = $anggota->is_admin == 1;
            if(!empty($komunitas)){
                $iuran = Iuran::where('komunitas_id',$komunitas->id)->get();
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'successfully!',
                        'data' => [
                            'komunitas' => $komunitas,
                            'is_admin' => $is_admin,
                            'iuran' => $iuran
                        ]
                    ]
                );
            }
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'not found!'
                    ]
                );
            }
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'forbidden'
                ]
            );
        }
    }

    public function createKomunitas(Request $request)
    {
        $token = $request->input('token');
        $nama =  $request->input('nama');
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

        DB::transaction(
            function() use($nama,$deskripsi,$user) 
            { 
                $komunitas = Komunitas::create(
                    [
                        'nama' => $nama,
                        'deskripsi' => $deskripsi
                    ]
                );    
                
                $anggota = Anggota::create(
                    [
                        'user_id' => $user->id,
                        'komunitas_id' => $komunitas->id,
                        'is_admin' => 1,
                    ]
                ); 
            }
        );
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully!',
            ]
        );
    }

    public function updateKomunitas(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        $komunitas = Komunitas::find($komunitas_id);
        if(empty($komunitas)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found',
                ]
            );
        }
        $komunitas->nama = $nama;
        $komunitas->deskripsi = $deskripsi;
        $komunitas->update();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'Successfully',
            ]
        );
    }

    public function addAdminToKomunitas(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $user_id = $request->input('user_id'); 
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user_id)->where('komunitas_id',$komunitas_id)->first();
        if(!empty($anggota)){
            $anggota->is_admin = 1;
            $anggota->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully',
                ]
            ); 
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Anggota tidak terdaftar di komunitas',
                ]
            );
        }  
    }

    public function deleteAdminFromKomunitas(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $user_id = $request->input('user_id'); 
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user_id)->where('komunitas_id',$komunitas_id)->first();
        if(!empty($anggota)){
            $anggota->is_admin = 0;
            $anggota->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully',
                ]
            ); 
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Anggota tidak terdaftar di komunitas',
                ]
            );
        }
    }


    //Anggota komunitas
    public function getAllAnggotaKomunitas(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        else{
            $data = [];
            $anggota_komunitas = Anggota::where('komunitas_id',$komunitas_id)->get();
            foreach($anggota_komunitas as $ak){
                $data[] = array(
                    'id' => $ak->user->id,
                    'nama' => $ak->user->name
                );
            }
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully',
                    'data' => $data
                ]
            );
        }
    }

    public function inviteAnggota(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $user_id = $request->input('user_id');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        else{
            $if_exist = Invitation::where('user_id',$user_id)
            ->where('komunitas_id',$komunitas_id)
            ->where('is_confirm',0)
            ->first();
            $if_joined = Anggota::where('user_id',$user_id)
            ->where('komunitas_id',$komunitas_id)
            ->first();
            if(!empty($if_exist) || !empty($if_joined)){
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Undangan telah dikirimkan sebelumnya',
                    ]
                );
            }
            $invitation = Invitation::create(
                [
                    'user_id' => $user_id,
                    'komunitas_id' => $komunitas_id
                ]
            );
            if($invitation){
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'Successfully',
                    ]
                );
            }
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'failed!',
                    ]
                );
            }
        } 
    }

    public function deleteAnggota(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $user_id = $request->input('user_id');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        else{
            $deleted_anggota = Anggota::where('user_id',$user_id)->where('komunitas_id',$komunitas_id)->first();
            if(!empty($deleted_anggota)){
                $deleted_anggota->delete();
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'successfully!',
                    ]
                );
            }
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Not found!',
                    ]
                );
            }
        }   
    }

    public function sendMessageToAnggota(Request $request)
    {
        $token = $request->input('token');
        $user_id = $request->input('user_id');
        $message = $request->input('message');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $message =  Message::create(
            [
                'user_id' => $user_id,
                'message' => $message,
                'is_seen' => 0
            ]
        );
        if($message){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' =>'Successfully!',
                    'data' => $message
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'failed!',
                ]
            );
        }
    }


    //Detail Komunitas
    public function createIuran(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        else{
            $iuran = Iuran::create(
                [
                    'komunitas_id' => $komunitas_id,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi
                ]
            );
            if($iuran){
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'Successfully!',
                        'data' => $iuran
                    ]
                );
            }
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Failed',
                    ]
                );
            }
        }
    }

    public function getListIuran(Request $request)
    {
        $token = $request->input('token'); 
        $komunitas_id = $request->input('komunitas_id');  
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$komunitas_id)->first();
        if(empty($anggota)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );  
        }
        $iurans = Iuran::where('komunitas_id',$komunitas_id)->get();
        if($iurans){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully!',
                    'data' => $iurans
                ]
            ); 
        }
    } 
    
    public function getDetailIuran(Request $request)
    {
        $token = $request->input('token');  
        $komunitas_id = $request->input('komunitas_id'); 
        $iuran_id = $request->input('iuran_id'); 
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $iuran = Iuran::where('id',$iuran_id)->where('komunitas_id',$komunitas_id)->first();
        if(!empty($iuran)){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully!',
                    'data' => $iuran
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not Found!',
                ]
            );
        }
    }

    public function updateIuran(Request $request)
    {
        $token = $request->input('token'); 
        $komunitas_id = $request->input('komunitas_id');  
        $iuran_id = $request->input('iuran_id');  
        $nama = $request->input('nama');
        $deskripsi = $request->input('deskripsi');
        $admin = $this->isAdmin($token,$komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
         $iuran = Iuran::where('id',$iuran_id)->where('komunitas_id',$komunitas_id)->first();
        if(!empty($iuran)){
            $iuran->nama = $nama;
            $iuran->deskripsi = $deskripsi;
            $iuran->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully!',
                    'data' => $iuran
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found!',
                ]
            );
        }
    }


    //Detail iuran
    public function getTransaksiIuranDefault(Request $request)
    {
        $token = $request->input('token');
        $iuran_id = $request->input('iuran_id');
        $komunitas_id = $request->input('komunitas_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $month = date('m');
        $year = date('Y');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$komunitas_id)->first();
        if(empty($anggota)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        $iurans = Transaksi::where('month',$month)
            ->where('year',$year)
            ->where('iuran_id',$iuran_id)
            ->skip($offset)
            ->take($limit)
            ->get();

        $jumlah = Transaksi::where('month',$month)
            ->where('year',$year)
            ->where('iuran_id',$iuran_id)
            ->where('is_confirm_for_iuran',1)
            ->sum('nominal'); 
      
        if($iurans){
            $data = [];
            foreach($iurans as $i){
                $data = array(
                    'id' => $i->id,
                    'iuran_id' => $i->iuran_id,
                    'user_id' => $i->user_id,
                    'user_nama' => $i->user->name,
                    'user_username' => $i->user->username,
                    'date' => $i->date,
                    'month' => $i->month,
                    'year' => $i->year,
                    'nominal' => $i->nominal,
                    'keterangan' => $i->keterangan,
                );
            } 
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'success',
                    'data' => [
                        'data' => $data,
                        'jumlah' => $jumlah
                    ]
                ]
            ); 
        } 
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Failed!',
                ]
            );
        }   
    }
    
    public function setTransaksiIuran(Request $request)
    {
        $token = $request->input('token');
        $iuran_id = $request->input('iuran_id');
        $komunitas_id = $request->input('komunitas_id');
        $user_id = $request->input('user_id');
        $date = $request->input('date') ? $request->input('date') : date('d');
        $month = $request->input('month') ? $request->input('month') : date('m');
        $year = $request->input('year') ? $request->input('year') : date('Y');
        $nominal = $request->input('nominal');
        $kategori = $request->input('kategori');
        $keterangan = $request->input('keterangan');
        $year_month_date = date($year."-".$month."-".$date);
        $anggota = Anggota::where('user_id',$user_id)->where('komunitas_id',$komunitas_id)->first();
        $admin = $this->isAdmin($token,$komunitas_id);
        if($nominal < 1000){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'nominal terlalu kecil!',
                ]
            );
        }
        if($admin == false || empty($anggota)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        else{
            $transaksi = Transaksi::create(
                [
                    'user_id' => $user_id,
                    'date' => $date,
                    'month' => $month,
                    'year' => $year,
                    'year_month_date' => $year_month_date,
                    'nominal' => $nominal,
                    'kategori' => $kategori,
                    'keterangan' => $keterangan,
                    'in_or_out' => 'out',
                    'iuran_id' => $iuran_id,
                    'is_confirm_for_iuran' => 1
                ]
            );
            if($transaksi){
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'success',
                        'data' => $transaksi
                    ]
                ); 
            }
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'failed',
                    ]
                ); 
            }
        }
    }

    public function getTransaksiIuranByFilter(Request $request)
    {
        $token = $request->input('token');
        $komunitas_id = $request->input('komunitas_id');
        $iuran_id = $request->input('iuran_id');
        $year1 = $request->input('year1') == 0 ? date('Y') : $request->input('year1');
        $year2 = $request->input('year2') == 0 ? date('Y') : $request->input('year2');
        $month1 = $request->input('month1') == 0 ? date('m') : $request->input('month1');
        $month2 = $request->input('month2') == 0 ? date('m') : $request->input('month2');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $from = date($year1."-".$month1."-".$date1);
        $to = date($year2."-".$month2."-".$date2);
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$komunitas_id)->first();
        if(empty($anggota)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden!',
                ]
            );
        }
        else{
            $iurans = Transaksi::where('iuran_id',$iuran_id)
                ->whereBetween('year_month_date', [$from, $to])
                ->skip($offset)
                ->take($limit)
                ->get();

            $jumlah = Transaksi::where('iuran_id',$iuran_id)
                ->whereBetween('year_month_date', [$from, $to])
                ->where('is_confirm_for_iuran',1)
                ->sum('nominal');    
           
            if($iurans){
                $data = [];
                foreach($iurans as $i){
                    $data[] = array(
                        'id' => $i->id,
                        'iuran_id' => $i->iuran_id,
                        'user_id' => $i->user_id,
                        'user_name' => $i->user->name,
                        'user_username' => $i->user->username,
                        'date' => $i->date,
                        'month' => $i->month,
                        'year' => $i->year,
                        'nominal' => $i->nominal,
                        'keterangan' => $i->keterangan,
                    );
                } 
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'success',
                        'data' => [
                            'data' => $data,
                            'jumlah' => $jumlah
                        ],
                    ]
                ); 
            } 
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Failed!',
                    ]
                );
            }     
        }
    }

    public function confirmTransaksiIuran(Request $request)
    {
        $token = $request->input('token');
        $iuran_id = $request->input('iuran_id');
        $transaksi_id = $request->input('transaksi_id');
        $iuran = Iuran::find($iuran_id);
        if(empty($iuran)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found!',
                ]
            );
        }
        $admin = $this->isAdmin($token,$iuran->komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        else{
            $transaksi = Transaksi::where('id',$transaksi_id)->where('iuran_id',$iuran_id)->first();
            if(empty($transaksi)){
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Not found!',
                    ]
                );
            }
            $transaksi->is_confirm_for_iuran = 1;
            $transaksi->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully!',
                    'data' => $transaksi
                ]
            );
        }
    }

    public function deleteTransaksiIuran(Request $request)
    {
        $token = $request->input('token');
        $iuran_id = $request->input('iuran_id');
        $transaksi_id = $request->input('transaksi_id');
        $iuran = Iuran::find($iuran_id);
        if(empty($iuran)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found!',
                ]
            );
        }
        $admin = $this->isAdmin($token,$iuran->komunitas_id);
        if($admin == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Forbidden',
                ]
            );
        }
        else{
            $transaksi = Transaksi::where('id',$transaksi_id)->where('iuran_id',$iuran_id)->first();
            if(empty($transaksi)){
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'Not found!',
                    ]
                );
            }
            $transaksi->iuran_id = 0;
            $transaksi->update();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully!',
                ]
            );
        }
    }

   

}
