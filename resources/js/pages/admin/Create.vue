<script setup lang="ts">
import MagazineForm from '@/components/admin/MagazineForm.vue';
import MangaForm from '@/components/admin/MangaForm.vue';
import PersonForm from '@/components/admin/PersonForm.vue';
import TagForm from '@/components/admin/TagForm.vue';
import UserForm from '@/components/admin/UserForm.vue';
import { SharedData } from '@/types';
import { adminTabItems } from '@/utils/constants';
import { Head, router, usePage } from '@inertiajs/vue3';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { ref } from 'vue';

const page: { props: SharedData } = usePage();

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
    //No cambiar si es la vista actual
    if (tab === activeTab.value) return;

    activeTab.value = tab as string;
    router.visit(route('admin.create', tab), { preserveScroll: true, replace: true });
};

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();
</script>

<template>
    <Head>
        <title>Creación</title>
    </Head>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <UTabs
        :items="adminTabItems"
        class="mt-8"
        variant="link"
        :model-value="activeTab"
        @update:model-value="handleTabChange"
        :ui="{ label: activeBreakpoint || 'hidden' }"
    >
        <template #mangas><MangaForm purpose="create" v-bind="page.props" /></template>
        <template #people><PersonForm purpose="create" /></template>
        <template #magazines><MagazineForm purpose="create" /></template>
        <template #users><UserForm purpose="create" /></template>
        <template #tags><TagForm purpose="create" /></template>
    </UTabs>
</template>
