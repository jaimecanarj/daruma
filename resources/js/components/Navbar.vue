<script setup lang="ts">
import logo from '@/assets/logo.svg';
import DarkMode from '@/components/DarkMode.vue';
import SearchBar from '@/components/SearchBar.vue';
import { SharedData } from '@/types';
import { links } from '@/utils/links';
import { router, usePage } from '@inertiajs/vue3';
import { DropdownMenuItem } from '@nuxt/ui/components/DropdownMenu.vue';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { computed, ref } from 'vue';

const page: { props: SharedData } = usePage();
const route = computed(() => page.props.ziggy.location);

const activeBreakpoint = useBreakpoints(breakpointsTailwind).active();

const slideOver = ref(false);

const userItems = ref<DropdownMenuItem[][]>([
    [
        {
            label: 'Perfil',
            icon: 'lucide:user',
            to: '/profile',
        },
        {
            label: 'Opciones',
            icon: 'lucide:settings',
            to: '/profile',
        },
    ],
    [
        {
            label: 'Cerrar sesión',
            icon: 'lucide:log-out',
            class: 'cursor-pointer',
            onSelect() {
                router.post(
                    '/logout',
                    {},
                    {
                        onSuccess: () => router.visit('/login'),
                    },
                );
            },
        },
    ],
]);
</script>

<template>
    <nav class="border-default bg-default/60 sticky top-0 z-[1] border-b backdrop-blur">
        <UContainer class="flex items-center justify-between py-3">
            <template v-if="activeBreakpoint">
                <!--Logo-->
                <div class="flex items-center gap-2">
                    <img :src="logo" class="h-11 w-11" alt="logo" />
                    <p class="text-2xl font-semibold">Daruma</p>
                </div>
                <!--Enlaces-->
                <div class="flex gap-4">
                    <template v-for="link of links" :key="link.path">
                        <!--Activo en caso de empezar por la ruta, excepto / que tiene que ser exacto-->
                        <ULink
                            :to="link.path"
                            :active="(link.path === '/' && route === '/') || (link.path !== '/' && route.startsWith(link.path.slice(1)))"
                            active-class="text-primary font-bold"
                            class="flex flex-col items-center"
                        >
                            <UIcon :name="link.icon" class="h-7 w-7" />
                            <p class="text-xs">{{ link.label }}</p>
                        </ULink>
                    </template>
                </div>
            </template>
            <template v-else>
                <USlideover side="left" title="Daruma" v-model:open="slideOver" :ui="{ content: 'z-[2]' }">
                    <!--Logo-->
                    <div class="flex cursor-pointer items-center gap-2">
                        <img :src="logo" class="h-10 w-10" alt="logo" />
                        <p class="text-xl font-semibold">Daruma</p>
                    </div>

                    <template #body>
                        <div class="flex flex-col gap-6">
                            <template v-for="link of links" :key="link.path">
                                <!--Activo en caso de empezar por la ruta, excepto / que tiene que ser exacto-->
                                <ULink
                                    :to="link.path"
                                    :active="(link.path === '/' && route === '/') || (link.path !== '/' && route.startsWith(link.path.slice(1)))"
                                    active-class="text-primary font-bold"
                                    class="flex items-center gap-3"
                                    @click="slideOver = false"
                                >
                                    <UIcon :name="link.icon" class="h-7 w-7" />
                                    <p>{{ link.label }}</p>
                                </ULink>
                            </template>
                        </div>
                    </template>
                </USlideover>
            </template>
            <!--Botones lateral derecha-->
            <div class="flex items-center gap-2">
                <!--Botón modo de color-->
                <DarkMode />
                <!--Botón de búsqueda-->
                <SearchBar />
                <!--Botón de usuario-->
                <UDropdownMenu
                    :items="userItems"
                    :content="{
                        align: 'end',
                        side: 'bottom',
                        sideOffset: 18,
                    }"
                    :ui="{
                        content: 'w-48',
                    }"
                >
                    <UAvatar src="#" alt="Avatar" class="cursor-pointer" />
                </UDropdownMenu>
            </div>
        </UContainer>
    </nav>
</template>
