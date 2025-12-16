<script setup lang="ts">
interface Props {
    modelValue: boolean
}

defineProps<Props>()
const emit = defineEmits<{
    'update:modelValue': [value: boolean]
}>()
</script>

<template>
    <button
        @click="emit('update:modelValue', !modelValue)"
        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors text-sm"
        :class="modelValue 
            ? 'bg-purple-600 text-white' 
            : 'bg-gray-700 text-gray-400 hover:bg-gray-600'"
        :title="modelValue ? 'Mode Thinking activé' : 'Mode Thinking désactivé'"
    >
        <span>🧠</span>
        <span class="hidden sm:inline">{{ modelValue ? 'Thinking ON' : 'Thinking' }}</span>
    </button>
</template>