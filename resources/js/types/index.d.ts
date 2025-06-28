import type { PageProps } from '@inertiajs/core';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface SharedData extends PageProps {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Manga {
    id: number;
    name: string;
    cover: string;
    startDate: string;
    endDate: string;
    volumes: number;
    tankoubon: number;
    chapters: number;
    sinopsis: string;
    readingDirection: string;
    finished: boolean | number;
    mal: number;
    listadoManga: number;
    language: 'es' | 'en' | 'jp';
    magazineId: number;
    createdAt: string;
    updatedAt: string;
    names?: { id: number; name: string; type: 'japanese' | 'spanish' | 'other' }[];
    people?: (Person & { pivot: { job: 'writer' | 'illustrator' | 'both' } })[];
    tags?: Tag[];
    mangasRelated?: {
        id: number;
        name: string;
        pivot: {
            relation: 'prequel' | 'sequel' | 'spin-off' | 'main story';
        };
    }[];
}

export interface Person {
    id: number;
    name: string;
    surname: string;
    kanjiName: string;
    kanjiSurname: string;
}

export interface Magazine {
    id: number;
    name: string;
    publisher: string;
    demography: 'shounen' | 'shoujo' | 'seinen' | 'josei';
    date: string;
    frequency: 'weekly' | 'biweekly' | 'monthly' | 'bimonthly' | 'quarterly' | 'irregular';
}

export interface Tag {
    id: number;
    name: string;
    type: 'genre' | 'theme';
}

//Formularios
export interface CreatePageProps {
    mangas?: { name: string; id: number }[];
    people?: { name: string; surname: string; id: number }[];
    magazines?: { name: string; id: number }[];
    tags?: { name: string; id: number; type: string }[];
}

export interface EditPageProps {
    item: Manga | Person | Magazine | User | Tag;
    mangas?: { name: string; id: number }[];
    people?: { name: string; surname: string; id: number }[];
    magazines?: { name: string; id: number }[];
    tags?: { name: string; id: number; type: string }[];
}

export interface MagazineCreateForm {
    name?: string;
    publisher?: string;
    demography: 'shounen' | 'shoujo' | 'seinen' | 'josei' | undefined;
    date?: CalendarDate;
    frequency: 'weekly' | 'biweekly' | 'monthly' | 'bimonthly' | 'quarterly' | 'irregular' | undefined;
    [key: string]: any;
}

export interface PersonCreateForm {
    name?: string;
    kanjiName?: string;
    surname?: string;
    kanjiSurname?: string;
    [key: string]: any;
}

export interface TagCreateForm {
    name?: string;
    type?: 'genre' | 'theme';
    [key: string]: any;
}

export interface UserCreateForm {
    name?: string;
    email?: string;
    password?: string;
    passwordConfirmation?: string;
    [key: string]: any;
}

export interface MangaCreateForm {
    cover?: File;
    name?: string;
    startDate?: CalendarDate;
    endDate?: CalendarDate;
    volumes?: number;
    tankoubon?: number;
    chapters?: number;
    sinopsis?: string;
    readingDirection?: boolean;
    language?: 'es' | 'en' | 'jp';
    finished?: boolean;
    alternativeNames?: MultiValues[];
    authors?: MultiValues[];
    tags?: MultiValues[];
    magazineId?: number;
    relatedMangas?: MultiValues[];
    mal?: number;
    listadoManga?: number;
    [key: string]: any;
}

//Utilidades

export type MultiValues = {
    label: string;
    value?: number;
    category?: string;
    color?: string;
};
