<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Building2, FolderKanban, Ticket } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as companiesIndex } from '@/routes/companies';
import { index as projectsIndex } from '@/routes/projects';
import { index as ticketsIndex } from '@/routes/tickets';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

const props = defineProps<{
    stats: { companies: number; projects: number; tickets: number };
}>();

const cards = [
    { label: 'Companies', value: props.stats.companies, icon: Building2, href: companiesIndex() },
    { label: 'Projects',  value: props.stats.projects,  icon: FolderKanban, href: projectsIndex() },
    { label: 'Tickets',   value: props.stats.tickets,   icon: Ticket, href: ticketsIndex() },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <Heading title="Dashboard" description="Overview of your ServiceHub workspace" />

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="card in cards"
                :key="card.label"
                :href="card.href"
                class="block transition-transform hover:-translate-y-0.5"
            >
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">{{ card.label }}</CardTitle>
                        <component :is="card.icon" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ card.value }}</div>
                    </CardContent>
                </Card>
            </Link>
        </div>
    </div>
</template>
