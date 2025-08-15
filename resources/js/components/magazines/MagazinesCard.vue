<script setup lang="ts">
import { Magazine } from '@/types';
import { frequencies } from '@/utils/constants';
import { router } from '@inertiajs/vue3';

defineProps<{ magazines?: Magazine[] }>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <UCard v-for="magazine in magazines" :key="magazine.id" variant="subtle" class="shadow-md" :ui="{ body: 'p-2 sm:p-3' }">
            <div class="flex gap-2 overflow-hidden">
                <!--Imagen-->
                <div class="max-w-[150px] basis-1/4">
                    <ULink :to="`magazines/${magazine.id}`">
                        <UIcon name="lucide:book-image" class="bg-accented mx-auto size-20 cursor-pointer rounded-lg p-2" />
                    </ULink>
                </div>
                <!--Datos-->
                <div class="flex min-w-0 basis-3/4 flex-col gap-1 px-1">
                    <!--Nombre-->
                    <div class="flex items-center">
                        <ULink :to="`magazines/${magazine.id}`" class="min-w-0 flex-1 cursor-pointer">
                            <h3 class="truncate text-lg font-bold" @click="router.visit(route('magazines.show', magazine.id))">
                                {{ magazine.name }}
                            </h3>
                        </ULink>
                    </div>
                    <!--Editorial-->
                    <div class="text-muted -mt-2 flex items-center text-sm lg:text-base">
                        <p>{{ magazine.publisher }}</p>
                    </div>
                    <!--Demografía, fecha, periodicidad-->
                    <div class="mt-1 flex flex-wrap gap-x-2 text-sm lg:text-base">
                        <p v-if="magazine.demography" class="capitalize">
                            <UIcon name="lucide:square-user-round" class="mb-1 inline" /> {{ magazine.demography }}
                        </p>
                        <p v-if="magazine.date !== undefined">
                            <UIcon name="lucide:calendar" class="mb-1 inline" />
                            {{
                                new Intl.DateTimeFormat('es', {
                                    day: 'numeric',
                                    month: 'short',
                                    year: 'numeric',
                                }).format(new Date(magazine.date))
                            }}
                        </p>
                        <p v-if="magazine.frequency">
                            <UIcon name="lucide:calendar-1" class="mb-1 inline" />{{
                                frequencies.find((frequency) => frequency.value === magazine.frequency)?.label
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </UCard>
    </div>
</template>
