<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { show, update } from '@/routes/user-profile';

defineOptions({
    layout: { breadcrumbs: [{ title: 'My Profile', href: show() }] },
});

type UserProfile = { id: number; phone: string | null; position: string | null };

const props = defineProps<{ userProfile: UserProfile }>();

const form = useForm({
    phone: props.userProfile.phone ?? '',
    position: props.userProfile.position ?? '',
});

function submit() {
    form.put(update());
}
</script>

<template>
    <Head title="My Profile" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <Heading title="My Profile" description="Manage your profile information" />

        <Card class="max-w-lg">
            <CardHeader><CardTitle>Profile Information</CardTitle></CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="phone">Phone</Label>
                        <Input
                            id="phone"
                            v-model="form.phone"
                            placeholder="e.g. 11999998888"
                            :aria-invalid="!!form.errors.phone"
                        />
                        <InputError :message="form.errors.phone" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="position">Position / Role</Label>
                        <Input
                            id="position"
                            v-model="form.position"
                            placeholder="e.g. Systems Analyst"
                            :aria-invalid="!!form.errors.position"
                        />
                        <InputError :message="form.errors.position" />
                    </div>
                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">
                            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                            Save
                        </Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-muted-foreground">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
