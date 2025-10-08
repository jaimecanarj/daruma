import { Manga, MangaUserData, MangaUserStatus, User } from '@/types';
import axios from 'axios';
import { Ref } from 'vue';

export function useMangaStatus() {
    const changeMangaStatus = async (manga: Manga, user: User, newStatus?: MangaUserStatus) => {
        //Si no se pasa un nuevo status, se calcula el nuevo status
        if (!newStatus) {
            switch (manga.currentUserData?.status) {
                case 'completed':
                case 'on_hold':
                    newStatus = 'reading';
                    newStatus = 'reading';
                    break;
                case 'reading':
                    newStatus = 'completed';
                    break;
                case 'wishlist':
                    break;
                default:
                    newStatus = 'wishlist';
                    break;
            }
        }

        //Si no hay nuevo status, se borra el registro
        if (!newStatus) {
            await axios.delete('/api/manga-user/delete/', { data: { mangaId: manga.id, userId: user.id } });
            return null;
        }
        //Crear o actualizar según si existe registro
        else if (!manga.currentUserData) {
            return await createMangaStatus(manga, user, newStatus);
        } else {
            return await updateMangaStatus(manga, user, newStatus);
        }
    };

    const createMangaStatus = async (manga: Manga, user: User, status: MangaUserStatus) => {
        const tracking = await axios.post('/api/manga-user/store', {
            mangaId: manga.id,
            userId: user.id,
            status: status,
        });
        return tracking.data;
    };

    const updateMangaStatus = async (manga: Manga, user: User, status: MangaUserStatus) => {
        await axios.put('api/manga-user/update', { mangaId: manga.id, userId: user.id, status: status });
        return { ...manga.currentUserData, status: status };
    };

    const updateMangasList = (mangas: Ref<Manga[]>, id: number, mangaUserData: MangaUserData) => {
        //Encuentro el manga en la lista, y si está, actualizo el currentUserData con el nuevo mangaUserData
        const mangaIndex = mangas.value.findIndex((manga) => manga.id === id);
        if (mangaIndex !== -1) {
            mangas.value[mangaIndex].currentUserData = mangaUserData;
        }
    };

    return { changeMangaStatus, updateMangasList };
}
