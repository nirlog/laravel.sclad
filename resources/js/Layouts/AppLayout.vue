<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import MobileBottomNav from '@/Components/App/MobileBottomNav.vue';
import FloatingActionButton from '@/Components/App/FloatingActionButton.vue';
import ProjectSelector from '@/Components/App/ProjectSelector.vue';

const page = usePage();
const navigation = [
    ['Дашборд', 'app.dashboard'],
    ['Операции', 'app.operations.index'],
    ['Покупки', 'app.purchases.index'],
    ['Списания', 'app.write-offs.index'],
    ['Услуги', 'app.services.index'],
    ['Склад', 'app.inventory.index'],
    ['Материалы', 'app.materials.index'],
    ['Исполнители', 'app.contractors.index'],
    ['Аналитика', 'app.analytics.index'],
    ['Настройки', 'app.settings.index'],
];
</script>

<template>
    <div class="min-h-screen bg-slate-100 pb-20 md:pb-0">
        <aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-slate-200 bg-white p-5 md:block">
            <div class="text-xl font-bold text-slate-950">Construction Ledger</div>
            <nav class="mt-8 space-y-1">
                <Link v-for="item in navigation" :key="item[1]" :href="route(item[1])" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-950">
                    {{ item[0] }}
                </Link>
            </nav>
        </aside>

        <div class="md:pl-64">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 px-4 py-3 backdrop-blur md:px-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Пользовательское приложение</p>
                        <h1 class="text-lg font-semibold text-slate-950"><slot name="title">Стройка</slot></h1>
                    </div>
                    <ProjectSelector v-if="page.props.projects?.length" :projects="page.props.projects" :project="page.props.project" />
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-6 md:px-8">
                <div v-if="page.props.flash?.success" class="mb-4 rounded-2xl bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ page.props.flash.success }}
                </div>
                <div v-if="page.props.flash?.error" class="mb-4 rounded-2xl bg-rose-50 p-4 text-sm font-medium text-rose-800">
                    {{ page.props.flash.error }}
                </div>
                <slot />
            </main>
        </div>

        <FloatingActionButton />
        <MobileBottomNav />
    </div>
</template>
