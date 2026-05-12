<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import TagSelector from '@/Components/App/TagSelector.vue';
import { formatMoney } from '@/lib/formatters';

const props = defineProps({ project: Object, contractors: Array, tags: Array });
const form = useForm({ project_id: props.project.id, contractor_id: '', date: new Date().toISOString().slice(0, 10), name: '', pricing_type: 'fixed', hours: null, hourly_rate: null, quantity: null, unit_name: '', unit_price: null, total_amount: 0, payment_status: 'paid', paid_amount: null, tag_ids: [], comment: '' });
const previewTotal = computed(() => form.pricing_type === 'hourly' ? Number(form.hours || 0) * Number(form.hourly_rate || 0) : form.pricing_type === 'unit' ? Number(form.quantity || 0) * Number(form.unit_price || 0) : Number(form.total_amount || 0));
function submit() { form.post(route('app.services.store')); }
</script>

<template>
    <AppLayout>
        <template #title>Новая услуга</template>
        <form class="space-y-4" @submit.prevent="submit">
            <Card class="grid gap-4 md:grid-cols-2">
                <input v-model="form.date" type="date" class="rounded-xl border-slate-300" />
                <input v-model="form.name" placeholder="Название работы" class="rounded-xl border-slate-300" />
                <select v-model="form.contractor_id" class="rounded-xl border-slate-300"><option value="">Исполнитель</option><option v-for="contractor in contractors" :key="contractor.id" :value="contractor.id">{{ contractor.name }}</option></select>
                <select v-model="form.pricing_type" class="rounded-xl border-slate-300"><option value="fixed">Фиксированная сумма</option><option value="hourly">По часам</option><option value="unit">По количеству</option></select>
                <template v-if="form.pricing_type === 'hourly'"><input v-model="form.hours" type="number" step="0.01" placeholder="Часы" class="rounded-xl border-slate-300" /><input v-model="form.hourly_rate" type="number" step="0.01" placeholder="Ставка" class="rounded-xl border-slate-300" /></template>
                <template v-if="form.pricing_type === 'unit'"><input v-model="form.quantity" type="number" step="0.001" placeholder="Количество" class="rounded-xl border-slate-300" /><input v-model="form.unit_name" placeholder="Единица" class="rounded-xl border-slate-300" /><input v-model="form.unit_price" type="number" step="0.01" placeholder="Цена" class="rounded-xl border-slate-300" /></template>
                <input v-if="form.pricing_type === 'fixed'" v-model="form.total_amount" type="number" step="0.01" placeholder="Сумма" class="rounded-xl border-slate-300" />
                <select v-model="form.payment_status" class="rounded-xl border-slate-300"><option value="paid">Оплачено</option><option value="partial">Частично</option><option value="unpaid">Не оплачено</option></select>
                <input v-model="form.paid_amount" type="number" step="0.01" placeholder="Оплачено" class="rounded-xl border-slate-300" />
                <div class="md:col-span-2"><TagSelector v-model="form.tag_ids" :tags="tags" /></div>
                <textarea v-model="form.comment" placeholder="Комментарий" class="rounded-xl border-slate-300 md:col-span-2" />
            </Card>
            <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-sm"><b>Итого: {{ formatMoney(previewTotal) }}</b><button class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white">Сохранить услугу</button></div>
        </form>
    </AppLayout>
</template>
