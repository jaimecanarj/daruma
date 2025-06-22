<script setup lang="ts">
import { type Tag, type TagCreateForm } from '@/types';
import { tagTypeOptions } from '@/utils/constants';
import { tagSchema } from '@/utils/zodSchemas';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    item?: Tag;
    purpose: 'create' | 'edit';
}>();

const submitLabel = props.purpose === 'create' ? 'Crea' : 'Edita';

const toast = useToast();

const form = useForm<TagCreateForm>({
    name: props.item?.name,
    type: props.item?.type,
});

const onSubmit = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            toast.add({ title: `Etiqueta ${props.purpose == 'create' ? 'creada' : 'actualizada'} satisfactoriamente.` });
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    };

    if (props.purpose === 'create') {
        form.post(route('tag.store'), options);
    } else {
        form.put(route('tag.update', { tag: props.item?.id }), options);
    }
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
            <UButton type="submit" class="text-md mt-4 justify-center" :loading="form.processing">
                {{ form.processing ? `${submitLabel}ndo` : `${submitLabel}r` }}
            </UButton>
        </UForm>
    </UCard>
</template>
