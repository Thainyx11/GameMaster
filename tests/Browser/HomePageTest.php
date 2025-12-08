<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    /**
     * Test que la page d'accueil s'affiche correctement.
     */
    public function test_home_page_displays_correctly(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(1000)
                ->assertSee('GameMaster')
                ->assertSee('Maître de Jeu')
                ->assertSee('Commencer l\'aventure')
                ->assertSee('Connexion')
                ->assertSee('S\'inscrire');
        });
    }

    /**
     * Test que les liens de navigation fonctionnent.
     */
    public function test_navigation_links_work(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(500)
                ->clickLink('Connexion')
                ->pause(500)
                ->assertPathIs('/login')
                ->assertSee('Email');
        });
    }

    /**
     * Test que les pages légales sont accessibles.
     */
    public function test_legal_pages_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/mentions-legales')
                ->pause(500)
                ->assertSee('Mentions Légales')
                ->visit('/politique-confidentialite')
                ->pause(500)
                ->assertSee('Politique de Confidentialité')
                ->visit('/cookies')
                ->pause(500)
                ->assertSee('Cookies');
        });
    }
}