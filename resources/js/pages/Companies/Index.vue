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
import { destroy, index, store, update } from '@/routes/companies';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Companies', href: index() }] },
});

type Company = { id: number; name: string };

defineProps<{ companies: Company[] }>();

const showDialog = ref(false);
const editing = ref<Company | null>(null);

const form = useForm({ name: '' });

function openCreate() {
    editing.value = null;
    form.reset();
    showDialog.value = true;
}

function openEdit(company: Company) {
    editing.value = company;
    form.name = company.name;
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

function remove(company: Company) {
    if (confirm(`Delete "${company.name}"?`)) {
        useForm({}).delete(destroy(company));
    }
}
</script>

<template>
    <Head title="Companies" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <Heading title="Companies" description="Manage your companies" />
            <Button @click="openCreate">
                <Plus class="mr-2 h-4 w-4" /> New Company
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full min-w-[400px] text-sm">
                <thead class="bg-muted/50 text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">#</th>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="companies.length === 0">
                        <td colspan="3" class="px-4 py-8 text-center text-muted-foreground">No companies yet.</td>
                    </tr>
                    <tr
                        v-for="company in companies"
                        :key="company.id"
                        class="border-t border-sidebar-border/50 transition-colors hover:bg-muted/30"
                    >
                        <td class="px-4 py-3 text-muted-foreground">{{ company.id }}</td>
                        <td class="px-4 py-3 font-medium">{{ company.name }}</td>
                        <td class="px-4 py-3 text-right">
                            <Button variant="ghost" size="icon" @click="openEdit(company)">
                                <Pencil class="h-4 w-4" />
                            </Button>
                            <Button variant="ghost" size="icon" class="text-destructive hover:text-destructive" @click="remove(company)">
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
                <DialogTitle>{{ editing ? 'Edit Company' : 'New Company' }}</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Company name"
                        autofocus
                        :aria-invalid="!!form.errors.name"
                    />
                    <InputError :message="form.errors.name" />
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
