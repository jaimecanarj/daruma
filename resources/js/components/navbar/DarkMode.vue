<script setup lang="ts">
import { useColorMode } from '@vueuse/core';
import { computed } from 'vue';

const { store, system } = useColorMode();

const isDarkMode = computed({
    //Comprueba sistema si el valor almacenado es auto
    get() {
        if (store.value === 'auto') {
            return system.value === 'dark';
        } else {
            return store.value === 'dark';
        }
    },
    set() {
        if (store.value === 'auto') {
            store.value = system.value === 'dark' ? 'light' : 'dark';
        } else {
            store.value = store.value === 'dark' ? 'light' : 'dark';
        }
    },
});
</script>

<template>
    <UButton :icon="isDarkMode ? 'lucide:moon' : 'lucide:sun'" color="neutral" variant="ghost" @click="isDarkMode = !isDarkMode" />
</template>
