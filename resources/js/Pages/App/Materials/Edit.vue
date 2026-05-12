<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
const props = defineProps({ material: Object, units: Array });
const form = useForm({ name: props.material.name, unit_id: props.material.unit_id, sku: props.material.sku || '', description: props.material.description || '', is_active: props.material.is_active });
function submit() { form.patch(route('app.materials.update', props.material.id)); }
</script>
<template><AppLayout><template #title>Редактировать материал</template><form @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.name" class="rounded-xl border-slate-300" /><select v-model="form.unit_id" class="rounded-xl border-slate-300"><option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.short_name }}</option></select><input v-model="form.sku" class="rounded-xl border-slate-300" /><textarea v-model="form.description" class="rounded-xl border-slate-300 md:col-span-2" /></Card><button class="mt-4 w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Сохранить</button></form></AppLayout></template>
