<script setup>
const labels = { paid: 'Оплачено', unpaid: 'Не оплачено', partial: 'Частично' };
const classes = { paid: 'bg-emerald-100 text-emerald-700', unpaid: 'bg-rose-100 text-rose-700', partial: 'bg-amber-100 text-amber-700' };
defineProps({ status: String });
</script>

<template>
    <span :class="['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', classes[status] || 'bg-slate-100 text-slate-700']">
        {{ labels[status] || status }}
    </span>
</template>
