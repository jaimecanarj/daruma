import { z } from 'zod';

export const multiItemSchema = z.object({
    label: z.string(),
    value: z.number().optional(),
    category: z.string().optional(),
    color: z.string().optional(),
});

export const optionalNumberSchema = z.preprocess((arg: unknown) => {
    if (typeof arg !== 'number') return undefined;
    return isNaN(arg) ? undefined : arg;
}, z.number().optional());
