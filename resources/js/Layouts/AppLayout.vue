<script setup lang="ts">
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth?.user as { name: string } | undefined

const mobileMenuOpen = ref(false)
</script>

<template>
    <div class="min-h-screen bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-gray-800 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link :href="route('home')" class="flex items-center">
                            <span class="text-2xl mr-2">🎲</span>
                            <span class="text-xl font-bold text-purple-400">GameMaster</span>
                        </Link>
                        
                        <div class="hidden md:flex ml-10 space-x-4">
                            <Link 
                                :href="route('chat.index')" 
                                class="text-gray-300 hover:text-white px-3 py-2 rounded-md transition-colors"
                            >
                                💬 Chat
                            </Link>
                            <Link 
                                :href="route('instructions.edit')" 
                                class="text-gray-300 hover:text-white px-3 py-2 rounded-md transition-colors"
                            >
                                ⚙️ Instructions
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="hidden md:flex items-center space-x-4">
                            <span class="text-gray-400">{{ user?.name }}</span>
                            <Link 
                                :href="route('profile.edit')" 
                                class="text-gray-300 hover:text-white"
                            >
                                Profil
                            </Link>
                            <Link 
                                :href="route('logout')" 
                                method="post" 
                                as="button"
                                class="text-gray-300 hover:text-white"
                            >
                                Déconnexion
                            </Link>
                        </div>

                        <button 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden text-gray-400 hover:text-white"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu mobile -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-gray-800 border-t border-gray-700">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link :href="route('chat.index')" class="block text-gray-300 hover:text-white px-3 py-2 rounded-md">
                        💬 Chat
                    </Link>
                    <Link :href="route('instructions.edit')" class="block text-gray-300 hover:text-white px-3 py-2 rounded-md">
                        ⚙️ Instructions
                    </Link>
                    <Link :href="route('profile.edit')" class="block text-gray-300 hover:text-white px-3 py-2 rounded-md">
                        👤 Profil
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="block w-full text-left text-gray-300 hover:text-white px-3 py-2 rounded-md">
                        🚪 Déconnexion
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Contenu -->
        <main>
            <slot />
        </main>
    </div>
</template>