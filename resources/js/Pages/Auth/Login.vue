<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: 'demo@example.com',
    password: 'password',
    remember: true,
});

function submit() {
    form.post(route('login.store'));
}
</script>

<template>
    <main class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
        <form class="w-full max-w-md rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200" @submit.prevent="submit">
            <h1 class="text-2xl font-bold text-slate-950">Construction Ledger</h1>
            <p class="mt-2 text-sm text-slate-500">Войдите, чтобы открыть пользовательское приложение /app.</p>

            <label class="mt-6 block text-sm font-medium text-slate-700">Email</label>
            <input v-model="form.email" type="email" class="mt-1 w-full rounded-xl border-slate-300" autofocus autocomplete="username" />
            <p v-if="form.errors.email" class="mt-1 text-sm text-rose-600">{{ form.errors.email }}</p>

            <label class="mt-4 block text-sm font-medium text-slate-700">Пароль</label>
            <input v-model="form.password" type="password" class="mt-1 w-full rounded-xl border-slate-300" autocomplete="current-password" />
            <p v-if="form.errors.password" class="mt-1 text-sm text-rose-600">{{ form.errors.password }}</p>

            <label class="mt-4 flex items-center gap-2 text-sm text-slate-600">
                <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
                Запомнить меня
            </label>

            <button class="mt-6 w-full rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white disabled:opacity-60" :disabled="form.processing">
                Войти
            </button>
        </form>
    </main>
</template>
