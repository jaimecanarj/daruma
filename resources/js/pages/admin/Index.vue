<script setup lang="ts">
import MagazinesTable from '@/components/admin/index/MagazinesTable.vue';
import MangasTable from '@/components/admin/index/MangasTable.vue';
import PeopleTable from '@/components/admin/index/PeopleTable.vue';
import TagsTable from '@/components/admin/index/TagsTable.vue';
import UsersTable from '@/components/admin/index/UsersTable.vue';
import { adminTabItems } from '@/utils/constants';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-xl sm:text-2xl lg:text-3xl',
        icon: 'lucide:monitor-cog',
    },
];

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <UTabs :items="adminTabItems" class="mt-8" variant="link" :ui="{ label: activeBreakpoint || 'hidden' }">
        <template #mangas><MangasTable /></template>
        <template #people><PeopleTable /></template>
        <template #magazines><MagazinesTable /></template>
        <template #users><UsersTable /></template>
        <template #tags><TagsTable /></template>
    </UTabs>
</template>
