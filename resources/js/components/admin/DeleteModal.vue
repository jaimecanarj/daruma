<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const toast = useToast();

const props = defineProps<{ deleteRoute: string; deleteDesc: string; deleteSuccessMessage: string }>();
const emit = defineEmits(['itemDeleted']);

const deleteFormOpen = ref(false);
const deleteForm = useForm<{ id: number | undefined }>({ id: undefined });

const deleteItem = () => {
    deleteForm.delete(route(props.deleteRoute), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteFormOpen.value = false;
            deleteForm.reset();
            toast.add({ title: 'Borrado satisfactorio', description: props.deleteSuccessMessage, icon: 'lucide:circle-check-big', color: 'success' });
            emit('itemDeleted');
        },
        onError: () => {
            const error = Object.values(page.props.errors)[0];
            toast.add({ title: 'Hubo un problema', description: error, icon: 'lucide:server-crash', color: 'error' });
        },
    });
};

defineExpose({
    toggleDeleteForm: () => (deleteFormOpen.value = !deleteFormOpen.value),
    setDeleteFormId: (id: number) => (deleteForm.id = id),
});
</script>

<template>
    <UModal
        v-model:open="deleteFormOpen"
        title="Borrar usuario"
        :description="`Estás a punto de borrar ${deleteDesc}, ¿estás seguro?`"
        :ui="{ footer: 'justify-end' }"
    >
        <template #footer>
            <UButton label="Cancelar" color="neutral" variant="outline" @click="deleteFormOpen = false" />
            <UButton label="Borrar" color="error" :loading="deleteForm.processing" @click="deleteItem" />
        </template>
    </UModal>
</template>
