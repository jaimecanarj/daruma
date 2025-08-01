<script setup lang="ts">
import logo from '@/assets/logo.svg';
import DarkMode from '@/components/navbar/DarkMode.vue';
import { es } from '@nuxt/ui/locale';
</script>

<template>
    <UApp :locale="es">
        <UContainer class="flex h-screen items-center justify-center">
            <UCard variant="subtle" class="w-sm">
                <div class="flex justify-between">
                    <div class="flex items-center gap-1">
                        <img :src="logo" class="h-11 w-11" alt="logo" />
                        <p class="text-2xl font-semibold">Daruma</p>
                    </div>
                    <DarkMode />
                </div>
                <slot />
            </UCard>
        </UContainer>
    </UApp>
</template>
