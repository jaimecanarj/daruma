import type { PageProps } from '@inertiajs/core';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    userRoles: string[];
    userPermissions: string[];
}

export interface SharedData extends PageProps {
    name: string;
    auth: Auth;
    ziggy: Config & { location: string };
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
    names?: Name[];
    people?: (Person & { pivot: { job: 'writer' | 'illustrator' | 'both' } })[];
    tags?: Tag[];
    magazine?: Magazine;
    mangasRelated?: (Manga & {
        pivot: {
            relation: 'prequel' | 'sequel' | 'spin-off' | 'main story';
        };
    })[];
}

export interface Name {
    id: number;
    name: string;
    type: 'japanese' | 'spanish' | 'other';
}

export interface Person {
    id: number;
    name: string;
    surname: string;
    kanjiName: string;
    kanjiSurname: string;
    writerCount?: number;
    illustratorCount?: number;
}

export interface Magazine {
    id: number;
    name: string;
    publisher: string;
    demography: 'shounen' | 'shoujo' | 'seinen' | 'josei';
    date: string;
    frequency: 'weekly' | 'biweekly' | 'monthly' | 'bimonthly' | 'quarterly' | 'irregular';
    mangasCount?: number;
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

export interface Tag {
    id: number;
    name: string;
    type: 'genre' | 'theme';
}

//Formularios
export interface MangaForm {
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
    magazineId?: { label: string; value: number };
    relatedMangas?: MultiValues[];
    mal?: number;
    listadoManga?: number;
    [key: string]: any;
}

export interface PersonForm {
    name?: string;
    kanjiName?: string;
    surname?: string;
    kanjiSurname?: string;
    [key: string]: any;
}

export interface MagazineForm {
    name?: string;
    publisher?: string;
    demography: 'shounen' | 'shoujo' | 'seinen' | 'josei' | undefined;
    date?: CalendarDate;
    frequency: 'weekly' | 'biweekly' | 'monthly' | 'bimonthly' | 'quarterly' | 'irregular' | undefined;
    [key: string]: any;
}

export interface UserForm {
    name?: string;
    email?: string;
    password?: string;
    passwordConfirmation?: string;
    roles?: { label: string; value: string }[];
    [key: string]: any;
}

export interface TagForm {
    name?: string;
    type?: 'genre' | 'theme';
    [key: string]: any;
}

//Filtros
export interface MangaFilters {
    search?: string;
    volumes?: string;
    date?: CalendarDate;
    tags: MultiValues[];
    order: string;
    people: number[];
    magazines: number[];
    demographies: string[];
    finished: boolean[];
    readingDirection: string[];
    language?: string[];
}

export interface MagazineFilters {
    search?: string;
    demographies: string[];
    order: string;
    date?: CalendarDate;
    publishers: string[];
    frequencies: string[];
}

//Utilidades
export type MultiValues = {
    label: string;
    value?: number;
    category?: string;
    color?: string;
};
