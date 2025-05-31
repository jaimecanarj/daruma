<script setup lang="ts">
import { useBase64 } from '@vueuse/core';
import { ref } from 'vue';

const cover = defineModel<File>();
const tempImage = ref<File>();

const { base64 } = useBase64(tempImage);

defineExpose({
    clear: () => (tempImage.value = undefined),
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target && target.files) {
        tempImage.value = target.files[0];
    }
};
</script>

<template>
    <div class="flex w-full flex-col items-center">
        <div :class="[{ 'border-accented border-2 border-dashed': !tempImage }, 'flex h-[392px] w-[280px] items-center justify-center rounded']">
            <img v-if="tempImage" :src="base64" class="h-full w-full rounded object-cover" alt="tempImage" />
            <UIcon v-else name="lucide:image" class="text text-dimmed size-24" />
        </div>
        <UInput type="file" accept="image/*" class="mt-3" @change="handleFileChange" @input="cover = $event.target.files[0]" />
    </div>
</template>
