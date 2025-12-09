import { ref, watch, onMounted } from 'vue'

const isDark = ref(true)

export function useTheme() {
    
    function initTheme() {
        // Récupère la préférence sauvegardée ou utilise le thème sombre par défaut
        const saved = localStorage.getItem('theme')
        if (saved) {
            isDark.value = saved === 'dark'
        } else {
            // Par défaut : sombre (c'est le thème de GameMaster)
            isDark.value = true
        }
        applyTheme()
    }

    function applyTheme() {
        if (isDark.value) {
            document.documentElement.classList.add('dark')
            document.documentElement.classList.remove('light')
        } else {
            document.documentElement.classList.add('light')
            document.documentElement.classList.remove('dark')
        }
    }

    function toggleTheme() {
        isDark.value = !isDark.value
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
        applyTheme()
    }

    // Initialiser au montage
    onMounted(() => {
        initTheme()
    })

    return {
        isDark,
        toggleTheme,
        initTheme,
    }
}