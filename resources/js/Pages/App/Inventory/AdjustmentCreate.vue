<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
const props = defineProps({ materials: Array });
const form = useForm({ material_id: '', date: new Date().toISOString().slice(0, 10), quantity: 0, unit_price: null, amount: null, comment: '' });
function submit() { form.post(route('app.inventory.adjustments.store')); }
</script>
<template><AppLayout><template #title>Корректировка склада</template><form @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.date" type="date" class="rounded-xl border-slate-300" /><select v-model="form.material_id" class="rounded-xl border-slate-300"><option value="">Материал</option><option v-for="material in materials" :key="material.id" :value="material.id">{{ material.name }}</option></select><input v-model="form.quantity" type="number" step="0.001" class="rounded-xl border-slate-300" placeholder="Корректировка (+/-)" /><input v-model="form.unit_price" type="number" step="0.01" class="rounded-xl border-slate-300" placeholder="Цена" /><textarea v-model="form.comment" class="rounded-xl border-slate-300 md:col-span-2" placeholder="Комментарий" /></Card><button class="mt-4 w-full rounded-xl bg-purple-600 px-5 py-3 font-semibold text-white">Сохранить корректировку</button></form></AppLayout></template>
