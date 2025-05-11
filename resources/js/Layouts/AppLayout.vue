<script setup>
import { ref, provide, computed } from 'vue';
import {Link} from "@inertiajs/vue3";

// Создаём язык
const language = ref(localStorage.getItem('language') || 'ru');

const theme = ref(localStorage.getItem('theme') || 'light');


const toggleLanguage = () => {
    language.value = language.value === 'ru' ? 'en' : 'ru';
    localStorage.setItem('language', language.value);
};

// Делаем provide
provide('language', language);
provide('toggleLanguage', toggleLanguage);
provide('theme', theme)

const texts = {
    ru: { home: 'Главная', footer: 'Все права защищены.' },
    en: { home: 'Home', footer: 'All rights reserved.' },
};

const currentTexts = computed(() => texts[language.value]);

const toggleTheme = () => {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    if (isDark) {
        html.classList.remove('dark');
        theme.value = 'light'; // ⬅️ ЭТО важно
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        theme.value = 'dark'; // ⬅️ ЭТО важно
        localStorage.setItem('theme', 'dark');
    }
};


if (localStorage.getItem('theme') === 'dark') {
    document.documentElement.classList.add('dark');
}
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <header>
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <Link :href="route('board.index')" class="text-2xl font-bold">Imageboard</Link>
                <Transition name="fade" mode="out-in">
                <nav class="space-x-4">
                    <Link :href="route('board.index')">{{ currentTexts.home }}</Link>
                </nav>
                </Transition>
                <div class="flex items-center space-x-4">
                    <button @click="toggleTheme" class="px-3 py-1 border rounded text-sm">🌗</button>
                    <button @click="toggleLanguage" class="px-3 py-1 border rounded text-sm">
                        {{ language === 'ru' ? '🇷🇺' : '🇺🇸' }}
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 container mx-auto px-4 py-8">
            <slot />
        </main>


        <Transition name="fade" mode="out-in">
        <footer class="text-center py-4 text-sm mt-auto">
            &copy; {{ new Date().getFullYear() }} Imageboard. {{ currentTexts.footer }}
        </footer>
        </Transition>
    </div>
</template>
<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.4s ease, transform 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
.fade-enter-to, .fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>

