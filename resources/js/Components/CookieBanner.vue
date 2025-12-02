<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const showBanner = ref(false)

onMounted(() => {
    // Vérifie si l'utilisateur a déjà fait un choix
    const consent = localStorage.getItem('cookie_consent')
    if (!consent) {
        showBanner.value = true
    }
})

function acceptAll() {
    localStorage.setItem('cookie_consent', 'all')
    showBanner.value = false
}

function acceptEssential() {
    localStorage.setItem('cookie_consent', 'essential')
    showBanner.value = false
}
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
    >
        <div 
            v-if="showBanner"
            class="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-4 z-50"
        >
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-gray-300 text-sm">
                    <p>
                        🍪 Nous utilisons des cookies pour assurer le bon fonctionnement du site. 
                        <Link :href="route('legal.cookies')" class="text-purple-400 hover:underline">
                            En savoir plus
                        </Link>
                    </p>
                </div>
                <div class="flex gap-3">
                    <button 
                        @click="acceptEssential"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors text-sm"
                    >
                        Essentiels uniquement
                    </button>
                    <button 
                        @click="acceptAll"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors text-sm"
                    >
                        Tout accepter
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>