<script setup lang="ts">
import { Person } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{ person: Person }>();
</script>

<template>
    <Head :title="person.name" />
    {{ person }}
</template>
