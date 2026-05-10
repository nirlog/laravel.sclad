<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="route('app.operations.create')" class="fixed bottom-20 right-5 z-40 rounded-full bg-blue-600 px-5 py-4 text-sm font-semibold text-white shadow-xl md:hidden">
        + Добавить
    </Link>
</template>
