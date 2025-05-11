<script setup>
// Импортируем необходимые библиотеки
import {ref, inject, onMounted} from 'vue';

// Определяем переменные с помощью реактивных данных
const message = ref('Привет, Vue!');
const count = ref(0);
const increment = () => count.value++;
const decrement = () => count.value--;

</script>

<template>
    <div class="container">
        <h1>{{ message }}</h1>
        <p>Вы нажали {{ count }} раз.</p>

        <div>
            <!-- Кнопки для увеличения и уменьшения значения -->
            <button @click="increment" class="btn">Увеличить</button>
            <button @click="decrement" class="btn">Уменьшить</button>
        </div>
    </div>
</template>

<style scoped>
.container {
    text-align: center;
    padding: 20px;
}

h1 {
    color: #3490dc;
}

p {
    font-size: 1.2em;
    margin-bottom: 20px;
}

.btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 1em;
    cursor: pointer;
    margin: 5px;
    border-radius: 5px;
}

.btn:hover {
    background-color: #45a049;
}
</style>
