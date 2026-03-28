<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
defineProps<{ tickets: { id: number; title: string }[] }>();
</script>
<template>
    <Head title="Tickets" />
    <ul>
        <li v-for="ticket in tickets" :key="ticket.id">{{ ticket.title }}</li>
    </ul>
</template>
