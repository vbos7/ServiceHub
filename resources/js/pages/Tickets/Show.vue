<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
defineProps<{ ticket: { id: number; title: string; ticket_detail: object | null } }>();
</script>
<template>
    <Head title="Ticket" />
    <div>{{ ticket.title }}</div>
</template>
