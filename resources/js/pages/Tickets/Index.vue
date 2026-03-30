<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Eye, Loader2, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { destroy, index, show, store } from '@/routes/tickets';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Tickets', href: index() }] },
});

type Project = { id: number; name: string };
type User = { id: number; name: string };
type Ticket = { id: number; title: string; description: string; project_id: number; project: Project; user: User; attachment: string | null };

defineProps<{ tickets: Ticket[]; projects: Project[] }>();

const showDialog = ref(false);
const form = useForm<{ title: string; description: string; project_id: string; attachment: File | null }>({
    title: '',
    description: '',
    project_id: '',
    attachment: null,
});

function openCreate() {
    form.reset();
    showDialog.value = true;
}

function submit() {
    form.post(store(), {
        forceFormData: true,
        onSuccess: () => { showDialog.value = false; form.reset(); },
    });
}

function remove(ticket: Ticket) {
    if (confirm(`Delete "${ticket.title}"?`)) {
        useForm({}).delete(destroy(ticket));
    }
}

function onFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    form.attachment = target.files?.[0] ?? null;
}
</script>

<template>
    <Head title="Tickets" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <Heading title="Tickets" description="Service order tickets" />
            <Button @click="openCreate">
                <Plus class="mr-2 h-4 w-4" /> New Ticket
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="bg-muted/50 text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Title</th>
                        <th class="px-4 py-3 text-left font-medium">Project</th>
                        <th class="px-4 py-3 text-left font-medium">Author</th>
                        <th class="px-4 py-3 text-left font-medium">Attachment</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="tickets.length === 0">
                        <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">No tickets yet.</td>
                    </tr>
                    <tr
                        v-for="ticket in tickets"
                        :key="ticket.id"
                        class="border-t border-sidebar-border/50 transition-colors hover:bg-muted/30"
                    >
                        <td class="px-4 py-3 text-muted-foreground">{{ ticket.id }}</td>
                        <td class="px-4 py-3 font-medium">{{ ticket.title }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ ticket.project?.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ ticket.user?.name }}</td>
                        <td class="px-4 py-3">
                            <Badge v-if="ticket.attachment" variant="secondary">attached</Badge>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Button variant="ghost" size="icon" as-child>
                                <Link :href="show(ticket)">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button variant="ghost" size="icon" class="text-destructive hover:text-destructive" @click="remove(ticket)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <Dialog v-model:open="showDialog">
        <DialogContent class="max-w-lg">
            <DialogHeader>
                <DialogTitle>New Ticket</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        placeholder="Ticket title"
                        autofocus
                        :aria-invalid="!!form.errors.title"
                    />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Describe the issue..."
                        rows="3"
                        :aria-invalid="!!form.errors.description"
                    />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="grid gap-2">
                    <Label>Project</Label>
                    <Select v-model="form.project_id">
                        <SelectTrigger :aria-invalid="!!form.errors.project_id">
                            <SelectValue placeholder="Select a project" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="project in projects"
                                :key="project.id"
                                :value="String(project.id)"
                            >
                                {{ project.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.project_id" />
                </div>
                <div class="grid gap-2">
                    <Label for="attachment">Attachment (JSON or TXT, optional)</Label>
                    <Input id="attachment" type="file" accept=".json,.txt" @change="onFileChange" />
                    <InputError :message="form.errors.attachment" />
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showDialog = false">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Create
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
