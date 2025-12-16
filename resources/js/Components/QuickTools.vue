<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
    command: [value: string]
}>()

const isOpen = ref(false)

const tools = [
    { icon: '🎲', label: 'Lancer 1d20', command: '/roll 1d20' },
    { icon: '🎯', label: 'Lancer 1d6', command: '/roll 1d6' },
    { icon: '⚔️', label: 'Attaque (2d6)', command: '/roll 2d6' },
    { icon: '🛡️', label: 'Défense (1d20+5)', command: '/roll 1d20+5' },
    { icon: '💰', label: 'Trésor (3d6)', command: '/roll 3d6' },
]

function selectTool(command: string) {
    emit('command', command)
    isOpen.value = false
}
</script>

<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-amber-400 transition-colors"
            title="Outils rapides"
            aria-label="Outils rapides"
        >
            🎲
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
                class="absolute bottom-full left-0 mb-2 w-48 bg-gray-800 rounded-lg shadow-lg border border-gray-700 z-50"
            >
                <div class="p-2">
                    <div class="text-xs text-gray-500 uppercase mb-2 px-2">Lancers rapides</div>
                    <button
                        v-for="tool in tools"
                        :key="tool.command"
                        @click="selectTool(tool.command)"
                        class="w-full text-left px-3 py-2 text-gray-300 hover:bg-gray-700 rounded flex items-center gap-2 text-sm"
                    >
                        <span>{{ tool.icon }}</span>
                        <span>{{ tool.label }}</span>
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Overlay pour fermer le menu -->
        <div
            v-if="isOpen"
            class="fixed inset-0 z-40"
            @click="isOpen = false"
        />
    </div>
</template>