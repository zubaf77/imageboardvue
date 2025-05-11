<script setup>
import { inject, computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';
import {Link} from "@inertiajs/vue3";

const props = defineProps(['boards']);
const language = inject('language');

if (!language) {
    throw new Error('language not provided!');
}

const texts = {
    ru: {
        welcome: 'Добро пожаловать на Imageboard',
        selectBoard: 'Выберите раздел, чтобы просматривать треды или создать новый.',
    },
    en: {
        welcome: 'Welcome to Imageboard',
        selectBoard: 'Select a board to browse threads or create a new one.',
    },
};

const currentTexts = computed(() => texts[language.value]);

defineOptions({
    layout: AppLayout,
});
</script>


<template>
    <div>
        <Transition name="fade" mode="out-in">
            <h1 class="text-4xl font-bold mb-4">
                {{ currentTexts.welcome }}
            </h1>
        </Transition>

        <Transition name="fade" mode="out-in">
            <p class="text-xl font-bold">
                {{ currentTexts.selectBoard }}
            </p>
        </Transition>



        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6">
            <div v-for="board in boards" :key="board.slug" class="board-card">
                <Link :href="route('board.show', { slug: board.slug })" class="board-title">
                    /{{ board.slug }}/
                </Link>
                <p class="board-description text-gray-800 dark:text-white mt-2">{{ board.name }}</p>
            </div>
        </div>
    </div>
</template>
<style>

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease; /* вместо 0.3s */
}

</style>
