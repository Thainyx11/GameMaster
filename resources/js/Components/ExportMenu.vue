<script setup lang="ts">
import { ref } from 'vue'

interface Props {
    conversationId: number
}

const props = defineProps<Props>()

const isOpen = ref(false)

function toggleMenu() {
    isOpen.value = !isOpen.value
}

function closeMenu() {
    isOpen.value = false
}

function exportAs(format: 'markdown' | 'json') {
    const url = route('chat.export', { conversation: props.conversationId, format })
    window.open(url, '_blank')
    closeMenu()
}
</script>

<template>
    <div class="relative">
        <button
            @click="toggleMenu"
            class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-300 transition-colors"
            title="Exporter la conversation"
            aria-label="Menu d'export"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </button>

        <!-- Menu dropdown -->
        <Transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg border border-gray-700 z-50"
            >
                <div class="py-1">
                    <button
                        @click="exportAs('markdown')"
                        class="w-full text-left px-4 py-2 text-gray-300 hover:bg-gray-700 flex items-center gap-2"
                    >
                        <span>📝</span>
                        <span>Markdown (.md)</span>
                    </button>
                    <button
                        @click="exportAs('json')"
                        class="w-full text-left px-4 py-2 text-gray-300 hover:bg-gray-700 flex items-center gap-2"
                    >
                        <span>📦</span>
                        <span>JSON (.json)</span>
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Overlay pour fermer le menu -->
        <div
            v-if="isOpen"
            class="fixed inset-0 z-40"
            @click="closeMenu"
        />
    </div>
</template>