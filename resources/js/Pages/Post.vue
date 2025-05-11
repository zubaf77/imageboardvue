<template>
    <div class="container mx-auto px-4 py-8">
        <!-- Заголовок треда -->
        <h2 class="relative mb-6 pl-4 text-2xl font-bold border-l-4 border-indigo-500">
            {{ thread?.title || 'Тред' }}
        </h2>

        <!-- Автор треда -->
        <div class="mb-6 p-4 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 rounded shadow">
            <strong class="block text-base font-semibold mb-1">Автор треда:</strong>
            <span v-if="thread.user">
                {{ thread.user.name }}
                <span v-if="['owner', 'admin', 'moderator'].includes(thread.user.role)" class="text-sm font-normal text-purple-600 dark:text-purple-300">
                    ({{ thread.user.role }})
                </span>
            </span>
            <span v-else>Anonymous</span>
        </div>

        <!-- Вложения треда -->
        <div v-if="thread?.attachments?.length" class="mb-6">
            <h3 class="text-2xl font-bold mb-6">{{ currentMessages.attachmentsTitle }}</h3>
            <div class="flex flex-wrap gap-4 mt-4">
                <div v-for="attachment in thread.attachments" :key="attachment.id">
                    <AttachmentPreview :attachment="attachment" />
                </div>
            </div>
        </div>

        <!-- Сообщение об ошибке -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ errorMessage }}
        </div>

        <!-- Форма создания поста -->
        <div class="mb-8">
            <div v-if="parentPost" class="mb-4 p-3 bg-blue-100 text-blue-800 rounded text-sm">
                {{ currentMessages.replyToPost }}{{ parentPost.id }}
            </div>

            <div v-if="successMessage" class="mb-4 p-3 bg-green-100 text-green-800 rounded text-sm">
                {{ successMessage }}
            </div>

            <form @submit.prevent="store" class="space-y-4">
                <textarea v-model="content"
                          rows="3"
                          class="w-full p-3 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                          :placeholder="currentMessages.writeMessage"
                ></textarea>

                <div class="border-2 border-dashed p-4 rounded text-center relative">
                    <input id="media" type="file" @change="handleFileChange" multiple
                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" />
                    <label for="media" class="block text-gray-500 dark:text-gray-400 z-0 relative">
                        {{ mediaFiles.length ? `${mediaFiles.length} ${currentMessages.chooseFiles}` : currentMessages.chooseFiles }}
                    </label>
                </div>

                <button type="submit" :disabled="isSubmitting"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow disabled:opacity-50">
                    {{ isSubmitting ? currentMessages.sending : currentMessages.createPost }}
                </button>
            </form>
        </div>

        <!-- Список постов -->
        <div class="space-y-6">
            <div v-for="post in posts.data" :key="post.id" class="board-card">
                <!-- Заголовок поста -->
                <div class="flex justify-between items-center mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    #{{ post.id }}
                    <span v-if="post.user">
                        <template v-if="['owner', 'admin', 'moderator'].includes(post.user.role)">
                            {{ post.user.name }}
                            <span class="text-xs text-gray-500">({{ post.user.role }})</span>
                        </template>
                        <template v-else>
                            Anonymous
                        </template>
                    </span>
                    <span v-else>Anonymous</span>
                </div>

                <!-- Ответ на пост -->
                <div v-if="post.parent_post_id" class="text-xs text-indigo-500 mb-2">
                    >>#{{ post.parent_post_id }}
                </div>

                <!-- Текст поста -->
                <p class="text-sm text-gray-800 dark:text-gray-200">{{ post.content }}</p>

                <!-- Вложения поста -->
                <div v-if="post.attachments?.length" class="flex flex-wrap gap-3 mt-4">
                    <div v-for="attachment in post.attachments" :key="attachment.id">
                        <AttachmentPreview :attachment="attachment" />
                    </div>
                </div>

                <!-- Кнопка ответа -->
                <button @click="replyToPost(post)"
                        class="mt-4 bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded text-sm">
                    Ответить
                </button>

                <!-- Ответы на пост -->
                <div v-for="reply in post.replies" :key="reply.id" class="ml-6 mt-4 board-card text-xs">
                    <div class="font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        #{{ reply.id }}
                        <span v-if="reply.parent_post_id" class="ml-1 text-indigo-500">>>#{{ reply.parent_post_id }}</span>
                        <span v-if="reply.user">
                            <template v-if="['owner', 'admin', 'moderator'].includes(reply.user.role)">
                                {{ reply.user.name }}
                                <span class="text-xs text-gray-500">({{ reply.user.role }})</span>
                            </template>
                            <template v-else>
                                Anonymous
                            </template>
                        </span>
                        <span v-else>Anonymous</span>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300">{{ reply.content }}</p>

                    <div v-if="reply.attachments?.length" class="flex flex-wrap gap-3 mt-2">
                        <div v-for="attachment in reply.attachments" :key="attachment.id">
                            <AttachmentPreview :attachment="attachment" small />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пагинация -->
        <div class="mt-8">
            <Pagination :data="posts" @paginate="loadMorePosts" />
        </div>
    </div>
