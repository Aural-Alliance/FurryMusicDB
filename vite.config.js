import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import {resolve} from "path";

// https://vitejs.dev/config/
export default defineConfig({
    root: resolve(__dirname, "./frontend"),
    build: {
        emptyOutDir: true,
    },
    resolve: {
        alias: {
            '!': resolve(__dirname),
            "~": resolve(__dirname, "./frontend"),
        },
        extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.vue']
    },
    plugins: [vue()],
})
