<script setup lang="ts">
const display = defineModel<'grid' | 'list'>({ default: 'grid' });
</script>

<template>
    <UButtonGroup class="ml-auto">
        <UButton color="neutral" :variant="display === 'grid' ? 'solid' : 'subtle'" icon="lucide:grid-3x2" size="lg" @click="display = 'grid'" />
        <UButton color="neutral" :variant="display === 'list' ? 'solid' : 'subtle'" icon="lucide:layout-list" size="lg" @click="display = 'list'" />
    </UButtonGroup>
</template>
