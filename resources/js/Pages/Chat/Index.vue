<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MessageBubble from '@/Components/MessageBubble.vue'
import ConversationSidebar from '@/Components/ConversationSidebar.vue'
import ModelSelector from '@/Components/ModelSelector.vue'

// Types
interface Message {
    id: number
    role: 'user' | 'assistant'
    content: string
    created_at: string
}

interface Conversation {
    id: number
    title: string
    model: string
    updated_at: string
}

interface Model {
    id: string
    name: string
    description: string
    context_length: number
}

interface Props {
    conversations: Conversation[]
    currentConversation: Conversation | null
    messages: Message[]
    models: Model[]
    defaultModel: string
}

// Props
const props = defineProps<Props>()

// State
const userMessage = ref('')
const isLoading = ref(false)
const streamingContent = ref('')
const isStreaming = ref(false)
const selectedModel = ref(props.defaultModel)
const messagesContainer = ref<HTMLElement>()
const localMessages = ref<Message[]>([])

// Watchers
watch(() => props.messages, (newMessages) => {
    localMessages.value = [...newMessages]
}, { immediate: true })

watch(() => props.currentConversation, (conv) => {
    if (conv) {
        selectedModel.value = conv.model
    }
}, { immediate: true })

// Computed
const displayMessages = computed(() => {
    const msgs = [...localMessages.value]
    
    if (isStreaming.value && streamingContent.value) {
        msgs.push({
            id: -1,
            role: 'assistant',
            content: streamingContent.value,
            created_at: new Date().toISOString()
        })
    }
    
    return msgs
})

// Methods
async function scrollToBottom() {
    await nextTick()
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

async function createNewConversation() {
    try {
        const response = await fetch(route('chat.create'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ model: selectedModel.value })
        })
        
        const data = await response.json()
        router.visit(route('chat.show', data.conversation.id))
    } catch (error) {
        console.error('Erreur création conversation:', error)
    }
}

async function deleteConversation(id: number) {
    try {
        await fetch(route('chat.delete', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
            }
        })
        
        if (props.currentConversation?.id === id) {
            router.visit(route('chat.index'))
        } else {
            router.reload()
        }
    } catch (error) {
        console.error('Erreur suppression:', error)
    }
}

async function sendMessage() {
    if (!userMessage.value.trim() || isLoading.value) return
    
    // Créer une conversation si nécessaire
    if (!props.currentConversation) {
        try {
            const response = await fetch(route('chat.create'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ model: selectedModel.value })
            })
            
            const data = await response.json()
            
            // Envoyer le message après création
            const message = userMessage.value.trim()
            userMessage.value = ''
            
            router.visit(route('chat.show', data.conversation.id), {
                onSuccess: () => {
                    userMessage.value = message
                    nextTick(() => sendMessage())
                }
            })
            return
        } catch (error) {
            console.error('Erreur:', error)
            return
        }
    }
    
    const message = userMessage.value.trim()
    userMessage.value = ''
    isLoading.value = true
    isStreaming.value = true
    streamingContent.value = ''
    
    // Ajouter le message utilisateur localement
    localMessages.value.push({
        id: Date.now(),
        role: 'user',
        content: message,
        created_at: new Date().toISOString()
    })
    
    scrollToBottom()
    
    try {
        const response = await fetch(route('chat.send'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
                'Accept': 'text/event-stream',
            },
            body: JSON.stringify({
                conversation_id: props.currentConversation.id,
                message: message,
            })
        })
        
        if (!response.ok) throw new Error('Erreur réseau')
        
        const reader = response.body?.getReader()
        const decoder = new TextDecoder()
        
        if (!reader) throw new Error('Stream non disponible')
        
        while (true) {
            const { done, value } = await reader.read()
            if (done) break
            
            const chunk = decoder.decode(value, { stream: true })
            const lines = chunk.split('\n')
            
            for (const line of lines) {
                if (line.startsWith('data: ')) {
                    try {
                        const data = JSON.parse(line.slice(6))
                        
                        if (data.token) {
                            streamingContent.value += data.token
                            scrollToBottom()
                        }
                        
                        if (data.title) {
                            router.reload({ only: ['conversations', 'currentConversation'] })
                        }
                        
                        if (data.done) {
                            router.reload({ only: ['messages'] })
                        }
                    } catch (e) {}
                }
            }
        }
    } catch (error) {
        console.error('Erreur:', error)
        streamingContent.value = '❌ Une erreur est survenue. Veuillez réessayer.'
    } finally {
        isLoading.value = false
        isStreaming.value = false
    }
}

