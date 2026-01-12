<?php

namespace App\Http\Controllers\Teacher\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TeacherLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\LogController;
use Illuminate\Support\Carbon;

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
        // determine timezone: prefer client-provided timezone (input/header/cookie/session)
        $clientTz = $request->input('timezone')
            ?? $request->header('X-User-Timezone')
            ?? $request->cookie('user_timezone')
            ?? $request->session()->get('teacher_timezone');

        $tz = $clientTz ?: config('app.timezone');

        // record latest_login on teacher record and persist timezone
        $teacher = Auth::guard('teacher')->user();
        if ($teacher) {
            $tdata = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
            $tdata['latest_login'] = Carbon::now($tz)->format('Y-m-d H:i:s');
            $tdata['timezone'] = $tz;
            $teacher->data = $tdata;
            $teacher->save();

            // remember timezone in session for logout
            $request->session()->put('teacher_timezone', $tz);
        }

        $this->logger->info('teacher.login', ['ip' => $request->ip(), 'user_id' => Auth::guard('teacher')->id()]);

        return redirect()->intended(route('v1.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // determine timezone for logout timestamp: prefer request/session/teacher data
        $teacher = Auth::guard('teacher')->user();
        $tz = null;
        if ($teacher) {
            $tdata = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
            $tz = $tdata['timezone'] ?? null;
        } elseif ($request->has('timezone')) {
            $tz = $request->input('timezone');
        } elseif ($request->session()->has('teacher_timezone')) {
            $tz = $request->session()->get('teacher_timezone');
        }

        $tz = $tz ?: config('app.timezone');

        // record latest_logout before logging out
        if ($teacher) {
            $tdata = is_array($teacher->data) ? $teacher->data : json_decode($teacher->data, true);
            $tdata['latest_logout'] = Carbon::now($tz)->format('Y-m-d H:i:s');
            $teacher->data = $tdata;
            $teacher->save();
        }

        Auth::guard('teacher')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('v1.login');
    }
}
