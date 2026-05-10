<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import PaymentStatusBadge from '@/Components/App/PaymentStatusBadge.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ service: Object });
function destroy(service) { if (confirm('Удалить услугу?')) router.delete(route('app.services.destroy', service.id)); }
</script>
<template><AppLayout><template #title>Услуга</template><Card><h2 class="text-xl font-semibold">{{ service.name }}</h2><p class="text-sm text-slate-500">{{ formatDate(service.date) }} · {{ service.contractor?.name || 'Без исполнителя' }}</p><PaymentStatusBadge class="mt-2" :status="service.payment_status" /><p class="mt-4 text-2xl font-bold"><MoneyAmount :value="service.total_amount" /></p><p class="mt-2 text-sm text-slate-600">Оплачено: <MoneyAmount :value="service.paid_amount" /></p><p class="mt-2 text-slate-600">{{ service.comment }}</p><div class="mt-5 flex gap-2"><Link :href="route('app.services.edit', service.id)" class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">Редактировать</Link><button class="rounded-xl bg-rose-600 px-4 py-2 font-semibold text-white" @click="destroy(service)">Удалить</button></div></Card></AppLayout></template>
