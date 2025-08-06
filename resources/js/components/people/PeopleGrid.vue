<script setup lang="ts">
import { Person } from '@/types';
import { router } from '@inertiajs/vue3';

defineProps<{ people?: Person[] }>();
</script>

<template>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <UCard
            v-for="person of people"
            :key="person.id"
            variant="subtle"
            class="hover:bg-accented cursor-pointer shadow-md"
            @click="router.visit(route('people.show', person.id))"
        >
            <div class="relative flex aspect-[1/1.4142] flex-col items-center justify-center pb-6">
                <div class="flex w-full flex-col items-center">
                    <UIcon name="lucide:user" class="bg-accented my-3 size-20 rounded-full p-2" />
                    <p class="1. line-clamp-2 h-[2.5rem] text-center leading-tight font-semibold">{{ person.name }} {{ person.surname }}</p>
                </div>
                <div class="absolute bottom-0">
                    <p class="line-clamp-1 text-center font-light">{{ person.kanjiName }} {{ person.kanjiSurname }}</p>
                </div>
            </div>
        </UCard>
    </div>
</template>
