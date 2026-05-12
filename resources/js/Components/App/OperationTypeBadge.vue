<script setup>
const props = defineProps({ type: String, label: String });
const classes = {
    purchase: 'bg-emerald-100 text-emerald-700',
    write_off: 'bg-orange-100 text-orange-700',
    service: 'bg-blue-100 text-blue-700',
    adjustment: 'bg-purple-100 text-purple-700',
};
</script>

<template>
    <span :class="['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', classes[props.type] || 'bg-slate-100 text-slate-700']">
        {{ label || type }}
    </span>
</template>
