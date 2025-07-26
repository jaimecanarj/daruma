import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMagazinesStore = defineStore('magazines', () => {
    const state = ref({
        search: '',
        publishers: [],
        date: undefined,
        demographies: [],
        frequencies: [],
        order: 'nameAsc',
    });

    const filters = ref<{ publishers: string[] }>({
        publishers: [],
    });

    return { state, filters };
});
