<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import { useStream } from '@laravel/stream-vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import ConversationSidebar from '@/Components/ConversationSidebar.vue'
import MessageBubble from '@/Components/MessageBubble.vue'
import ModelSelector from '@/Components/ModelSelector.vue'
import ExportMenu from '@/Components/ExportMenu.vue'
import ImageGenerator from '@/Components/ImageGenerator.vue'
import QuickTools from '@/Components/QuickTools.vue'
import ImageUpload from '@/Components/ImageUpload.vue'
import ThinkingToggle from '@/Components/ThinkingToggle.vue'
import { useI18n } from '@/Composables/useI18n'

const { t } = useI18n()

interface Message {
    id: number
    role: 'user' | 'assistant'
    content: string
    image_url?: string | null
    created_at: string
}

interface Conversation {
    id: number
    title: string
    model: string
    updated_at: string
}

interface Props {
    conversations: Conversation[]
    currentConversation: Conversation | null
    messages: Message[]
    models: Array<{ id: string; name: string }>
    defaultModel: string
}

const props = defineProps<Props>()

const userMessage = ref('')
const localMessages = ref<Message[]>([])
const selectedModel = ref(props.currentConversation?.model || props.defaultModel)
const messagesContainer = ref<HTMLElement>()
const thinkingEnabled = ref(false)
const uploadedImage = ref<{ path: string; url: string } | null>(null)
const currentConversationId = ref<number | null>(props.currentConversation?.id || null)

// useStream hook
const { data: streamData, isFetching, isStreaming, send } = useStream(
    route('chat.send'),
    {
        onFinish: () => {
            handleStreamFinish()
        },
        onError: (err: Error) => {
            console.error('Erreur streaming:', err)
        },
    }
)

// Computed pour extraire le contenu sans les marqueurs
const streamedContent = computed(() => {
    if (!streamData.value) return ''
    return streamData.value
        .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
        .replace(/\[TITLE\][\s\S]*?\[\/TITLE\]/g, '')
        .replace(/\[CONVERSATION_ID\][\s\S]*?\[\/CONVERSATION_ID\]/g, '')
        .replace(/\[DONE\]/g, '')
        .replace(/\[ERROR\].*$/g, '')
        .trim()
})

const isLoading = computed(() => isFetching.value || isStreaming.value)

// Initialiser les messages locaux
watch(() => props.messages, (newMessages) => {
    localMessages.value = [...newMessages]
}, { immediate: true })

watch(() => props.currentConversation, (newConv) => {
    if (newConv) {
        selectedModel.value = newConv.model
        currentConversationId.value = newConv.id
    }
}, { immediate: true })

// Messages à afficher (avec streaming)
const displayMessages = computed(() => {
    const msgs = [...localMessages.value]
    
    if (isStreaming.value && streamedContent.value) {
        msgs.push({
            id: -1,
            role: 'assistant',
            content: streamedContent.value,
            created_at: new Date().toISOString()
        })
    }
    
    return msgs
})

// Scroll automatique
watch(displayMessages, async () => {
    await nextTick()
    scrollToBottom()
}, { deep: true })

function scrollToBottom() {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Gérer la fin du stream
function handleStreamFinish() {
    if (!streamData.value) return

    // Extraire le titre si présent
    const titleMatch = streamData.value.match(/\[TITLE\]([\s\S]*?)\[\/TITLE\]/)
    const convIdMatch = streamData.value.match(/\[CONVERSATION_ID\]([\s\S]*?)\[\/CONVERSATION_ID\]/)

    if (titleMatch && convIdMatch) {
        const convId = parseInt(convIdMatch[1].trim())
        router.visit(route('chat.show', convId), { preserveState: false })
    } else {
        // Ajouter le message final à la liste locale
        const finalContent = streamedContent.value
        if (finalContent) {
            localMessages.value.push({
                id: Date.now(),
                role: 'assistant',
                content: finalContent,
                created_at: new Date().toISOString()
            })
        }
    }

    // Reset
    uploadedImage.value = null
}

// Envoyer un message
async function sendMessage() {
    const message = userMessage.value.trim()
    if (!message || isLoading.value) return

    userMessage.value = ''

    // Ajouter le message utilisateur localement
    localMessages.value.push({
        id: Date.now(),
        role: 'user',
        content: message,
        image_url: uploadedImage.value?.url || null,
        created_at: new Date().toISOString()
    })

    // Si pas de conversation, en créer une
    if (!currentConversationId.value) {
        try {
            const response = await fetch(route('chat.create'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ model: selectedModel.value }),
            })
            
            const data = await response.json()
            currentConversationId.value = data.conversation.id
        } catch (error) {
            console.error('Erreur création conversation:', error)
            return
        }
    }

    // Envoyer avec useStream
    send({
        conversation_id: currentConversationId.value,
        message: message,
        thinking_enabled: thinkingEnabled.value,
        image_path: uploadedImage.value?.path || null,
    })
}

