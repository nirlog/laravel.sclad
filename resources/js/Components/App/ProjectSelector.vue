<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps({ projects: Array, project: Object });
function change(event) {
    router.get(route('app.dashboard'), { project_id: event.target.value }, { preserveState: false });
}
</script>

<template>
    <select class="rounded-xl border-slate-300 text-sm" :value="project?.id" @change="change">
        <option v-for="item in projects" :key="item.id" :value="item.id">{{ item.name }}</option>
    </select>
</template>
