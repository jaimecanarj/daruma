import axios from 'axios';
import { ref } from 'vue';

export const useFetchTable = <T>(fetchRoute: string) => {
    const data = ref<T[]>([]);

    const fetching = ref(false);

    const fetchData = () => {
        fetching.value = true;
        axios
            .get(route(fetchRoute))
            .then((response) => {
                data.value = response.data;
            })
            .finally(() => {
                fetching.value = false;
            });
    };

    return { fetchData, data, fetching };
};
