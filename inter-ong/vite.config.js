import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(),
    tailwindcss()
  ],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000', // Substitua pela URL real do seu back-end
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
