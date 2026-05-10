<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/App/Card.vue';
import MoneyAmount from '@/Components/App/MoneyAmount.vue';
import { formatDate } from '@/lib/formatters';

defineProps({ writeOff: Object });
function destroy(writeOff) { if (confirm('Удалить списание?')) router.delete(route('app.write-offs.destroy', writeOff.id)); }
</script>

<template>
    <AppLayout><template #title>Списание</template><Card><h2 class="text-xl font-semibold">{{ writeOff.material?.name }}</h2><p class="text-sm text-slate-500">{{ formatDate(writeOff.date) }} · {{ writeOff.quantity }} {{ writeOff.material?.unit?.short_name }}</p><p class="mt-4 text-2xl font-bold"><MoneyAmount :value="writeOff.total_amount" /></p><p class="mt-2 text-slate-600">{{ writeOff.comment }}</p><div class="mt-5 flex gap-2"><Link :href="route('app.write-offs.edit', writeOff.id)" class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white">Редактировать</Link><button class="rounded-xl bg-rose-600 px-4 py-2 font-semibold text-white" @click="destroy(writeOff)">Удалить</button></div></Card></AppLayout>
</template>
