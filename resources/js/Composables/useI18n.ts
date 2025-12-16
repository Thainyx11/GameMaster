import { ref, computed } from 'vue'

type Locale = 'fr' | 'en'

const currentLocale = ref<Locale>('fr')

const translations: Record<Locale, Record<string, string>> = {
    fr: {
        // Navigation
        'nav.chat': '💬 Chat',
        'nav.instructions': '⚙️ Instructions',
        'nav.profile': 'Profil',
        'nav.logout': 'Déconnexion',
        'nav.login': 'Connexion',
        'nav.register': "S'inscrire",
        
        // Chat
        'chat.newAdventure': '✨ Nouvelle aventure',
        'chat.welcome': 'Bienvenue, aventurier !',
        'chat.welcomeSubtitle': 'Je suis votre Maître de Jeu personnel. Décrivez-moi l\'aventure que vous souhaitez vivre !',
        'chat.placeholder': 'Décrivez votre action ou tapez /roll 1d20...',
        'chat.send': 'Envoyer',
        'chat.you': 'Vous',
        'chat.gameMaster': 'Maître du Jeu',
        'chat.noConversation': 'Aucune aventure',
        'chat.startQuest': 'Lancez votre première quête !',
        'chat.regenerate': 'Regénérer la réponse',
        
        // Theme
        'theme.light': 'Mode clair',
        'theme.dark': 'Mode sombre',
        
        // Language
        'language.fr': '🇫🇷 Français',
        'language.en': '🇬🇧 English',
    },
    en: {
        // Navigation
        'nav.chat': '💬 Chat',
        'nav.instructions': '⚙️ Instructions',
        'nav.profile': 'Profile',
        'nav.logout': 'Logout',
        'nav.login': 'Login',
        'nav.register': 'Sign up',
        
        // Chat
        'chat.newAdventure': '✨ New Adventure',
        'chat.welcome': 'Welcome, adventurer!',
        'chat.welcomeSubtitle': 'I am your personal Game Master. Tell me about the adventure you want to experience!',
        'chat.placeholder': 'Describe your action or type /roll 1d20...',
        'chat.send': 'Send',
        'chat.you': 'You',
        'chat.gameMaster': 'Game Master',
        'chat.noConversation': 'No adventure',
        'chat.startQuest': 'Start your first quest!',
        'chat.regenerate': 'Regenerate response',
        
        // Theme
        'theme.light': 'Light mode',
        'theme.dark': 'Dark mode',
        
        // Language
        'language.fr': '🇫🇷 Français',
        'language.en': '🇬🇧 English',
    },
}

export function useI18n() {
    function initLocale() {
        const saved = localStorage.getItem('locale') as Locale | null
        if (saved && (saved === 'fr' || saved === 'en')) {
            currentLocale.value = saved
        } else {
            const browserLang = navigator.language.substring(0, 2)
            currentLocale.value = browserLang === 'en' ? 'en' : 'fr'
        }
    }

    function setLocale(locale: Locale) {
        currentLocale.value = locale
        localStorage.setItem('locale', locale)
    }

    function t(key: string): string {
        return translations[currentLocale.value][key] || key
    }

    const locale = computed(() => currentLocale.value)

    return {
        locale,
        currentLocale,
        setLocale,
        initLocale,
        t,
    }
}