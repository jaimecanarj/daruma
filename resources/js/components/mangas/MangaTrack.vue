<script setup lang="ts">
import { useMangaStatus } from '@/composables/useMangaStatus';
import { Auth, Manga, MangaUserData, MangaUserStatus } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, inject, ref } from 'vue';

const props = defineProps<{ manga: Manga; type: 'grid' | 'card' }>();

const modalOpen = ref(false);

const buttonConfig = computed(() => {
    const base = {
        ui: { leadingIcon: 'text-white' },
        hoverWidth: 'hover:w-28',
    };

    switch (props.manga.currentUserData?.status) {
        case 'completed':
            return {
                ...base,
                icon: 'lucide:bookmark-check',
                text: 'Leído',
                color: 'primary' as const,
                classes: 'hover:bg-primary text-white',
                hoverWidth: 'hover:w-24',
            };
        case 'on_hold':
            return {
                ...base,
                icon: 'lucide:bookmark-x',
                text: 'Pausado',
                color: 'warning' as const,
                classes: 'bg-warning-500 hover:bg-warning-500 text-white',
            };
        case 'wishlist':
            return {
                ...base,
                icon: 'lucide:bookmark-minus',
                text: 'Por leer',
                color: 'neutral' as const,
                classes: 'bg-white text-neutral-900 hover:bg-white',
                ui: undefined,
            };
        case 'reading':
            return {
                ...base,
                icon: 'lucide:bookmark',
                text: 'Leyendo',
                color: 'success' as const,
                classes: 'bg-success-500 hover:bg-success-500 text-white',
            };
        default:
            return {
                ...base,
                icon: 'lucide:bookmark-plus',
                text: 'Por leer',
                color: 'secondary' as const,
                classes: 'hover:bg-secondary text-white',
            };
    }
});

const buttonClasses = computed(() => {
    const common = [
        buttonConfig.value.classes,
        props.type === 'grid'
            ? 'group/btn absolute bottom-2 mx-2 w-10 origin-left overflow-hidden opacity-0 transition-all transition-discrete duration-200 ease-in-out group-hover:opacity-85 hover:opacity-100'
            : 'w-full max-w-[150px] justify-center',
    ];

    // Solo aplicar hover de ancho si type === 'grid'
    if (props.type === 'grid') {
        common.push(buttonConfig.value.hoverWidth);
    }

    return common;
});

const items = computed(() => {
    const baseItems = {
        wishlist: {
            label: 'Por leer',
            icon: 'lucide:bookmark-plus',
            color: 'secondary',
            onSelect: () => updateTracking('wishlist'),
        },
        reading: {
            label: 'Leyendo',
            icon: 'lucide:bookmark',
            color: 'success',
            onSelect: () => updateTracking('reading'),
        },
        completed: {
            label: 'Leído',
            icon: 'lucide:bookmark-check',
            color: 'primary',
            onSelect: () => updateTracking('completed'),
        },
        on_hold: {
            label: 'En pausa',
            icon: 'lucide:bookmark-x',
            color: 'warning',
            onSelect: () => updateTracking('on_hold'),
        },
    };

    // opciones según el estado actual
    const rules: Record<MangaUserStatus | 'none', (keyof typeof baseItems)[]> = {
        none: ['reading', 'completed', 'on_hold'],
        wishlist: ['reading', 'completed', 'on_hold'],
        reading: ['wishlist', 'completed', 'on_hold'],
        completed: ['wishlist', 'reading', 'on_hold'],
        on_hold: ['wishlist', 'reading', 'completed'],
    };

    return [rules[props.manga?.currentUserData?.status ?? 'none'].map((key) => baseItems[key])];
});

const page = usePage();

const { changeMangaStatus } = useMangaStatus();
const updateMangas = inject<(id: number, mangaUserData: MangaUserData) => void>('updateMangas');

const updateTracking = async (status?: MangaUserStatus) => {
    const newStatus = await changeMangaStatus(props.manga, (page.props.auth as Auth).user, status);
    if (updateMangas) {
        updateMangas(props.manga.id, newStatus);
    } else {
        console.warn('updateMangas is not defined');
    }
};

const checkStateChange = () => {
    //En caso de cambiar de estado un manga, se abre un modal para confirmar el cambio si no está en el estado deseado
    if (props.manga.currentUserData?.status && props.manga.currentUserData?.status !== 'wishlist') {
        modalOpen.value = true;
    } else {
        updateTracking();
    }
};
</script>

<template>
    <UContextMenu :items="items">
        <UButton
            :leading-icon="buttonConfig.icon"
            :color="buttonConfig.color"
            :class="buttonClasses"
            :ui="buttonConfig.ui"
            :size="type === 'card' ? 'sm' : undefined"
            @click="checkStateChange"
        >
            <span :class="['whitespace-nowrap', type === 'grid' ? 'ml-1 opacity-0 group-hover/btn:opacity-100' : '']">
                {{ buttonConfig.text }}
            </span>
        </UButton>
    </UContextMenu>
    <!--Modal-->
    <UModal v-model:open="modalOpen" class="w-fit" title="Cambiar estado" description="¿Estás seguro que quieres cambiar el estado?" :close="false">
        <template #footer="{ close }">
            <UButton
                label="Actualizar"
                color="neutral"
                @click="
                    updateTracking();
                    close();
                "
            />
            <UButton label="Cancelar" color="neutral" variant="outline" @click="close" />
        </template>
    </UModal>
</template>
