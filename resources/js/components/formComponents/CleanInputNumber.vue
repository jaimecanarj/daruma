<script setup lang="ts">
const model = defineModel<number>();
</script>

<template>
    <UInputNumber v-model="model" orientation="vertical" disable-wheel-change v-bind="$attrs" :ui="{ increment: 'hidden', decrement: 'hidden' }" />
</template>
