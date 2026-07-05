import { spawnSync } from 'node:child_process'
import { existsSync, rmSync } from 'node:fs'
import { basename, dirname, resolve } from 'node:path'
import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'

function zipBuildOutput() {
  let resolvedConfig

  return {
    name: 'zip-build-output',
    apply: 'build',
    configResolved(config) {
      resolvedConfig = config
    },
    closeBundle() {
      const outDir = resolve(resolvedConfig.root, resolvedConfig.build.outDir)
      const zipFile = resolve(resolvedConfig.root, `${basename(outDir)}.zip`)

      if (!existsSync(outDir)) return
      if (existsSync(zipFile)) {
        rmSync(zipFile, { force: true })
      }

      const result = spawnSync('zip', ['-rq', zipFile, basename(outDir)], {
        cwd: dirname(outDir),
        stdio: 'inherit',
      })

      if (result.status !== 0) {
        throw new Error(`压缩构建产物失败: ${zipFile}`)
      }
    },
  }
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    base: './',
    plugins: [vue(), zipBuildOutput()],
    build: {
      outDir: 'dist',
      emptyOutDir: true,
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      proxy: {
        '/api': {
          target: env.VITE_API_PROXY_TARGET || 'http://127.0.0.1:18002',
          changeOrigin: true,
        },
      },
    },
  }
})
