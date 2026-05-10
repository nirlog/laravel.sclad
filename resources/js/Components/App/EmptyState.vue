<script setup>
defineProps({ title: String, text: String });
</script>

<template>
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
        <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
        <p class="mt-2 text-sm text-slate-500">{{ text }}</p>
        <div class="mt-4"><slot /></div>
    </div>
</template>
