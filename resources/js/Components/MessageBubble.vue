<script setup lang="ts">
import { ref, computed } from 'vue'
import { useMarkdown } from '@/Composables/useMarkdown'
import { useI18n } from '@/Composables/useI18n'

interface Props {
    role: 'user' | 'assistant'
    content: string
    imageUrl?: string | null
    isStreaming?: boolean
    isLast?: boolean
    canRegenerate?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    isStreaming: false,
    isLast: false,
    canRegenerate: false,
    imageUrl: null,
})

const emit = defineEmits<{
    regenerate: []
}>()

const { renderMarkdown } = useMarkdown()
const { t } = useI18n()

const isUser = computed(() => props.role === 'user')
const roleLabel = computed(() => isUser.value ? 'Votre message' : 'Réponse du Maître du Jeu')

// Extraire le reasoning avec la nouvelle syntaxe [REASONING]...[/REASONING]
const reasoningContent = computed(() => {
    if (isUser.value) return null
    const matches = props.content.match(/\[REASONING\]([\s\S]*?)\[\/REASONING\]/g)
    if (!matches) return null
    return matches
        .map(m => m.replace(/\[REASONING\]/g, '').replace(/\[\/REASONING\]/g, ''))
        .join('')
        .trim()
})

// Contenu sans le reasoning
const mainContent = computed(() => {
    return props.content
        .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
        .trim()
})

const hasReasoning = computed(() => !!reasoningContent.value)

const renderedContent = computed(() => {
    return renderMarkdown(mainContent.value)
})

// État pour afficher/masquer le reasoning
const showReasoning = ref(false)
</script>

<template>
    <article 
        class="flex gap-4 p-4 group"
        :class="isUser ? 'bg-gray-800/50' : 'bg-gray-900'"
        :aria-label="roleLabel"
        role="article"
    >
        <!-- Avatar -->
        <div 
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl"
            :class="isUser ? 'bg-purple-600' : 'bg-amber-600'"
            role="img"
            :aria-label="isUser ? 'Avatar utilisateur' : 'Avatar GameMaster'"
        >
            {{ isUser ? '🧙' : '🎲' }}
        </div>

        <!-- Contenu -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-1">
                <div 
                    class="text-sm font-medium" 
                    :class="isUser ? 'text-purple-400' : 'text-amber-400'"
                    aria-hidden="true"
                >
                    {{ isUser ? t('chat.you') : t('chat.gameMaster') }}
                </div>

                <!-- Bouton Regénérer -->
                <button
                    v-if="!isUser && isLast && canRegenerate && !isStreaming"
                    @click="emit('regenerate')"
                    class="opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded hover:bg-gray-700 text-gray-400 hover:text-white"
                    :title="t('chat.regenerate')"
                    :aria-label="t('chat.regenerate')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            <!-- Image jointe -->
            <div v-if="imageUrl && isUser" class="mb-3">
                <a :href="imageUrl" target="_blank" class="inline-block">
                    <img 
                        :src="imageUrl" 
                        alt="Image jointe" 
                        class="max-w-xs max-h-48 rounded-lg border border-gray-600 hover:border-purple-500 transition-colors"
                    />
                </a>
            </div>

            <!-- Bloc Reasoning (Mode Thinking) -->
            <div v-if="hasReasoning && !isStreaming" class="mb-3">
                <button
                    @click="showReasoning = !showReasoning"
                    class="flex items-center gap-2 text-sm text-purple-400 hover:text-purple-300 transition-colors"
                >
                    <svg 
                        class="w-4 h-4 transition-transform" 
                        :class="{ 'rotate-90': showReasoning }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>🧠</span>
                    <span>{{ showReasoning ? 'Masquer la réflexion' : 'Voir la réflexion du MJ' }}</span>
                </button>
                
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-[500px]"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 max-h-[500px]"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div 
                        v-if="showReasoning"
                        class="mt-2 p-4 bg-purple-900/30 border border-purple-700/50 rounded-lg overflow-hidden"
                    >
                        <div class="flex items-center gap-2 mb-2 text-purple-400 text-xs font-medium uppercase tracking-wide">
                            <span>🧠</span>
                            <span>Processus de réflexion</span>
                        </div>
                        <div class="text-gray-300 text-sm whitespace-pre-wrap">{{ reasoningContent }}</div>
                    </div>
                </Transition>
            </div>

            <!-- Contenu principal -->
            <div 
                class="prose prose-invert prose-purple max-w-none text-gray-200"
                v-html="renderedContent"
                :aria-live="isStreaming ? 'polite' : 'off'"
            />

            <!-- Curseur de streaming -->
            <span 
                v-if="isStreaming" 
                class="inline-block w-2 h-5 bg-purple-400 animate-pulse ml-1"
                role="status"
                aria-label="Réponse en cours de génération"
            />
        </div>
    </article>
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