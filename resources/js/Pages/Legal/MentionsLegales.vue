<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="min-h-screen bg-gray-900">
        <!-- Navigation simple -->
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
            <h1 class="text-3xl font-bold text-white mb-8">📜 Mentions Légales</h1>
            
            <div class="prose prose-invert max-w-none">
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">1. Éditeur du site</h2>
                    <div class="bg-gray-800 p-4 rounded-lg text-gray-300">
                        <p><strong>Raison sociale :</strong> GameMaster SAS (fictif)</p>
                        <p><strong>Siège social :</strong> 42 rue des Aventuriers, 75001 Paris, France</p>
                        <p><strong>SIRET :</strong> 123 456 789 00012 (fictif)</p>
                        <p><strong>Capital social :</strong> 10 000 €</p>
                        <p><strong>Directeur de la publication :</strong> Jean Dupont</p>
                        <p><strong>Email :</strong> contact@gamemaster.ai</p>
                        <p><strong>Téléphone :</strong> +33 1 23 45 67 89</p>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">2. Hébergeur</h2>
                    <div class="bg-gray-800 p-4 rounded-lg text-gray-300">
                        <p><strong>Nom :</strong> o2switch</p>
                        <p><strong>Adresse :</strong> 222 Boulevard Gustave Flaubert, 63000 Clermont-Ferrand, France</p>
                        <p><strong>Téléphone :</strong> 04 44 44 60 40</p>
                        <p><strong>Site web :</strong> https://www.o2switch.fr</p>
                    </div>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">3. Propriété intellectuelle</h2>
                    <p class="text-gray-300">
                        L'ensemble du contenu de ce site (textes, images, logos, icônes, etc.) est la propriété exclusive de GameMaster SAS ou de ses partenaires. Toute reproduction, représentation, modification ou exploitation non autorisée est interdite et constitue une contrefaçon sanctionnée par le Code de la propriété intellectuelle.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">4. Limitation de responsabilité</h2>
                    <p class="text-gray-300">
                        GameMaster utilise des technologies d'intelligence artificielle pour générer du contenu. Les réponses fournies par l'IA peuvent contenir des erreurs, des inexactitudes ou des "hallucinations". GameMaster ne peut être tenu responsable des décisions prises sur la base des informations générées par l'IA.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">5. Données personnelles</h2>
                    <p class="text-gray-300">
                        Pour plus d'informations sur la collecte et le traitement de vos données personnelles, veuillez consulter notre <Link :href="route('legal.privacy')" class="text-purple-400 hover:underline">Politique de confidentialité</Link>.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">6. Droit applicable</h2>
                    <p class="text-gray-300">
                        Les présentes mentions légales sont soumises au droit français. En cas de litige, les tribunaux français seront seuls compétents.
                    </p>
                </section>
            </div>

            <p class="text-gray-500 text-sm mt-8">Dernière mise à jour : Décembre 2025</p>
        </main>
    </div>
</template>