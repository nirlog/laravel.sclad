<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
defineProps({ material: Object, stock: [String, Number], averageCost: [String, Number], movements: Object });
function destroy(material) { if (confirm('Удалить материал?')) router.delete(route('app.materials.destroy', material.id)); }
</script>
<template><AppLayout><template #title>{{ material.name }}</template><Card><h2 class="text-xl font-semibold">{{ material.name }}</h2><p class="text-sm text-slate-500">Единица: {{ material.unit?.short_name }}</p><p class="mt-4 text-2xl font-bold">{{ stock }} {{ material.unit?.short_name }}</p><p class="text-sm text-slate-500">Средняя цена: <MoneyAmount :value="averageCost" /></p><div class="mt-5 flex gap-2"><Link :href="route('app.materials.edit', material.id)" class="rounded-xl bg-slate-900 px-4 py-2 text-white">Редактировать</Link><button class="rounded-xl bg-rose-600 px-4 py-2 text-white" @click="destroy(material)">Удалить</button></div></Card></AppLayout></template>
