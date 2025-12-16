<script setup lang="ts">
import { ref } from 'vue'

const isOpen = ref(false)
const prompt = ref('')
const selectedStyle = ref('fantasy')
const isGenerating = ref(false)
const generatedImage = ref<string | null>(null)
const error = ref<string | null>(null)

const styles = [
    { value: 'fantasy', label: '🐉 Fantasy' },
    { value: 'horror', label: '🐙 Horreur' },
    { value: 'cyberpunk', label: '🌃 Cyberpunk' },
]

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

async function generateImage() {
    if (!prompt.value.trim() || isGenerating.value) return
    isGenerating.value = true
    error.value = null
    generatedImage.value = null

    try {
        const response = await fetch(route('image.generate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ prompt: prompt.value, style: selectedStyle.value }),
        })
        const data = await response.json()
        if (data.success) {
            generatedImage.value = data.url
        } else {
            error.value = data.error || 'Erreur'
        }
    } catch (e) {
        error.value = 'Erreur de connexion'
    } finally {
        isGenerating.value = false
    }
}

function closeModal() {
    isOpen.value = false
    generatedImage.value = null
    error.value = null
}
</script>

<template>
    <button
        @click="isOpen = true"
        class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-pink-400 transition-colors"
        title="Générer une image"
    >
        🎨
    </button>

    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4" @click.self="closeModal">
            <div class="bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                    <h2 class="text-xl font-bold text-white">🎨 Générateur d'images</h2>
                    <button @click="closeModal" class="text-gray-400 hover:text-white text-2xl">&times;</button>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Style</label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="style in styles"
                                :key="style.value"
                                @click="selectedStyle = style.value"
                                class="px-3 py-2 rounded-lg border text-sm"
                                :class="selectedStyle === style.value ? 'bg-purple-600 border-purple-500 text-white' : 'bg-gray-700 border-gray-600 text-gray-300'"
                            >{{ style.label }}</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea
                            v-model="prompt"
                            rows="3"
                            class="w-full bg-gray-700 border-gray-600 text-gray-200 rounded-lg px-4 py-3 placeholder-gray-400"
                            placeholder="Ex: Un dragon rouge survolant une forêt..."
                            :disabled="isGenerating"
                        />
                    </div>

                    <button
                        @click="generateImage"
                        :disabled="isGenerating || !prompt.trim()"
                        class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-6 py-3 rounded-lg font-medium"
                    >{{ isGenerating ? '⏳ Génération...' : '🎨 Générer' }}</button>

                    <div v-if="error" class="p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-300">{{ error }}</div>

                    <div v-if="generatedImage" class="space-y-3">
                        <img :src="generatedImage" alt="Image générée" class="w-full rounded-lg" />
                        <a :href="generatedImage" target="_blank" class="block text-center bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">🔗 Ouvrir en grand</a>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>