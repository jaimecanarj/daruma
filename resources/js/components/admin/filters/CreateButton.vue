<script setup lang="ts">
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

defineProps<{ tab: string }>();
</script>

<template>
    <ULink :to="`/admin/create/${tab}`">
        <UButton trailing-icon="lucide:square-plus" :size="activeBreakpoint ? 'xl' : 'lg'" class="font-semibold">{{
            activeBreakpoint ? 'Crear' : ''
        }}</UButton>
    </ULink>
</template>