</template>


<script setup>
import { ref, computed, nextTick, inject } from 'vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import AttachmentPreview from '@/Components/AttachmentPreview.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    board: Object,
    thread: Object,
    posts: Object,
});

const content = ref('');
const mediaFiles = ref([]);
const isSubmitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const parentPost = ref(null);

// Локализация
const language = inject('language');

const messages = {
    ru: {
        replyToPost: 'Вы отвечаете на пост №',
        successPost: 'Пост успешно добавлен!',
        errorPost: 'Ошибка при создании поста.',
        chooseFiles: 'Выбрать файлы',
        threadNotFound: 'Тред не найден.',
        attachmentsTitle: 'Вложения треда:',
        createPost: '🚀 Создать пост',
        sending: '⏳ Отправка...',
        writeMessage: 'Введите сообщение...',
    },
    en: {
        replyToPost: 'You are replying to post #',
        successPost: 'Post successfully created!',
        errorPost: 'Error creating post.',
        chooseFiles: 'Choose files',
        threadNotFound: 'Thread not found.',
        attachmentsTitle: 'Thread attachments:',
        createPost: '🚀 Create Post',
        sending: '⏳ Sending...',
        writeMessage: 'Enter your message...',
    }
};

const currentMessages = computed(() => messages[language.value]);

const handleFileChange = (e) => {
    mediaFiles.value = Array.from(e.target.files);
};

const store = async () => {
    if (!props.thread?.id) {
        errorMessage.value = currentMessages.value.threadNotFound;
        return;
    }

    const formData = new FormData();
    formData.append('content', content.value);
    formData.append('thread_id', props.thread.id);
    if (parentPost.value) {
        formData.append('parent_post_id', parentPost.value.id);
    }
    mediaFiles.value.forEach((file) => formData.append('media[]', file));

    isSubmitting.value = true;

    await router.post(route('posts.store'), formData, {
        forceFormData: true,
        onFinish: () => {
            content.value = '';
            mediaFiles.value = [];
            parentPost.value = null;
            successMessage.value = currentMessages.value.successPost;
            errorMessage.value = '';
            isSubmitting.value = false;

            // nextTick(() => {
            //     const lastPost = document.querySelector('.space-y-6 > div:last-child');
            //     lastPost?.scrollIntoView({ behavior: 'smooth' });
            // });
        },
        onError: () => {
            errorMessage.value = currentMessages.value.errorPost;
            successMessage.value = '';
            isSubmitting.value = false;
        },
    });
};

const replyToPost = (post) => {
    parentPost.value = post;
};

const loadMorePosts = (page) => {
    router.get(route('posts.index', props.thread.id), { page }, { preserveState: true });
};
</script>
