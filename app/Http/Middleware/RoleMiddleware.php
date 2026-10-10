<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route("login");
        }
        
        $userRole = Auth::user()->role;
        
        // Admin can access everything
        if ($userRole === "admin") {
            return $next($request);
        }
        
        if (!in_array($userRole, $roles)) {
            // Redirect based on role if unauthorized
            if ($userRole === "cashier") return redirect()->route("cashier.index")->with("error", "Bạn không có quyền truy cập trang này.");
            if ($userRole === "kitchen") return redirect()->route("kitchen.index")->with("error", "Bạn không có quyền truy cập trang này.");
            return redirect("/");
        }

        return $next($request);
    }
}

