<script setup lang="ts">
import MagazineCreateForm from '@/components/admin/create/MagazineCreateForm.vue';
import MangaCreateForm from '@/components/admin/create/MangaCreateForm.vue';
import PersonCreateForm from '@/components/admin/create/PersonCreateForm.vue';
import TagCreateForm from '@/components/admin/create/TagCreateForm.vue';
import UserCreateForm from '@/components/admin/create/UserCreateForm.vue';
import { CreatePageProps, SharedData } from '@/types';
import { adminTabItems } from '@/utils/constants';
import { router, usePage } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { ref } from 'vue';

const page: { props: SharedData } = usePage();
const props = defineProps<CreatePageProps>();

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-xl sm:text-2xl lg:text-3xl',
        icon: 'lucide:monitor-cog',
        to: '/admin',
    },
    {
        label: 'Crear',
        class: 'text-xl sm:text-2xl lg:text-3xl',
        icon: 'lucide:square-plus',
    },
];

const activeTab = ref(page.props.ziggy.location.split('/')[2]);

const handleTabChange = (tab: string | number) => {
    activeTab.value = tab as string;
    router.visit(route('admin.create', tab), { preserveScroll: true, replace: true });
};

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <UTabs
        :items="adminTabItems"
        class="mt-8"
        variant="link"
        :model-value="activeTab"
        @update:model-value="handleTabChange"
        :ui="{ label: activeBreakpoint || 'hidden' }"
    >
        <template #mangas><MangaCreateForm v-bind="props" /></template>
        <template #people><PersonCreateForm /></template>
        <template #magazines><MagazineCreateForm /></template>
        <template #users><UserCreateForm /></template>
        <template #tags><TagCreateForm /></template>
    </UTabs>
</template>
