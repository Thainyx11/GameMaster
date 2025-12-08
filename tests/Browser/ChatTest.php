<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Conversation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChatTest extends DuskTestCase
{
    /**
     * Test que la page de chat est accessible aux utilisateurs connectés.
     */
    public function test_chat_page_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chat')
                ->pause(2000)
                ->assertSee('Bienvenue, aventurier')
                ->assertSee('Nouvelle aventure');
        });
    }

    /**
     * Test que la page de chat nécessite une authentification.
     * Note: Avec le middleware 'verified', ça peut rediriger différemment.
     */
    public function test_chat_requires_authentication(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/chat')
                ->pause(2000)
                ->screenshot('chat-not-auth')
                ->assertPathIsNot('/chat');
        });
    }

    /**
     * Test que le sélecteur de modèle est présent.
     */
    public function test_model_selector_is_present(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chat')
                ->pause(2000)
                ->assertSee('gpt-4o-mini');
        });
    }

    /**
     * Test que les boutons de suggestion sont présents.
     */
    public function test_suggestion_buttons_are_present(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/chat')
                ->pause(2000)
                ->assertSee('Heroic Fantasy')
                ->assertSee('Horreur cosmique')
                ->assertSee('Cyberpunk');
        });
    }

    /**
     * Test qu'une conversation existante s'affiche correctement.
     */
    public function test_existing_conversation_displays(): void
    {
        $user = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => 'Aventure de test',
            'model' => 'openai/gpt-4o-mini',
        ]);

        $this->browse(function (Browser $browser) use ($user, $conversation) {
            $browser->loginAs($user)
                ->visit('/chat/' . $conversation->id)
                ->pause(2000)
                ->assertSee('Aventure de test');
        });
    }
}