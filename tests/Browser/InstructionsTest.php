<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InstructionsTest extends DuskTestCase
{
    /**
     * Test que la page d'instructions est accessible.
     */
    public function test_instructions_page_accessible(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/instructions')
                ->pause(2000)
                ->assertSee('Instructions personnalisées')
                ->assertSee('Sauvegarder');
        });
    }

    /**
     * Test qu'on peut sauvegarder des instructions.
     */
    public function test_can_save_instructions(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/instructions')
                ->pause(2000)
                ->screenshot('instructions-page')
                ->type('#instructions', 'Je suis un joueur de D&D 5e expérimenté.')
                ->press('Sauvegarder')
                ->pause(2000)
                ->assertSee('sauvegardées');
        });
    }

    /**
     * Test que les exemples d'instructions sont présents.
     */
    public function test_instruction_examples_present(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/instructions')
                ->pause(2000)
                ->assertSee('Idées')
                ->assertSee('Débutant');
        });
    }
}