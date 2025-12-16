<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-8 mb-8">
      <div class="max-w-4xl">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
          欢迎使用 Vue 3 应用
        </h1>
        <p class="text-lg text-gray-600 mb-6">
          这是一个现代化的 Vue 3 应用，集成了 Vue Router、Axios 和 Tailwind CSS
        </p>
        <div class="flex flex-wrap gap-4">
          <router-link
            to="/about"
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200"
          >
            了解更多
            <span class="ml-2">→</span>
          </router-link>
          <router-link
            to="/users"
            class="inline-flex items-center px-6 py-3 bg-white text-primary-600 border border-primary-200 font-medium rounded-lg hover:bg-primary-50 transition-colors duration-200"
          >
            查看用户
            <span class="ml-2">👥</span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Grid -->
    <section class="features mb-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">主要功能</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            🚀
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">快速开发</h3>
          <p class="text-gray-600">基于 Vite 构建工具，提供极速的开发体验和热重载功能</p>
        </div>

        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            🎨
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">现代化设计</h3>
          <p class="text-gray-600">使用 Tailwind CSS 构建响应式界面，支持暗黑模式切换</p>
        </div>

        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            🔧
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">完整功能</h3>
          <p class="text-gray-600">集成路由管理、HTTP 请求、状态管理等现代化开发工具</p>
        </div>

        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            📱
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">移动端优化</h3>
          <p class="text-gray-600">完全响应式设计，在移动设备上提供出色的用户体验</p>
        </div>

        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            ⚡
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">高性能</h3>
          <p class="text-gray-600">基于 Vue 3 Composition API，提供更优的性能和更小的包体积</p>
        </div>

        <div class="feature-card bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
          <div class="feature-icon w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-2xl mb-4">
            🛡️
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">类型安全</h3>
          <p class="text-gray-600">支持 TypeScript，提供更好的代码智能提示和错误检查</p>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats bg-white rounded-xl p-6 shadow-sm border border-gray-200 mb-8">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
        <div>
          <div class="text-2xl font-bold text-primary-600 mb-1">Vue 3</div>
          <div class="text-sm text-gray-600">最新版本</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-green-600 mb-1">Tailwind</div>
          <div class="text-sm text-gray-600">CSS 框架</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-purple-600 mb-1">Vite</div>
          <div class="text-sm text-gray-600">构建工具</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-orange-600 mb-1">Axios</div>
          <div class="text-sm text-gray-600">HTTP 客户端</div>
        </div>
      </div>
    </section>

    <!-- Quick Links -->
    <section class="quick-links">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">快速链接</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a
          href="https://vuejs.org/"
          target="_blank"
          rel="noopener noreferrer"
          class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
        >
          <span class="text-2xl">📖</span>
          <div>
            <div class="font-medium text-gray-900">Vue 3 文档</div>
            <div class="text-sm text-gray-600">学习 Vue 3 的官方文档</div>
          </div>
        </a>

        <a
          href="https://tailwindcss.com/"
          target="_blank"
          rel="noopener noreferrer"
          class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
        >
          <span class="text-2xl">🎨</span>
          <div>
            <div class="font-medium text-gray-900">Tailwind CSS</div>
            <div class="text-sm text-gray-600">实用优先的 CSS 框架</div>
          </div>
        </a>

        <a
          href="https://router.vuejs.org/"
          target="_blank"
          rel="noopener noreferrer"
          class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
        >
          <span class="text-2xl">🛣️</span>
          <div>
            <div class="font-medium text-gray-900">Vue Router</div>
            <div class="text-sm text-gray-600">Vue.js 官方路由管理器</div>
          </div>
        </a>

        <a
          href="https://axios-http.com/"
          target="_blank"
          rel="noopener noreferrer"
          class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
        >
          <span class="text-2xl">🌐</span>
          <div>
            <div class="font-medium text-gray-900">Axios</div>
            <div class="text-sm text-gray-600">基于 Promise 的 HTTP 客户端</div>
          </div>
        </a>
      </div>
    </section>
  </div>
</template>

<script setup>
// 首页不需要额外的逻辑，主要展示内容
</script>

<style scoped>
/* 所有样式都由 Tailwind CSS 类处理 */
</style>