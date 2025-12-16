<template>
  <div class="min-h-screen flex items-center justify-center py-6 px-4 sm:px-6 lg:px-8" :class="themeClass">
    <!-- 主题切换按钮 - 登录页面也可用 -->
    <div class="fixed top-4 right-4 z-50">
      <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg border border-gray-200">
        <button
          @click="switchTheme('gold')"
          :class="{
            'w-8 h-8 rounded-full transition-all duration-200': true,
            'ring-2 ring-amber-400 ring-offset-2': currentTheme === 'gold',
            'opacity-60 hover:opacity-100': currentTheme !== 'gold'
          }"
          class="bg-gradient-to-br from-amber-400 to-amber-600"
          title="暗金色主题"
        />
        <button
          @click="switchTheme('purple')"
          :class="{
            'w-8 h-8 rounded-full transition-all duration-200': true,
            'ring-2 ring-purple-400 ring-offset-2': currentTheme === 'purple',
            'opacity-60 hover:opacity-100': currentTheme !== 'purple'
          }"
          class="bg-gradient-to-br from-purple-400 to-purple-600"
          title="暗紫色主题"
        />
      </div>
    </div>

    <!-- 登录页面内容 -->
    <router-view />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const currentTheme = ref('gold')

// 获取当前主题对应的背景类
const themeClass = computed(() => {
  return currentTheme.value === 'gold'
    ? 'bg-gradient-to-br from-amber-100 via-yellow-50 to-orange-100'
    : 'bg-gradient-to-br from-purple-50 to-indigo-50'
})

const switchTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('appTheme', theme)

  // 移除所有主题类
  document.body.classList.remove('theme-gold', 'theme-purple')

  // 添加新主题类
  document.body.classList.add(`theme-${theme}`)

  // 触发全局主题变化事件
  window.dispatchEvent(new CustomEvent('themeChanged', {
    detail: { theme }
  }))
}

onMounted(() => {
  // 初始化主题
  const savedTheme = localStorage.getItem('appTheme') || 'gold'
  currentTheme.value = savedTheme
  switchTheme(savedTheme)
})
</script>

<style scoped>
/* 登录页面特有的样式 */
</style>