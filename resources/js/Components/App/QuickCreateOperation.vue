<script setup>
import { Link } from '@inertiajs/vue3';
const actions = [
    ['+ Покупка', 'app.purchases.create', 'bg-emerald-600'],
    ['+ Списание', 'app.write-offs.create', 'bg-orange-600'],
    ['+ Услуга', 'app.services.create', 'bg-blue-600'],
    ['+ Корректировка', 'app.inventory.adjustments.create', 'bg-purple-600'],
];
</script>

<template>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <Link v-for="action in actions" :key="action[1]" :href="route(action[1])" :class="['rounded-2xl px-4 py-4 text-center font-semibold text-white shadow-sm', action[2]]">
            {{ action[0] }}
        </Link>
    </div>
</template>
