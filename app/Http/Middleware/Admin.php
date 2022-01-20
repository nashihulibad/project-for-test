<?php

namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Closure; 
 
class Admin
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token') ?? $request->query('token');
        if (!$token) {
            return new Response('Token required!', 401);
        }

        [
            $header_base64url,
            $payload_base64url,
            $signature_base64url
        ] = preg_split('/\./', $token);

        $header = $this->base64url_decode($header_base64url);
        $json_header = json_decode($header);
        // dd($header->typ);
        if (!$json_header->alg || $json_header->alg !== 'HS256') {
            return new Response('This type of token is not valid!', 401);
        }

        if (!$json_header->typ || $json_header->typ !== 'JWT') {
            return new Response('This type of token is not valid!', 401);
        }

        $payload = $this->base64url_decode($payload_base64url);
        $json_payload = json_decode($payload);
        if (!$json_payload->sub) {
            return new Response('Token not valid!', 401);
        }

        $verified = $this->verify($signature_base64url, $header_base64url, $payload_base64url, 'Secret');
        //  dd($verified);
        if (!$verified) {
            return new Response('Token sign not valid!', 401);
        }

        [$id, $email] = preg_split('/\:/', $json_payload->sub);
        $user = User::where('id', $id)->where('email', $email)->first();
        if (!$user) {
            return new Response('Token not valid!', 401);
        }
        if($user->role != 'admin'){
            return response()->json(["anda bukan admin"]);
        }
        $request->user = $user;
        return $next($request);
    }

    private function base64url_encode(string $data): string
    {

        $base64 = base64_encode($data); // ubah json string menjadi base64
        $base64url = strtr($base64, '+/', '-_'); // ubah char '+' -> '-' dan '/' -> '_'

        return rtrim($base64url, '='); // menghilangkan '=' pada akhir string
    } 

    private function base64url_decode(string $base64url): string
    {
        $base64 = strtr($base64url, '-_', '+/');
        $json = base64_decode($base64);

        return $json;
    }

    private function sign(string $header_base64url, string $payload_base64url, string $secret): string
    {
        $signature = hash_hmac('sha256', "{$header_base64url}.{$payload_base64url}", $secret, true);
        $signature_base64url = $this->base64url_encode($signature);

        return $signature_base64url;
    }

    private function verify(string $signature_base64url, string $header_base64url, string $payload_base64url, string $secret): bool
    {
        $signature = $this->base64url_decode($signature_base64url);
        $expected_signature = $this->base64url_decode($this->sign($header_base64url, $payload_base64url, $secret));

        return hash_equals($expected_signature, $signature);
    }

}