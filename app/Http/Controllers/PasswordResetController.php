<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * "Forgot password" flow for marketplace accounts (buyer + seller).
 *
 * Riders, logistics companies and the super admin have their own separate
 * portals, so this flow only ever touches buyer/seller accounts.
 */
class PasswordResetController extends Controller
{
    private const ROLES = ['buyer', 'seller'];

    // Step 1: the "enter your email" page
    public function create()
    {
        return view('auth.forgot-password');
    }

    // Step 2: send the reset link
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            ['email' => $request->input('email'), 'role' => self::ROLES],
            function ($user, $token) {
                // Sends Laravel's normal reset email. With MAIL_MAILER=log (see .env)
                // the email is written to storage/logs/laravel.log instead of sent.
                $user->sendPasswordResetNotification($token);

                // The email in the log file is line-wrapped, which makes the link hard
                // to copy. On a local machine we also log the plain link on ONE line.
                if (app()->isLocal()) {
                    $link = url(route('password.reset', [
                        'token' => $token,
                        'email' => $user->email,
                    ], false));

                    Log::info('[LubosMart] Password reset link for ' . $user->email . ': ' . $link);
                }
            }
        );

        if ($status === PasswordBroker::RESET_THROTTLED) {
            return back()
                ->withErrors(['email' => 'Please wait a minute before requesting another reset link.'])
                ->onlyInput('email');
        }

        // Same message whether or not the email exists, so nobody can use this
        // form to find out which emails are registered.
        return back()->with(
            'status',
            'If that email belongs to a LubosMart account, a password reset link is on its way.'
        );
    }

    // Step 3: the "choose a new password" page (opened from the link in the email)
    public function edit(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // Step 4: save the new password
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token') + ['role' => self::ROLES],
            function ($user, $password) {
                // The User model's 'hashed' cast encrypts the password automatically.
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Your password has been reset. You can now sign in.');
        }

        return back()
            ->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.'])
            ->onlyInput('email');
    }
}
