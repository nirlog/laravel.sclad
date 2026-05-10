<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/App/StatCard.vue';
import Card from '@/Components/App/Card.vue';
import { formatMoney } from '@/lib/formatters';
defineProps({ summary: Object, stageCost: Object, byTags: Array, byContractors: Array });
</script>
<template>
    <AppLayout><template #title>Аналитика</template><div class="space-y-4"><div class="grid gap-4 md:grid-cols-3"><StatCard label="Фактические платежи" :value="formatMoney(summary.actual_total)" /><StatCard label="Себестоимость этапов" :value="formatMoney(Number(stageCost.materials || 0) + Number(stageCost.services || 0))" /><StatCard label="Склад" :value="formatMoney(summary.inventory_value)" /></div><Card><h2 class="font-semibold">Расходы по тегам</h2><div class="mt-3 space-y-2"><div v-for="tag in byTags" :key="tag.id" class="flex justify-between text-sm"><span>#{{ tag.name }}</span><span>{{ formatMoney(tag.actual_total || tag.stage_cost_total || 0) }}</span></div></div></Card></div></AppLayout>
</template>
