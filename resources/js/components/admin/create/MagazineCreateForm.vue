<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import { MagazineCreateForm } from '@/types';
import { demographies, frequencies } from '@/utils/constants';
import { magazineSchema } from '@/utils/zodSchemas';
import { useForm } from '@inertiajs/vue3';

const toast = useToast();

//Variables
const form = useForm<MagazineCreateForm>({
    name: undefined,
    publisher: undefined,
    demography: undefined,
    date: undefined,
    frequency: undefined,
});

//Métodos
const onSubmit = async () => {
    form.transform((data) => ({ ...data, date: data.date?.toString() })).post(route('magazine.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ title: 'Revista creada satisfactoriamente.' });
            form.reset();
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    });
};
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="magazineSchema" class="mt-4 flex flex-col gap-4 md:gap-8" :state="form" @submit="onSubmit">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>

                <UFormField label="Editorial" name="publisher" class="w-full" required>
                    <UInput v-model="form.publisher" class="w-full" />
                </UFormField>
            </div>
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Demografía" name="demography" class="w-full" required>
                    <USelect v-model="form.demography" :items="demographies" placeholder="Selecciona una demografía" class="w-full" />
                </UFormField>

                <UFormField label="Periodicidad" name="frequency" class="w-full" required>
                    <USelect v-model="form.frequency" :items="frequencies" placeholder="Selecciona una periodicidad" class="w-full" />
                </UFormField>
            </div>

            <UFormField label="Fecha" name="date" class="md:w-1/2 md:pr-3">
                <DatePicker v-model="form.date" decades />
            </UFormField>

            <UButton type="submit" class="text-md mt-4 justify-center" :loading="form.processing">
                {{ form.processing ? 'Creando' : 'Crear' }}
            </UButton>
        </UForm>
    </UCard>
</template>
