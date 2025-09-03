<script setup lang="ts">
const model = defineModel<number>();
</script>

<template>
    <UInputNumber
        v-model="model"
        orientation="vertical"
        disable-wheel-change
        increment-disabled
        decrement-disabled
        :ui="{ base: 'px-2.5!' }"
        v-bind="$attrs"
    >
        <template #increment>{{ '' }}</template>
        <template #decrement>{{ '' }}</template>
    </UInputNumber>
</template>
