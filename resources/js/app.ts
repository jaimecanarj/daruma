import { createInertiaApp } from '@inertiajs/vue3';
import ui from '@nuxt/ui/vue-plugin';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            default:
                return AppLayout;
        }
    },
    withApp(app) {
        app.use(ui);
    },
    progress: {
        color: '#4B5563',
    },
});
