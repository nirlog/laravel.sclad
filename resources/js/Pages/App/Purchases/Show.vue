<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import PaymentStatusBadge from '@/Components/App/PaymentStatusBadge.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ purchase: Object });
function destroy(purchase) {
    if (confirm('Удалить покупку и связанные складские приходы?')) {
        router.delete(route('app.purchases.destroy', purchase.id));
    }
}
</script>

<template>
    <AppLayout>
        <template #title>Покупка</template>
        <Card>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold">{{ purchase.supplier_name || 'Покупка материалов' }}</h2>
                    <p class="text-sm text-slate-500">{{ formatDate(purchase.date) }} · документ {{ purchase.document_number || '—' }}</p>
                    <PaymentStatusBadge class="mt-2" :status="purchase.payment_status" />
                </div>
                <MoneyAmount :value="purchase.total_amount" class="text-2xl font-bold" />
            </div>
            <div class="mt-5 space-y-2">
                <div v-for="item in purchase.items" :key="item.id" class="flex justify-between rounded-xl bg-slate-50 p-3 text-sm">
                    <span>{{ item.material?.name }} — {{ item.quantity }} {{ item.material?.unit?.short_name }}</span>
                    <MoneyAmount :value="item.total_price" />
                </div>
            </div>
            <div class="mt-5 flex gap-2">
                <Link :href="route('app.purchases.edit', purchase.id)" class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">Редактировать</Link>
                <button class="rounded-xl bg-rose-600 px-4 py-2 font-semibold text-white" @click="destroy(purchase)">Удалить</button>
            </div>
        </Card>
    </AppLayout>
</template>
