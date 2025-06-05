<script setup lang="ts">
import { TagCreateForm } from '@/types';
import { tagTypeOptions } from '@/utils/constants';
import { tagSchema } from '@/utils/zodSchemas';
import { useForm } from '@inertiajs/vue3';

const toast = useToast();

const form = useForm<TagCreateForm>({
    name: undefined,
    type: undefined,
});

const onSubmit = () => {
    form.post(route('tag.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            toast.add({ title: 'Etiqueta creada satisfactoriamente.' });
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    });
};
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="tagSchema" class="mt-4 flex flex-col gap-4 md:gap-8" :state="form" @submit="onSubmit">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Tipo" name="type" class="w-full" required>
                    <USelect v-model="form.type" :items="tagTypeOptions" placeholder="Selecciona un tipo" class="w-full" />
                </UFormField>
            </div>
            <UButton type="submit" class="text-md mt-4 justify-center" :disabled="form.processing">
                {{ form.processing ? 'Creando' : 'Crear' }}
            </UButton>
        </UForm>
    </UCard>
</template>
