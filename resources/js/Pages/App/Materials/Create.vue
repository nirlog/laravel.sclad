<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
defineProps({ units: Array });
const form = useForm({ name: '', unit_id: '', sku: '', description: '', is_active: true });
function submit() { form.post(route('app.materials.store')); }
</script>
<template><AppLayout><template #title>Новый материал</template><form @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.name" placeholder="Название" class="rounded-xl border-slate-300" /><select v-model="form.unit_id" class="rounded-xl border-slate-300"><option value="">Единица</option><option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.short_name }}</option></select><input v-model="form.sku" placeholder="Артикул" class="rounded-xl border-slate-300" /><textarea v-model="form.description" placeholder="Описание" class="rounded-xl border-slate-300 md:col-span-2" /></Card><button class="mt-4 w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Сохранить</button></form></AppLayout></template>
