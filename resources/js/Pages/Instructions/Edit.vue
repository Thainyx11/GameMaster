<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

interface Props {
    instructions: string | null
}

const props = defineProps<Props>()

const form = useForm({
    instructions: props.instructions || ''
})

function submit() {
    form.put(route('instructions.update'))
}
</script>

<template>
    <AppLayout>
        <div class="max-w-3xl mx-auto py-8 px-4">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <h1 class="text-2xl font-bold text-purple-400 mb-2">⚙️ Instructions personnalisées</h1>
                <p class="text-gray-400 mb-6">
                    Définissez comment GameMaster doit se comporter avec vous. Ces instructions seront utilisées dans toutes vos conversations.
                </p>

                <form @submit.prevent="submit">
                    <div class="mb-6">
                        <label for="instructions" class="block text-sm font-medium text-gray-300 mb-2">
                            Vos instructions
                        </label>
                        <textarea
                            id="instructions"
                            v-model="form.instructions"
                            rows="8"
                            class="w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-purple-500 focus:border-purple-500 placeholder-gray-400"
                            placeholder="Exemples :
- Je joue à D&D 5e, utilise ces règles
- J'aime les descriptions détaillées des combats
- Je préfère les ambiances sombres et mystérieuses
- Évite le gore, je joue avec mes enfants"
                        />
                        <p class="text-xs text-gray-500 mt-2">
                            {{ form.instructions.length }} / 2000 caractères
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                        >
                            <span v-if="form.processing">Sauvegarde...</span>
                            <span v-else>💾 Sauvegarder</span>
                        </button>

                        <span v-if="form.recentlySuccessful" class="text-green-400 text-sm">
                            ✅ Instructions sauvegardées !
                        </span>
                    </div>
                </form>

                <!-- Exemples -->
                <div class="mt-8 pt-6 border-t border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-300 mb-4">💡 Idées d'instructions</h2>
                    <div class="grid gap-3">
                        <button 
                            @click="form.instructions = 'Je suis un joueur expérimenté de D&D 5e. Tu peux utiliser les termes techniques et les règles officielles. J\'aime les combats tactiques et les énigmes complexes.'"
                            class="text-left p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg text-sm text-gray-300 transition-colors"
                        >
                            🎯 <strong>Joueur expérimenté D&D</strong> - Termes techniques, combats tactiques
                        </button>
                        <button 
                            @click="form.instructions = 'Je débute dans le jeu de rôle. Explique-moi les mécaniques simplement et guide-moi dans mes choix. Sois patient et pédagogue.'"
                            class="text-left p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg text-sm text-gray-300 transition-colors"
                        >
                            🌱 <strong>Débutant</strong> - Explications simples, guidage
                        </button>
                        <button 
                            @click="form.instructions = 'Je joue avec mes enfants (8-12 ans). Garde un ton adapté, évite la violence graphique et le contenu mature. Favorise l\'humour et l\'aventure positive.'"
                            class="text-left p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg text-sm text-gray-300 transition-colors"
                        >
                            👨‍👩‍👧‍👦 <strong>Jeu en famille</strong> - Contenu adapté aux enfants
                        </button>
                        <button 
                            @click="form.instructions = 'J\'adore les ambiances sombres style Lovecraft. N\'hésite pas à créer de la tension, du mystère et une atmosphère oppressante. Les descriptions détaillées sont les bienvenues.'"
                            class="text-left p-3 bg-gray-700/50 hover:bg-gray-700 rounded-lg text-sm text-gray-300 transition-colors"
                        >
                            🌑 <strong>Horreur cosmique</strong> - Ambiance sombre et mystérieuse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>