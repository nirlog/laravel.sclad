<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import TagSelector from '@/Components/App/TagSelector.vue';
import { formatMoney } from '@/lib/formatters';

const props = defineProps({ project: Object, materials: Array, tags: Array });
const form = useForm({ project_id: props.project.id, date: new Date().toISOString().slice(0, 10), supplier_name: '', document_number: '', payment_status: 'paid', tag_ids: [], comment: '', items: [{ material_id: '', quantity: 1, unit_price: 0 }] });
const total = computed(() => form.items.reduce((sum, item) => sum + Number(item.quantity || 0) * Number(item.unit_price || 0), 0));
function addItem() { form.items.push({ material_id: '', quantity: 1, unit_price: 0 }); }
function submit() { form.post(route('app.purchases.store')); }
</script>

<template>
    <AppLayout>
        <template #title>Новая покупка</template>
        <form class="space-y-4" @submit.prevent="submit">
            <Card class="grid gap-4 md:grid-cols-2">
                <input v-model="form.date" type="date" class="rounded-xl border-slate-300" />
                <input v-model="form.supplier_name" placeholder="Поставщик" class="rounded-xl border-slate-300" />
                <input v-model="form.document_number" placeholder="Номер документа" class="rounded-xl border-slate-300" />
                <select v-model="form.payment_status" class="rounded-xl border-slate-300"><option value="paid">Оплачено</option><option value="partial">Частично</option><option value="unpaid">Не оплачено</option></select>
                <div class="md:col-span-2"><TagSelector v-model="form.tag_ids" :tags="tags" /></div>
                <textarea v-model="form.comment" placeholder="Комментарий" class="rounded-xl border-slate-300 md:col-span-2" />
            </Card>
            <Card>
                <div class="space-y-3">
                    <div v-for="(item, index) in form.items" :key="index" class="grid gap-3 rounded-xl bg-slate-50 p-3 md:grid-cols-4">
                        <select v-model="item.material_id" class="rounded-xl border-slate-300 md:col-span-2"><option value="">Материал</option><option v-for="material in materials" :key="material.id" :value="material.id">{{ material.name }} ({{ material.unit?.short_name }})</option></select>
                        <input v-model="item.quantity" type="number" step="0.001" min="0.001" class="rounded-xl border-slate-300" placeholder="Кол-во" />
                        <input v-model="item.unit_price" type="number" step="0.01" min="0" class="rounded-xl border-slate-300" placeholder="Цена" />
                    </div>
                </div>
                <button type="button" class="mt-3 rounded-xl bg-slate-100 px-4 py-2 font-medium" @click="addItem">Добавить строку</button>
            </Card>
            <div class="flex items-center justify-between rounded-2xl bg-white p-4 shadow-sm">
                <span class="font-semibold">Итого: {{ formatMoney(total) }}</span>
                <button class="rounded-xl bg-emerald-600 px-5 py-3 font-semibold text-white" :disabled="form.processing">Сохранить покупку</button>
            </div>
        </form>
    </AppLayout>
</template>
