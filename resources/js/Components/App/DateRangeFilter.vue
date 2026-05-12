<script setup>
defineProps({ filters: Object });
defineEmits(['submit']);
</script>

<template>
    <form class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm md:grid-cols-3" @submit.prevent="$emit('submit', filters)">
        <input v-model="filters.date_from" type="date" class="rounded-xl border-slate-300" />
        <input v-model="filters.date_to" type="date" class="rounded-xl border-slate-300" />
        <button class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">Фильтровать</button>
    </form>
</template>
