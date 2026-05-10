<script setup>
defineProps({ label: String, value: [String, Number], hint: String });
</script>

<template>
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
        <p class="text-sm text-slate-500">{{ label }}</p>
        <p class="mt-2 text-2xl font-semibold text-slate-950">{{ value }}</p>
        <p v-if="hint" class="mt-1 text-xs text-slate-400">{{ hint }}</p>
    </div>
</template>
