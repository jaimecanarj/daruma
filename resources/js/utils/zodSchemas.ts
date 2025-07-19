import { multiItemSchema, optionalNumberSchema } from '@/utils/zodUtils';
import { CalendarDate } from '@internationalized/date';
import { z } from 'zod';

export const loginSchema = z.object({
    email: z.string({ required_error: 'Obligatorio' }).email('Introduce un email válido'),
    password: z.string({ required_error: 'Obligatorio' }).min(8, 'Debe tener al menos 8 caracteres'),
    remember: z.boolean(),
});

export const registerSchema = z.object({
    name: z.string({ required_error: 'Obligatorio' }),
    email: z.string({ required_error: 'Obligatorio' }).email('Introduce un email válido'),
    avatar: z.custom<File | undefined>(
        (val) => {
            if (val === undefined) return true;
            if (!(val instanceof File)) return false;
            return val.type.startsWith('image/');
        },
        { message: 'Selecciona una imagen' },
    ),
    password: z.string({ required_error: 'Obligatorio' }).min(8, 'Debe tener al menos 8 caracteres'),
    passwordConfirmation: z.string({ required_error: 'Obligatorio' }),
});

export const personSchema = z.object({
    name: z.string({ required_error: 'Obligatorio' }),
    kanjiName: z.string().optional(),
    surname: z.string().optional(),
    kanjiSurname: z.string().optional(),
});

export const magazineSchema = z.object({
    name: z.string({ required_error: 'Obligatorio' }),
    publisher: z.string({ required_error: 'Obligatorio' }),
    demography: z.enum(['shounen', 'shoujo', 'seinen', 'josei'], { required_error: 'Obligatorio' }),
    date: z.instanceof(CalendarDate).optional(),
    frequency: z.enum(['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular'], { required_error: 'Obligatorio' }),
});

export const tagSchema = z.object({
    name: z.string({ required_error: 'Obligatorio' }),
    type: z.enum(['genre', 'theme'], { required_error: 'Obligatorio' }),
});

export const userSchema = z
    .object({
        name: z.string({ required_error: 'Obligatorio' }),
        email: z.string({ required_error: 'Obligatorio' }),
        password: z.string({ required_error: 'Obligatorio' }).min(8, 'Debe tener al menos 8 caracteres'),
        passwordConfirmation: z.string({ required_error: 'Obligatorio' }),
    })
    .refine((data) => data.password === data.passwordConfirmation, {
        message: 'Las contraseñas no coinciden',
        path: ['passwordConfirmation'],
    });

export const mangaSchema = z
    .object({
        name: z.string({ required_error: 'Obligatorio' }),
        alternativeNames: z.array(multiItemSchema),
        authors: z.array(multiItemSchema).min(0, 'Debe haber un escritor y un ilustrador'),
        tags: z.array(multiItemSchema),
        sinopsis: z.string().optional(),
        startDate: z.instanceof(CalendarDate).optional(),
        endDate: z.instanceof(CalendarDate).optional(),
        magazineId: z.object({ label: z.string(), value: z.number() }).optional(),
        mal: optionalNumberSchema,
        listadoManga: optionalNumberSchema,
        relatedMangas: z.array(multiItemSchema),
        cover: z.custom<File | undefined>(
            (val) => {
                if (val === undefined) return true;
                if (!(val instanceof File)) return false;
                return val.type.startsWith('image/');
            },
            { message: 'Selecciona una imagen' },
        ),
        volumes: optionalNumberSchema,
        tankoubon: optionalNumberSchema,
        chapters: optionalNumberSchema,
        readingDirection: z.boolean(),
        language: z.enum(['es', 'en', 'jp'], { required_error: 'Obligatorio' }),
        finished: z.boolean(),
        purpose: z.enum(['create', 'edit']),
    })
    .refine(
        (data) => {
            return !(data.purpose === 'create' && !data.cover);
        },
        {
            message: 'Selecciona una imagen',
            path: ['cover'],
        },
    );
