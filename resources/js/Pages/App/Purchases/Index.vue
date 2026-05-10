<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ purchases: Object });
</script>

<template>
    <AppLayout>
        <template #title>Покупки</template>
        <div class="mb-4 flex justify-end">
            <Link :href="route('app.purchases.create')" class="rounded-xl bg-emerald-600 px-4 py-2 font-semibold text-white">+ Покупка</Link>
        </div>
        <div class="space-y-3">
            <Card v-for="purchase in purchases.data" :key="purchase.id">
                <div class="flex justify-between gap-3">
                    <div>
                        <Link :href="route('app.purchases.show', purchase.id)" class="font-semibold text-slate-950">{{ purchase.supplier_name || 'Покупка материалов' }}</Link>
                        <p class="text-sm text-slate-500">{{ formatDate(purchase.date) }}</p>
                    </div>
                    <MoneyAmount :value="purchase.total_amount" class="font-semibold" />
                </div>
            </Card>
        </div>
    </AppLayout>
</template>
