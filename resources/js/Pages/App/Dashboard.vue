<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import QuickCreateOperation from '@/Components/App/QuickCreateOperation.vue';
import StatCard from '@/Components/App/StatCard.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import OperationTypeBadge from '@/Components/App/OperationTypeBadge.vue';
import { formatMoney, formatDate } from '@/lib/formatters';

defineProps({ summary: Object, recentOperations: Array, topTags: Array, inventoryAlerts: Array });
</script>

<template>
    <AppLayout>
        <template #title>Дашборд</template>
        <div class="space-y-6">
            <QuickCreateOperation />
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <StatCard label="Фактические расходы" :value="formatMoney(summary.actual_total)" />
                <StatCard label="Материалы куплено" :value="formatMoney(summary.materials_purchased_total)" />
                <StatCard label="Услуги" :value="formatMoney(summary.services_total)" />
                <StatCard label="Списано материалов" :value="formatMoney(summary.materials_written_off_total)" />
                <StatCard label="Стоимость склада" :value="formatMoney(summary.inventory_value)" />
                <StatCard label="Текущий месяц" :value="formatMoney(summary.current_month_total)" />
            </div>
            <div class="grid gap-4 lg:grid-cols-2">
                <Card>
                    <h2 class="text-lg font-semibold">Последние операции</h2>
                    <div class="mt-4 space-y-3">
                        <div v-for="operation in recentOperations" :key="operation.id" class="rounded-xl border border-slate-100 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <OperationTypeBadge :type="operation.type" :label="operation.type_label" />
                                <span class="text-sm text-slate-500">{{ formatDate(operation.date) }}</span>
                            </div>
                            <p class="mt-2 font-medium">{{ operation.title }}</p>
                            <p class="text-sm text-slate-500">{{ operation.description }}</p>
                            <p class="mt-2 font-semibold"><MoneyAmount :value="operation.amount" /></p>
                        </div>
                    </div>
                </Card>
                <Card>
                    <h2 class="text-lg font-semibold">Материалы требуют внимания</h2>
                    <div class="mt-4 space-y-3">
                        <div v-for="item in inventoryAlerts" :key="item.material.id" class="flex items-center justify-between rounded-xl bg-amber-50 p-3 text-sm">
                            <span>{{ item.material.name }}</span>
                            <span class="font-semibold">{{ item.current_stock }} {{ item.unit }}</span>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
