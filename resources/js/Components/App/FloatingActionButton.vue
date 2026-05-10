<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentProjectParams = () => page.props.project?.id ? { project_id: page.props.project.id } : {};
</script>

<template>
    <Link :href="route('app.operations.create', currentProjectParams())" class="fixed bottom-20 right-5 z-40 rounded-full bg-blue-600 px-5 py-4 text-sm font-semibold text-white shadow-xl md:hidden">
        + Добавить
    </Link>
</template>
