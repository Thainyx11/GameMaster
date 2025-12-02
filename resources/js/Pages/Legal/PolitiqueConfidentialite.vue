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
            <h1 class="text-3xl font-bold text-white mb-8">🔒 Politique de Confidentialité</h1>
            
            <div class="prose prose-invert max-w-none text-gray-300">
                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">1. Introduction</h2>
                    <p>
                        GameMaster SAS ("nous", "notre") s'engage à protéger la vie privée des utilisateurs de notre service. Cette politique explique comment nous collectons, utilisons et protégeons vos données personnelles conformément au Règlement Général sur la Protection des Données (RGPD).
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">2. Données collectées</h2>
                    <p>Nous collectons les données suivantes :</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li><strong>Données d'inscription :</strong> nom, adresse email, mot de passe (hashé)</li>
                        <li><strong>Données d'utilisation :</strong> conversations, messages, préférences</li>
                        <li><strong>Données techniques :</strong> adresse IP, type de navigateur, cookies</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">3. Finalités du traitement</h2>
                    <p>Vos données sont utilisées pour :</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li>Fournir et améliorer notre service de chat IA</li>
                        <li>Gérer votre compte utilisateur</li>
                        <li>Sauvegarder l'historique de vos conversations</li>
                        <li>Assurer la sécurité du service</li>
                        <li>Respecter nos obligations légales</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">4. Base légale</h2>
                    <p>Le traitement de vos données repose sur :</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li><strong>Exécution du contrat :</strong> pour fournir le service</li>
                        <li><strong>Consentement :</strong> pour les cookies non essentiels</li>
                        <li><strong>Intérêt légitime :</strong> pour améliorer nos services</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">5. Durée de conservation</h2>
                    <ul class="list-disc pl-6 space-y-1">
                        <li><strong>Données de compte :</strong> jusqu'à suppression du compte + 1 an</li>
                        <li><strong>Conversations :</strong> jusqu'à suppression par l'utilisateur</li>
                        <li><strong>Logs techniques :</strong> 12 mois</li>
                    </ul>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">6. Transferts hors UE</h2>
                    <p>
                        Notre service utilise des API de modèles de langage (OpenRouter) dont les serveurs peuvent être situés hors de l'Union Européenne. Ces transferts sont encadrés par des clauses contractuelles types approuvées par la Commission européenne.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">7. Vos droits</h2>
                    <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li><strong>Accès :</strong> obtenir une copie de vos données</li>
                        <li><strong>Rectification :</strong> corriger vos données inexactes</li>
                        <li><strong>Effacement :</strong> supprimer vos données ("droit à l'oubli")</li>
                        <li><strong>Portabilité :</strong> recevoir vos données dans un format structuré</li>
                        <li><strong>Opposition :</strong> vous opposer au traitement</li>
                        <li><strong>Limitation :</strong> restreindre le traitement</li>
                    </ul>
                    <p class="mt-4">
                        Pour exercer ces droits, contactez-nous à : <strong>privacy@gamemaster.ai</strong>
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">8. Sécurité</h2>
                    <p>
                        Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données : chiffrement SSL/TLS, hashage des mots de passe, accès restreint aux données, sauvegardes régulières.
                    </p>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">9. Réclamation</h2>
                    <p>
                        Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la CNIL (Commission Nationale de l'Informatique et des Libertés) : <a href="https://www.cnil.fr" class="text-purple-400 hover:underline">www.cnil.fr</a>
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-purple-400 mb-4">10. Contact</h2>
                    <p>
                        Pour toute question relative à cette politique :<br>
                        📧 privacy@gamemaster.ai<br>
                        📍 GameMaster SAS, 42 rue des Aventuriers, 75001 Paris
                    </p>
                </section>
            </div>

            <p class="text-gray-500 text-sm mt-8">Dernière mise à jour : Décembre 2025</p>
        </main>
    </div>
</template>