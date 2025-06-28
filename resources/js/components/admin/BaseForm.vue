<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    item?: any;
    purpose: 'create' | 'edit';
    resourceName: string;
    resourceGender: 'masculine' | 'feminine';
    resourceRoute: string;
    updateMethod?: 'post' | 'put';
    schema: any;
    initialValues: Record<string, any>;
    formTransform?: (data: any) => any;
}>();

const emit = defineEmits(['success', 'error']);

const toast = useToast();
const submitLabel = computed(() => (props.purpose === 'create' ? 'Crea' : 'Edita'));

const form = useForm(props.initialValues);

const onSubmit = () => {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            toast.add({
                title: `${props.resourceName} ${props.purpose == 'create' ? 'cread' : 'actualizad'}${props.resourceGender === 'masculine' ? 'o' : 'a'} satisfactoriamente.`,
            });
            emit('success');
        },
        onError: () => {
            toast.add({ title: 'Hubo algún problema.' });
            emit('error', form);
        },
    };

    const formData = props.formTransform ? props.formTransform(form) : form;

    if (props.purpose === 'create') {
        formData.post(route(`${props.resourceRoute}.store`), options);
    } else {
        const routeParams = { [props.resourceRoute]: props.item?.id };

        if (props.updateMethod === 'post') {
            formData.post(route(`${props.resourceRoute}.update`, routeParams), options);
        } else {
            formData.put(route(`${props.resourceRoute}.update`, routeParams), options);
        }
    }
};

defineExpose({ form });
</script>

<template>
    <UCard class="bg-muted mx-auto mt-10 w-full md:max-w-3xl">
        <UForm :schema="props.schema" class="mt-4 flex flex-col gap-4 md:gap-8" :state="form" @submit="onSubmit">
            <slot :form="form"></slot>

            <UButton type="submit" class="text-md mt-4 justify-center" :loading="form.processing">
                {{ form.processing ? `${submitLabel}ndo` : `${submitLabel}r` }}
            </UButton>
        </UForm>
    </UCard>
</template>
