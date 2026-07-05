<template>
  <div class="min-h-screen px-4 py-6" :class="pageBgClass">
    <div class="mx-auto w-full max-w-md">
      <div class="mb-4 flex items-center justify-between">
        <div>
          <div class="text-base font-bold text-gray-900">开奖</div>
          <div class="text-xs text-gray-500">
            <span v-if="qishu">第 {{ qishu }} 期</span>
            <span v-else>获取期号中...</span>
            <span class="mx-1">·</span>
            <span>盘口：{{ plateCode }}</span>
          </div>
        </div>

        <div class="flex items-center gap-2 bg-white/90 backdrop-blur-sm rounded-full px-3 py-2 shadow-lg border border-gray-200">
          <button
            @click="switchTheme('gold')"
            :class="{
              'w-7 h-7 rounded-full transition-all duration-200': true,
              'ring-2 ring-amber-400 ring-offset-2': currentTheme === 'gold',
              'opacity-60 hover:opacity-100': currentTheme !== 'gold'
            }"
            class="bg-gradient-to-br from-amber-400 to-amber-600"
            title="暗金主题"
          />
          <button
            @click="switchTheme('purple')"
            :class="{
              'w-7 h-7 rounded-full transition-all duration-200': true,
              'ring-2 ring-purple-400 ring-offset-2': currentTheme === 'purple',
              'opacity-60 hover:opacity-100': currentTheme !== 'purple'
            }"
            class="bg-gradient-to-br from-purple-400 to-purple-600"
            title="暗紫主题"
          />
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white/90 backdrop-blur-sm p-4 shadow-sm">
        <DrawNumbersRow :numbers="displayNumbers" :special-index="6" :active-index="activeIndex" />

        <div class="mt-3 text-center text-xs font-medium">
          <div :class="currentTheme === 'gold' ? 'text-amber-700' : 'text-purple-700'">
            {{ statusTitle }}
          </div>
          <div v-if="statusSub" class="mt-1 text-[11px] text-gray-500">
            {{ statusSub }}
          </div>
        </div>

        <div class="mt-3 flex items-center justify-center">
          <button
            type="button"
            class="text-xs text-gray-600 hover:text-gray-900 underline disabled:opacity-60 disabled:cursor-not-allowed"
            :disabled="loading"
            @click="startFlow"
          >
            {{ loading ? '刷新中...' : '刷新' }}
          </button>
        </div>
      </div>

      <div v-if="errorText" class="mt-3 text-center text-xs text-red-600">
        {{ errorText }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { lotteryService } from '@/services/api'
import DrawNumbersRow from '@/components/Layout/DrawNumbersRow.vue'

const route = useRoute()

const currentTheme = ref(localStorage.getItem('appTheme') || 'gold')

const pageBgClass = computed(() => {
  return currentTheme.value === 'gold'
    ? 'bg-gradient-to-br from-amber-100 via-yellow-50 to-orange-100'
    : 'bg-gradient-to-br from-purple-50 to-indigo-50'
})

const loading = ref(false)
const errorText = ref('')

const gid = computed(() => {
  const raw = route.query.gid
  const value = Array.isArray(raw) ? raw[0] : raw
  const parsed = parseInt(String(value ?? ''), 10)
  return Number.isFinite(parsed) ? parsed : 200
})

const plateCode = computed(() => {
  const raw = route.query.plate_code ?? route.query.plate ?? route.query.p
  const value = Array.isArray(raw) ? raw[0] : raw
  return String(value || localStorage.getItem('selectedPlateCode') || 'A')
})

const qishu = ref('')
const displayNumbers = ref(Array(7).fill('?'))
const activeIndex = ref(-1)
const secondsToKj = ref(null)
const phase = ref('idle') // idle | loading | waiting | polling | animating | done

let pollTimer = null
let countdownTimer = null
let runId = 0

const statusTitle = computed(() => {
  if (phase.value === 'animating') return '开奖中...'
  if (phase.value === 'done') return '本期已开奖'
  if (phase.value === 'loading') return '加载中...'
  return '等待开奖'
})

const statusSub = computed(() => {
  if (phase.value === 'waiting' || phase.value === 'polling') {
    const text = formatSeconds(secondsToKj.value)
    return `距离开奖：${text}`
  }
  return ''
})

const storageKey = computed(() => `kj:display-result:v2:${gid.value}:${plateCode.value}`)

const safeParseJson = (text) => {
  try {
    return JSON.parse(text)
  } catch {
    return null
  }
}

const loadCachedResult = (targetQishu) => {
  const cached = safeParseJson(localStorage.getItem(storageKey.value) || '')
  if (!cached) return null
  if (cached.qishu !== targetQishu) return null
  if (!Array.isArray(cached.numbers) || cached.numbers.length < 7) return null
  return cached.numbers.slice(0, 7)
}

const saveCachedResult = (targetQishu, numbers) => {
  try {
    localStorage.setItem(storageKey.value, JSON.stringify({ qishu: targetQishu, numbers }))
  } catch {
    // ignore quota errors
  }
}

const clearTimers = () => {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
  if (countdownTimer) {
    clearInterval(countdownTimer)
    countdownTimer = null
  }
}

const pad2 = (n) => String(n).padStart(2, '0')

const formatSeconds = (seconds) => {
  if (!Number.isFinite(seconds) || seconds === null || seconds === undefined) return '--:--:--'
  const s = Math.max(0, parseInt(String(seconds), 10) || 0)
  const h = Math.floor(s / 3600)
  const m = Math.floor((s % 3600) / 60)
  const ss = s % 60
  return `${pad2(h)}:${pad2(m)}:${pad2(ss)}`
}

const parseDateTime = (value) => {
  if (!value) return null
  const text = String(value).trim()
  if (!text) return null
  const normalized = text.includes('T') ? text : text.replace(' ', 'T')
  const d = new Date(normalized)
  if (Number.isNaN(d.getTime())) return null
  return d
}

const extractSecondsToKj = (data) => {
  const candidates = [
    data?.seconds_to_kj,
    data?.seconds_to_draw,
    data?.time_info?.seconds_to_kj,
    data?.time_info?.seconds_to_draw
  ]
  for (const c of candidates) {
    const v = parseInt(String(c ?? ''), 10)
    if (Number.isFinite(v)) return v
  }

  const maybeTime = data?.kj_time || data?.draw_time || data?.time_info?.kj_time || data?.time_info?.draw_time
  const d = parseDateTime(maybeTime)
  if (!d) return null
  return Math.max(0, Math.floor((d.getTime() - Date.now()) / 1000))
}

const startCountdown = (id, initialSeconds) => {
  const v = parseInt(String(initialSeconds ?? ''), 10)
  if (!Number.isFinite(v) || v < 0) return

  secondsToKj.value = v
  if (countdownTimer) clearInterval(countdownTimer)

  countdownTimer = setInterval(() => {
    if (id !== runId) return
    if (!Number.isFinite(secondsToKj.value)) return
    if (secondsToKj.value <= 0) {
      clearInterval(countdownTimer)
      countdownTimer = null
      return
    }
    secondsToKj.value -= 1
  }, 1000)
}

const normalizeNumbers = (numbers) => {
  if (!Array.isArray(numbers)) return null
  const trimmed = numbers.slice(0, 7).map(n => String(n).trim())
  if (trimmed.length < 7) return null
  const allNumeric = trimmed.every(n => Number.isFinite(parseInt(n, 10)))
  if (!allNumeric) return null
  return trimmed
}

const randomSpinNumber = () => {
  // 按需求：随机范围 1-48（最终停到实际号码，可能为49）
  return String(Math.floor(Math.random() * 48) + 1)
}

const randomBetween = (min, max) => {
  const lo = Math.min(min, max)
  const hi = Math.max(min, max)
  return lo + Math.floor(Math.random() * (hi - lo + 1))
}

const spinOne = (id, index, target) => {
  return new Promise((resolve) => {
    // 每个球的动画时长控制在 3s - 5s
    const totalMs = randomBetween(3000, 5000)
    const settleMs = 220
    const spinMs = Math.max(0, totalMs - settleMs)
    const tickMs = 80

    const interval = setInterval(() => {
      if (id !== runId) {
        clearInterval(interval)
        resolve()
        return
      }
      displayNumbers.value[index] = randomSpinNumber()
    }, tickMs)

    setTimeout(() => {
      clearInterval(interval)
      if (id === runId) {
        displayNumbers.value[index] = target
      }
      setTimeout(resolve, settleMs)
    }, spinMs)
  })
}

const animateDraw = async (id, numbers) => {
  phase.value = 'animating'
  activeIndex.value = -1

  for (let i = 0; i < 7; i++) {
    if (id !== runId) return
    activeIndex.value = i
    await spinOne(id, i, numbers[i])
  }

  if (id !== runId) return
  activeIndex.value = -1
  phase.value = 'done'
  saveCachedResult(qishu.value, numbers)
}

const fetchCurrentQishu = async (id) => {
  const response = await lotteryService.getCurrentPeriod(gid.value, plateCode.value)

  if (id !== runId) return null
  if (response?.code !== 1 || !response?.data?.qishu) {
    throw new Error(response?.msg || '获取当前期号失败')
  }

  const nextQishu = String(response.data.qishu)
  qishu.value = nextQishu

  const seconds = extractSecondsToKj(response.data)
  if (seconds !== null) startCountdown(id, seconds)

  return nextQishu
}

const fetchDrawResultOnce = async (id) => {
  if (!qishu.value) return
  if (phase.value === 'animating' || phase.value === 'done') return

  const response = await lotteryService.getDrawResult(gid.value, qishu.value, plateCode.value)
  if (id !== runId) return

  const seconds = extractSecondsToKj(response?.data)
  if (secondsToKj.value === null && seconds !== null) {
    startCountdown(id, seconds)
  }

  const numbers = normalizeNumbers(response?.data?.display_numbers || response?.data?.numbers)
  if (!numbers) return

  // 已经有结果：停止轮询并开奖动画（仅首次）
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
  if (countdownTimer) {
    clearInterval(countdownTimer)
    countdownTimer = null
  }
  secondsToKj.value = 0

  const cached = loadCachedResult(qishu.value)
  if (cached) {
    displayNumbers.value = cached
    phase.value = 'done'
    return
  }

  await animateDraw(id, numbers)
}

const startPollingDrawResult = (id) => {
  if (pollTimer) clearInterval(pollTimer)
  pollTimer = setInterval(() => {
    fetchDrawResultOnce(id)
  }, 3000)
}

const applyTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('appTheme', theme)

  document.body.classList.remove('theme-gold', 'theme-purple')
  document.body.classList.add(`theme-${theme}`)

  window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }))
}

