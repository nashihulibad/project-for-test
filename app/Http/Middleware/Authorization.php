<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
 
class Authorization
{ 
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('token');
        $user = User::where('email', $email)->first();
        if (!$user) {
             return response()->json([
                'success' => false, 
                'message' => "sorryyy",
            ], 401); 
        }
        $request->user = $user;
        return $next($request);
    }

   
}