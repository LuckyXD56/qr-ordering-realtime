<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check()) return $this->redirectBasedOnRole(Auth::user()->role);
        return view('auth.login');
    }
    public function login(Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return back()->withErrors(['email' => 'Thông tin đăng nhập không chính xác.'])->onlyInput('email');
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
    private function redirectBasedOnRole($role) {
        if ($role === 'admin') return redirect()->route('reports.index');
        if ($role === 'cashier') return redirect()->route('cashier.index');
        if ($role === 'kitchen') return redirect()->route('kitchen.index');
        return redirect('/');
    }
}
