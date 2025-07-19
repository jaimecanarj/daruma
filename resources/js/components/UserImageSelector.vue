<script setup lang="ts">
import { useBase64 } from '@vueuse/core';
import { ref, useTemplateRef } from 'vue';

defineProps<{ storedImage?: string }>();

const avatar = defineModel<File>();
const tempImage = ref<File>();

const { base64 } = useBase64(tempImage);

defineExpose({
    clear: () => (tempImage.value = undefined),
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target && target.files) {
        tempImage.value = target.files[0];
        avatar.value = target.files[0];
    }
};

const fileInput = useTemplateRef<HTMLInputElement>('fileInput');
</script>

<template>
    <div class="flex">
        <div
            :class="[
                { 'border-accented border-2 border-dashed': !tempImage && !storedImage },
                'flex size-36 items-center justify-center rounded-full',
            ]"
            @click="fileInput?.click()"
        >
            <img
                v-if="tempImage || storedImage"
                :src="tempImage ? base64 : `/storage/${storedImage}`"
                class="size-full rounded-full object-cover"
                alt="imagen"
            />
            <UIcon v-else name="lucide:image" class="text text-dimmed size-2/5" />
        </div>
        <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleFileChange" />
    </div>
</template>
