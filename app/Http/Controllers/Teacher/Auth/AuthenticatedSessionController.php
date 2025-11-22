<?php

namespace App\Http\Controllers\Teacher\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TeacherLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\LogController;

class AuthenticatedSessionController extends Controller
{
    protected $logger;
    /**
     * Display the login view.
     */
    public function __construct(LogController $logger)
    {
        $this->logger = $logger;
    }

    public function create(): View
    {
        return view('teacher.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(TeacherLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        $this->logger->info('teacher.login', ['ip' => $request->ip(), 'user_id' => Auth::guard('teacher')->id()]);

        return redirect()->intended(route('v1.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('teacher')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('v1.login');
    }
}
