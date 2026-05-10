<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
defineProps({ materials: Array });
</script>
<template><AppLayout><template #title>Материалы</template><div class="mb-4 flex justify-end"><Link :href="route('app.materials.create')" class="rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white">+ Материал</Link></div><div class="grid gap-3 md:grid-cols-2"><Card v-for="material in materials" :key="material.id"><Link :href="route('app.materials.show', material.id)" class="font-semibold">{{ material.name }}</Link><p class="text-sm text-slate-500">Остаток: {{ material.current_stock }} {{ material.unit?.short_name }}</p></Card></div></AppLayout></template>
