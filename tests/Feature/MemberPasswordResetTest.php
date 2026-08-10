<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Notifications\MemberResetPassword;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class MemberPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'name' => 'Test Member',
            'phone' => '01712345678',
            'email' => 'member@example.com',
            'password' => 'password123',
            'dob' => '1990-01-01',
            'address' => 'Dhaka',
            'last4' => '5678',
            'unique_card_number' => 'MEM0001_5678',
            'type' => 'membership',
            'status' => 'active',
            'approval_status' => 'approved',
            'expires_at' => now()->addYear(),
        ], $overrides));
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->post(route('frontend.members.register'), [
            'name' => 'New Member',
            'phone' => '01812345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'dob' => '1995-05-05',
            'address' => 'Chittagong',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        $this->makeMember(['email' => 'taken@example.com', 'phone' => '01711111111', 'unique_card_number' => 'MEM0002_1111']);

        $response = $this->post(route('frontend.members.register'), [
            'name' => 'Another Member',
            'phone' => '01822222222',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'dob' => '1995-05-05',
            'address' => 'Chittagong',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_blocked_when_member_has_no_email(): void
    {
        $member = $this->makeMember(['email' => null]);

        $response = $this->from(route('frontend.member.login'))
            ->post(route('frontend.member.login.submit'), [
                'login' => $member->phone,
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('frontend.member.login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest('member');
    }

    public function test_forgot_password_sends_reset_notification(): void
    {
        Notification::fake();
        $member = $this->makeMember();

        $response = $this->post(route('frontend.member.password.email'), [
            'email' => $member->email,
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($member, MemberResetPassword::class);
    }

    public function test_member_can_login_with_email(): void
    {
        $member = $this->makeMember([
            'email' => 'member@example.com',
            'phone' => '01712345678',
        ]);

        $response = $this->post(route('frontend.member.login.submit'), [
            'login' => 'member@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('frontend.member.dashboard'));
        $this->assertAuthenticatedAs($member, 'member');
    }

    public function test_member_can_login_with_phone(): void
    {
        $member = $this->makeMember([
            'email' => 'phoneuser@example.com',
            'phone' => '01712345678',
            'unique_card_number' => 'MEM0009_5678',
        ]);

        $response = $this->post(route('frontend.member.login.submit'), [
            'login' => '01712345678',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('frontend.member.dashboard'));
        $this->assertAuthenticatedAs($member, 'member');
    }

    public function test_forgot_password_rejects_unknown_email(): void
    {
        $response = $this->from(route('frontend.member.password.request'))
            ->post(route('frontend.member.password.email'), [
                'email' => 'nobody@example.com',
            ]);

        $response->assertRedirect(route('frontend.member.password.request'));
        $response->assertSessionHasErrors('email');
    }

    public function test_member_can_reset_password_with_valid_token(): void
    {
        $member = $this->makeMember();

        $broker = Password::broker('members');
        $this->assertInstanceOf(PasswordBroker::class, $broker);
        $token = $broker->createToken($member);

        $response = $this->post(route('frontend.member.password.update'), [
            'token' => $token,
            'email' => $member->email,
            'password' => 'newpassword99',
            'password_confirmation' => 'newpassword99',
        ]);

        $response->assertRedirect(route('frontend.member.login'));
        $this->assertTrue(Hash::check('newpassword99', $member->fresh()->password));
    }
}
