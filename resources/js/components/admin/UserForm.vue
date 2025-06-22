<script setup lang="ts">
import { type User, UserCreateForm } from '@/types';
import { userSchema } from '@/utils/zodSchemas';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    item?: User;
    purpose: 'create' | 'edit';
}>();

const submitLabel = props.purpose === 'create' ? 'Crea' : 'Edita';

const toast = useToast();

const form = useForm<UserCreateForm>({
    name: props.item?.name,
    email: props.item?.email,
    password: props.purpose === 'edit' ? 'contraseña' : undefined,
    passwordConfirmation: props.purpose === 'edit' ? 'contraseña' : undefined,
});

const onSubmit = () => {
    console.log('voy o no');
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            toast.add({ title: `Usuario ${props.purpose == 'create' ? 'creado' : 'actualizado'} satisfactoriamente.` });
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
        },
    };

    if (props.purpose === 'create') {
        form.post(route('user.store'), options);
    } else {
        form.put(route('user.update', { user: props.item?.id }), options);
    }
};
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="userSchema" class="mt-4 flex flex-col gap-4 md:gap-8" :state="form" @submit="onSubmit">
            <div class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Nombre" name="name" class="w-full" required>
                    <UInput v-model="form.name" class="w-full" />
                </UFormField>
                <UFormField label="Email" name="email" class="w-full" required>
                    <UInput v-model="form.email" class="w-full" />
                </UFormField>
            </div>
            <div v-if="purpose === 'create'" class="flex w-full flex-col gap-6 md:flex-row">
                <UFormField label="Contraseña" name="password" class="w-full" required>
                    <UInput v-model="form.password" class="w-full" />
                </UFormField>
                <UFormField label="Repetir contraseña" name="passwordConfirmation" class="w-full" required>
                    <UInput v-model="form.passwordConfirmation" class="w-full" />
                </UFormField>
            </div>
            <UButton type="submit" class="text-md mt-4 justify-center" :loading="form.processing">
                {{ form.processing ? `${submitLabel}ndo` : `${submitLabel}r` }}
            </UButton>
        </UForm>
    </UCard>
</template>
