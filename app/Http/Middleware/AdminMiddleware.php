<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user_categories = auth()->user()->categories;
        if(count($user_categories) === 0){
            return response()->json([
                "status" => 'unauthorized',
                "message" => 'you are not authorized empty'
            ], 401);
          
        }else{
            foreach ($user_categories as $cat) {
                $cats_id[] = $cat->id;
            }
            if (!in_array(1, $cats_id)) {
                return response()->json([
                    "status" => 'unauthorized',
                    "message" => 'you are not authorized'
                ], 401);
            }
            return $next($request);
        }
        //return $next($request);
     
        // if((int) $request->user()->user_categories_id !== 1){
        //     return response()->json([
        //         "status" => 'unauthorized',
        //         "message" => 'you are not authorized'
        //     ],401);
        // }
       
    }
}
