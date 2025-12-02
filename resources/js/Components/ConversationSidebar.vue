<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface Conversation {
    id: number
    title: string
    updated_at: string
}

interface Props {
    conversations: Conversation[]
    currentId?: number | null
}

const emit = defineEmits<{
    newConversation: []
    deleteConversation: [id: number]
}>()

defineProps<Props>()

function formatDate(dateString: string): string {
    const date = new Date(dateString)
    const now = new Date()
    const diffMs = now.getTime() - date.getTime()
    const diffMins = Math.floor(diffMs / 60000)
    const diffHours = Math.floor(diffMs / 3600000)
    const diffDays = Math.floor(diffMs / 86400000)

    if (diffMins < 1) return "À l'instant"
    if (diffMins < 60) return `Il y a ${diffMins} min`
    if (diffHours < 24) return `Il y a ${diffHours}h`
    if (diffDays < 7) return `Il y a ${diffDays}j`
    
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

function confirmDelete(id: number, event: Event) {
    event.preventDefault()
    event.stopPropagation()
    
    if (confirm('Supprimer cette conversation ?')) {
        emit('deleteConversation', id)
    }
}
</script>

<template>
    <aside class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col h-full">
        <!-- Bouton nouvelle conversation -->
        <div class="p-4 border-b border-gray-700">
            <button 
                @click="emit('newConversation')"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors"
            >
                <span class="text-xl">✨</span>
                Nouvelle aventure
            </button>
        </div>

        <!-- Liste des conversations -->
        <nav class="flex-1 overflow-y-auto p-2 space-y-1">
            <div v-if="conversations.length === 0" class="text-gray-500 text-center py-8 px-4">
                <p class="text-4xl mb-2">🎭</p>
                <p>Aucune aventure</p>
                <p class="text-sm">Lancez votre première quête !</p>
            </div>

            <Link
                v-for="conv in conversations"
                :key="conv.id"
                :href="route('chat.show', conv.id)"
                class="group flex items-center gap-2 p-3 rounded-lg transition-colors"
                :class="conv.id === currentId 
                    ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' 
                    : 'text-gray-300 hover:bg-gray-700/50'"
            >
                <span class="text-lg flex-shrink-0">📜</span>
                
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm font-medium">{{ conv.title }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(conv.updated_at) }}</p>
                </div>

                <button 
                    @click="confirmDelete(conv.id, $event)"
                    class="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-red-400 transition-opacity p-1"
                    title="Supprimer"
                >
                    🗑️
                </button>
            </Link>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-700 text-xs text-gray-500 text-center">
            🎲 GameMaster AI
        </div>
    </aside>
</template>