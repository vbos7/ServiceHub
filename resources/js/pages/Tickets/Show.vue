<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { destroy, index, update } from '@/routes/tickets';
import { update as updateDetail } from '@/routes/ticket-detail';
import { router } from '@inertiajs/vue3';

type Project = { id: number; name: string };
type TicketDetail = { id: number; notes: string | null; enriched_data: string | null };
type Ticket = {
    id: number;
    title: string;
    description: string;
    project_id: number;
    attachment: string | null;
    project: Project;
    user: { id: number; name: string };
    ticket_detail: TicketDetail | null;
};

const props = defineProps<{ ticket: Ticket; projects: Project[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Tickets', href: index() },
            { title: 'View Ticket' },
        ],
    },
});

const editingTicket = ref(false);
const editingNotes = ref(false);

const ticketForm = useForm({
    title: props.ticket.title,
    description: props.ticket.description,
    project_id: String(props.ticket.project_id),
});

const notesForm = useForm({
    notes: props.ticket.ticket_detail?.notes ?? '',
});

function saveTicket() {
    ticketForm.put(update(props.ticket), {
        onSuccess: () => { editingTicket.value = false; },
    });
}

function saveNotes() {
    notesForm.put(updateDetail(props.ticket), {
        onSuccess: () => { editingNotes.value = false; },
    });
}

function remove() {
    if (confirm(`Delete "${props.ticket.title}"?`)) {
        router.delete(destroy(props.ticket), {
            onSuccess: () => router.visit(index()),
        });
    }
}
</script>

<template>
    <Head :title="ticket.title" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <Button variant="ghost" size="icon" as-child>
                    <Link :href="index()"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <Heading :title="ticket.title" :description="`#${ticket.id} · ${ticket.project?.name}`" />
            </div>
            <div class="flex gap-2">
                <Button v-if="!editingTicket" variant="outline" @click="editingTicket = true">Edit</Button>
                <Button v-if="editingTicket" variant="outline" @click="editingTicket = false">Cancel</Button>
                <Button variant="destructive" size="icon" @click="remove">
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Ticket info -->
            <Card>
                <CardHeader><CardTitle>Details</CardTitle></CardHeader>
                <CardContent>
                    <form v-if="editingTicket" @submit.prevent="saveTicket" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input id="title" v-model="ticketForm.title" :aria-invalid="!!ticketForm.errors.title" />
                            <InputError :message="ticketForm.errors.title" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="ticketForm.description" rows="4" :aria-invalid="!!ticketForm.errors.description" />
                            <InputError :message="ticketForm.errors.description" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Project</Label>
                            <Select v-model="ticketForm.project_id">
                                <SelectTrigger :aria-invalid="!!ticketForm.errors.project_id">
                                    <SelectValue />
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
                            <InputError :message="ticketForm.errors.project_id" />
                        </div>
                        <Button type="submit" :disabled="ticketForm.processing">
                            <Loader2 v-if="ticketForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                            Save changes
                        </Button>
                    </form>

                    <dl v-else class="space-y-3 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Title</dt>
                            <dd class="font-medium">{{ ticket.title }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Description</dt>
                            <dd class="whitespace-pre-wrap">{{ ticket.description }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Project</dt>
                            <dd>{{ ticket.project?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Author</dt>
                            <dd>{{ ticket.user?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">Attachment</dt>
                            <dd>
                                <Badge v-if="ticket.attachment" variant="secondary">attached</Badge>
                                <span v-else class="text-muted-foreground">—</span>
                            </dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>

            <!-- Technical Detail -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle>Technical Detail</CardTitle>
                    <Button v-if="!editingNotes" variant="ghost" size="icon" @click="editingNotes = true">
                        <Pencil class="h-4 w-4" />
                    </Button>
                    <Button v-else variant="ghost" size="sm" @click="editingNotes = false">Cancel</Button>
                </CardHeader>
                <CardContent>
                    <form v-if="editingNotes" @submit.prevent="saveNotes" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="notes">Notes</Label>
                            <Textarea
                                id="notes"
                                v-model="notesForm.notes"
                                placeholder="Add technical notes..."
                                rows="6"
                                :aria-invalid="!!notesForm.errors.notes"
                            />
                            <InputError :message="notesForm.errors.notes" />
                        </div>
                        <Button type="submit" :disabled="notesForm.processing">
                            <Loader2 v-if="notesForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                            Save notes
                        </Button>
                    </form>

                    <dl v-else class="space-y-3 text-sm">
                        <div v-if="ticket.ticket_detail?.notes">
                            <dt class="text-muted-foreground">Notes</dt>
                            <dd class="whitespace-pre-wrap">{{ ticket.ticket_detail.notes }}</dd>
                        </div>
                        <div v-if="ticket.ticket_detail?.enriched_data">
                            <dt class="text-muted-foreground">Enriched Data</dt>
                            <dd class="rounded-md bg-muted p-3 font-mono text-xs whitespace-pre-wrap">
                                {{ ticket.ticket_detail.enriched_data }}
                            </dd>
                        </div>
                        <p v-if="!ticket.ticket_detail?.notes && !ticket.ticket_detail?.enriched_data" class="text-sm text-muted-foreground">
                            No additional data yet. Click <Pencil class="inline h-3 w-3" /> to add notes.
                        </p>
                    </dl>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
