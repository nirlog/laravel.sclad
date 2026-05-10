<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ writeOffs: Object });
</script>

<template>
    <AppLayout><template #title>Списания</template><div class="mb-4 flex justify-end"><Link :href="route('app.write-offs.create')" class="rounded-xl bg-orange-600 px-4 py-2 font-semibold text-white">+ Списание</Link></div><div class="space-y-3"><Card v-for="writeOff in writeOffs.data" :key="writeOff.id"><div class="flex justify-between gap-3"><div><Link :href="route('app.write-offs.show', writeOff.id)" class="font-semibold">{{ writeOff.material?.name }}</Link><p class="text-sm text-slate-500">{{ formatDate(writeOff.date) }} · {{ writeOff.quantity }} {{ writeOff.material?.unit?.short_name }}</p></div><MoneyAmount :value="writeOff.total_amount" /></div></Card></div></AppLayout>
</template>
