<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Loader2, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { destroy, index, store, update } from '@/routes/projects';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Projects', href: index() }] },
});

type Company = { id: number; name: string };
type Project = { id: number; name: string; company_id: number; company: Company };

const props = defineProps<{ projects: Project[]; companies: Company[] }>();

const showDialog = ref(false);
const editing = ref<Project | null>(null);

const form = useForm({ name: '', company_id: '' });

function openCreate() {
    editing.value = null;
    form.reset();
    showDialog.value = true;
}

function openEdit(project: Project) {
    editing.value = project;
    form.name = project.name;
    form.company_id = String(project.company_id);
    showDialog.value = true;
}

function submit() {
    if (editing.value) {
        form.put(update(editing.value), {
            onSuccess: () => { showDialog.value = false; },
        });
    } else {
        form.post(store(), {
            onSuccess: () => { showDialog.value = false; form.reset(); },
        });
    }
}

function remove(project: Project) {
    if (confirm(`Delete "${project.name}"?`)) {
        useForm({}).delete(destroy(project));
    }
}
</script>

<template>
    <Head title="Projects" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <Heading title="Projects" description="Manage your projects" />
            <Button @click="openCreate">
                <Plus class="mr-2 h-4 w-4" /> New Project
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full min-w-[500px] text-sm">
                <thead class="bg-muted/50 text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Company</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="projects.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">No projects yet.</td>
                    </tr>
                    <tr
                        v-for="project in projects"
                        :key="project.id"
                        class="border-t border-sidebar-border/50 transition-colors hover:bg-muted/30"
                    >
                        <td class="px-4 py-3 text-muted-foreground">{{ project.id }}</td>
                        <td class="px-4 py-3 font-medium">{{ project.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ project.company?.name }}</td>
                        <td class="px-4 py-3 text-right">
                            <Button variant="ghost" size="icon" @click="openEdit(project)">
                                <Pencil class="h-4 w-4" />
                            </Button>
                            <Button variant="ghost" size="icon" class="text-destructive hover:text-destructive" @click="remove(project)">
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <Dialog v-model:open="showDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ editing ? 'Edit Project' : 'New Project' }}</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Project name"
                        autofocus
                        :aria-invalid="!!form.errors.name"
                    />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label>Company</Label>
                    <Select v-model="form.company_id">
                        <SelectTrigger :aria-invalid="!!form.errors.company_id">
                            <SelectValue placeholder="Select a company" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="company in companies"
                                :key="company.id"
                                :value="String(company.id)"
                            >
                                {{ company.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.company_id" />
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showDialog = false">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        {{ editing ? 'Save changes' : 'Create' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
