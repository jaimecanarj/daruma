<script setup lang="ts">
const input = defineModel<string>();
const show = defineModel<boolean>('show', { default: false });
</script>

<template>
    <UInput v-model="input" :type="show ? 'text' : 'password'" :ui="{ trailing: 'pe-1' }" class="w-full">
        <template #trailing>
            <UButton
                color="neutral"
                variant="link"
                size="sm"
                :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                :aria-label="show ? 'Hide password' : 'Show password'"
                :aria-pressed="show"
                aria-controls="password"
                @click="show = !show"
            />
        </template>
    </UInput>
</template>
