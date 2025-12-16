<template>
  <!-- Mobile Overlay -->
    <div
      v-if="isMobile && props.isOpen"
      class="fixed inset-0 bg-black/50 z-40"
      @click="closeSidebar"
    ></div>

    <!-- Sidebar -->
  <div
    ref="sidebarRef"
    class="fixed top-0 left-0 h-screen w-64 text-white shadow-2xl transition-transform duration-300 ease-in-out overflow-y-auto z-50"
    :class="{
      '-translate-x-full': !props.isOpen,
      'translate-x-0': props.isOpen,
      'bg-gradient-to-br from-amber-700 via-amber-800 to-amber-900': currentTheme === 'gold',
      'bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700': currentTheme === 'purple'
    }"
  >
      <!-- Logo Section -->
      <div class="p-5 border-b border-white/20">
        <div class="flex items-center gap-2.5">
          <img src="/vite.svg" alt="Logo" class="w-7 h-7 brightness-0 invert" />
          <div v-show="props.isOpen" class="flex-1">
            <div class="text-lg font-bold text-white transition-opacity duration-300">
              彩票平台
            </div>
            <div class="text-xs text-white/70 transition-opacity duration-300">
              账户: demo12345
            </div>
          </div>
        </div>
      </div>

      <!-- Theme Switcher -->
      <div class="p-3 border-b border-white/20">
        <button
          @click="toggleThemeDropdown"
          class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-all duration-200 text-sm"
        >
          <span class="text-base">🎨</span>
          <span class="text-xs">主题切换</span>
          <span class="ml-auto text-xs transition-transform duration-200" :class="{ 'rotate-180': showThemeDropdown }">
            ▼
          </span>
        </button>

        <!-- Theme Options Dropdown -->
        <div
          v-if="showThemeDropdown"
          class="mt-1.5 space-y-1 pl-6 pr-2 py-1.5 bg-white/10 rounded-lg"
        >
          <button
            @click="selectTheme('gold')"
            :class="{
              'w-full flex items-center gap-1.5 px-2 py-1 rounded text-xs transition-all duration-200': true,
              'bg-white/20 text-white': currentTheme === 'gold',
              'text-white/80 hover:bg-white/10': currentTheme !== 'gold'
            }"
          >
            <span class="text-sm">🏆</span>
            <span>暗金色主题</span>
          </button>
          <button
            @click="selectTheme('purple')"
            :class="{
              'w-full flex items-center gap-1.5 px-2 py-1 rounded text-xs transition-all duration-200': true,
              'bg-white/20 text-white': currentTheme === 'purple',
              'text-white/80 hover:bg-white/10': currentTheme !== 'purple'
            }"
          >
            <span class="text-sm">💜</span>
            <span>暗紫色主题</span>
          </button>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 p-3 overflow-y-auto custom-scrollbar">
        <ul class="space-y-1">
          <li>
            <router-link
              to="/betting"
              class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 group"
              :class="{ 'bg-white/25 text-white border-l-3 border-white font-medium': $route.path === '/betting' }"
              @click="() => { closeOnMobile(); emit('view-change', 'default') }"
            >
              <span class="text-lg flex-shrink-0">🎰</span>
              <span
                class="text-sm transition-all duration-300"
                :class="{ 'opacity-100 translate-x-0': props.isOpen, 'opacity-0 -translate-x-4': !props.isOpen }"
              >
                号码下注
              </span>
            </router-link>
          </li>
          <li>
            <router-link
              to="/bet-status"
              class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 group"
              :class="{ 'bg-white/25 text-white border-l-3 border-white font-medium': $route.path === '/bet-status' }"
              @click="() => { closeOnMobile(); emit('view-change', 'default') }"
            >
              <span class="text-lg flex-shrink-0">📊</span>
              <span
                class="text-sm transition-all duration-300"
                :class="{ 'opacity-100 translate-x-0': props.isOpen, 'opacity-0 -translate-x-4': !props.isOpen }"
              >
                下注状况
              </span>
            </router-link>
          </li>
          <li>
            <router-link
              to="/results"
              class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 group"
              :class="{ 'bg-white/25 text-white border-l-3 border-white font-medium': $route.path === '/results' }"
              @click="() => { closeOnMobile(); emit('view-change', 'default') }"
            >
              <span class="text-lg flex-shrink-0">🎯</span>
              <span
                class="text-sm transition-all duration-300"
                :class="{ 'opacity-100 translate-x-0': props.isOpen, 'opacity-0 -translate-x-4': !props.isOpen }"
              >
                开奖结果
              </span>
            </router-link>
          </li>
          <li>
            <router-link
              to="/rules"
              class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 group"
              :class="{ 'bg-white/25 text-white border-l-3 border-white font-medium': $route.path === '/rules' }"
              @click="() => { closeOnMobile(); emit('view-change', 'default') }"
            >
              <span class="text-lg flex-shrink-0">📖</span>
              <span
                class="text-sm transition-all duration-300"
                :class="{ 'opacity-100 translate-x-0': props.isOpen, 'opacity-0 -translate-x-4': !props.isOpen }"
              >
                游戏规则
              </span>
            </router-link>
          </li>
          <li>
            <router-link
              to="/change-password"
              class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200 group"
              :class="{ 'bg-white/25 text-white border-l-3 border-white font-medium': $route.path === '/change-password' }"
              @click="() => { closeOnMobile(); emit('view-change', 'default') }"
            >
              <span class="text-lg flex-shrink-0">🔐</span>
              <span
                class="text-sm transition-all duration-300"
                :class="{ 'opacity-100 translate-x-0': props.isOpen, 'opacity-0 -translate-x-4': !props.isOpen }"
              >
                修改密码
              </span>
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Logout Button -->
      <div
        class="p-3 border-t border-white/20 transition-all duration-300"
        :class="{ 'opacity-100': props.isOpen, 'opacity-0 h-0 overflow-hidden': !props.isOpen }"
        v-show="props.isOpen"
      >
        <button
          @click="handleLogout"
          class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-white/80 hover:text-white hover:bg-red-500/25 transition-all duration-200 group"
        >
          <span class="text-lg flex-shrink-0">🚪</span>
          <span class="text-sm font-medium">登出</span>
        </button>
      </div>

    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const emit = defineEmits(['toggle', 'view-change'])
