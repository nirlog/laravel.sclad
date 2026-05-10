<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import TagSelector from '@/Components/App/TagSelector.vue';

const props = defineProps({ writeOff: Object, materials: Array, tags: Array });
const form = useForm({ project_id: props.writeOff.project_id, date: props.writeOff.date, material_id: props.writeOff.material_id, quantity: props.writeOff.quantity, tag_ids: props.writeOff.tags?.map((tag) => tag.id) || [], comment: props.writeOff.comment || '' });
const material = computed(() => props.materials.find((item) => item.id === Number(form.material_id)));
const exceedsStock = computed(() => material.value && Number(form.quantity || 0) > Number(material.value.current_stock || 0) + Number(props.writeOff.quantity || 0));
function submit() { if (!exceedsStock.value) form.patch(route('app.write-offs.update', props.writeOff.id)); }
</script>

<template>
    <AppLayout><template #title>Редактировать списание</template><form class="space-y-4" @submit.prevent="submit"><Card class="grid gap-4 md:grid-cols-2"><input v-model="form.date" type="date" class="rounded-xl border-slate-300" /><select v-model="form.material_id" class="rounded-xl border-slate-300"><option v-for="item in materials" :key="item.id" :value="item.id">{{ item.name }}</option></select><div class="rounded-xl bg-slate-50 p-4">Доступно: <b>{{ material?.current_stock ?? '—' }} {{ material?.unit?.short_name }}</b></div><input v-model="form.quantity" type="number" step="0.001" min="0.001" class="rounded-xl border-slate-300" /><div class="md:col-span-2"><TagSelector v-model="form.tag_ids" :tags="tags" /></div><textarea v-model="form.comment" class="rounded-xl border-slate-300 md:col-span-2" /><p v-if="exceedsStock" class="rounded-xl bg-rose-50 p-3 text-sm text-rose-700 md:col-span-2">Недостаточно материала на складе.</p></Card><button class="w-full rounded-xl bg-orange-600 px-5 py-3 font-semibold text-white disabled:opacity-50" :disabled="exceedsStock">Сохранить</button></form></AppLayout>
</template>
