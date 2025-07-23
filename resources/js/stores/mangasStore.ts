import { Magazine, Person, Tag } from '@/types';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMangasStore = defineStore('mangas', () => {
    const state = ref({
        search: '',
        volumes: undefined,
        date: undefined,
        tags: [],
        order: 'updateDesc',
        people: [],
        language: undefined,
        magazines: [],
        demographies: [],
        finished: [],
        readingDirection: [],
    });

    const filters = ref<{ tags: Tag[]; people: Person[]; magazines: Magazine[] }>({
        tags: [],
        people: [],
        magazines: [],
    });

    return { state, filters };
});
