<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
    uploaded: [data: { path: string; url: string }]
    removed: []
}>()

const isUploading = ref(false)
const previewUrl = ref<string | null>(null)
const uploadedPath = ref<string | null>(null)
const error = ref<string | null>(null)
const fileInput = ref<HTMLInputElement>()

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

async function handleFileSelect(event: Event) {
    const input = event.target as HTMLInputElement
    const file = input.files?.[0]
    
    if (!file) return
    
    // Vérifier le type
    if (!file.type.startsWith('image/')) {
        error.value = 'Veuillez sélectionner une image'
        return
    }
    
    // Vérifier la taille (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        error.value = 'Image trop volumineuse (max 10MB)'
        return
    }
    
    error.value = null
    isUploading.value = true
    
    // Preview local
    previewUrl.value = URL.createObjectURL(file)
    
    // Upload
    const formData = new FormData()
    formData.append('image', file)
    
    try {
        const response = await fetch(route('upload.image'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            },
            body: formData,
        })
        
        const data = await response.json()
        
        if (data.success) {
            uploadedPath.value = data.path
            emit('uploaded', { path: data.path, url: data.url })
        } else {
            error.value = data.error || 'Erreur lors de l\'upload'
            previewUrl.value = null
        }
    } catch (e) {
        error.value = 'Erreur de connexion'
        previewUrl.value = null
    } finally {
        isUploading.value = false
    }
}

function removeImage() {
    previewUrl.value = null
    uploadedPath.value = null
    error.value = null
    if (fileInput.value) {
        fileInput.value.value = ''
    }
    emit('removed')
}

function triggerFileInput() {
    fileInput.value?.click()
}
</script>

<template>
    <div class="relative">
        <!-- Bouton d'upload -->
        <button
            v-if="!previewUrl"
            @click="triggerFileInput"
            :disabled="isUploading"
            class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-blue-400 transition-colors disabled:opacity-50"
            title="Joindre une image"
            aria-label="Joindre une image"
        >
            <svg v-if="!isUploading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span v-else class="animate-spin">⏳</span>
        </button>

        <!-- Preview de l'image -->
        <div v-else class="relative">
            <button
                @click="triggerFileInput"
                class="p-1 rounded-lg bg-gray-700 hover:bg-gray-600 transition-colors"
                title="Changer l'image"
            >
                <img 
                    :src="previewUrl" 
                    alt="Image jointe" 
                    class="w-10 h-10 object-cover rounded"
                />
            </button>
            <!-- Bouton supprimer -->
            <button
                @click="removeImage"
                class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center text-xs"
                title="Supprimer l'image"
            >
                ×
            </button>
        </div>

        <!-- Input file caché -->
        <input
            ref="fileInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleFileSelect"
        />

        <!-- Erreur -->
        <div 
            v-if="error" 
            class="absolute bottom-full left-0 mb-2 p-2 bg-red-900/90 text-red-200 text-xs rounded whitespace-nowrap"
        >
            {{ error }}
        </div>
    </div>
</template>