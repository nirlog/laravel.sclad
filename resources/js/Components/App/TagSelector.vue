<script setup>
defineProps({ tags: Array, modelValue: Array });
defineEmits(['update:modelValue']);
</script>

<template>
    <select multiple class="w-full rounded-xl border-slate-300" :value="modelValue" @change="$emit('update:modelValue', Array.from($event.target.selectedOptions).map(o => Number(o.value)))">
        <option v-for="tag in tags" :key="tag.id" :value="tag.id">{{ tag.name }}</option>
    </select>
</template>
