<script setup>
import { onMounted } from 'vue'

// 全局主题初始化 - 确保整个应用都有正确的主题
onMounted(() => {
  const savedTheme = localStorage.getItem('appTheme') || 'gold'

  // 确保body有正确的主题类
  document.body.classList.remove('theme-gold', 'theme-purple')
  document.body.classList.add(`theme-${savedTheme}`)

  // 触发主题变化事件，让各个组件同步
  window.dispatchEvent(new CustomEvent('themeChanged', {
    detail: { theme: savedTheme }
  }))
})
</script>

<template>
  <!-- 路由视图由布局组件管理 -->
  <router-view />
</template>

<style scoped>
/* Layout styles are handled by Tailwind utilities in style.css */
</style>
