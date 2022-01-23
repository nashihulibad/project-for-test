<?php

namespace App\Http\Controllers;
use App\Http\Controllers\MainController; 
use App\Models\User; 
use App\Models\KategoriPemasukan; 
use App\Models\KategoriPengeluaran; 
use App\Models\Transaksi; 
use App\Models\Anggota;
use App\Models\Komunitas;
use App\Models\Iuran;
use App\Models\Message;   
use App\Models\Invitation; 
use App\Models\Notifikasi; 
use App\Models\Tabungan; 
use DB;
use Illuminate\Http\Request;
 
 
class MainController extends Controller
{

    public function manualDebug(Request $request){
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        
        return $this->limitMonthly($user);

        // $date = 16;
        // $month = 01;
        // $year = 2022;
        // $year_month_date = date($year."-".$month."-".$date);
        // $nominal = 20000;
        // $kategori = "Makan";
        // $keterangan = "Makan Mie";
        // $transaksi = Transaksi::create(
        //     [
        //         'user_id' => 1,
        //         'date' => $date,
        //         'month' => $month,
        //         'year' => $year,
        //         'year_month_date' => $year_month_date,
        //         'nominal' => $nominal,
        //         'kategori' => $kategori,
        //         'keterangan' => $keterangan,
        //         'in_or_out' => "out",
        //     ]
        // );
    }
 
    public function getUser(Request $request)
    {
        $token = $request->input('token');
        $user = User::where('token',$token)->where('is_confirm',1)->first();
        if(!empty($user)){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'sukses',
                    'data' => $user
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
    }

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

    public function getKategoriPemasukan()
    {
        $k_pemasukkan = KategoriPemasukan::all();
        if($k_pemasukkan){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'success',
                    'data' => $k_pemasukkan
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

    public function getKategoriPengeluaran()
    {
        $k_pengeluaran = KategoriPengeluaran::all();
        if($k_pengeluaran){
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'success',
                    'data' => $k_pengeluaran
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

    private function limitTransaksi($user,$year_month_date)
    {
        $transaksi = Transaksi::where('user_id',$user->id)
        ->where('year_month_date',$year_month_date)
        ->count();
        
        if($transaksi >= 25){
            return true;
        }
        else{
            return false;
        }
    }




    //Home {Transaksi}
    public function setTransaksi(Request $request)
    {
        $token = $request->input('token');
        $date = $request->input('date') ? $request->input('date') : date('d');
        $month = $request->input('month') ? $request->input('month') : date('m');
        $year = $request->input('year') ? $request->input('year') : date('Y');
        $nominal = $request->input('nominal');
        $kategori = $request->input('kategori');
        $keterangan = $request->input('keterangan');
        $in_or_out = $request->input('in_or_out');
        $year_month_date = date($year."-".$month."-".$date);
        $user = $this->getUserPrivate($token);
        if($user ==  false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            ); 
        } 
        if($nominal < 1000){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'nominal terlalu kecil, minimal Rp 1000!',
                ]
            );
        }
        if($this->limitTransaksi($user,$year_month_date)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Tidak berhasil! Pencatatan sudah lebih dari 25 kali pada hari ini!',
                ]
            );   
        }
        $transaksi = Transaksi::create(
            [
                'user_id' => $user->id,
                'date' => $date,
                'month' => $month,
                'year' => $year,
                'year_month_date' => $year_month_date,
                'nominal' => $nominal,
                'kategori' => $kategori,
                'keterangan' => $keterangan,
                'in_or_out' => $in_or_out,
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

    public function getTransaksiDefault(Request $request)
    {
        $token = $request->input('token');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $month = date('m');
        $year = date('Y');
        $user = $this->getUserPrivate($token);
        if($user){
            $data = Transaksi::where('user_id',$user->id)
            ->where('month',$month)
            ->where('year',$year)
            ->where('is_delete',0)
            ->skip($offset)
            ->take($limit)
            ->get();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'success',
                    'data' => $data
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

    public function getTransaksiById(Request $request)
    {
        $token = $request->input('token');
        $transaksi_id = $request->input('transaksi_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token not valid',
                ]
            ); 
        }
        $data = Transaksi::where('user_id',$user->id)
            ->where('id',$transaksi_id)
            ->get();
        if(empty($data)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found',
                ]
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

    public function getTransaksiByFilter(Request $request)
    {
        $token = $request->input('token');
        $year1 = $request->input('year1') == 0 ? date('Y') : $request->input('year1');
        $year2 = $request->input('year2') == 0 ? date('Y') : $request->input('year2');
        $month1 = $request->input('month1') == 0 ? date('m') : $request->input('month1');
        $month2 = $request->input('month2') == 0 ? date('m') : $request->input('month2');
        $date1 = $request->input('date1');
        $date2 = $request->input('date2');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $in_or_out = $request->input('in_or_out');
        $from = date($year1."-".$month1."-".$date1);
        $to = date($year2."-".$month2."-".$date2);
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token tidak valid'
                ]
            );
        }
        $data = [];
        if($in_or_out == 'all'){
            $data = Transaksi::where('user_id',$user->id)
            ->whereBetween('year_month_date', [$from, $to])
            ->where('is_delete',0)
            ->skip($offset)
            ->take($limit)
            ->get();
        }
        else{
            $data = Transaksi::where('user_id',$user->id)
            ->whereBetween('year_month_date', [$from, $to])
            ->where('is_delete',0)
            ->where('in_or_out',$in_or_out)
            ->skip($offset)
            ->take($limit)
            ->get();
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'success',
                'data' => $data
            ]
        );
    }

