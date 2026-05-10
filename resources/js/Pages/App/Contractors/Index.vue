<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
defineProps({ contractors: [Object, Array], contractor: Object, totals: Object, services: Array, stock: [String, Number], averageCost: [String, Number], movements: Object });
</script>
<template>
    <AppLayout>
        <template #title>Исполнители</template>
        <div class="space-y-3">
            <Card v-if="contractor">
                <h2 class="text-xl font-semibold">{ contractor.name || contractor.title || contractor.supplier_name || 'Исполнители' }</h2>
                <p class="mt-2 text-sm text-slate-500">Карточка записи. Расширенное редактирование доступно в административной панели.</p>
            </Card>
            <Card v-for="row in (contractors.data || contractors || [])" :key="row.id">
                <div class="flex justify-between gap-3">
                    <div><h2 class="font-semibold">{ row.name || row.supplier_name || row.material?.name || row.date }</h2><p class="text-sm text-slate-500">{ row.comment || row.description }</p></div>
                    <MoneyAmount v-if="row.total_amount" :value="row.total_amount" />
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
