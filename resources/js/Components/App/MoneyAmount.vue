<script setup>
import { formatMoney } from '@/lib/formatters';
defineProps({ value: [Number, String] });
</script>

<template>
    <span>{{ formatMoney(value) }}</span>
</template>
