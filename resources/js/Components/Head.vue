<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

interface Props {
    title?: string
    description?: string
    ogImage?: string
    canonical?: string
}

withDefaults(defineProps<Props>(), {
    title: 'GameMaster - Votre Maître de Jeu IA',
    description: 'Vivez des aventures épiques avec GameMaster, votre Maître de Jeu personnel propulsé par l\'IA. Heroic fantasy, horreur cosmique, cyberpunk et plus encore.',
    ogImage: '/images/og-image.png',
    canonical: '',
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:title" :content="title">
        <meta property="og:description" :content="description">
        <meta property="og:image" :content="ogImage">
        <meta property="og:site_name" content="GameMaster">
        <meta property="og:locale" content="fr_FR">
        
        <!-- Twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="title">
        <meta name="twitter:description" :content="description">
        <meta name="twitter:image" :content="ogImage">
        
        <!-- Canonical -->
        <link v-if="canonical" rel="canonical" :href="canonical">
        
        <!-- Autres meta SEO -->
        <meta name="robots" content="index, follow">
        <meta name="author" content="GameMaster">
        <meta name="theme-color" content="#7c3aed">
    </Head>
</template>