<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthenticationTest extends DuskTestCase
{
    /**
     * Test qu'un utilisateur peut s'inscrire.
     */
    public function test_user_can_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->pause(2000)
                ->waitFor('#name', 5)
                ->type('#name', 'Test User')
                ->type('#email', 'test' . time() . '@example.com')
                ->type('#password', 'password123')
                ->type('#password_confirmation', 'password123')
                ->press('REGISTER')
                ->pause(3000)
                ->assertPathIs('/chat');
        });
    }

    /**
     * Test qu'un utilisateur peut se connecter.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'logintest' . time() . '@test.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                ->visit('/login')
                ->pause(3000)
                ->waitFor('#email', 5)
                ->type('#email', $user->email)
                ->type('#password', 'password123')
                ->press('LOG IN')
                ->pause(3000)
                ->assertPathIs('/chat');
        });
    }

    /**
     * Test qu'un utilisateur connecté peut accéder au chat.
     */
    public function test_authenticated_user_can_access_chat(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chat')
                ->pause(2000)
                ->assertSee('Bienvenue, aventurier')
                ->assertSee($user->name);
        });
    }

    /**
     * Test qu'un utilisateur connecté voit le bouton déconnexion.
     */
    public function test_authenticated_user_sees_logout(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chat')
                ->pause(2000)
                ->assertSee('Déconnexion');
        });
    }
}