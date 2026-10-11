import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/login.css',
                'resources/css/admin.css',
                'resources/css/user.css',
                'resources/js/app.js',
                'resources/js/auth/login.js',
                'resources/js/admin/admin.js',
                'resources/js/user/user.js',
                'resources/js/general/navigationAdmin.js',
                'resources/js/general/navigationUser.js',
                'resources/js/user/user-create.js',
                'resources/js/user/user-edit.js',
                'resources/js/general/accessibility-user.js',
                'resources/js/general/accessibility-admin.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});