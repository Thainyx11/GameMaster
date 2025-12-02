<script setup lang="ts">
import { ref, computed } from 'vue'

interface Model {
    id: string
    name: string
    description: string
    context_length: number
}

interface Props {
    models: Model[]
    modelValue: string
    disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    disabled: false
})

const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()

const isOpen = ref(false)

const selectedModel = computed(() => {
    return props.models.find(m => m.id === props.modelValue)
})

function formatModelName(id: string): string {
    const parts = id.split('/')
    return parts.length > 1 ? parts[1] : id
}

function selectModel(modelId: string) {
    emit('update:modelValue', modelId)
    isOpen.value = false
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            @click="isOpen = !isOpen"
            :disabled="disabled"
            class="flex items-center gap-2 px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm text-gray-200 transition-colors disabled:opacity-50"
        >
            <span class="text-purple-400">🤖</span>
            <span class="max-w-[150px] truncate">
                {{ selectedModel ? formatModelName(selectedModel.id) : 'Sélectionner' }}
            </span>
            <svg class="w-4 h-4" :class="{ 'rotate-180': isOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown -->
        <div 
            v-if="isOpen"
            class="absolute right-0 z-50 mt-2 w-72 bg-gray-800 border border-gray-700 rounded-lg shadow-xl max-h-64 overflow-y-auto"
        >
            <div class="p-2">
                <button
                    v-for="model in models"
                    :key="model.id"
                    @click="selectModel(model.id)"
                    class="w-full text-left px-3 py-2 rounded-md transition-colors"
                    :class="model.id === modelValue 
                        ? 'bg-purple-600/20 text-purple-300' 
                        : 'text-gray-300 hover:bg-gray-700'"
                >
                    <div class="font-medium text-sm">{{ formatModelName(model.id) }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ model.id }}</div>
                </button>
            </div>
        </div>

        <!-- Overlay pour fermer -->
        <div v-if="isOpen" class="fixed inset-0 z-40" @click="isOpen = false" />
    </div>
</template>