<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage<{ flash: { success?: string; error?: string } }>();

const visible = ref(false);
const message = ref('');
const type = ref<'success' | 'error'>('success');

let timer: ReturnType<typeof setTimeout>;

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            message.value = flash.success;
            type.value = 'success';
            show();
        } else if (flash?.error) {
            message.value = flash.error;
            type.value = 'error';
            show();
        }
    },
    { deep: true },
);

function show() {
    visible.value = true;
    clearTimeout(timer);
    timer = setTimeout(() => { visible.value = false; }, 4000);
}
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-4 opacity-0"
        leave-active-class="transition duration-200 ease-in"
        leave-to-class="translate-y-4 opacity-0"
    >
        <div
            v-if="visible"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
            :class="{
                'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300': type === 'success',
                'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300': type === 'error',
            }"
        >
            <CheckCircle v-if="type === 'success'" class="h-5 w-5 shrink-0" />
            <XCircle v-else class="h-5 w-5 shrink-0" />
            {{ message }}
            <button class="ml-2 opacity-60 hover:opacity-100" @click="visible = false">✕</button>
        </div>
    </Transition>
</template>
