<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ services: Object });
</script>
<template><AppLayout><template #title>Услуги</template><div class="mb-4 flex justify-end"><Link :href="route('app.services.create')" class="rounded-xl bg-blue-600 px-4 py-2 font-semibold text-white">+ Услуга</Link></div><div class="space-y-3"><Card v-for="service in services.data" :key="service.id"><div class="flex justify-between"><div><Link :href="route('app.services.show', service.id)" class="font-semibold">{{ service.name }}</Link><p class="text-sm text-slate-500">{{ formatDate(service.date) }} · {{ service.contractor?.name || 'Без исполнителя' }}</p></div><MoneyAmount :value="service.total_amount" /></div></Card></div></AppLayout></template>
