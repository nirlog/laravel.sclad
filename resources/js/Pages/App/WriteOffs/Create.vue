<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import TagSelector from '@/Components/App/TagSelector.vue';

const props = defineProps({ project: Object, materials: Array, tags: Array });
const form = useForm({ project_id: props.project.id, date: new Date().toISOString().slice(0, 10), material_id: '', quantity: 1, tag_ids: [], comment: '' });
const material = computed(() => props.materials.find((item) => item.id === Number(form.material_id)));
const exceedsStock = computed(() => material.value && Number(form.quantity || 0) > Number(material.value.current_stock || 0));
function submit() { if (!exceedsStock.value) form.post(route('app.write-offs.store')); }
</script>

<template>
    <AppLayout>
        <template #title>Новое списание</template>
        <form class="space-y-4" @submit.prevent="submit">
            <Card class="grid gap-4 md:grid-cols-2">
                <input v-model="form.date" type="date" class="rounded-xl border-slate-300" />
                <select v-model="form.material_id" class="rounded-xl border-slate-300"><option value="">Материал</option><option v-for="item in materials" :key="item.id" :value="item.id">{{ item.name }}</option></select>
                <div class="rounded-xl bg-slate-50 p-4">Текущий остаток: <b>{{ material?.current_stock ?? '—' }} {{ material?.unit?.short_name }}</b></div>
                <input v-model="form.quantity" type="number" step="0.001" min="0.001" class="rounded-xl border-slate-300" placeholder="Количество" />
                <div class="md:col-span-2"><TagSelector v-model="form.tag_ids" :tags="tags" /></div>
                <textarea v-model="form.comment" placeholder="Комментарий" class="rounded-xl border-slate-300 md:col-span-2" />
                <p v-if="exceedsStock" class="rounded-xl bg-rose-50 p-3 text-sm font-medium text-rose-700 md:col-span-2">Нельзя списать {{ form.quantity }}. На складе доступно {{ material.current_stock }} {{ material.unit?.short_name }}.</p>
            </Card>
            <button class="w-full rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white disabled:opacity-50" :disabled="form.processing || exceedsStock">Сохранить списание</button>
        </form>
    </AppLayout>
</template>
