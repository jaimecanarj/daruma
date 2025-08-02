<script setup lang="ts">
defineProps<{ items: (string | { label: string; value: string | number | boolean })[]; label: string; icon: string }>();

const model = defineModel<(string | number | boolean)[]>({ default: [] });
</script>

<template>
    <div class="w-full">
        <div class="m-0.5 flex items-end justify-between">
            <p class="first-letter:capitalize">
                {{ label }} <span v-if="model.length > 0" class="text-primary ml-2 text-sm">+{{ model.length }}</span>
            </p>
            <UButton v-if="model.length > 0" class="p-0.5" color="neutral" variant="link" size="sm" icon="lucide:circle-x" @click="model = []" />
        </div>
        <USelect v-model="model" :items="items" multiple :placeholder="`Cualquier ${label}`" :icon="icon" class="w-full" />
    </div>
</template>
