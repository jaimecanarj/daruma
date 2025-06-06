<script setup lang="ts">
import MagazinesTable from '@/components/admin/index/MagazinesTable.vue';
import TagsTable from '@/components/admin/index/TagsTable.vue';
import UsersTable from '@/components/admin/index/UsersTable.vue';

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-3xl',
        icon: 'lucide:monitor-cog',
    },
];

const tabItems = [
    {
        slot: 'mangas' as const,
        label: 'Mangas',
        icon: 'lucide:book-text',
    },
    {
        slot: 'people' as const,
        label: 'Personas',
        icon: 'lucide:users',
    },
    {
        slot: 'magazines' as const,
        label: 'Revistas',
        icon: 'lucide:newspaper',
    },
    {
        slot: 'users' as const,
        label: 'Usuarios',
        icon: 'lucide:circle-user',
    },
    {
        slot: 'tags' as const,
        label: 'Etiquetas',
        icon: 'lucide:tag',
    },
];
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <UTabs :items="tabItems" class="mt-8" variant="link">
        <template #mangas>Hola</template>
        <template #people>Hola</template>
        <template #magazines><MagazinesTable /></template>
        <template #users><UsersTable /></template>
        <template #tags><TagsTable /></template>
    </UTabs>
</template>
