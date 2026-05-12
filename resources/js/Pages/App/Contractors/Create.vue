<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
const form = useForm({ name: '', phone: '', email: '', comment: '', is_active: true });
function submit() { form.post(route('app.contractors.store')); }
</script>
<template><AppLayout><template #title>Новый исполнитель</template><form @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.name" placeholder="Имя" class="rounded-xl border-slate-300" /><input v-model="form.phone" placeholder="Телефон" class="rounded-xl border-slate-300" /><input v-model="form.email" placeholder="Email" class="rounded-xl border-slate-300" /><textarea v-model="form.comment" placeholder="Комментарий" class="rounded-xl border-slate-300 md:col-span-2" /></Card><button class="mt-4 w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Сохранить</button></form></AppLayout></template>
