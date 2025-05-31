import { multiItemSchema, optionalNumberSchema } from '@/utils/zodUtils';
import { CalendarDate } from '@internationalized/date';
import { z } from 'zod';

export const personSchema = z.object({
    name: z.string(),
    kanjiName: z.string().optional(),
    surname: z.string().optional(),
    kanjiSurname: z.string().optional(),
});

export const magazineSchema = z.object({
    name: z.string(),
    publisher: z.string(),
    demography: z.enum(['shounen', 'shoujo', 'seinen', 'josei']),
    date: z.instanceof(CalendarDate).optional(),
    frequency: z.enum(['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular']),
});

export const mangaSchema = z.object({
    name: z.string({ required_error: 'Obligatorio' }),
    alternativeNames: z.array(multiItemSchema),
    authors: z.array(multiItemSchema).min(0, 'Debe haber un escritor y un ilustrador'),
    tags: z.array(multiItemSchema),
    sinopsis: z.string().optional(),
    startDate: z.instanceof(CalendarDate).optional(),
    endDate: z.instanceof(CalendarDate).optional(),
    magazine: z.number().optional(),
    mal: optionalNumberSchema,
    listadoManga: optionalNumberSchema,
    relatedMangas: z.array(multiItemSchema),
    cover: z.custom<File>(
        (val) => {
            if (!(val instanceof File)) {
                return false;
            }
            return val.type.startsWith('image/');
        },
        { message: 'Selecciona una imagen' },
    ),
    volumes: optionalNumberSchema,
    tankoubon: optionalNumberSchema,
    chapters: optionalNumberSchema,
    readingDirection: z.boolean(),
    language: z.enum(['es', 'en', 'jp']),
    finished: z.boolean(),
});
