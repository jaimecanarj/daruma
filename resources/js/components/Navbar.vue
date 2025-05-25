<script setup lang="ts">
import logo from '@/assets/logo.svg';
import { SharedData } from '@/types';
import { links } from '@/utils/links';
import { usePage } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
import { computed } from 'vue';

const page: { props: SharedData } = usePage();
const route = page.props.ziggy.location;
const { store } = useColorMode();

const isDarkMode = computed({
    get() {
        return store.value === 'dark';
    },
    set() {
        store.value = store.value === 'dark' ? 'light' : 'dark';
    },
});
</script>

<template>
    <nav class="border-default sticky border-b backdrop-blur">
        <UContainer class="flex items-center justify-between py-3">
            <!--Logo-->
            <div class="flex items-center gap-2">
                <img :src="logo" class="h-11 w-11" />
                <p class="text-2xl font-semibold">Daruma</p>
            </div>
            <!--Enlaces-->
            <div class="flex gap-4">
                <template v-for="link of links" :key="link.path">
                    <!--Activo en caso de empezar por la ruta, excepto / que tiene que ser exacto-->
                    <ULink
                        :to="link.path"
                        :active="(link.path === '/' && route === '/') || (link.path !== '/' && route.startsWith(link.path))"
                        active-class="text-primary font-bold"
                        class="flex flex-col items-center"
                    >
                        <UIcon :name="link.icon" class="h-7 w-7" />
                        <p class="text-xs">{{ link.label }}</p>
                    </ULink>
                </template>
            </div>
            <!--Botones lateral derecha-->
            <div class="flex items-center gap-2">
                <!--Botón modo de color-->
                <UButton
                    :icon="isDarkMode ? 'lucide:moon' : 'lucide:sun'"
                    color="neutral"
                    variant="ghost"
                    class="cursor-pointer"
                    @click="isDarkMode = !isDarkMode"
                />
                <!--Botón de búsqueda-->
                <UButton icon="lucide:search" color="neutral" variant="ghost" class="cursor-pointer" />
                <!--Botón de usuario-->
                <UAvatar src="#" alt="Avatar" class="cursor-pointer" />
            </div>
        </UContainer>
    </nav>
</template>
