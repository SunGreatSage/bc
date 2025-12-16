import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    plugins: [vue()],

    // Path aliases
    resolve: {
      alias: {
        '@': resolve(__dirname, 'src'),
      },
    },

    // Development server configuration
    server: {
      port: env.VITE_PORT || 3000,
      host: env.VITE_HOST || 'localhost',
      open: true,
      cors: true,
      proxy: {
        '/api': {
          target: 'http://localhost',  // PHP 后端地址 (默认 80 端口)
          changeOrigin: true,
          secure: false,
          // 不要 rewrite，保持 /api 前缀
        },
        '/adminapi': {
          target: 'http://localhost',  // PHP 后端地址 (默认 80 端口)
          changeOrigin: true,
          secure: false,
          // 不要 rewrite，保持 /adminapi 前缀
        },
      },
    },

    // Build configuration
    build: {
      outDir: 'dist',
      sourcemap: mode === 'development',
      minify: mode === 'production' ? 'terser' : 'esbuild',
      chunkSizeWarningLimit: 500,

      // Production-specific optimizations
      ...(mode === 'production' && {
        terserOptions: {
          compress: {
            drop_console: true,
            drop_debugger: true,
          },
        },
        rollupOptions: {
          output: {
            manualChunks: (id) => {
              if (id.includes('node_modules/vue')) {
                return 'vendor'
              }
              if (id.includes('node_modules/vue-router')) {
                return 'router'
              }
              if (id.includes('node_modules/axios')) {
                return 'http'
              }
            },
          },
        },
        manifest: true,
      }),
    },

    // CSS configuration
    css: {
      devSourcemap: mode === 'development',
    },

    // Optimize dependencies
    optimizeDeps: {
      include: ['vue', 'vue-router', 'axios'],
    },

    // Environment variables
    define: {
      __DEV__: mode === 'development',
      __PROD__: mode === 'production',
    },
  }
})
