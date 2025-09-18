<script setup lang="ts">
import { Magazine, Manga, Name, Person } from '@/types';
import axios from 'axios';
import { ref, watch } from 'vue';

const searchTerm = ref('');
const isLoading = ref(false);
const modalOpen = ref(false);
const searchResults = ref<any[]>([]);

//Ejecutar búsqueda al abrir el modal
watch(modalOpen, async (isOpen) => {
    if (isOpen && searchResults.value.length === 0) {
        await fetchSearchData();
    }
    if (!isOpen) {
        searchTerm.value = '';
    }
});

defineShortcuts({
    meta_k: () => {
        modalOpen.value = !modalOpen.value;
    },
});

//Función que formatea nombres de mangas para buscar por ellos
const formatNames = (names: Name[] | undefined) => {
    if (names === undefined || names.length === 0) return '';
    return names.map((name) => name.name).join(', ');
};

//Función para obtener los datos de la búsqueda
const fetchSearchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/search');

        // Convertir los resultados a un formato adecuado para el CommandPalette
        searchResults.value = [
            {
                id: 'mangas',
                label: 'Mangas',
                items: response.data.mangas.map((manga: Manga) => ({
                    label: manga.name,
                    names: formatNames(manga.names),
                    avatar: {
                        src: `/storage/${manga.cover}`,
                    },
                    to: `/mangas/${manga.id}`,
                })),
            },
            {
                id: 'people',
                label: 'Personas',
                items: response.data.people.map((person: Person) => ({
                    label: `${person.name} ${person.surname}`,
                    suffix: `${person.kanjiName} ${person.kanjiSurname}`,
                    to: `/people/${person.id}`,
                })),
            },
            {
                id: 'magazines',
                label: 'Revistas',
                items: response.data.magazines.map((magazine: Magazine) => ({
                    label: magazine.name,
                    suffix: magazine.publisher,
                    to: `/magazines/${magazine.id}`,
                })),
            },
        ];
    } catch (error) {
        console.error('Error al cargar datos de búsqueda:', error);
    } finally {
        isLoading.value = false;
    }
};

//Función que se ejecuta cuando se selecciona un elemento
const resetSearch = () => {
    modalOpen.value = false;
    searchTerm.value = '';
};
</script>

<template>
    <UModal v-model:open="modalOpen">
        <UButton icon="lucide:search" color="neutral" variant="ghost" />
        <template #content>
            <UCommandPalette
                v-model:search-term="searchTerm"
                placeholder="Buscar..."
                :loading="isLoading"
                :groups="searchResults"
                :fuse="{ fuseOptions: { keys: ['label', 'names', 'suffix'], threshold: 0.3 } }"
                @update:model-value="resetSearch"
                class="h-80"
            />
        </template>
    </UModal>
</template>
