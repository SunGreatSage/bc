<template>
  <div class="change-password">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">修改密码</h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- 当前密码 -->
          <div>
            <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-2">
              当前密码
            </label>
            <div class="relative">
              <input
                id="currentPassword"
                v-model="form.currentPassword"
                :type="showCurrentPassword ? 'text' : 'password'"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent pr-10"
                placeholder="请输入当前密码"
                required
              />
              <button
                type="button"
                @click="showCurrentPassword = !showCurrentPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <span class="text-gray-400">{{ showCurrentPassword ? '🙈' : '👁️' }}</span>
              </button>
            </div>
            <p v-if="errors.currentPassword" class="mt-1 text-sm text-red-600">
              {{ errors.currentPassword }}
            </p>
          </div>

          <!-- 新密码 -->
          <div>
            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-2">
              新密码
            </label>
            <div class="relative">
              <input
                id="newPassword"
                v-model="form.newPassword"
                :type="showNewPassword ? 'text' : 'password'"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent pr-10"
                placeholder="请输入新密码"
                required
                @input="validatePassword"
              />
              <button
                type="button"
                @click="showNewPassword = !showNewPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <span class="text-gray-400">{{ showNewPassword ? '🙈' : '👁️' }}</span>
              </button>
            </div>
            <p v-if="errors.newPassword" class="mt-1 text-sm text-red-600">
              {{ errors.newPassword }}
            </p>

            <!-- 密码强度指示器 -->
            <div v-if="form.newPassword" class="mt-2">
              <div class="flex items-center justify-between text-sm mb-1">
                <span class="text-gray-600">密码强度</span>
                <span :class="passwordStrengthColor">{{ passwordStrengthText }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full transition-all duration-300"
                  :class="passwordStrengthBarColor"
                  :style="{ width: passwordStrength + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <!-- 确认新密码 -->
          <div>
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
              确认新密码
            </label>
            <div class="relative">
              <input
                id="confirmPassword"
                v-model="form.confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent pr-10"
                placeholder="请再次输入新密码"
                required
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <span class="text-gray-400">{{ showConfirmPassword ? '🙈' : '👁️' }}</span>
              </button>
            </div>
            <p v-if="errors.confirmPassword" class="mt-1 text-sm text-red-600">
              {{ errors.confirmPassword }}
            </p>
          </div>

          <!-- 验证码 -->
          <div>
            <label for="captcha" class="block text-sm font-medium text-gray-700 mb-2">
              验证码
            </label>
            <div class="flex gap-2">
              <input
                id="captcha"
                v-model="form.captcha"
                type="text"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                placeholder="请输入验证码"
                required
              />
              <button
                type="button"
                @click="getCaptcha"
                :disabled="captchaLoading"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
              >
                {{ captchaLoading ? '发送中...' : '获取验证码' }}
              </button>
            </div>
            <p v-if="captchaSent" class="mt-1 text-sm text-green-600">
              验证码已发送到您的手机
            </p>
          </div>

          <!-- 提交按钮 -->
          <div class="flex gap-4">
            <button
              type="submit"
              :disabled="loading"
              class="flex-1 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              {{ loading ? '提交中...' : '修改密码' }}
            </button>
            <button
              type="button"
              @click="resetForm"
              class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200"
            >
              重置
            </button>
          </div>
        </form>

        <!-- 密码要求说明 -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
          <h4 class="font-semibold text-blue-800 mb-2">密码要求</h4>
          <ul class="text-blue-700 space-y-1 text-sm">
            <li :class="{ 'text-green-600 font-semibold': passwordChecks.length }">
              ✓ 至少8个字符
            </li>
            <li :class="{ 'text-green-600 font-semibold': passwordChecks.uppercase }">
              ✓ 包含大写字母
            </li>
            <li :class="{ 'text-green-600 font-semibold': passwordChecks.lowercase }">
              ✓ 包含小写字母
            </li>
            <li :class="{ 'text-green-600 font-semibold': passwordChecks.number }">
              ✓ 包含数字
            </li>
            <li :class="{ 'text-green-600 font-semibold': passwordChecks.special }">
              ✓ 包含特殊字符
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const form = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  captcha: ''
})

