<template>
    <div class="flex justify-center mt-6 space-x-1">
        <!-- Назад -->
        <button
            :disabled="!data.prev_page_url"
            @click="$emit('paginate', data.current_page - 1)"
            class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded disabled:opacity-50"
        >
            <=
        </button>

        <!-- Номера страниц -->
        <template v-for="page in pages" :key="page">
            <button
                @click="$emit('paginate', page)"
                :class="[
          'px-3 py-1 font-semibold',
          page === data.current_page
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-200 hover:bg-gray-300 text-gray-800'
        ]"
                class="rounded"
            >
                {{ page }}
            </button>
        </template>

        <!-- Вперёд -->
        <button
            :disabled="!data.next_page_url"
            @click="$emit('paginate', data.current_page + 1)"
            class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded disabled:opacity-50"
        >
            =>
        </button>
    </div>
</template>

<script setup>

import {computed} from "vue";
const props = defineProps({
    data: Object, // Сюда приходит объект пагинации типа posts
});

// Создаём массив страниц
const pages = computed(() => {
    if (!props.data) return [];
    const pagesArray = [];
    for (let i = 1; i <= props.data.last_page; i++) {
        pagesArray.push(i);
    }
    return pagesArray;
});
</script>
