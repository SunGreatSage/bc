<template>
  <div class="w-full max-w-md">
    <!-- Logo和标题 -->
    <div class="text-center mb-10">
      <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br rounded-3xl shadow-xl mb-6 relative overflow-hidden" :class="themeClasses.logoGradient">
        <div class="absolute inset-0 bg-white/10 rounded-3xl"></div>
        <svg class="w-12 h-12 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
      </div>
      <h1 class="text-4xl font-bold bg-gradient-to-r bg-clip-text text-transparent mb-3" :class="themeClasses.titleGradient">
        彩票平台
      </h1>
      <p class="text-gray-600 text-lg">
        开启您的幸运之旅
      </p>
    </div>

    <!-- 登录表单卡片 -->
    <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 border border-amber-100">
      <form class="space-y-6" @submit.prevent="handleLogin">
        <!-- 用户名输入 -->
        <div class="space-y-2">
          <label for="username" class="block text-sm font-bold text-gray-700 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full" :class="themeClasses.iconColor"></div>
            用户名
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-6 w-6" :class="themeClasses.iconColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
            </div>
            <input
              id="username"
              v-model="loginForm.username"
              name="username"
              type="text"
              autocomplete="username"
              required
              :disabled="isLoading"
              class="block w-full pl-12 pr-4 py-4 border-2 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 disabled:bg-gray-50 disabled:opacity-50 text-lg"
              :class="[themeClasses.inputBorder, themeClasses.inputFocus, themeClasses.inputBg]"
              placeholder="请输入您的用户名"
            />
          </div>
        </div>

        <!-- 密码输入 -->
        <div class="space-y-2">
          <label for="password" class="block text-sm font-bold text-gray-700 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full" :class="themeClasses.iconColor"></div>
            密码
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-6 w-6" :class="themeClasses.iconColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </div>
            <input
              id="password"
              v-model="loginForm.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              :disabled="isLoading"
              class="block w-full pl-12 pr-4 py-4 border-2 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 disabled:bg-gray-50 disabled:opacity-50 text-lg"
              :class="[themeClasses.inputBorder, themeClasses.inputFocus, themeClasses.inputBg]"
              placeholder="请输入您的密码"
            />
          </div>
        </div>

        <!-- 错误信息显示 -->
        <div v-if="errorMessage" class="bg-red-50 border-2 border-red-200 rounded-xl p-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">
                {{ errorMessage }}
              </p>
            </div>
          </div>
        </div>

        <!-- 登录按钮 -->
        <div>
          <button
            type="submit"
            :disabled="isLoading || !isFormValid"
            class="w-full flex justify-center py-4 px-6 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2"
            :class="[themeClasses.buttonGradient, themeClasses.buttonFocus]"
          >
            {{ isLoading ? '登录中...' : '立即登录' }}
          </button>
        </div>
      </form>

      </div>

    <!-- 版权信息 -->
    <div class="text-center text-sm text-gray-500 mt-8">
      <p>&copy; 2025 彩票平台</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { userService } from '@/services/api'

const router = useRouter()

// 主题状态管理
const currentTheme = ref('gold')

// 表单数据
const loginForm = ref({
  username: '', // 默认填充测试账号
  password: '', // 默认填充测试密码
  terminal: 1          // 终端类型：1表示PC端
})

// 状态管理
const isLoading = ref(false)
const errorMessage = ref('')

// 主题相关的计算属性
const themeClasses = computed(() => {
  return {
    logoGradient: currentTheme.value === 'gold'
      ? 'from-amber-500 to-orange-600'
      : 'from-purple-500 to-indigo-600',
    titleGradient: currentTheme.value === 'gold'
      ? 'from-amber-600 to-orange-600'
      : 'from-purple-600 to-indigo-600',
    iconColor: currentTheme.value === 'gold'
      ? 'text-amber-500'
      : 'text-purple-500',
    inputBorder: currentTheme.value === 'gold'
      ? 'border-amber-200'
      : 'border-purple-200',
    inputFocus: currentTheme.value === 'gold'
      ? 'focus:ring-amber-500'
      : 'focus:ring-purple-500',
    inputBg: currentTheme.value === 'gold'
      ? 'bg-amber-50/30'
      : 'bg-purple-50/30',
    buttonGradient: currentTheme.value === 'gold'
      ? 'from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700'
      : 'from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700',
    buttonFocus: currentTheme.value === 'gold'
      ? 'focus:ring-amber-500'
      : 'focus:ring-purple-500'
  }
})

// 监听主题变化
const updateTheme = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

// 表单验证
const isFormValid = computed(() => {
  return loginForm.value.username.trim() !== '' &&
         loginForm.value.password.trim() !== ''
})

// 处理登录
const handleLogin = async () => {
  if (!isFormValid.value) {
    errorMessage.value = '请填写完整的用户名和密码'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const result = await userService.login(
      loginForm.value.username,
      loginForm.value.password,
      loginForm.value.terminal
    )

    if (result.code === 1) {
      // 登录成功
      const { token, userid, username, nickname, kmoney } = result.data

      // 存储用户信息和token
      localStorage.setItem('userToken', token)
      localStorage.setItem('userInfo', JSON.stringify({
        userid,
        username,
        nickname,
        kmoney
      }))

      // 触发登录成功事件
      window.dispatchEvent(new CustomEvent('userLoggedIn', {
        detail: { userid, username }
      }))

      // 跳转到主页
      router.push('/')

    } else {
      // 登录失败
      errorMessage.value = result.msg || '登录失败，请检查用户名和密码'
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = '网络错误，请稍后重试'
  } finally {
    isLoading.value = false
  }
}

// 生命周期钩子
onMounted(() => {
  // 初始化主题
  updateTheme()

  // 监听 localStorage 变化
  window.addEventListener('storage', updateTheme)

  // 监听全局主题变化事件
  window.addEventListener('themeChanged', updateTheme)
})

onUnmounted(() => {
  // 清理事件监听器
  window.removeEventListener('storage', updateTheme)
  window.removeEventListener('themeChanged', updateTheme)
})
</script>

<style scoped>
/* 自定义样式 */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>