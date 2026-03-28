<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
defineProps<{ userProfile: { id: number; phone: string | null; position: string | null } }>();
</script>
<template>
    <Head title="Profile" />
    <div>{{ userProfile.position }}</div>
</template>
