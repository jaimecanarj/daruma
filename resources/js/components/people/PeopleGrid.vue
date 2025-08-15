<script setup lang="ts">
import { Person } from '@/types';

defineProps<{ people?: Person[] }>();
</script>

<template>
    <div class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7">
        <ULink v-for="person of people" :key="person.id" :to="`/people/${person.id}`">
            <UCard variant="subtle" class="hover:bg-accented cursor-pointer shadow-md">
                <div class="relative flex aspect-[1/1.4142] flex-col items-center justify-center pb-6">
                    <div class="flex w-full flex-col items-center">
                        <UIcon name="lucide:user" class="bg-accented my-3 size-16 rounded-full p-2 sm:size-20" />
                        <p class="line-clamp-2 h-[2.5rem] text-center leading-tight font-semibold">{{ person.name }} {{ person.surname }}</p>
                    </div>
                    <div class="absolute bottom-0">
                        <p class="line-clamp-1 text-center text-xs font-light lg:text-sm">{{ person.kanjiName }} {{ person.kanjiSurname }}</p>
                    </div>
                </div>
            </UCard>
        </ULink>
    </div>
</template>
