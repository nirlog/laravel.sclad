<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
defineProps({ contractors: Array });
</script>
<template><AppLayout><template #title>Исполнители</template><div class="mb-4 flex justify-end"><Link :href="route('app.contractors.create')" class="rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white">+ Исполнитель</Link></div><div class="grid gap-3 md:grid-cols-2"><Card v-for="contractor in contractors" :key="contractor.id"><Link :href="route('app.contractors.show', contractor.id)" class="font-semibold">{{ contractor.name }}</Link><p class="text-sm text-slate-500">{{ contractor.phone || 'Телефон не указан' }}</p></Card></div></AppLayout></template>
