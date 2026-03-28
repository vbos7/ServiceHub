<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    projects: { id: number; name: string; company_id: number }[];
}>();
</script>

<template>
    <Head title="Projects" />
    <ul>
        <li v-for="project in projects" :key="project.id">
            {{ project.name }}
        </li>
    </ul>
</template>
