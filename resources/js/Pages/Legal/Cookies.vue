<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-gray-900">
        <nav class="bg-gray-800 border-b border-gray-700 py-4 px-4">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <Link :href="route('home')" class="flex items-center">
                    <span class="text-2xl mr-2">🎲</span>
                    <span class="text-xl font-bold text-purple-400">GameMaster</span>
                </Link>
                <Link :href="route('home')" class="text-gray-400 hover:text-white">
                    ← Retour
                </Link>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto py-12 px-4">
            <h1 class="text-3xl font-bold text-white mb-8">🍪 Politique des Cookies</h1>
            
            <div class="prose prose-invert max-w-none text-gray-300">
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">1. Qu'est-ce qu'un cookie ?</h2>
                    <p>
                        Un cookie est un petit fichier texte stocké sur votre appareil lors de votre visite sur notre site. Il permet de mémoriser vos préférences et d'améliorer votre expérience utilisateur.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">2. Cookies utilisés</h2>
                    
                    <h3 class="text-lg font-medium text-white mt-4 mb-2">Cookies essentiels (obligatoires)</h3>
                    <div class="bg-gray-800 p-4 rounded-lg mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-2">Nom</th>
                                    <th class="text-left py-2">Finalité</th>
                                    <th class="text-left py-2">Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-700">
                                    <td class="py-2">session</td>
                                    <td class="py-2">Authentification</td>
                                    <td class="py-2">2 heures</td>
                                </tr>
                                <tr class="border-b border-gray-700">
                                    <td class="py-2">XSRF-TOKEN</td>
                                    <td class="py-2">Sécurité CSRF</td>
                                    <td class="py-2">2 heures</td>
                                </tr>
                                <tr>
                                    <td class="py-2">cookie_consent</td>
                                    <td class="py-2">Mémoriser votre choix</td>
                                    <td class="py-2">1 an</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-medium text-white mt-4 mb-2">Cookies analytiques (optionnels)</h3>
                    <p>
                        Nous n'utilisons actuellement pas de cookies analytiques tiers. Si cela change, nous mettrons à jour cette politique et vous demanderons votre consentement.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">3. Gérer vos préférences</h2>
                    <p>
                        Vous pouvez à tout moment modifier vos préférences de cookies :
                    </p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li>Via le bandeau de consentement (en cliquant sur "Gérer les cookies" en bas de page)</li>
                        <li>Via les paramètres de votre navigateur</li>
                    </ul>
                    <p class="mt-4">
                        <strong>Note :</strong> La désactivation des cookies essentiels peut empêcher le bon fonctionnement du site.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">4. Configurer votre navigateur</h2>
                    <ul class="list-disc pl-6 space-y-1">
                        <li><a href="https://support.google.com/chrome/answer/95647" class="text-purple-400 hover:underline">Google Chrome</a></li>
                        <li><a href="https://support.mozilla.org/fr/kb/activer-desactiver-cookies" class="text-purple-400 hover:underline">Mozilla Firefox</a></li>
                        <li><a href="https://support.apple.com/fr-fr/guide/safari/sfri11471/mac" class="text-purple-400 hover:underline">Safari</a></li>
                        <li><a href="https://support.microsoft.com/fr-fr/microsoft-edge/supprimer-les-cookies-dans-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" class="text-purple-400 hover:underline">Microsoft Edge</a></li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">5. Contact</h2>
                    <p>
                        Pour toute question sur notre utilisation des cookies :<br>
                        📧 privacy@gamemaster.ai
                    </p>
                </section>
            </div>

            <p class="text-gray-500 text-sm mt-8">Dernière mise à jour : Décembre 2025</p>
        </main>
    </div>
</template>