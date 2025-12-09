<script setup lang="ts">
import { onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import CookieBanner from '@/Components/CookieBanner.vue'
import HeadMeta from '@/Components/Head.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import { useTheme } from '@/Composables/useTheme'

const page = usePage()
const user = page.props.auth?.user

const { initTheme } = useTheme()

onMounted(() => {
    initTheme()
})
</script>

<template>
    <HeadMeta
        title="GameMaster - Votre Maître de Jeu IA Personnel"
        description="Vivez des aventures épiques avec GameMaster. Heroic fantasy, horreur cosmique, cyberpunk... Votre imagination est la seule limite. Essayez gratuitement !"
    />
    <div class="min-h-screen bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-gray-800/50 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">🎲</span>
                        <span class="text-xl font-bold text-purple-400">GameMaster</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <ThemeToggle />
                        <template v-if="user">
                            <Link :href="route('chat.index')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Accéder au chat
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-gray-300 hover:text-white transition-colors">
                                Connexion
                            </Link>
                            <Link :href="route('register')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                S'inscrire
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="py-20 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    <span class="text-white">Votre </span>
                    <span class="text-purple-400">Maître de Jeu</span>
                    <span class="text-white"> personnel</span>
                </h1>
                <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">
                    🎲 Vivez des aventures épiques avec une IA narrative. Heroic fantasy, horreur cosmique, cyberpunk... Votre imagination est la seule limite.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="user ? route('chat.index') : route('register')"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-xl text-lg font-semibold transition-colors"
                    >
                        ✨ Commencer l'aventure
                    </Link>
                    <a href="#features" class="border border-gray-600 hover:border-gray-500 text-gray-300 px-8 py-4 rounded-xl text-lg font-semibold transition-colors">
                        En savoir plus
                    </a>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-20 px-4 bg-gray-800/30">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center text-white mb-12">🎭 Fonctionnalités</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">🐉</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Univers variés</h3>
                        <p class="text-gray-400">Fantasy, SF, horreur, contemporain... Explorez tous les genres de jeu de rôle.</p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">⚡</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Réponses en temps réel</h3>
                        <p class="text-gray-400">Streaming des réponses pour une expérience fluide et immersive.</p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">💾</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Historique sauvegardé</h3>
                        <p class="text-gray-400">Reprenez vos aventures là où vous les avez laissées.</p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">🤖</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Choix du modèle IA</h3>
                        <p class="text-gray-400">Sélectionnez parmi plusieurs modèles de langage selon vos préférences.</p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">⚙️</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Personnalisation</h3>
                        <p class="text-gray-400">Configurez le comportement de l'IA selon votre style de jeu.</p>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <div class="text-4xl mb-4">🎲</div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Jets de dés</h3>
                        <p class="text-gray-400">Le MJ suggère des jets appropriés pour pimenter l'aventure.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section class="py-20 px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center text-white mb-12">💰 Tarifs</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Gratuit</h3>
                        <div class="text-4xl font-bold text-white mb-4">0€</div>
                        <ul class="text-gray-400 space-y-2 mb-6">
                            <li>✅ 10 messages/jour</li>
                            <li>✅ 3 conversations</li>
                            <li>✅ Modèles basiques</li>
                            <li>❌ Support prioritaire</li>
                        </ul>
                        <Link :href="route('register')" class="block text-center border border-gray-600 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Commencer
                        </Link>
                    </div>
                    <div class="bg-purple-900/30 p-6 rounded-xl border-2 border-purple-500 relative">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-purple-600 text-white text-sm px-3 py-1 rounded-full">
                            Populaire
                        </div>
                        <h3 class="text-xl font-semibold text-purple-400 mb-2">Pro</h3>
                        <div class="text-4xl font-bold text-white mb-4">9€<span class="text-lg text-gray-400">/mois</span></div>
                        <ul class="text-gray-400 space-y-2 mb-6">
                            <li>✅ Messages illimités</li>
                            <li>✅ Conversations illimitées</li>
                            <li>✅ Tous les modèles</li>
                            <li>✅ Support prioritaire</li>
                        </ul>
                        <Link :href="route('register')" class="block text-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Choisir Pro
                        </Link>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Enterprise</h3>
                        <div class="text-4xl font-bold text-white mb-4">Sur devis</div>
                        <ul class="text-gray-400 space-y-2 mb-6">
                            <li>✅ Tout Pro inclus</li>
                            <li>✅ API dédiée</li>
                            <li>✅ SLA garanti</li>
                            <li>✅ Support 24/7</li>
                        </ul>
                        <a href="mailto:contact@gamemaster.ai" class="block text-center border border-gray-600 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-20 px-4 bg-gray-800/30">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-center text-white mb-12">💬 Témoignages</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <p class="text-gray-400 mb-4">"GameMaster a révolutionné mes sessions solo. L'IA crée des rebondissements que je n'aurais jamais imaginés !"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">🧙</div>
                            <div>
                                <div class="text-white font-medium">Thomas R.</div>
                                <div class="text-gray-500 text-sm">Rôliste depuis 15 ans</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <p class="text-gray-400 mb-4">"Parfait pour préparer mes sessions de MJ. Je teste mes scénarios avant de les jouer avec mon groupe."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-600 rounded-full flex items-center justify-center">🎭</div>
                            <div>
                                <div class="text-white font-medium">Marie L.</div>
                                <div class="text-gray-500 text-sm">MJ de D&D 5e</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
                        <p class="text-gray-400 mb-4">"Je joue avec mes enfants le weekend. Le mode famille est top, zéro contenu inapproprié."</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">👨‍👧</div>
                            <div>
                                <div class="text-white font-medium">Pierre D.</div>
                                <div class="text-gray-500 text-sm">Papa geek</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-20 px-4">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-center text-white mb-12">❓ FAQ</h2>
                <div class="space-y-4">
                    <details class="bg-gray-800 rounded-xl border border-gray-700">
                        <summary class="p-4 cursor-pointer text-white font-medium">Comment fonctionne GameMaster ?</summary>
                        <p class="px-4 pb-4 text-gray-400">GameMaster utilise des modèles de langage avancés pour créer des aventures interactives. Vous décrivez vos actions, et l'IA génère les conséquences et fait avancer l'histoire.</p>
                    </details>
                    <details class="bg-gray-800 rounded-xl border border-gray-700">
                        <summary class="p-4 cursor-pointer text-white font-medium">Puis-je jouer à n'importe quel système de JDR ?</summary>
                        <p class="px-4 pb-4 text-gray-400">Oui ! GameMaster s'adapte à tous les univers : D&D, Pathfinder, Call of Cthulhu, Cyberpunk, ou même des systèmes personnalisés.</p>
                    </details>
                    <details class="bg-gray-800 rounded-xl border border-gray-700">
                        <summary class="p-4 cursor-pointer text-white font-medium">Mes données sont-elles sécurisées ?</summary>
                        <p class="px-4 pb-4 text-gray-400">Absolument. Vos conversations sont chiffrées et nous ne partageons jamais vos données avec des tiers. Consultez notre politique de confidentialité pour plus de détails.</p>
                    </details>
                    <details class="bg-gray-800 rounded-xl border border-gray-700">
                        <summary class="p-4 cursor-pointer text-white font-medium">Puis-je annuler mon abonnement ?</summary>
                        <p class="px-4 pb-4 text-gray-400">Oui, vous pouvez annuler à tout moment. Votre accès reste actif jusqu'à la fin de la période payée.</p>
                    </details>
                    <details class="bg-gray-800 rounded-xl border border-gray-700">
                        <summary class="p-4 cursor-pointer text-white font-medium">L'IA remplace-t-elle un vrai MJ ?</summary>
                        <p class="px-4 pb-4 text-gray-400">GameMaster est un complément, pas un remplacement ! C'est parfait pour jouer solo, s'entraîner, ou préparer des sessions, mais rien ne vaut une table avec des amis.</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 border-t border-gray-700 py-12 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <span class="text-2xl mr-2">🎲</span>
                            <span class="text-xl font-bold text-purple-400">GameMaster</span>
                        </div>
                        <p class="text-gray-400 text-sm">Votre Maître de Jeu personnel, disponible 24/7.</p>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Produit</h4>
                        <ul class="text-gray-400 space-y-2 text-sm">
                            <li><a href="#features" class="hover:text-white">Fonctionnalités</a></li>
                            <li><a href="#" class="hover:text-white">Tarifs</a></li>
                            <li><a href="#" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Légal</h4>
                        <ul class="text-gray-400 space-y-2 text-sm">
                            <li><Link :href="route('legal.mentions')" class="hover:text-white">Mentions légales</Link></li>
                            <li><Link :href="route('legal.privacy')" class="hover:text-white">Confidentialité</Link></li>
                            <li><Link :href="route('legal.cookies')" class="hover:text-white">Cookies</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">Contact</h4>
                        <ul class="text-gray-400 space-y-2 text-sm">
                            <li>📧 contact@gamemaster.ai</li>
                            <li>🐦 @GameMasterAI</li>
                            <li>💬 Discord</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 pt-8 text-center text-gray-500 text-sm">
                    <p>© 2025 GameMaster. Tous droits réservés.</p>
                    <p class="mt-2">⚠️ GameMaster est une IA. Les réponses peuvent contenir des erreurs ou des hallucinations.</p>
                </div>
            </div>
        </footer>

        <!-- Cookie Banner RGPD -->
        <CookieBanner />
    </div>
</template>
