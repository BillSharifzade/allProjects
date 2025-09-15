<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CashboxUser;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class SigninController extends Controller
{
    public function index(Request $request) {
        $key = 'login_attempts:' . $request->ip();
        $lockKey = 'login_lock:' . $request->ip();
        $attempts = (int)cache()->get($key, 0);
        $lockedUntil = (int)cache()->get($lockKey, 0);
        return view('signin.index', [
            'attempts' => $attempts,
            'lockedUntil' => $lockedUntil,
        ]);
    }

    public function store(Request $request)
    {
        // Anti-bruteforce: lockout after 5 failed attempts; backoff grows by 5 minutes per lock step
        $key = 'login_attempts:' . $request->ip();
        $lockKey = 'login_lock:' . $request->ip();

        $attempts = (int)cache()->get($key, 0);
        $lockedUntil = (int)cache()->get($lockKey, 0);
        if ($lockedUntil > time()) {
            $wait = $lockedUntil - time();
            return redirect()->back()->withErrors(['Слишком много попыток. Подождите ' . ceil($wait/60) . ' мин.'])->withInput();
        }

        // Honeypot: simple bot trap (hidden field 'website')
        if ($request->filled('website')) {
            return redirect()->back()->withErrors(['Неверные данные'])->withInput();
        }

        $credentials = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ],[
            'login.required' => 'Логин пустой',
            'password.required' => 'Пароль пустой',
        ]);

        // Per-user throttle in addition to IP throttle
        $userKey = 'login_attempts_user:' . mb_strtolower($credentials['login'], 'UTF-8');
        $userLockKey = 'login_lock_user:' . mb_strtolower($credentials['login'], 'UTF-8');
        $userLockedUntil = (int)cache()->get($userLockKey, 0);
        if ($userLockedUntil > time()) {
            $wait = $userLockedUntil - time();
            return redirect()->back()->withErrors(['Слишком много попыток для этого пользователя. Подождите ' . ceil($wait/60) . ' мин.'])->withInput();
        }

        // Removed unsupported 'is_blocked' constraint; column not present in users table

        if (Schema::hasColumn('users', 'is_blocked')) {
            $credentials['is_blocked'] = false;
        }

        if (Auth::attempt($credentials, true)) {
            cache()->forget($key);
            cache()->forget($lockKey);
            cache()->forget($userKey);
            cache()->forget($userLockKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if($user->isCashier() || $user->isCashierAudit()) {
                if(is_null($user->cashboxUser)) {
                    $trashedLink = CashboxUser::withTrashed()
                        ->where('user_id', $user->id)
                        ->first();

                    if($trashedLink) {
                        $trashedLink->restore();
                    }
                }

                if(is_null($user->fresh()->cashboxUser)) {
                    Auth::logout();
                    return redirect()->back()->withErrors([
                        'Кассир не привязан к кассе',
                    ])->withInput();
                }

                return redirect()->route('loans');
            } else if($user->isAdmin()) {
                return redirect()->route('admin-loans');
            } else if(method_exists($user, 'isIncassator') && $user->isIncassator()) {
                return redirect()->route('inc-todeliver');
            } else {
                return redirect()->route('reporter-loans');
            }
        }

        // Fallback: handle failed auth in a privacy-preserving way
        $user = \App\Models\User::where('login', $credentials['login'])->first();

        if(!$user) {
            // Increment throttles but do not reveal whether login exists
            $attempts++;
            cache()->put($key, $attempts, 3600);
            $userAttempts = (int)cache()->increment($userKey, 1);
            cache()->put($userKey, $userAttempts, 3600);
            if ($attempts >= 5) {
                $steps = (int)floor(($attempts - 5) / 5) + 1; // 5,10,15... minutes increasing
                $lockSeconds = max(300, $steps * 300);
                cache()->put($lockKey, time() + $lockSeconds, $lockSeconds);
            }
            if ($userAttempts >= 5) {
                $stepsU = (int)floor(($userAttempts - 5) / 5) + 1;
                $lockSecondsU = max(300, $stepsU * 300);
                cache()->put($userLockKey, time() + $lockSecondsU, $lockSecondsU);
            }
            return redirect()->back()->withErrors(['Логин или пароль не верны'])->withInput();
        }

        if (Schema::hasColumn('users', 'is_blocked') && (int)($user->is_blocked ?? 0) === 1) {
            return redirect()->back()->withErrors([
                'Пользователь заблокирован',
            ])->withInput();
        }

        if(!Hash::check($credentials['password'], $user->password)) {
            $attempts++;
            cache()->put($key, $attempts, 3600);
            $userAttempts = (int)cache()->increment($userKey, 1);
            cache()->put($userKey, $userAttempts, 3600);
            if ($attempts >= 5) {
                $steps = (int)floor(($attempts - 5) / 5) + 1; // 5,10,15... minutes increasing
                $lockSeconds = max(300, $steps * 300);
                cache()->put($lockKey, time() + $lockSeconds, $lockSeconds);
            }
            if ($userAttempts >= 5) {
                $stepsU = (int)floor(($userAttempts - 5) / 5) + 1;
                $lockSecondsU = max(300, $stepsU * 300);
                cache()->put($userLockKey, time() + $lockSecondsU, $lockSecondsU);
            }
            return redirect()->back()->withErrors(['Логин или пароль не верны'])->withInput();
        }

        Auth::login($user, true);
        cache()->forget($key);
        cache()->forget($lockKey);
        $request->session()->regenerate();

        if($user->isCashier() || $user->isCashierAudit()) {
            if(is_null($user->cashboxUser)) {
                $trashedLink = CashboxUser::withTrashed()
                    ->where('user_id', $user->id)
                    ->first();

                if($trashedLink) {
                    $trashedLink->restore();
                }
            }

            if(is_null($user->fresh()->cashboxUser)) {
                Auth::logout();
                return redirect()->back()->withErrors([
                    'Кассир не привязан к кассе',
                ])->withInput();
            }

            return redirect()->route('loans');
        } else if($user->isAdmin()) {
            return redirect()->route('admin-loans');
        } else if(method_exists($user, 'isIncassator') && $user->isIncassator()) {
            return redirect()->route('inc-todeliver');
        } else {
            return redirect()->route('reporter-loans');
        }
    }
}