async function updateModel(newModel: string) {
    if (!props.currentConversation) {
        selectedModel.value = newModel
        return
    }
    
    try {
        await fetch(route('chat.updateModel', props.currentConversation.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({ model: newModel })
        })
        selectedModel.value = newModel
    } catch (error) {
        console.error('Erreur:', error)
    }
}

onMounted(() => {
    scrollToBottom()
})
</script>

<template>
    <AppLayout>
        <div class="flex h-[calc(100vh-4rem)]">
            <!-- Sidebar -->
            <ConversationSidebar
                :conversations="conversations"
                :current-id="currentConversation?.id"
                @new-conversation="createNewConversation"
                @delete-conversation="deleteConversation"
            />

            <!-- Zone de chat -->
            <div class="flex-1 flex flex-col bg-gray-900">
                <!-- Header -->
                <header class="flex items-center justify-between px-4 py-3 border-b border-gray-700 bg-gray-800/50">
                    <h1 class="text-lg font-semibold text-gray-200">
                        {{ currentConversation?.title || '🎲 Nouvelle aventure' }}
                    </h1>
                    
                    <ModelSelector
                        :models="models"
                        :model-value="selectedModel"
                        :disabled="isLoading"
                        @update:model-value="updateModel"
                    />
                </header>

                <!-- Messages -->
                <div ref="messagesContainer" class="flex-1 overflow-y-auto">
                    <!-- Message de bienvenue -->
                    <div v-if="displayMessages.length === 0" class="flex flex-col items-center justify-center h-full text-center px-4">
                        <div class="text-6xl mb-4">🎲</div>
                        <h2 class="text-2xl font-bold text-purple-400 mb-2">Bienvenue, aventurier !</h2>
                        <p class="text-gray-400 max-w-md mb-6">
                            Je suis votre Maître de Jeu personnel. Décrivez-moi l'aventure que vous souhaitez vivre !
                        </p>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <button 
                                @click="userMessage = 'Je veux jouer une aventure heroic fantasy avec des donjons et des dragons !'"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
                            >
                                🐉 Heroic Fantasy
                            </button>
                            <button 
                                @click="userMessage = 'Lance-moi dans une enquête mystérieuse style Cthulhu !'"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
                            >
                                🐙 Horreur cosmique
                            </button>
                            <button 
                                @click="userMessage = 'Je veux explorer un univers cyberpunk dystopique !'"
                                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition-colors"
                            >
                                🌃 Cyberpunk
                            </button>
                        </div>
                    </div>

                    <!-- Liste des messages -->
                    <div v-else>
                        <MessageBubble
                            v-for="msg in displayMessages"
                            :key="msg.id"
                            :role="msg.role"
                            :content="msg.content"
                            :is-streaming="msg.id === -1 && isStreaming"
                        />
                    </div>
                </div>

                <!-- Zone de saisie -->
                <div class="border-t border-gray-700 p-4 bg-gray-800/50">
                    <form @submit.prevent="sendMessage" class="flex gap-3">
                        <input
                            v-model="userMessage"
                            type="text"
                            placeholder="Décrivez votre action..."
                            class="flex-1 bg-gray-700 border-gray-600 text-gray-200 rounded-lg px-4 py-3 focus:ring-purple-500 focus:border-purple-500 placeholder-gray-400"
                            :disabled="isLoading"
                        />
                        <button
                            type="submit"
                            :disabled="isLoading || !userMessage.trim()"
                            class="bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2"
                        >
                            <span v-if="isLoading" class="animate-spin">⏳</span>
                            <span v-else>🎲</span>
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>