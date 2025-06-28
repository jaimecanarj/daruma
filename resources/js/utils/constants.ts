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

export const languageOptions = [
    { value: 'es', label: 'Español' },
    { value: 'en', label: 'English' },
    { value: 'jp', label: '日本語' },
];

export const readingDirections = [
    { label: 'Oriental', value: 'rtl' },
    { label: 'Occidental', value: 'ltr' },
];

export const alternativeNamesOptions = [
    { label: '日本語', value: 'japanese', color: 'primary' },
    { label: 'Español', value: 'spanish', color: 'secondary' },
    { label: 'Otro', value: 'other', color: 'info' },
];

export const authorsOptions = [
    { label: 'Escritor', value: 'writer', color: 'primary' },
    { label: 'Ilustrador', value: 'illustrator', color: 'secondary' },
    { label: 'Ambos', value: 'both', color: 'info' },
];

export const relatedMangasOptions = [
    { label: 'Precuela', value: 'prequel', color: 'primary' },
    { label: 'Secuela', value: 'sequel', color: 'secondary' },
    { label: 'Spin-off', value: 'spin-off', color: 'info' },
    { label: 'Historia principal', value: 'main story', color: 'warning' },
];

export const tagTypeOptions = [
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
