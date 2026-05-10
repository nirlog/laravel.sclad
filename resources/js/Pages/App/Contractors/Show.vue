<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
defineProps({ contractor: Object, totals: Object, services: Array });
function destroy(contractor) { if (confirm('Удалить исполнителя?')) router.delete(route('app.contractors.destroy', contractor.id)); }
</script>
<template><AppLayout><template #title>{{ contractor.name }}</template><Card><h2 class="text-xl font-semibold">{{ contractor.name }}</h2><p class="text-sm text-slate-500">{{ contractor.phone }} {{ contractor.email }}</p><div class="mt-4 grid gap-3 md:grid-cols-3"><div>Начислено: <b><MoneyAmount :value="totals.total_amount" /></b></div><div>Оплачено: <b><MoneyAmount :value="totals.paid_amount" /></b></div><div>Долг: <b><MoneyAmount :value="totals.debt" /></b></div></div><div class="mt-5 flex gap-2"><Link :href="route('app.contractors.edit', contractor.id)" class="rounded-xl bg-slate-900 px-4 py-2 text-white">Редактировать</Link><button class="rounded-xl bg-rose-600 px-4 py-2 text-white" @click="destroy(contractor)">Удалить</button></div></Card></AppLayout></template>
