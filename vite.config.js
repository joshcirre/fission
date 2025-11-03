import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    // Clean up APP_URL: remove trailing slash and ensure it's a valid URL
    const appUrl = env.APP_URL ? env.APP_URL.replace(/\/$/, '') : 'http://localhost:8000'

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            open: appUrl,
        },
    }
})
