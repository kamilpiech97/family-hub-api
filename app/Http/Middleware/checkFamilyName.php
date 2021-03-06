<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class CheckFamilyName
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $family = DB::table('families')->where('name', '=', $request->name )->first();
        if($family){
            $family = DB::table('families')->orderBy('id', 'desc')->first();
            $request->merge(['name' => $request->name.'_'.$family->id]);
            return $next($request);
        }else{
            return $next($request);
        }
    }
}
