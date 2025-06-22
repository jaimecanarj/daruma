<script setup lang="ts">
import TagForm from '@/components/admin/TagForm.vue';
import UserForm from '@/components/admin/UserForm.vue';
import { SharedData } from '@/types';
import { Deferred, usePage } from '@inertiajs/vue3';

const page: { props: SharedData } = usePage();

const breadcrumbItems = [
    {
        label: 'Admin',
        class: 'text-3xl',
        icon: 'lucide:monitor-cog',
        to: '/admin',
    },
    {
        label: 'Editar',
        class: 'text-3xl',
        icon: 'lucide:square-pen',
    },
];

let component: unknown | null = null;

switch (page.props.ziggy.location.split('/')[2]) {
    case 'manga':
        component = TagForm;
        break;
    case 'person':
        component = TagForm;
        break;
    case 'magazine':
        component = TagForm;
        break;
    case 'user':
        component = UserForm;
        break;
    case 'tag':
        component = TagForm;
        break;
}
</script>

<template>
    <UBreadcrumb :items="breadcrumbItems" :ui="{ linkLeadingIcon: 'size-8' }" />
    <Deferred data="item">
        <template #fallback>
            <USkeleton class="mx-auto mt-10 h-[750px] w-full md:max-w-3xl" />
        </template>
        <component :is="component" v-bind="page.props" purpose="edit" />
    </Deferred>
</template>
