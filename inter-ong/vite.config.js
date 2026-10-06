import react from '@vitejs/plugin-react'
import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, '.', '')
  const proxy = {
    target: env.BACKEND_PROXY_URL || 'http://127.0.0.1:8000',
    changeOrigin: true,
    cookieDomainRewrite: '',
  }
  return {
    plugins: [react(), tailwindcss()],
    server: { proxy: { '/api': proxy, '/img': proxy } },
    preview: { proxy: { '/api': proxy, '/img': proxy } },
  }
})
