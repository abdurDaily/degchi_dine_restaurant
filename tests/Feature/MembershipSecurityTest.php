<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MembershipSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Welcome SMS is outside the scope of these tests; prevent real calls.
        Http::fake();
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();

        parent::tearDown();
    }

    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rahim Uddin',
            'phone' => '01712345678',
            'email' => 'member@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'dob' => '1990-01-01',
            'address' => 'Flat 4A, Green Road, Dhaka 1205.',
            'terms' => '1',
        ], $overrides);
    }

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

    private function makeAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'user_number' => User::generateUserNumber(),
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'status' => false,
        ], $overrides));
    }

    private function postMemberLogin(array $data)
    {
        return $this->postJson(
            route('frontend.member.login.submit'),
            $data,
            ['X-Requested-With' => 'XMLHttpRequest']
        );
    }

    public function test_name_of_exactly_fourteen_characters_is_accepted(): void
    {
        $name = str_repeat('a', 14);

        $response = $this->post(route('frontend.members.register'), $this->applicationData([
            'name' => $name,
        ]));

        $response->assertRedirect(route('frontend.member.dashboard'));
        $this->assertDatabaseHas('members', ['name' => $name]);
    }

    public function test_name_of_fifteen_characters_is_rejected(): void
    {
        $response = $this->post(route('frontend.members.register'), $this->applicationData([
            'name' => str_repeat('a', 15),
        ]));

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('members', 0);
    }

    public function test_normal_bangla_address_is_accepted(): void
    {
        $address = 'বাড়ী নম্বর ১২, গ্রিন রোড, ঢাকা ১২০৫।';

        $response = $this->post(route('frontend.members.register'), $this->applicationData([
            'address' => $address,
        ]));

        $response->assertRedirect(route('frontend.member.dashboard'));
        $this->assertDatabaseHas('members', ['address' => $address]);
    }

    public function test_normal_english_address_is_accepted(): void
    {
        $address = 'Flat 4A, St. John Road, Apt. 12, Dhaka 1205.';

        $response = $this->post(route('frontend.members.register'), $this->applicationData([
            'address' => $address,
        ]));

        $response->assertRedirect(route('frontend.member.dashboard'));
        $this->assertDatabaseHas('members', ['address' => $address]);
    }

    public function test_addresses_with_links_or_scripts_are_rejected(): void
    {
        $blocked = [
            'https://example.com',
            'http://example.com',
            'www.example.com',
            'example.com',
            'sub.example.com',
            'bit.ly/example',
            'tinyurl.com/example',
            '192.168.1.10/login',
            'ftp://files.example.com',
            '<script>alert(1)</script>',
            'javascript:alert(1)',
        ];

        foreach ($blocked as $link) {
            Cache::flush();

            $response = $this->post(route('frontend.members.register'), $this->applicationData([
                'address' => 'House 12, '.$link.', Dhaka',
            ]));

            $response->assertSessionHasErrors('address');
        }

        $this->assertDatabaseCount('members', 0);
    }

    public function test_address_over_250_characters_is_rejected(): void
    {
        $response = $this->post(route('frontend.members.register'), $this->applicationData([
            'address' => str_repeat('a', 251),
        ]));

        $response->assertSessionHasErrors('address');
        $this->assertDatabaseCount('members', 0);
    }

    public function test_sixth_failed_member_login_attempt_is_throttled(): void
    {
        $member = $this->makeMember();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postMemberLogin([
                'login' => $member->phone,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $response = $this->postMemberLogin([
            'login' => $member->phone,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
        $response->assertJsonValidationErrors('login');
        $this->assertGuest('member');
    }

    public function test_sixth_failed_admin_login_attempt_is_throttled(): void
    {
        $admin = $this->makeAdmin();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $response = $this->postJson(route('login'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
        $response->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_registration_is_limited_to_three_requests_per_minute(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->postJson(route('register'), ['name' => ''])->assertStatus(422);
        }

        $response = $this->postJson(route('register'), ['name' => '']);

        $response->assertStatus(429);
        $response->assertJsonPath('message', 'Too many registration attempts. Please wait a minute and try again.');
    }

    public function test_membership_application_post_is_limited_to_three_requests_per_minute(): void
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->postJson(route('frontend.members.register'), $this->applicationData([
                'name' => '',
            ]))->assertStatus(422);
        }

        $response = $this->postJson(route('frontend.members.register'), $this->applicationData([
            'name' => '',
        ]));

        $response->assertStatus(429);
        $response->assertJsonPath('message', 'Too many membership applications. Please wait a minute and try again.');

        // The application form page itself must remain available.
        $this->get(route('frontend.card.apply'))->assertOk();
    }

    public function test_successful_member_login_resets_failed_attempt_throttling(): void
    {
        $member = $this->makeMember();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->postMemberLogin([
                'login' => $member->phone,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->post(route('frontend.member.login.submit'), [
            'login' => $member->phone,
            'password' => 'password123',
        ])->assertRedirect(route('frontend.member.dashboard'));

        $this->assertAuthenticatedAs($member, 'member');
        Auth::guard('member')->logout();

        // A full fresh batch of five failures must be allowed again.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postMemberLogin([
                'login' => $member->phone,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }
    }

    public function test_successful_admin_login_resets_failed_attempt_throttling(): void
    {
        $admin = $this->makeAdmin();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->postJson(route('login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('pending-approval'));

        $this->assertAuthenticatedAs($admin);
        Auth::logout();

        // A full fresh batch of five failures must be allowed again.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }
    }
}
