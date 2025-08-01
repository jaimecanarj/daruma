<script setup lang="ts">
defineProps<{ label: string; size: number }>();
</script>

<template>
    <p>
        Autores <span v-if="size > 0" class="text-primary ml-2 text-sm">+{{ size }}</span>
    </p>
</template>
