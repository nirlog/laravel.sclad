<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import OperationTypeBadge from '@/Components/App/OperationTypeBadge.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import { formatDate } from '@/lib/formatters';
defineProps({ operations: Object, filters: Object });
</script>

<template>
    <AppLayout>
        <template #title>Операции</template>
        <div class="space-y-3">
            <Card v-for="operation in operations.data" :key="operation.id">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <OperationTypeBadge :type="operation.type" :label="operation.type_label" />
                        <h2 class="mt-2 text-lg font-semibold">{{ operation.title }}</h2>
                        <p class="text-sm text-slate-500">{{ operation.description }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ operation.quantity_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">{{ formatDate(operation.date) }}</p>
                        <p class="mt-2 font-semibold"><MoneyAmount :value="operation.amount" /></p>
                    </div>
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
