<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    companies: { id: number; name: string }[];
}>();
</script>

<template>
    <Head title="Companies" />
    <ul>
        <li v-for="company in companies" :key="company.id">
            {{ company.name }}
        </li>
    </ul>
</template>
