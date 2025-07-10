<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
            toast.add({ title: props.deleteSuccessMessage });
            emit('itemDeleted');
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
