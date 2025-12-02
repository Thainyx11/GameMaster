<script setup lang="ts">
import { computed } from 'vue'
import { useMarkdown } from '@/Composables/useMarkdown'

interface Props {
    role: 'user' | 'assistant'
    content: string
    isStreaming?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    isStreaming: false
})

const { renderMarkdown } = useMarkdown()

const renderedContent = computed(() => {
    return renderMarkdown(props.content)
})

const isUser = computed(() => props.role === 'user')
</script>

<template>
    <div 
        class="flex gap-4 p-4"
        :class="isUser ? 'bg-gray-800/50' : 'bg-gray-900'"
    >
        <!-- Avatar -->
        <div 
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl"
            :class="isUser ? 'bg-purple-600' : 'bg-amber-600'"
        >
            {{ isUser ? '🧙' : '🎲' }}
        </div>

        <!-- Contenu -->
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium mb-1" :class="isUser ? 'text-purple-400' : 'text-amber-400'">
                {{ isUser ? 'Vous' : 'GameMaster' }}
            </div>

            <div 
                class="prose prose-invert prose-purple max-w-none text-gray-200"
                v-html="renderedContent"
            />

            <!-- Curseur de streaming -->
            <span 
                v-if="isStreaming" 
                class="inline-block w-2 h-5 bg-purple-400 animate-pulse ml-1"
            />
        </div>
    </div>
</template>

<style>
.prose pre {
    background-color: #1f2937;
    border-radius: 0.5rem;
    padding: 1rem;
    overflow-x: auto;
}

.prose code {
    color: #c4b5fd;
}

.prose pre code {
    color: #e5e7eb;
}

.prose strong {
    color: #fcd34d;
}
</style>