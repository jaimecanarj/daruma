<script setup lang="ts">
import { Person, PersonCreateForm } from '@/types';
import { personSchema } from '@/utils/zodSchemas';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    item?: Person;
    purpose: 'create' | 'edit';
}>();

const submitLabel = props.purpose === 'create' ? 'Crea' : 'Edita';

const toast = useToast();

const form = useForm<PersonCreateForm>({
    name: props.item?.name,
    kanjiName: props.item?.kanjiName,
    surname: props.item?.surname,
    kanjiSurname: props.item?.kanjiSurname,
});

const onSubmit = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            toast.add({ title: `Persona ${props.purpose == 'create' ? 'creada' : 'actualizada'} satisfactoriamente.` });
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    };

    if (props.purpose === 'create') {
        form.post(route('person.store'), options);
    } else {
        form.put(route('person.update', { person: props.item?.id }), options);
    }
};
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="personSchema" class="mt-4 flex flex-col gap-4 md:gap-8" :state="form" @submit="onSubmit">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Nombre 「漢字」" name="kanjiName" class="w-full">
                    <UInput v-model="form.kanjiName" class="w-full" />
                </UFormField>
            </div>
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Apellido" name="surname" class="w-full">
                    <UInput v-model="form.surname" class="w-full" />
                </UFormField>

                <UFormField label="Apellido 「漢字」" name="kanjiSurname" class="w-full">
                    <UInput v-model="form.kanjiSurname" class="w-full" />
                </UFormField>
            </div>
            <UButton type="submit" class="text-md mt-4 justify-center" :loading="form.processing">
                {{ form.processing ? `${submitLabel}ndo` : `${submitLabel}r` }}
            </UButton>
        </UForm>
    </UCard>
</template>
