<template>
    <div>
        <template v-if="attachment.file_type.includes('image')">
            <a :href="getStorageUrl(attachment.file_path)" target="_blank">
                <img :src="getStorageUrl(attachment.file_path)"
                     :class="small ? 'w-20 h-20' : 'w-32 h-32'"
                     class="object-cover rounded hover:scale-110 transition" />
            </a>
        </template>
        <template v-else-if="attachment.file_type.includes('pdf')">
            <a :href="getStorageUrl(attachment.file_path)" target="_blank">
                <button class="bg-blue-500 text-white py-1 px-3 rounded text-xs">PDF</button>
            </a>
        </template>
        <template v-else>
            <a :href="getStorageUrl(attachment.file_path)" target="_blank">
                <button class="bg-gray-500 text-white py-1 px-3 rounded text-xs">Файл</button>
            </a>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    attachment: Object,
    small: Boolean,
});

const getStorageUrl = (path) => `/storage/${path}`;
</script>
