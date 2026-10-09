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
                'resources/js/login.js',
                'resources/js/admin.js',
                'resources/js/user.js',
                'resources/js/navigationAdmin.js',
                'resources/js/navigationUser.js',
                'resources/js/user-create.js',
                'resources/js/user-edit.js',
                'resources/js/accessibility-user.js',
                'resources/js/accessibility-admin.js'
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