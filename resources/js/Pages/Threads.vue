<template>
    <div>
        <!-- Заголовок доски -->
        <h1 class="text-2xl font-bold mb-6">
            {{ board.name }} - {{ board.slug }}
        </h1>

        <!-- Кнопки сортировки -->
        <div class="mb-6 flex justify-between items-center">
            <div class="space-x-2">
                <button
                    @click="changeSort('latest')"
                    :class="['sort-button', sort === 'latest' ? 'sort-button--active' : '']"
                >
                    {{ currentTexts.new }}
                </button>
                <button
                    @click="changeSort('popular')"
                    :class="['sort-button', sort === 'popular' ? 'sort-button--active' : '']"
                >
                    {{ currentTexts.popular }}
                </button>
            </div>
        </div>

        <div class="text-center mt-10 mb-10">
            <button @click="showForm = true"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded shadow">
                + {{ currentTexts.createThread }}
            </button>
        </div>

        <div v-if="showForm"
             :class="theme === 'dark' ? 'bg-gray-900 text-white border border-gray-700' : 'bg-white text-gray-900 border border-gray-300'"
             class="max-w-xl mx-auto mt-8 p-6 rounded-xl shadow-md">

            <div class="relative">
                <button @click="closeForm"
                        class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl">
                    &times;
                </button>
            </div>

            <h2 class="text-2xl font-semibold mb-6 text-center">{{ currentTexts.createNewThread }}</h2>

            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-5">
                <!-- Название треда -->
                <div>
                    <label for="title" class="block text-sm font-medium mb-1"
                           :class="theme === 'dark' ? 'text-gray-300' : 'text-gray-700'">
                        {{ currentTexts.threadTitle }}
                    </label>
                    <input type="text" v-model="title"
                           class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                           :class="theme === 'dark'
                        ? 'bg-gray-800 border-gray-600 text-white placeholder-gray-400'
                        : 'bg-white border-gray-300 text-gray-900 placeholder-gray-400'">
                </div>

                <!-- Загрузка файлов -->
                <div
                    @dragover.prevent="isDropping = true"
                    @dragleave.prevent="isDropping = false"
                    @drop.prevent="handleDrop"
                    :class="isDropping ? 'border-indigo-500 bg-indigo-50 dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'"
                    class="w-full p-6 border-2 border-dashed rounded-lg text-center transition">

                    <p class="text-sm mb-2 text-gray-500 dark:text-gray-400">
                        {{ currentTexts.dropFiles }}
                    </p>

                    <input id="media" type="file" ref="media" multiple class="hidden" @change="handleFileChange">
                    <label for="media"
                           class="inline-block px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded hover:bg-indigo-700 cursor-pointer">
                        {{ currentTexts.chooseFiles }}
                    </label>

                    <div v-if="mediaFiles.length > 0">
                        <ul class="mt-3 text-sm text-gray-400">
                            <li v-for="file in mediaFiles" :key="file.name">📎 {{ file.name }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Кнопка отправки -->
                <button type="submit"
                        :disabled="isSubmitting"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isSubmitting ? '⏳ Отправка...' : currentTexts.submitThread }}
                </button>

            </form>
        </div>

        <!-- Список тредов -->
        <div v-if="threads && threads.length" class="grid">
            <div v-for="thread in threads" :key="thread.id" class="board-card">
                <div class="flex justify-between items-center">
                    <Link :href="route('posts.index',{thread_id: thread.id})"
                          class="text-xl font-semibold text-gray-800 dark:text-white hover:underline">
                        {{ thread.title || currentTexts.noTitle }}
                    </Link>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ thread.posts_count }} {{ currentTexts.posts }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Сообщение если нет тредов -->
        <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-8">
            {{ currentTexts.noThreads }}
        </div>

    </div>

</template>


<script setup>
import AppLayout from "../Layouts/AppLayout.vue";
import { inject, ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    board: Object,
    threads: Array,
    sort: String,
});

const showForm = ref(false);
const isDropping = ref(false);
const title = ref('');
const mediaFiles = ref([]);
const theme = inject('theme');

const closeForm = () => {
    showForm.value = false;
    title.value = '';
    mediaFiles.value = [];
};

const isSubmitting = ref(false); // для блокировки кнопки во время отправки

const submitForm = () => {
    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('board_id', props.board.id);
    mediaFiles.value.forEach(file => formData.append('media[]', file));

    isSubmitting.value = true;

    router.post(route('threads.store'), formData, {
        forceFormData: true,
        onFinish: () => {
            isSubmitting.value = false;
            closeForm();
        },
    });
};


const handleFileChange = (event) => {
    mediaFiles.value = Array.from(event.target.files);
};

const handleDrop = (event) => {
    isDropping.value = false;
    mediaFiles.value = Array.from(event.dataTransfer.files);
};

const language = inject('language');
if (!language) {
    throw new Error('language not provided!');
}

const texts = {
    ru: {
        new: 'Новые',
        popular: 'Популярные',
        posts: 'постов',
        noTitle: '(без названия)',
        noThreads: 'Нет тредов на доске.',
        createThread: 'Создать тред',
        createNewThread: '📝 Создать новый тред',
        threadTitle: 'Название треда',
        dropFiles: 'Перетащите файлы сюда или кликните',
        chooseFiles: 'Выбрать файлы',
        submitThread: '🚀 Создать тред',
    },
    en: {
        new: 'New',
        popular: 'Popular',
        posts: 'posts',
        noTitle: '(untitled)',
        noThreads: 'No threads yet.',
        createThread: 'Create Thread',
        createNewThread: '📝 Create New Thread',
        threadTitle: 'Thread Title',
        dropFiles: 'Drag & drop files here or click',
        chooseFiles: 'Choose Files',
        submitThread: '🚀 Create Thread',
    },
};

const currentTexts = computed(() => texts[language.value]);

function changeSort(sort) {
    router.get(`/board/${props.board.slug}`, { sort }, { preserveState: true });
}
</script>


<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}


</style>
