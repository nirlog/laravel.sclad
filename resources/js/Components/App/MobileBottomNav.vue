<script setup>
import { Link } from '@inertiajs/vue3';
const items = [
    ['Главная', 'app.dashboard'],
    ['Операции', 'app.operations.index'],
    ['+ Добавить', 'app.operations.create'],
    ['Склад', 'app.inventory.index'],
    ['Аналитика', 'app.analytics.index'],
];
</script>

<template>
    <nav class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-slate-200 bg-white/95 text-xs shadow-lg backdrop-blur md:hidden">
        <Link v-for="item in items" :key="item[1]" :href="route(item[1])" class="px-2 py-3 text-center font-medium text-slate-600" :class="{'text-blue-700': item[0].includes('+')}">
            {{ item[0] }}
        </Link>
    </nav>
</template>
