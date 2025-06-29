<script setup lang="ts">
import BaseForm from '@/components/admin/BaseForm.vue';
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

const show = ref(false);
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
                    <UInput v-model="form.password" :type="show ? 'text' : 'password'" :ui="{ trailing: 'pe-1' }" class="w-full">
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="show ? 'Hide password' : 'Show password'"
                                :aria-pressed="show"
                                aria-controls="password"
                                @click="show = !show"
                            />
                        </template>
                    </UInput>
                </UFormField>
                <UFormField label="Repetir contraseña" name="passwordConfirmation" class="w-full" required>
                    <UInput v-model="form.passwordConfirmation" :type="show ? 'text' : 'password'" :ui="{ trailing: 'pe-1' }" class="w-full">
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="show ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="show ? 'Hide password' : 'Show password'"
                                :aria-pressed="show"
                                aria-controls="password"
                                @click="show = !show"
                            />
                        </template>
                    </UInput>
                </UFormField>
            </div>
        </template>
    </BaseForm>
</template>
