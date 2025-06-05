<script setup lang="ts">
import MagazineCreateForm from '@/components/admin/create/MagazineCreateForm.vue';
import MangaCreateForm from '@/components/admin/create/MangaCreateForm.vue';
import PersonCreateForm from '@/components/admin/create/PersonCreateForm.vue';
import TagCreateForm from '@/components/admin/create/TagCreateForm.vue';
import UserCreateForm from '@/components/admin/create/UserCreateForm.vue';
import { CreatePageProps, SharedData } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page: { props: SharedData } = usePage();
const props = defineProps<CreatePageProps>();

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-3xl',
        icon: 'lucide:monitor-cog',
        to: '/admin',
    },
    {
        label: 'Crear',
        class: 'text-3xl',
        icon: 'lucide:square-plus',
    },
];

const tabItems = [
    {
        slot: 'manga' as const,
        label: 'Manga',
        value: 'manga',
        icon: 'lucide:book-text',
    },
    {
        slot: 'person' as const,
        label: 'Persona',
        value: 'person',
        icon: 'lucide:users',
    },
    {
        slot: 'magazine' as const,
        label: 'Revista',
        value: 'magazine',
        icon: 'lucide:newspaper',
    },
    {
        slot: 'user' as const,
        label: 'Usuario',
        value: 'user',
        icon: 'lucide:circle-user',
    },
    {
        slot: 'tag' as const,
        label: 'Etiqueta',
        value: 'tag',
        icon: 'lucide:tag',
    },
];

const activeTab = ref(page.props.ziggy.location.split('/')[2]);

const handleTabChange = (tab: string | number) => {
    activeTab.value = tab as string;
    router.visit(route('admin.create', tab), { preserveScroll: true, replace: true });
};
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <UTabs :items="tabItems" class="mt-8" variant="link" :model-value="activeTab" @update:model-value="handleTabChange">
        <template #manga><MangaCreateForm v-bind="props" /></template>
        <template #person><PersonCreateForm /></template>
        <template #magazine><MagazineCreateForm /></template>
        <template #user><UserCreateForm /></template>
        <template #tag><TagCreateForm /></template>
    </UTabs>
</template>
