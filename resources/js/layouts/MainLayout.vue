<script setup lang="ts">
import EmailAlert from '@/components/EmailAlert.vue';
import Navbar from '@/components/navbar/Navbar.vue';
import { es } from '@nuxt/ui/locale';
</script>

<template>
    <UApp :locale="es">
        <Navbar />
        <UContainer class="my-5">
            <EmailAlert />
            <slot />
        </UContainer>
    </UApp>
</template>
