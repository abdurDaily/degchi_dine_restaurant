<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class MemberPasswordController extends Controller
{
    public function showForgotForm()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('frontend.member.dashboard');
        }

        return view('frontend.member.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->email));
        $member = Member::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $member || blank($member->email)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'No member account found with this email, or this account has no email on file. Please use the email from your membership registration, or contact support to update your email.',
                ]);
        }

        $status = Password::broker('members')->sendResetLink([
            'email' => $member->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'We have emailed your password reset link. Please check your inbox (and spam folder).');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Please wait a minute before requesting another reset link.']);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('frontend.member.dashboard');
        }

        return view('frontend.member.reset-password', [
            'token' => $token,
            'email' => $request->query('email', old('email')),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker('members')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Member $member, string $password) {
                $member->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($member));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('frontend.member.login')
                ->with('success', 'Your password has been reset. You can sign in with your new password.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