const errors = ref({})
const loading = ref(false)
const captchaLoading = ref(false)
const captchaSent = ref(false)

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const passwordChecks = ref({
  length: false,
  uppercase: false,
  lowercase: false,
  number: false,
  special: false
})

const passwordStrength = computed(() => {
  let score = 0
  const checks = Object.values(passwordChecks.value)
  const trueChecks = checks.filter(check => check).length

  score = (trueChecks / checks.length) * 100

  // 额外加分
  if (form.value.newPassword.length >= 12) score += 10
  if (form.value.newPassword.length >= 16) score += 10

  return Math.min(score, 100)
})

const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 30) return '弱'
  if (passwordStrength.value < 60) return '中等'
  if (passwordStrength.value < 80) return '强'
  return '非常强'
})

const passwordStrengthColor = computed(() => {
  if (passwordStrength.value < 30) return 'text-red-600'
  if (passwordStrength.value < 60) return 'text-yellow-600'
  if (passwordStrength.value < 80) return 'text-blue-600'
  return 'text-green-600'
})

const passwordStrengthBarColor = computed(() => {
  if (passwordStrength.value < 30) return 'bg-red-500'
  if (passwordStrength.value < 60) return 'bg-yellow-500'
  if (passwordStrength.value < 80) return 'bg-blue-500'
  return 'bg-green-500'
})

const validatePassword = () => {
  const password = form.value.newPassword

  passwordChecks.value = {
    length: password.length >= 8,
    uppercase: /[A-Z]/.test(password),
    lowercase: /[a-z]/.test(password),
    number: /\d/.test(password),
    special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
  }

  // 清除新密码错误
  if (errors.value.newPassword) {
    delete errors.value.newPassword
  }

  // 检查确认密码
  if (form.value.confirmPassword && form.value.confirmPassword !== password) {
    errors.value.confirmPassword = '两次输入的密码不一致'
  } else if (errors.value.confirmPassword) {
    delete errors.value.confirmPassword
  }
}

const validateForm = () => {
  errors.value = {}

  // 验证当前密码
  if (!form.value.currentPassword) {
    errors.value.currentPassword = '请输入当前密码'
  }

  // 验证新密码
  if (!form.value.newPassword) {
    errors.value.newPassword = '请输入新密码'
  } else if (!passwordChecks.value.length) {
    errors.value.newPassword = '密码至少需要8个字符'
  } else if (Object.values(passwordChecks.value).some(check => !check)) {
    errors.value.newPassword = '密码不符合安全要求'
  }

  // 验证确认密码
  if (!form.value.confirmPassword) {
    errors.value.confirmPassword = '请确认新密码'
  } else if (form.value.confirmPassword !== form.value.newPassword) {
    errors.value.confirmPassword = '两次输入的密码不一致'
  }

  // 验证验证码
  if (!form.value.captcha) {
    errors.value.captcha = '请输入验证码'
  }

  return Object.keys(errors.value).length === 0
}

const getCaptcha = async () => {
  captchaLoading.value = true
  try {
    // 模拟发送验证码
    await new Promise(resolve => setTimeout(resolve, 1000))
    captchaSent.value = true

    // 60秒倒计时
    setTimeout(() => {
      captchaSent.value = false
    }, 60000)

    alert('验证码已发送到您的手机')
  } catch (error) {
    alert('验证码发送失败，请重试')
  } finally {
    captchaLoading.value = false
  }
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    // 模拟API调用
    await new Promise(resolve => setTimeout(resolve, 2000))

    alert('密码修改成功！')
    resetForm()

  } catch (error) {
    alert('密码修改失败，请重试')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  form.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
    captcha: ''
  }
  errors.value = {}
  passwordChecks.value = {
    length: false,
    uppercase: false,
    lowercase: false,
    number: false,
    special: false
  }
  captchaSent.value = false
}
</script>