const router = useRouter()

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const isMobile = ref(false)
const currentTheme = ref('gold') // gold 或 purple
const showThemeDropdown = ref(false)

const toggleSidebar = () => {
  const newState = !props.isOpen
  emit('toggle', newState)
}

const closeSidebar = () => {
  emit('toggle', false)
}

const closeOnMobile = () => {
  if (isMobile.value) {
    closeSidebar()
  }
}

const toggleThemeDropdown = () => {
  showThemeDropdown.value = !showThemeDropdown.value
}

const selectTheme = (theme) => {
  switchTheme(theme)
  showThemeDropdown.value = false
  closeOnMobile()
}

const handleLogout = () => {
  if (confirm('确定要登出吗？')) {
    // 清除用户信息和token
    localStorage.removeItem('userToken')
    localStorage.removeItem('userInfo')

    // 跳转到登录页
    router.push('/login')
  }
}

const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024 // lg breakpoint
  // 不再自动展开侧边栏，保持用户选择的状态
}

onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)

  // 从localStorage读取主题设置
  const savedTheme = localStorage.getItem('appTheme') || 'gold'
  currentTheme.value = savedTheme
  document.body.className = `theme-${savedTheme}`
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
})

// 主题切换功能
const switchTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('appTheme', theme)

  // 移除所有主题类
  document.body.classList.remove('theme-gold', 'theme-purple')

  // 添加新主题类
  document.body.classList.add(`theme-${theme}`)

  // 触发重新渲染以更新响应式样式
  document.body.setAttribute('data-theme', theme)

  // 触发自定义事件通知其他组件主题已变化
  window.dispatchEvent(new CustomEvent('themeChanged', {
    detail: { theme }
  }))
}

// 暴露主题切换方法给父组件
defineExpose({
  switchTheme,
  currentTheme
})
</script>