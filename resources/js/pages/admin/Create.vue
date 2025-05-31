<script setup lang="ts">
import MagazineCreateForm from '@/components/admin/MagazineCreateForm.vue';
import MangaCreateForm from '@/components/admin/MangaCreateForm.vue';
import PersonCreateForm from '@/components/admin/PersonCreateForm.vue';
import { CreatePageProps } from '@/types';

const props = defineProps<CreatePageProps>();

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-xl',
        icon: 'lucide:monitor-cog',
        to: '/admin',
    },
    {
        label: 'Crear',
        class: 'text-xl',
        icon: 'lucide:square-plus',
    },
];

const tabItems = [
    {
        slot: 'manga' as const,
        label: 'Manga',
        icon: 'lucide:book-text',
    },
    {
        slot: 'people' as const,
        label: 'Persona',
        icon: 'lucide:users',
    },
    {
        slot: 'magazine' as const,
        label: 'Revista',
        icon: 'lucide:newspaper',
    },
];
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" />
    <UTabs :items="tabItems" class="mt-4">
        <template #manga><MangaCreateForm v-bind="props" /></template>
        <template #people><PersonCreateForm /></template>
        <template #magazine><MagazineCreateForm /></template>
    </UTabs>
</template>
