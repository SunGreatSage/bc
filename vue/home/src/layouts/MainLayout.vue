<template>
  <div class="min-h-screen bg-gray-50 relative overflow-hidden">
    <!-- Sidebar -->
    <Sidebar
      :is-open="sidebarOpen"
      @toggle="handleSidebarToggle"
    />

    <!-- Header - 固定定位，手动控制位置 -->
    <div
      class="fixed top-0 left-0 right-0 z-30 transition-transform duration-300 ease-in-out"
      :class="{
        'translate-x-64': sidebarOpen,
        'translate-x-0': !sidebarOpen
      }"
    >
      <Header
        @search="handleSearch"
        @settings="handleSettings"
        @view-change="handleViewChange"
        @toggle-sidebar="handleToggleSidebar"
      />
    </div>

    <!-- Main Content Wrapper - 只有内容会被推动 -->
    <div
      class="transition-transform duration-300 ease-in-out min-h-screen"
      :class="{
        'translate-x-64': sidebarOpen,
        'translate-x-0': !sidebarOpen
      }"
    >
      <!-- Dynamic Content Area -->
      <main class="pt-48 px-0" @click="handleMainClick">
        <!-- 搜索结果面板 -->
        <div v-if="currentContent === 'search-results'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">搜索结果</h3>
          <div class="text-gray-600">
            这里显示搜索结果内容...
          </div>
        </div>

        <!-- 通知面板 -->
        <div v-else-if="currentContent === 'notifications-panel'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">通知中心</h3>
          <div class="space-y-3">
            <div class="p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
              <div class="font-medium text-blue-800">新消息</div>
              <div class="text-blue-600 text-sm">您有一条新的系统消息</div>
            </div>
            <div class="p-3 bg-green-50 border-l-4 border-green-400 rounded">
              <div class="font-medium text-green-800">中奖通知</div>
              <div class="text-green-600 text-sm">恭喜您，您的注单已中奖！</div>
            </div>
          </div>
        </div>

        <!-- 设置面板 -->
        <div v-else-if="currentContent === 'settings-panel'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">系统设置</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div>
                <div class="font-medium">消息通知</div>
                <div class="text-sm text-gray-600">接收系统通知和提醒</div>
              </div>
              <button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                配置
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <div class="font-medium">账户安全</div>
                <div class="text-sm text-gray-600">密码和登录安全设置</div>
              </div>
              <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                管理
              </button>
            </div>
            <div class="flex items-center justify-between">
              <div>
                <div class="font-medium">支付设置</div>
                <div class="text-sm text-gray-600">充值和提现方式配置</div>
              </div>
              <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                设置
              </button>
            </div>
          </div>
        </div>

        <!-- 主题选择面板 -->
        <div v-else-if="currentContent === 'theme-selector'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold mb-4">主题设置</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
              @click="switchTheme('gold')"
              :class="{
                'border-2 rounded-lg p-4 cursor-pointer transition-all duration-200': true,
                'border-amber-500 bg-amber-50': currentTheme === 'gold',
                'border-gray-300 hover:bg-purple-50': currentTheme !== 'gold'
              }"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-600"></div>
                <span class="font-medium">暗金色主题</span>
              </div>
              <div class="text-sm text-gray-600">豪华大气的金色风格</div>
            </button>
            <button
              @click="switchTheme('purple')"
              :class="{
                'border-2 rounded-lg p-4 cursor-pointer transition-all duration-200': true,
                'border-purple-500 bg-purple-50': currentTheme === 'purple',
                'border-gray-300 hover:bg-purple-50': currentTheme !== 'purple'
              }"
            >
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600"></div>
                <span class="font-medium">暗紫色主题</span>
              </div>
              <div class="text-sm text-gray-600">神秘优雅的紫色风格</div>
            </button>
          </div>
        </div>

        <!-- 默认内容：路由视图 -->
        <router-view v-else />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Sidebar from '@/components/Layout/Sidebar.vue'
import Header from '@/components/Layout/Header.vue'

const sidebarOpen = ref(false) // 默认隐藏，桌面端会保持展开，移动端会自动收起
const currentView = ref('default') // 控制主内容区域显示的内容
const currentTheme = ref('gold') // gold 或 purple

// 可用的视图选项
const viewOptions = {
  default: 'default',
  search: 'search',
  notifications: 'notifications',
  settings: 'settings',
  theme: 'theme'
}

const handleSidebarToggle = (isOpen) => {
  sidebarOpen.value = isOpen
}

const handleToggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const handleViewChange = (view) => {
  currentView.value = view
}

const handleMainClick = () => {
  // 点击主内容区域时，返回默认视图
  currentView.value = 'default'
}

const switchTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('appTheme', theme)

  // 移除所有主题类
  document.body.classList.remove('theme-gold', 'theme-purple')

  // 添加新主题类
  document.body.classList.add(`theme-${theme}`)

  // 更新sidebar背景
  const sidebar = document.querySelector('.sidebar-content > div') || document.querySelector('.fixed.lg\\:static')
  if (sidebar) {
    sidebar.style.background = theme === 'gold'
      ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%)'
      : 'linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #6b21a8 100%)'
  }
}

// 监听主题变化
const updateTheme = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

onMounted(() => {
  // 初始化主题
  const savedTheme = localStorage.getItem('appTheme') || 'gold'
  currentTheme.value = savedTheme
  switchTheme(savedTheme)

  // 监听 localStorage 变化
  window.addEventListener('storage', updateTheme)

  // 监听全局主题变化事件
  window.addEventListener('themeChanged', () => {
    updateTheme()
  })
})

onUnmounted(() => {
  // 清理事件监听器
  window.removeEventListener('storage', updateTheme)
})

// 计算当前应该显示的内容
const currentContent = computed(() => {
  switch (currentView.value) {
    case 'search':
      return 'search-results'
    case 'notifications':
      return 'notifications-panel'
    case 'settings':
      return 'settings-panel'
    case 'theme':
      return 'theme-selector'
    default:
      return 'main-content'
  }
})
</script>

<style scoped>
/* Layout styles are handled by Tailwind utilities in style.css */
</style>