// Regénérer le dernier message (utilise fetch classique car route différente)
async function regenerateLastMessage() {
    if (!props.currentConversation || isLoading.value) return

    // Supprimer le dernier message assistant de la liste locale
    const lastIndex = localMessages.value.length - 1
    if (lastIndex >= 0 && localMessages.value[lastIndex].role === 'assistant') {
        localMessages.value.pop()
    }

    try {
        const response = await fetch(route('chat.regenerate', props.currentConversation.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        })

        const reader = response.body?.getReader()
        if (!reader) return

        const decoder = new TextDecoder()
        let fullContent = ''

        // Ajouter un message temporaire
        const tempId = Date.now()
        localMessages.value.push({
            id: tempId,
            role: 'assistant',
            content: '',
            created_at: new Date().toISOString()
        })

        while (true) {
            const { done, value } = await reader.read()
            if (done) break

            const chunk = decoder.decode(value, { stream: true })
            fullContent += chunk

            const cleanContent = fullContent
                .replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '')
                .replace(/\[DONE\]/g, '')
                .trim()

            // Mettre à jour le message
            const msgIndex = localMessages.value.findIndex(m => m.id === tempId)
            if (msgIndex >= 0) {
                localMessages.value[msgIndex].content = cleanContent
            }
        }

    } catch (error) {
        console.error('Erreur régénération:', error)
    }
}

function updateModel(model: string) {
    selectedModel.value = model
    
    if (props.currentConversation) {
        fetch(route('chat.updateModel', props.currentConversation.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ model }),
        })
    }
}

function insertCommand(command: string) {
    userMessage.value = command + ' '
}

function handleImageUploaded(data: { path: string; url: string }) {
    uploadedImage.value = data
}

function handleImageRemoved() {
    uploadedImage.value = null
}

const suggestions = [
    { text: '🐉 Heroic Fantasy', prompt: 'Je veux jouer une aventure heroic fantasy avec des dragons et de la magie.' },
    { text: '🐙 Horreur cosmique', prompt: 'Lance-moi dans une aventure d\'horreur lovecraftienne avec des mystères indicibles.' },
    { text: '🌃 Cyberpunk', prompt: 'Je veux explorer un monde cyberpunk dystopique avec des hackers et des corporations.' },
]

function useSuggestion(prompt: string) {
    userMessage.value = prompt
}
</script>

<template>
    <AppLayout>
        <div class="flex h-[calc(100vh-64px)]">
            <!-- Sidebar -->
            <ConversationSidebar
                :conversations="conversations"
                :current-conversation-id="currentConversation?.id"
            />

            <!-- Zone principale -->
            <div class="flex-1 flex flex-col bg-gray-900">
                <!-- Header -->
                <header class="flex items-center justify-between px-4 py-3 border-b border-gray-700 bg-gray-800/50">
                    <h1 class="text-lg font-semibold text-gray-200">
                        {{ currentConversation?.title || '🎲 Nouvelle aventure' }}
                    </h1>
                    
                    <div class="flex items-center gap-2">
                        <ImageGenerator />
                        <ExportMenu 
                            v-if="currentConversation" 
                            :conversation-id="currentConversation.id" 
                        />
                        <ModelSelector
                            :models="models"
                            :model-value="selectedModel"
                            :disabled="isLoading"
                            @update:model-value="updateModel"
                        />
                    </div>
                </header>

                <!-- Messages -->
                <div ref="messagesContainer" class="flex-1 overflow-y-auto">
                    <!-- État vide -->
                    <div v-if="displayMessages.length === 0" class="flex flex-col items-center justify-center h-full text-center p-8">
                        <div class="text-6xl mb-4">🎲</div>
                        <h2 class="text-2xl font-bold text-purple-400 mb-2">{{ t('chat.welcome') }}</h2>
                        <p class="text-gray-400 max-w-md mb-6">
                            {{ t('chat.welcomeSubtitle') }}
                        </p>
                        
                        <!-- Suggestions -->
                        <div class="flex flex-wrap gap-2 justify-center">
                            <button
                                v-for="suggestion in suggestions"
                                :key="suggestion.text"
                                @click="useSuggestion(suggestion.prompt)"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg border border-gray-700 transition-colors"
                            >
                                {{ suggestion.text }}
                            </button>
                        </div>
                    </div>

                    <!-- Liste des messages -->
                    <div v-else>
                        <MessageBubble
                            v-for="(msg, index) in displayMessages"
                            :key="msg.id"
                            :role="msg.role"
                            :content="msg.content"
                            :image-url="msg.image_url"
                            :is-streaming="msg.id === -1 && isStreaming"
                            :is-last="index === displayMessages.length - 1 || (index === displayMessages.length - 2 && isStreaming)"
                            :can-regenerate="!!currentConversation && !isLoading"
                            @regenerate="regenerateLastMessage"
                        />
                    </div>
                </div>

                <!-- Zone de saisie -->
                <div class="border-t border-gray-700 p-4 bg-gray-800/50">
                    <form @submit.prevent="sendMessage" class="flex gap-3" role="form" aria-label="Envoyer un message">
                        <QuickTools @command="insertCommand" />
                        <ImageUpload 
                            @uploaded="handleImageUploaded" 
                            @removed="handleImageRemoved" 
                        />
                        <ThinkingToggle v-model="thinkingEnabled" />
                        <label for="message-input" class="sr-only">Votre message</label>
                        <input
                            id="message-input"
                            v-model="userMessage"
                            type="text"
                            :placeholder="t('chat.placeholder')"
                            class="flex-1 bg-gray-700 border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-purple-500 focus:border-purple-500 placeholder-gray-400"
                            :disabled="isLoading"
                            autocomplete="off"
                        />
                        <button
                            type="submit"
                            :disabled="isLoading || !userMessage.trim()"
                            class="bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2"
                        >
                            <span v-if="isLoading" class="animate-spin">⏳</span>
                            <span v-else>🎲</span>
                            <span>{{ t('chat.send') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>