    public function getTransaksiMonthly(Request $request)
    {
        $token = $request->input('token');
        $year = $request->input('year') != 0 ? $request->input('year') : date('Y') ;
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token not valid'
                ]
            );
        }
        $total_pemasukkan = 0;
        $total_pengeluaran = 0; 
        for($i = 0; $i<12; $i++)
        {
            $in = Transaksi::where('user_id',$user->id)
                  ->where('year',$year)
                  ->where('month',$i+1)
                  ->where('in_or_out','in')
                  ->where('is_delete',0)
                  ->sum('nominal');
            $out = Transaksi::where('user_id',$user->id)
                  ->where('year',$year)
                  ->where('month',$i+1)
                  ->where('in_or_out','out')
                  ->where('is_delete',0)
                  ->sum('nominal');
            $data[] = array(
                'month' => $i+1,
                'pemasukkan' => $in,
                'pengeluaran' => $out
            );
            $total_pemasukkan += $in;
            $total_pengeluaran += $out;
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'success',
                'data' => [
                    'monthly' => $data,
                    'total_pemasukkan' => $total_pemasukkan,
                    'total_pengeluaran' => $total_pengeluaran
                ]
            ]
        ); 
    }

    public function deleteTransaksi(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('transaksi_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        else{  
            $transaksi = Transaksi::where('id',$id)->where('user_id',$user->id)->first();
            if(!empty($transaksi)){
                if($transaksi->iuran_id != 0 && $transaksi->is_confirm_for_iuran == 1){
                    $transaksi->is_delete = 1;
                    $transaksi->update();
                }
                else{
                    $transaksi->delete();
                }
                return response()->json(
                    [
                        'status' => '2xx',
                        'message' => 'success deleted!',
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
    }

    public function setTransaksiToIuran(Request $request)
    {
        $token = $request->input('token');
        $iuran_id = $request->input('iuran_id');
        $transaksi_id = $request->input('transaksi_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Token salah!',
                ]
            );
        }
        $iuran = Iuran::find($iuran_id);
        if(!empty($iuran)){
            $anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$iuran->komunitas_id)->first();
            if(!empty($anggota)){
                $transaksi = Transaksi::where('id',$transaksi_id)->where('user_id',$user->id)->first();
                if(!empty($transaksi)){
                    $transaksi->iuran_id = $iuran_id;
                    $transaksi->update();
                    return response()->json(
                        [
                            'status' => '2xx',
                            'message' => 'successfully',
                            'data' => $transaksi
                        ]
                    );
                } 
                else{
                    return response()->json(
                        [
                            'status' => '4xx',
                            'message' => 'Not found',
                        ]
                    );
                }
            } 
            else{
                return response()->json(
                    [
                        'status' => '4xx',
                        'message' => 'forbidden',
                    ]
                );
            }
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'Not found',
                ]
            );
        }
    }



    //message
    public function getMessage(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if(!$user){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $messages = Message::where('user_id',$user->id)->get();
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'success',
                'data' => $messages
            ]
        );
    }

    public function deleteMessage(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $deleted_message = Message::where('id',$id)->where('user_id',$user->id)->first();
        if(!empty($deleted_message)){
            $deleted_message->delete();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'success'
                ]
            );
        }
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found'
                ]
            );
        }
    }



    //invitation
    public function leaveKomunitas(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $deleted_anggota = Anggota::where('user_id',$user->id)->where('komunitas_id',$id)->first();
        if(!empty($deleted_anggota)){
            $deleted_anggota->delete();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'Successfully'
                ]
            );
        }   
        else{
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found'
                ]
            );
        }
    }

    public function joinKomunitas(Request $request)
    {
        $token = $request->input('token');
        $invitation_id = $request->input('invitation_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $invitation = Invitation::where('user_id',$user->id)->where('invitation_id',$invitation_id)->first();
        if(!empty($invitation)){
            DB::transaction(
                function() use($invitation,$user) 
                { 
                    $invitation->is_confirm = 1;
                    $invitation->update();
                    Anggota::create(
                        [
                            'user_id' => $user->id,
                            'komunitas_id' => $invitation->komunitas_id,
                            'is_admin' => 0
                        ]
                    );
                }
            );
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'successfully',
                ]
            );
        }
        else{
             return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found',
                ]
            );
        }
    }

    public function invitationFromKomunitas(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $invitation = Invitation::where('user_id',$user->id)->where('is_confirm',0)->get();
        $data = [];
        foreach($invitation as $i){
            $data[] = array(
                'komunitas_id' => $i->komunitas_id,
                'is_confirm' => $i->is_confirm,
                'nama_komunitas' => $i->komunitas->nama
            );
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully',
                'data' => $data
            ]
        );
    }

    public function deleteInvitation(Request $request)
    {
        $token = $request->input('token');
        $invitation_id = $request->input('invitation_id');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $invitation = Invitation::where('user_id',$user->id)->where('id',$invitation_id)->first();
        if(!empty($invitation)){
            $invitation->delete();
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'successfully',
                ]
            );
        }
        else{
             return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found',
                ]
            );
        }
    }


    public function getTabungan(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            );
        }
        $tabungan = Tabungan::where('user_id',$user->id)->first();
        if(empty($tabungan)){
            $tabungan = Tabungan::create(
                [
                    'user_id' => $user->id,
                    'total' => 0,
                    'target' => 0,
                    'deadline' => null,
                    'keterangan' => null
                ]
            );
            return response()->json(
                [
                    'status' => '2xx',
                    'message' => 'successfully',
                    'data' => $tabungan
                ]
            );
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully',
                'data' => $tabungan
            ]
        );
    }

    public function setTabungan(Request $request)
    {
        $token = $request->input('token');
        $nominal = $request->input('nominal');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            );
        }
        $tabungan = Tabungan::where('user_id',$user->id)->first();
        if(empty($tabungan)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found',
                ]
            );
        }
        $tabungan->total += $nominal;
        $tabungan->update();
        return response()->json(
            [
                'status' => '4xx',
                'message' => 'not found',
                'data' => $tabungan
            ]
        );
    }

    public function editTabungan(Request $request)
    {
        $token = $request->input('token');
        $target = $request->input('target');
        $deadline = $request->input('deadline');
        $keterangan = $request->input('keterangan');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            );
        }
        $tabungan = Tabungan::where('user_id',$user->id)->first();
        if(empty($tabungan)){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'not found',
                ]
            );
        }
        $tabungan->target = $target;
        $tabungan->deadline = $deadline;
        $tabungan->keterangan = $keterangan;
        $tabungan->update();
         return response()->json(
            [
                'status' => '4xx',
                'message' => 'not found',
                'data' => $tabungan
            ]
        );
    }
   


    //analisis dan rekomendasi
    public function getNotif(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid'
                ]
            );
        }
        $notifikasi = Notifikasi::where('user_id',$user->id)->first();
        if(empty($notifikasi)){
            Notifikasi::create(
                [
                    'user_id' => $user->id,
                    'year_month_date' => date('Y-m-d')
                ]
            );
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'successfully',
                    'data' => 'Notifikasi kosong'
                ]
            );
        }
        return response()->json(
            [
                'status' => '2xx',
                'message' => 'successfully',
                'data' => $notifikasi
            ]
        );
    }

    public function setNotif(Request $request)
    {
        $token = $request->input('token');
        $user = $this->getUserPrivate($token);

        if($user == false){
            return;
        }
 
        $date_now = date('Y-m-d');
        $notifikasi = Notifikasi::where('user_id',$user->id)->first();
        if($notifikasi->year_month_date == $date_now){
            return null;
        }
        
        $notif_limit = $this->limitMonthly($user);

        $notif1 = $notif_limit[0][0];
        $persentase = $notif_limit[0][1];
        $notif2 = $notif_limit[1];
        $notif3 = $this->getLastTransaksiUser($user);

        $notifikasi->notif1 = $notif1;
        $notifikasi->notif2 = $notif2;
        $notifikasi->notif3 = $notif3;
        $notifikasi->persentase = $persentase;
        $notifikasi->year_month_date = $date_now;
        $notifikasi->update();
    } 

    public function limitMonthly($user)
    {  
        $total_pengeluaran = Transaksi::where('user_id',$user->id)
        ->where('month',date('m'))
        ->where('year', date('Y'))
        ->where('in_or_out','out')
        ->sum('nominal');

        $pengeluaran_daily = Transaksi::where('user_id',$user->id)
        ->where('month',date('m'))
        ->where('year', date('Y'))
        ->where('in_or_out','out')
        ->select([DB::raw("SUM(nominal) as total")])
        ->groupBy('date')
        ->orderBy('total','asc')
        ->get();

     
        $arr_pengeluaran_daily = [];
        $total_pengeluaran = 0;
        foreach($pengeluaran_daily as $p){
           $arr_pengeluaran_daily[] = $p->total/1000;
           $total_pengeluaran += $p->total;
        }

        $messages = [];
        $date = date('d');
        $limit_monthly = $user->limit_monthly/1000;
        $total_pengeluaran = $total_pengeluaran/1000;
        $sisa = $limit_monthly - $total_pengeluaran;
        $minimum_pengeluaran = floor($sisa/(30-$date));
        $pengeluaran_kemarin = Transaksi::where('user_id',$user->id)
        ->where('month',date('m'))
        ->where('year', date('Y'))
        ->where('date',date('d')-1)
        ->where('in_or_out','out')
        ->sum('nominal');
        $pengeluaran_kemarin /= 1000;
        $data = [$total_pengeluaran,$limit_monthly,$date,$sisa,$minimum_pengeluaran,$pengeluaran_kemarin];
        
        $messages[0] = $this->getDailyLimitRecomendation($data); 
        

        $result = $this->removeOutlier($arr_pengeluaran_daily);
        $result = $this->kMeansKlustering($result);
        $result = $this->kMeansKlustering($result);
        $result = $this->kMeansKlustering($result);

        $avg_result = floor(array_sum($result)/count($result));
        if($avg_result > $minimum_pengeluaran){
            $messages[1] = "Pengeluaran anda pada bulan ini sudah melampaui batas normal, silakan lakukan penghematan dan mengontrol pengeluaran anda!";
        }
        else{
            $messages[1] = "Pengeluaran masih tergolong stabil";
        }
        return $messages;
    }

    private function removeOutlier($data){
        $numData = sizeof($data);
        $posQ1 = 0.25 * $numData;
        $posQ2 = 0.5 * $numData;
        $posQ3 = 0.75 * $numData;

        $q1 = (($posQ1 - floor($posQ1)) * $data[floor($posQ1)] + (ceil($posQ1) - $posQ1) * $data[ceil($posQ1)])/2;
        $q2 = ($data[floor($posQ2)] + $data[ceil($posQ2)])/2;
        $q3 = (($posQ3 - floor($posQ3)) * $data[floor($posQ3)] + (ceil($posQ3) - $posQ3) * $data[ceil($posQ3)])/2;
        
        $interQ = $q3 - $q1;

        $minimum = $q1 - (1.5 * $interQ);
        $maksimum = $q3 + (1.5 * $interQ);

        $result = $data;
        foreach($data as $ind => $val){
            if($val <= $minimum or $val >= $maksimum){
                unset($result[$ind]);
            }
        }
        return $result;
    }

    public function getDailyLimitRecomendation($data)
    {
        $total_pengeluaran = $data[0];
        $limit_monthly = $data[1];
        $date = $data[2];
        $sisa = $data[3];
        $minimum_pengeluaran = $data[4];
        $pengeluaran_kemarin = $data[5];

        $normal = floor($limit_monthly/30);
        if($normal == 0){
            return null;
        }
       
        $avg_pengeluaran_daily = floor($total_pengeluaran/$date); 
        $percentage = 100 * ($avg_pengeluaran_daily/$normal);
        $percentage = floor($percentage);
         
        if($percentage > 100){
            $kurangi = $avg_pengeluaran_daily - $minimum_pengeluaran;
            $jumlah_kelebihan = $percentage - 100;
            $message = "Pengeluaran anda melebihi batas, silakan turunkan hingga ".$jumlah_kelebihan."% atau kurangi sekitar ".$kurangi." ribu perhari";
        }
        else if($percentage > 50){
            $jumlah_kekurangan = 100 - $percentage;
            $jumlah_nabung = $normal - $pengeluaran_kemarin;
            $message = "Pengeluaran anda sudah stabil, anda menghemat sebanyak ".$jumlah_kekurangan."%, silakan menabung dengan nominal ".$jumlah_nabung." ribu";
        }
        else{
            $message = "Pengeluaran anda terlalu sedikit, silakan lakukan pencatatan kembali jika ada pengeluaran, sehingga bisa terkontrol";
        }
        return [$message,$percentage];
    } 

    public function kMeansKlustering($data)
    {
        $jumlah_data = count($data);
        $c1 = $data[0];
        $c2 = $data[floor($jumlah_data/2)];
        $cluster1 = [];
        $cluster2 = [];
        $cluster = [];
        $is_stop = false;

        while(!$is_stop)
        {
            $cluster1 = [];
            $cluster2 = [];
            for($i = 0; $i<$jumlah_data; $i++){
                $diff1 = abs($data[$i] - $c1);
                $diff2 = abs($data[$i] - $c2);
                if($diff1 < $diff2){
                    $cluster1[] = $data[$i]; 
                }
                else{
                    $cluster2[] = $data[$i];
                }
            }

            if(count($cluster1) == 0){
                $cluster = $cluster2;
                $is_stop = true;
                continue;
            }
            if(count($cluster2) == 0){
                $cluster = $cluster1;
                $is_stop = true;
                continue;
            }

            $means1 = array_sum($cluster1)/count($cluster1);
            $means2 = array_sum($cluster2)/count($cluster2);

            if(abs($means1 - $c1) <= 1 && abs($means2 - $c2) <= 1){
                if(count($cluster1) > count($cluster2)){
                    $cluster = $cluster1;
                }
                else{
                    $cluster = $cluster2;
                }
                $is_stop = true;
            }

            $c1 = $means1;
            $c2 = $means2;
        }
        return $cluster;
    }

    public function getLastTransaksiUser($user)
    {
        $prev_date = date('Y-m-d', strtotime("-1 days"));
        $prev_transaksi = Transaksi::where('user_id',$user->id)
        ->where('year_month_date',$prev_date)
        ->where('in_or_out','out')
        ->first();
        if(empty($prev_transaksi)){
            return "Anda belum melakukan pencatatan kemarin, silakan isi sekarang jika masih ingat!";
        }
        return null;
    }


    //statistik
    public function getStatistik(Request $request)
    {
        $token = $request->input('token');
        $year = $request->input('year');
        $month = $request->input('month');
        $user = $this->getUserPrivate($token);
        if($user == false){
            return response()->json(
                [
                    'status' => '4xx',
                    'message' => 'token not valid!',
                ]
            );
        }

        $pemasukkan_harian = Transaksi::where('user_id',$user->id)
        ->where('month',$month)
        ->where('year', $year)
        ->where('in_or_out','in')
        ->select([DB::raw("SUM(nominal) as total")])
        ->groupBy('date')
        ->orderBy('total','asc')
        ->get();

        $pengeluaran_harian = Transaksi::where('user_id',$user->id)
        ->where('month',$month)
        ->where('year', $year)
        ->where('in_or_out','out')
        ->select([DB::raw("SUM(nominal) as total")])
        ->groupBy('date')
        ->orderBy('total','asc')
        ->get();

        $pengeluaran_by_kategori = Transaksi::where('user_id',$user->id)
        ->where('month',$month)
        ->where('year', $year)
        ->where('in_or_out','out')
        ->select(['kategori',DB::raw("count(*) as banyak")])
        ->groupBy('kategori')
        ->get();

        return $pengeluaran_by_kategori;


    }

 
}
