<script setup lang="ts">
import { User } from '@/types';
import { router } from '@inertiajs/vue3';
import { DropdownMenuItem } from '@nuxt/ui/components/DropdownMenu.vue';
import { ref } from 'vue';

defineProps<{ user?: User }>();

//Menú de usuario logueado
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

//Menú de usuario no logueado
const guestItems = ref<DropdownMenuItem[][]>([
    [
        {
            label: 'Iniciar sesión',
            icon: 'lucide:log-in',
            class: 'cursor-pointer',
            onSelect() {
                router.visit('/login');
            },
        },
        {
            label: 'Registrarse',
            icon: 'lucide:user-plus',
            class: 'cursor-pointer',
            onSelect() {
                router.visit('/register');
            },
        },
    ],
]);
</script>

<template>
    <UDropdownMenu
        :items="user ? userItems : guestItems"
        :content="{
            align: 'end',
            side: 'bottom',
            sideOffset: 18,
        }"
        :ui="{
            content: 'w-48',
        }"
    >
        <div class="cursor-pointer">
            <UAvatar v-if="user" :src="`storage/${user.avatar}`" :alt="user.name" class="cursor-pointer" />
            <UButton v-else icon="lucide:user" class="rounded-full" variant="subtle" color="neutral" />
        </div>
    </UDropdownMenu>
</template>
