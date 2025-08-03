<script setup lang="ts">
import BaseForm from '@/components/admin/forms/BaseForm.vue';
import SecretInput from '@/components/formComponents/SecretInput.vue';
import { type User, UserForm } from '@/types';
import { roles } from '@/utils/constants';
import { userSchema } from '@/utils/zodSchemas';
import { ref } from 'vue';

const props = defineProps<{
    item?: User;
    purpose: 'create' | 'edit';
}>();

const initialValues: UserForm = {
    name: props.item?.name,
    email: props.item?.email,
    avatar: undefined,
    password: props.purpose === 'edit' ? 'contraseña' : undefined,
    passwordConfirmation: props.purpose === 'edit' ? 'contraseña' : undefined,
    roles: undefined,
};

const show = ref<boolean>();
</script>

<template>
    <BaseForm
        :item="item"
        :purpose="purpose"
        resource-name="Usuario"
        resource-gender="masculine"
        resource-route="user"
        update-method="post"
        :schema="userSchema"
        :initial-values="initialValues"
    >
        <template #default="{ form }">
            <div class="flex flex-col gap-6 sm:flex-row">
                <UFormField name="avatar" required class="flex justify-center sm:basis-2/5">
                    <UFileUpload v-model="form.avatar" accept="image/*" label="Avatar" class="size-36">
                        <template #default="{ open }">
                            <div v-if="props.item?.avatar && !form.avatar" @click.prevent="open(undefined)">
                                <img :src="`/storage/${props.item.avatar}`" class="size-36 rounded-lg object-cover" alt="avatar" />
                            </div>
                        </template>
                    </UFileUpload>
                </UFormField>
                <div class="flex w-full flex-col gap-6 sm:basis-3/5">
                    <UFormField label="Nombre" name="name" class="w-full" required>
                        <UInput v-model="form.name" class="w-full" />
                    </UFormField>
                    <UFormField label="Email" name="email" class="w-full" required>
                        <UInput v-model="form.email" class="w-full" />
                    </UFormField>
                </div>
            </div>
            <div class="flex flex-col gap-6 sm:flex-row">
                <div class="flex w-full flex-col gap-6 sm:order-2 sm:basis-3/5">
                    <UFormField label="Contraseña" name="password" class="w-full" required>
                        <SecretInput v-model="form.password" v-model:show="show" />
                    </UFormField>
                    <UFormField label="Repetir contraseña" name="passwordConfirmation" class="w-full" required>
                        <SecretInput v-model="form.passwordConfirmation" v-model:show="show" />
                    </UFormField>
                </div>
                <UFormField label="Roles" name="admin" class="sm:order-1 sm:basis-2/5">
                    <UCheckboxGroup v-model="form.roles" color="neutral" variant="table" :items="roles" />
                </UFormField>
            </div>
        </template>
    </BaseForm>
    <!--Hacer que la contraseña no sea obligatoria en edicion, pero si en creacion (revisar cover en mangas) -->
    <!--Al crear, simplemente asignar los roles al final de la creación-->
    <!--Al editar, comprobar los roles del usuario y eliminar o añadir según el caso-->
    <!--La parte complicada es obtener los roles previos a la edición-->
</template>
