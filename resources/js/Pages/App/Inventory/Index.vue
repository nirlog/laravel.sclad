<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
defineProps({ inventory: Array });
</script>
<template>
    <AppLayout><template #title>Склад</template><div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3"><Card v-for="item in inventory" :key="item.material.id"><h2 class="text-lg font-semibold">{{ item.material.name }}</h2><p class="mt-2 text-2xl font-bold">{{ item.current_stock }} {{ item.unit }}</p><p class="text-sm text-slate-500">Средняя цена: <MoneyAmount :value="item.average_price" /></p><p class="text-sm text-slate-500">Стоимость: <MoneyAmount :value="item.stock_value" /></p><div class="mt-3 flex gap-2 text-sm"><a :href="route('app.write-offs.create', { material_id: item.material.id })" class="text-orange-700">Списать</a><a :href="route('app.purchases.create')" class="text-emerald-700">Купить ещё</a></div></Card></div></AppLayout>
</template>
