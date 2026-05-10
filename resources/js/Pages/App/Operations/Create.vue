<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import QuickCreateOperation from '@/Components/App/QuickCreateOperation.vue';
</script>

<template>
    <AppLayout>
        <template #title>Добавить операцию</template>
        <div class="space-y-4">
            <p class="text-slate-600">Выберите тип операции. Формы используют backend Actions, поэтому итоговые суммы и остатки считаются на сервере.</p>
            <QuickCreateOperation />
        </div>
    </AppLayout>
</template>