const switchTheme = (theme) => {
  applyTheme(theme)
}

const handleThemeChange = (event) => {
  currentTheme.value = event?.detail?.theme || localStorage.getItem('appTheme') || 'gold'
}

const updateThemeFromStorage = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

const startFlow = async () => {
  runId += 1
  const id = runId

  clearTimers()
  errorText.value = ''
  loading.value = true
  qishu.value = ''
  secondsToKj.value = null
  activeIndex.value = -1
  displayNumbers.value = Array(7).fill('?')
  phase.value = 'loading'

  try {
    const current = await fetchCurrentQishu(id)
    if (id !== runId || !current) return

    const cached = loadCachedResult(current)
    if (cached) {
      displayNumbers.value = cached
      phase.value = 'done'
      clearTimers()
      return
    }

    phase.value = 'waiting'
    await fetchDrawResultOnce(id)
    if (id !== runId) return
    if (phase.value !== 'done' && phase.value !== 'animating') {
      phase.value = 'polling'
      startPollingDrawResult(id)
    }
  } catch (error) {
    console.error(error)
    errorText.value = error?.message || '加载失败'
    phase.value = 'waiting'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const savedTheme = localStorage.getItem('appTheme') || 'gold'
  applyTheme(savedTheme)

  startFlow()

  window.addEventListener('storage', updateThemeFromStorage)
  window.addEventListener('themeChanged', handleThemeChange)
})

onUnmounted(() => {
  runId += 1
  window.removeEventListener('storage', updateThemeFromStorage)
  window.removeEventListener('themeChanged', handleThemeChange)
  clearTimers()
})

watch([plateCode, gid], () => {
  startFlow()
})
</script>
