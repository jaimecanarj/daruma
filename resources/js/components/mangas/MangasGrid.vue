<script setup lang="ts">
import { Manga } from '@/types';
import { router } from '@inertiajs/vue3';

defineProps<{ mangas?: Manga[] }>();
</script>

<template>
    <div class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7">
        <div v-for="manga in mangas" :key="manga.id" class="group relative aspect-[1/1.4142] cursor-pointer rounded-md shadow-md">
            <!--Imagen-->
            <img :src="`/storage/${manga.cover}`" class="size-full rounded-md object-cover" alt="Portada" />
            <!--Efecto hover-->
            <div
                class="absolute inset-0 flex flex-col items-center justify-center bg-black/70 p-3 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                @click="router.visit(route('mangas.show', manga.id))"
            >
                <h3 class="line-clamp-2 text-center text-base font-bold text-white/90 select-none sm:text-lg">{{ manga.name }}</h3>
            </div>
        </div>
    </div>
</template>
