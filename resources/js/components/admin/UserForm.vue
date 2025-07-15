<script setup lang="ts">
import BaseForm from '@/components/admin/BaseForm.vue';
import SecretInput from '@/components/SecretInput.vue';
import { type User, UserForm } from '@/types';
import { userSchema } from '@/utils/zodSchemas';
import { ref } from 'vue';

const props = defineProps<{
    item?: User;
    purpose: 'create' | 'edit';
}>();

const initialValues: UserForm = {
    name: props.item?.name,
    email: props.item?.email,
    password: props.purpose === 'edit' ? 'contraseña' : undefined,
    passwordConfirmation: props.purpose === 'edit' ? 'contraseña' : undefined,
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
        :schema="userSchema"
        :initial-values="initialValues"
    >
        <template #default="{ form }">
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
                    <SecretInput v-model="form.password" v-model:show="show" />
                </UFormField>
                <UFormField label="Repetir contraseña" name="passwordConfirmation" class="w-full" required>
                    <SecretInput v-model="form.passwordConfirmation" v-model:show="show" />
                </UFormField>
            </div>
        </template>
    </BaseForm>
</template>
