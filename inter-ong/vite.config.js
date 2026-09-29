import react from '@vitejs/plugin-react'
import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, import.meta.dirname, '')
  const proxy = {
    '/api': {
      target: env.BACKEND_PROXY_URL || 'http://127.0.0.1:8000',
      changeOrigin: true,
      cookieDomainRewrite: '',
    },
  }
  return {
    plugins: [react(), tailwindcss()],
    server: { proxy },
    preview: { proxy },
  }
})
