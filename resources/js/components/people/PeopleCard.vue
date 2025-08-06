<script setup lang="ts">
import { Person } from '@/types';
import { router } from '@inertiajs/vue3';

defineProps<{ people?: Person[] }>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <UCard v-for="person in people" :key="person.id" variant="subtle" class="shadow-md" :ui="{ body: 'p-2 sm:p-3' }">
            <div class="flex gap-2 overflow-hidden">
                <!--Imagen-->
                <div class="max-w-[150px] basis-1/4">
                    <UIcon
                        name="lucide:user"
                        class="bg-accented mx-auto size-20 cursor-pointer rounded-full p-2"
                        @click="router.visit(route('people.show', person.id))"
                    />
                </div>
                <!--Datos-->
                <div class="flex min-w-0 basis-3/4 flex-col gap-1 px-1">
                    <!--Nombre-->
                    <div class="flex items-center">
                        <h3 class="min-w-0 flex-1 cursor-pointer truncate text-lg font-bold" @click="router.visit(route('people.show', person.id))">
                            {{ person.name }} {{ person.surname }}
                        </h3>
                    </div>
                    <!--Editorial-->
                    <div class="text-muted -mt-2 flex items-center text-sm lg:text-base">
                        <p>{{ person.kanjiName }} {{ person.kanjiSurname }}</p>
                    </div>
                    <!--Mangas escritos e ilustrados-->
                    <div class="mt-1 flex flex-wrap gap-x-2 text-sm lg:text-base">
                        <p v-if="person.writerCount">
                            <UIcon name="lucide:pen-tool" class="mb-1 inline" /> {{ person.writerCount }}
                            {{ person.writerCount > 1 ? 'escritos' : 'escrito' }}
                        </p>
                        <p v-if="person.illustratorCount">
                            <UIcon name="lucide:brush" class="mb-1 inline" /> {{ person.illustratorCount }}
                            {{ person.illustratorCount > 1 ? 'ilustrados' : 'ilustrado' }}
                        </p>
                    </div>
                </div>
            </div>
        </UCard>
    </div>
</template>
