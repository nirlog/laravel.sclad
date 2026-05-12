<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
const props = defineProps({ contractor: Object });
const form = useForm({ name: props.contractor.name, phone: props.contractor.phone || '', email: props.contractor.email || '', comment: props.contractor.comment || '', is_active: props.contractor.is_active });
function submit() { form.patch(route('app.contractors.update', props.contractor.id)); }
</script>
<template><AppLayout><template #title>Редактировать исполнителя</template><form @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.name" class="rounded-xl border-slate-300" /><input v-model="form.phone" class="rounded-xl border-slate-300" /><input v-model="form.email" class="rounded-xl border-slate-300" /><textarea v-model="form.comment" class="rounded-xl border-slate-300 md:col-span-2" /></Card><button class="mt-4 w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Сохранить</button></form></AppLayout></template>
