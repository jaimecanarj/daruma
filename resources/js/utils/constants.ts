export const demographies = [
    { label: 'Shounen', value: 'shounen' },
    { label: 'Shoujo', value: 'shoujo' },
    { label: 'Seinen', value: 'seinen' },
    { label: 'Josei', value: 'josei' },
];

export const frequencies = [
    { label: 'Semanal', value: 'weekly' },
    { label: 'Bisemanal', value: 'biweekly' },
    { label: 'Mensual', value: 'monthly' },
    { label: 'Bimensual', value: 'bimonthly' },
    { label: 'Trimestral', value: 'quarterly' },
    { label: 'Irregular', value: 'irregular' },
];

export const languages = [
    { value: 'es', label: 'Español', emoji: '🇪🇸' },
    { value: 'en', label: 'English', emoji: '🇬🇧' },
    { value: 'jp', label: '日本語', emoji: '🇯🇵' },
];

export const readingDirections = [
    { label: 'Oriental', value: 'rtl' },
    { label: 'Occidental', value: 'ltr' },
];

export const alternativeNames = [
    { label: '日本語', value: 'japanese', color: 'primary' },
    { label: 'Español', value: 'spanish', color: 'secondary' },
    { label: 'Otro', value: 'other', color: 'info' },
];

export const authorJobs = [
    { label: 'Escritor', value: 'writer', color: 'primary' },
    { label: 'Ilustrador', value: 'illustrator', color: 'secondary' },
    { label: 'Escritor e ilustrador', value: 'both', color: 'info' },
];

export const mangaRelations = [
    { label: 'Precuela', value: 'prequel', color: 'primary' },
    { label: 'Secuela', value: 'sequel', color: 'secondary' },
    { label: 'Spin-off', value: 'spin-off', color: 'info' },
    { label: 'Historia principal', value: 'main story', color: 'warning' },
];

export const tagTypes = [
    { value: 'genre', label: 'Género' },
    { value: 'theme', label: 'Tema' },
];

export const adminTabItems = [
    {
        slot: 'mangas' as const,
        label: 'Mangas',
        value: 'manga',
        icon: 'lucide:book-text',
    },
    {
        slot: 'people' as const,
        label: 'Personas',
        value: 'person',
        icon: 'lucide:users',
    },
    {
        slot: 'magazines' as const,
        label: 'Revistas',
        value: 'magazine',
        icon: 'lucide:newspaper',
    },
    {
        slot: 'users' as const,
        label: 'Usuarios',
        value: 'user',
        icon: 'lucide:circle-user',
    },
    {
        slot: 'tags' as const,
        label: 'Etiquetas',
        value: 'tag',
        icon: 'lucide:tag',
    },
];

export const roles = [
    { label: 'Administrador', value: 'admin' },
    { label: 'Usuario', value: 'user' },
];

export const sortingIcons = [
    { value: 'updateDesc', icon: 'lucide:clock-arrow-down' },
    { value: 'updateAsc', icon: 'lucide:clock-arrow-up' },
    { value: 'nameAsc', icon: 'lucide:arrow-up-a-z' },
    { value: 'nameDesc', icon: 'lucide:arrow-down-z-a' },
    { value: 'dateAsc', icon: 'lucide:calendar-arrow-up' },
    { value: 'dateDesc', icon: 'lucide:calendar-arrow-down' },
    { value: 'volumesAsc', icon: 'lucide:arrow-up-1-0' },
    { value: 'volumesDesc', icon: 'lucide:arrow-down-1-0' },
];

export const mangaSorting = [
    { label: 'Más reciente', value: 'updateDesc' },
    { label: 'Más antiguo', value: 'updateAsc' },
    { label: 'Título ascendente', value: 'nameAsc' },
    { label: 'Título descendente', value: 'nameDesc' },
    { label: 'Fecha ascendente', value: 'dateAsc' },
    { label: 'Fecha descendente', value: 'dateDesc' },
    { label: 'Tomos ascendente', value: 'volumesAsc' },
    { label: 'Tomos descendente', value: 'volumesDesc' },
];

export const magazineSorting = [
    { label: 'Título ascendente', value: 'nameAsc' },
    { label: 'Título descendente', value: 'nameDesc' },
    { label: 'Fecha ascendente', value: 'dateAsc' },
    { label: 'Fecha descendente', value: 'dateDesc' },
    { label: 'Más reciente', value: 'updateDesc' },
    { label: 'Más antiguo', value: 'updateAsc' },
];

export const mangaFiltersInitialState = {
    search: '',
    volumes: undefined,
    date: undefined,
    tags: { include: [], exclude: [] },
    order: 'updateDesc',
    people: [],
    language: undefined,
    magazines: [],
    demographies: [],
    finished: [],
    readingDirection: [],
};

export const magazineFiltersInitialState = {
    search: '',
    publishers: [],
    date: undefined,
    order: 'nameAsc',
    demographies: [],
    frequencies: [],
};
