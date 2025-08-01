<script setup lang="ts"></script>

<template>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
        <div v-for="item in 20" :key="item" class="relative aspect-[1/1.4142] rounded-md shadow-md">
            <USkeleton class="h-full w-full" />
        </div>
    </div>
</template>
