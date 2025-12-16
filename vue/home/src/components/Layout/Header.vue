<template>
  <header
    class="header text-white shadow-sm"
    :data-theme="currentTheme"
    :class="{
      'bg-gradient-to-br from-amber-700 via-amber-800 to-amber-900': currentTheme === 'gold',
      'bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700': currentTheme === 'purple'
    }"
    :style="{
      '--theme-primary': currentTheme === 'gold' ? '#f59e0b' : '#a855f7',
      '--theme-secondary': currentTheme === 'gold' ? '#fbbf24' : '#c084fc',
      '--theme-text': currentTheme === 'gold' ? '#78350f' : '#581c87'
    }"
  >
    <!-- First Row: Menu, Logo, Lottery Types -->
    <div class="flex items-center justify-between px-4 lg:px-6 h-12 border-b border-white/10"
         :class="{
           'border-amber-600/20': currentTheme === 'gold',
           'border-purple-400/20': currentTheme === 'purple'
         }">
      <!-- Left side - Menu Toggle -->
      <div class="flex items-center gap-3 flex-1">
        <button
          @click="$emit('toggle-sidebar')"
          class="flex items-center gap-1 p-1.5 text-white hover:bg-white/20 rounded-lg transition-all duration-200"
          :class="{
            'hover:bg-amber-600/30': currentTheme === 'gold',
            'hover:bg-purple-500/30': currentTheme === 'purple'
          }">
          <span class="text-lg">☰</span>
          <span class="text-xs">菜单</span>
        </button>

        <!-- Logo and Title -->
        <div class="flex items-center gap-1.5">
          <img src="/vite.svg" alt="Logo" class="w-5 h-5 brightness-0 invert" />
          <span class="text-sm font-bold">彩票平台</span>
        </div>
      </div>

      <!-- Right side - Lottery Type Selector -->
      <div class="flex items-center gap-1.5 flex-shrink-0">
        <span class="text-xs text-white/80"
              :class="{
                'text-amber-100': currentTheme === 'gold',
                'text-purple-100': currentTheme === 'purple'
              }">彩种：</span>
        <div class="relative">
          <select
            v-model="selectedLotteryType"
            class="bg-gray-200 text-gray-800 border border-gray-400 rounded pl-1.5 pr-6 py-0.5 text-xs focus:outline-none focus:ring-1 min-w-0 appearance-none cursor-pointer"
            :class="{
              'bg-amber-50 border-amber-300 text-amber-900 focus:ring-amber-400 theme-gold-dropdown': currentTheme === 'gold',
              'bg-purple-50 border-purple-300 text-purple-900 focus:ring-purple-400 theme-purple-dropdown': currentTheme === 'purple'
            }">
            <option
              v-for="type in lotteryTypes"
              :key="type.id"
              :value="type.id"
              class="text-gray-800"
            >
              {{ type.name }}
            </option>
          </select>
          <!-- 下拉箭头图标 -->
          <div class="absolute right-1 top-1/2 transform -translate-y-1/2 pointer-events-none"
               :class="{
                 'text-amber-600': currentTheme === 'gold',
                 'text-purple-600': currentTheme === 'purple'
               }">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Second Row: Current Market and Bet Types -->
    <div class="flex items-center justify-between px-4 lg:px-6 h-10 border-b border-white/10"
         :class="{
           'border-amber-600/20': currentTheme === 'gold',
           'border-purple-400/20': currentTheme === 'purple'
         }">
      <!-- Left side - Market Info -->
      <div class="flex items-center gap-3 flex-1">
        <span class="text-xs text-white/80"
              :class="{
                'text-amber-100': currentTheme === 'gold',
                'text-purple-100': currentTheme === 'purple'
              }">当前盘口：</span>
        <div class="relative">
          <select v-model="marketInfo.type" class="bg-gray-200 text-gray-800 border border-gray-400 rounded pl-1.5 pr-6 py-0.5 text-xs focus:outline-none focus:ring-1 flex-shrink-0 appearance-none cursor-pointer"
                  :class="{
                    'bg-amber-50 border-amber-300 text-amber-900 focus:ring-amber-400 theme-gold-dropdown': currentTheme === 'gold',
                    'bg-purple-50 border-purple-300 text-purple-900 focus:ring-purple-400 theme-purple-dropdown': currentTheme === 'purple'
                  }">
            <option
              v-for="plate in plateList"
              :key="plate.code"
              :value="plate.code"
              class="text-gray-800"
            >
              {{ plate.name }}
            </option>
          </select>
          <!-- 下拉箭头图标 -->
          <div class="absolute right-1 top-1/2 transform -translate-y-1/2 pointer-events-none"
               :class="{
                 'text-amber-600': currentTheme === 'gold',
                 'text-purple-600': currentTheme === 'purple'
               }">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Right side - Bet Type Selector -->
      <div class="flex items-center gap-1.5 flex-shrink-0">
        <span class="text-xs text-white/80"
              :class="{
                'text-amber-100': currentTheme === 'gold',
                'text-purple-100': currentTheme === 'purple'
              }">类型：</span>
        <div class="relative">
          <select
            v-model="selectedBetType"
            class="bg-gray-200 text-gray-800 border border-gray-400 rounded pl-2 pr-6 py-0.5 text-xs focus:outline-none focus:ring-1 min-w-0 appearance-none cursor-pointer"
            :class="{
              'bg-amber-50 border-amber-300 text-amber-900 focus:ring-amber-400 theme-gold-dropdown': currentTheme === 'gold',
              'bg-purple-50 border-purple-300 text-purple-900 focus:ring-purple-400 theme-purple-dropdown': currentTheme === 'purple'
            }">
            <option
              v-if="isLoading && betTypes.length === 0"
              value=""
              disabled
              class="text-gray-500"
            >
              加载中...
            </option>
            <option
              v-for="betType in betTypes"
              :key="betType.value"
              :value="betType.value"
              class="text-gray-800"
            >
              {{ betType.label }}
            </option>
            <option
              v-if="!isLoading && betTypes.length === 0"
              value=""
              disabled
              class="text-gray-500"
            >
              暂无玩法
            </option>
          </select>
          <!-- 下拉箭头图标 -->
          <div class="absolute right-1 top-1/2 transform -translate-y-1/2 pointer-events-none"
               :class="{
                 'text-amber-600': currentTheme === 'gold',
                 'text-purple-600': currentTheme === 'purple'
               }">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Third Row: Account Info -->
    <div class="grid grid-cols-4 gap-3 px-4 lg:px-6 py-1.5 text-xs border-b bg-gray-50"
         :class="{
           'border-amber-300 bg-amber-50': currentTheme === 'gold',
           'border-purple-300 bg-purple-50': currentTheme === 'purple'
         }">
      <!-- Credit Limit -->
      <div class="text-center">
        <div class="text-xs mb-0.5"
             :class="{
               'text-amber-700': currentTheme === 'gold',
               'text-purple-700': currentTheme === 'purple'
             }">信用额度</div>
        <div class="font-semibold text-sm"
             :class="{
               'text-amber-900': currentTheme === 'gold',
               'text-purple-900': currentTheme === 'purple'
             }">¥{{ formatNumber(accountInfo.creditLimit) }}</div>
      </div>
      <!-- Bet Amount -->
      <div class="text-center">
        <div class="text-xs mb-0.5"
             :class="{
               'text-amber-700': currentTheme === 'gold',
               'text-purple-700': currentTheme === 'purple'
             }">下注金额</div>
        <div class="font-semibold text-sm"
             :class="{
               'text-amber-900': currentTheme === 'gold',
               'text-purple-900': currentTheme === 'purple'
             }">¥{{ formatNumber(accountInfo.betAmount) }}</div>
      </div>
      <!-- Available Balance (可用余额/kmoney) -->
      <div class="text-center">
        <div class="text-xs mb-0.5"
             :class="{
               'text-amber-700': currentTheme === 'gold',
               'text-purple-700': currentTheme === 'purple'
             }">可用余额</div>
        <div class="font-semibold text-sm"
             :class="{
               'text-amber-900': currentTheme === 'gold',
               'text-purple-900': currentTheme === 'purple'
             }">¥{{ formatNumber(accountInfo.creditBalance) }}</div>
      </div>
      <!-- Period Number -->
      <div class="text-center">
        <div class="text-xs mb-0.5"
             :class="{
               'text-amber-700': currentTheme === 'gold',
               'text-purple-700': currentTheme === 'purple'
             }">期数</div>
        <div class="font-semibold text-sm"
             :class="{
               'text-amber-900': currentTheme === 'gold',
               'text-purple-900': currentTheme === 'purple'
             }">{{ accountInfo.periodNumber }}</div>
      </div>
    </div>

    <!-- Fourth Row: Timer Info -->
    <div class="flex items-center justify-center px-4 lg:px-6 h-7 text-xs gap-4"
         :class="{
           'bg-amber-50': currentTheme === 'gold',
           'bg-purple-50': currentTheme === 'purple'
         }">
      <!-- 主倒计时 -->
      <div class="flex items-center gap-1.5">
        <span class="text-xs"
              :class="{
                'text-amber-700': currentTheme === 'gold',
                'text-purple-700': currentTheme === 'purple'
              }">{{ timerInfo.primaryLabel }}：</span>
        <span class="font-semibold text-sm"
              :class="{
                'text-amber-900': currentTheme === 'gold',
                'text-purple-900': currentTheme === 'purple'
              }">{{ timerInfo.primaryTime }}</span>
      </div>
      <!-- 开奖倒计时（如果不是负数则显示） -->
      <div v-if="timerInfo.showKjTime" class="flex items-center gap-1.5">
        <span class="text-xs"
              :class="{
                'text-amber-700': currentTheme === 'gold',
                'text-purple-700': currentTheme === 'purple'
              }">距离开奖：</span>
        <span class="font-semibold text-sm"
              :class="{
                'text-amber-900': currentTheme === 'gold',
                'text-purple-900': currentTheme === 'purple'
              }">{{ timerInfo.kjTime }}</span>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { lotteryService, userService } from '@/services/api'

const emit = defineEmits(['toggle-sidebar'])

const currentTheme = ref(localStorage.getItem('appTheme') || 'gold')

// 彩票类型选择
const selectedLotteryType = ref('200')

// 彩种数据结构 - 包含ID和名称
const lotteryTypes = ref([
  { id: '200', name: '新澳门六合彩' }
])

// 当前选中的彩种对象
const currentLotteryType = computed(() => {
  return lotteryTypes.value.find(type => type.id === selectedLotteryType.value) || lotteryTypes.value[0]
})

// 市场信息
const marketInfo = ref({
  type: 'A'  // 改为存储plate_code而不是中文名称
})

// 盘口列表（动态从后端获取）- 初始为空数组
const plateList = ref([])

// 下注类型选择
const selectedBetType = ref('') // 将从API响应中设置

// 玩法类型数据结构
const betTypes = ref([])

// 账户信息
const accountInfo = ref({
  creditLimit: 100000,
  betAmount: 0,
  creditBalance: 100000,
  periodNumber: '加载中...'
})

// 投注相关状态
const currentQishu = ref('')
const playList = ref([])
const isLoading = ref(false)

// 倒计时信息
const timerInfo = ref({
  primaryLabel: '加载中',
  primaryTime: '',  // ✅ 初始为空,避免显示错误的倒计时
  kjTime: '',
  showKjTime: false
})

// API 相关方法
const fetchCurrentQishu = async () => {
  try {
    isLoading.value = true
    // 传入彩种ID获取期号
    const result = await lotteryService.getCurrentPeriod(selectedLotteryType.value)
    if (result.code === 1) {
      currentQishu.value = result.data.qishu
      accountInfo.value.periodNumber = result.data.qishu
    } else {
      console.error('获取期号失败:', result.msg)
    }
  } catch (error) {
    console.error('获取期号API错误:', error)
  } finally {
    isLoading.value = false
  }
}

// 获取用户账户信息（统一使用新接口）
const fetchUserInfo = async () => {
  try {
    const gid = selectedLotteryType.value
    const plateCode = marketInfo.value.type  // 获取当前选中的盘口
    console.log('获取用户信息，gid:', gid, 'plate_code:', plateCode)
    const result = await userService.getUserInfo(gid, plateCode)
    if (result.code === 1 && result.data) {
      // 更新账户信息 - 使用新接口返回的字段
      // balance: 可用余额（实际可投注金额）
      // frozen_amount: 冻结金额
      // total_bet: 累计投注
      // period_bet_amount: 当期下注金额
      accountInfo.value.creditLimit = 0  // 新表架构暂不使用信用额度
      accountInfo.value.betAmount = parseFloat(result.data.period_bet_amount) || 0  // 当期下注金额
      accountInfo.value.creditBalance = parseFloat(result.data.balance) || 0  // 可用余额

      // 更新时间信息
      // 兼容两种响应格式: result.data.time_info 或 result.data 直接包含时间信息
      const rawTimeInfo = result.data.time_info || result.data

      if (rawTimeInfo && (rawTimeInfo.open_time || rawTimeInfo.current_qishu || rawTimeInfo.issue)) {
        // 优先使用 issue 字段 (新系统), 其次使用 current_qishu (兼容老系统)
        accountInfo.value.periodNumber = rawTimeInfo.issue || rawTimeInfo.current_qishu || rawTimeInfo.qishu || '加载中...'
        currentQishu.value = rawTimeInfo.issue || rawTimeInfo.current_qishu || rawTimeInfo.qishu || ''

        // 计算倒计时秒数（如果后端没有返回）
        const now = new Date()
        const timeInfo = { ...rawTimeInfo }

        // 兼容不同的字段名: seconds_to_draw (后端) vs seconds_to_kj (前端)
        console.log('🔍 [字段映射] 原始 seconds_to_draw:', timeInfo.seconds_to_draw)
        console.log('🔍 [字段映射] 原始 seconds_to_kj:', timeInfo.seconds_to_kj)

        if (timeInfo.seconds_to_draw !== undefined && timeInfo.seconds_to_kj === undefined) {
          timeInfo.seconds_to_kj = timeInfo.seconds_to_draw
          console.log('🔍 [字段映射] 从 seconds_to_draw 复制到 seconds_to_kj:', timeInfo.seconds_to_kj)
        }
        if (timeInfo.seconds_to_kj !== undefined && timeInfo.seconds_to_draw === undefined) {
          timeInfo.seconds_to_draw = timeInfo.seconds_to_kj
          console.log('🔍 [字段映射] 从 seconds_to_kj 复制到 seconds_to_draw:', timeInfo.seconds_to_draw)
        }

        // 如果后端没有返回 seconds_to_* 字段，则前端计算
        if (timeInfo.seconds_to_open === undefined && timeInfo.open_time) {
          const openTime = new Date(timeInfo.open_time)
          timeInfo.seconds_to_open = Math.max(0, Math.floor((openTime - now) / 1000))
        }

        if (timeInfo.seconds_to_close === undefined && timeInfo.close_time) {
          const closeTime = new Date(timeInfo.close_time)
          timeInfo.seconds_to_close = Math.max(0, Math.floor((closeTime - now) / 1000))
        }

        if (timeInfo.seconds_to_kj === undefined && (timeInfo.kj_time || timeInfo.draw_time)) {
          const kjTimeStr = timeInfo.kj_time || timeInfo.draw_time
          console.log('🔍 [调试] 开奖时间字符串:', kjTimeStr)
          const kjTime = new Date(kjTimeStr)
          console.log('🔍 [调试] 解析后的时间对象:', kjTime)
          console.log('🔍 [调试] 当前时间:', now)
          const diffMs = kjTime - now
          console.log('🔍 [调试] 时间差(毫秒):', diffMs)
          const diffSeconds = Math.floor(diffMs / 1000)
          console.log('🔍 [调试] 时间差(秒):', diffSeconds)
          timeInfo.seconds_to_kj = Math.max(0, diffSeconds)
          console.log('🔍 [调试] 计算后的 seconds_to_kj:', timeInfo.seconds_to_kj)
        }

        console.log('📊 [时间信息] 原始数据:', rawTimeInfo)
        console.log('📊 [时间信息] 处理后:', {
          current_qishu: timeInfo.current_qishu || timeInfo.qishu,
          open_time: timeInfo.open_time,
          close_time: timeInfo.close_time,
          kj_time: timeInfo.kj_time || timeInfo.draw_time,
          status: timeInfo.status,
          seconds_to_open: timeInfo.seconds_to_open,
          seconds_to_close: timeInfo.seconds_to_close,
          seconds_to_kj: timeInfo.seconds_to_kj
        })

        // 更新倒计时状态
        console.log('📊 [倒计时] 开始更新倒计时...')
        updateTimerStatus(timeInfo)
        console.log('📊 [倒计时] 更新完成，当前显示:', timerInfo.value)
      }

      console.log('用户账户信息更新完成:', {
        balance: result.data.balance,
        frozen_amount: result.data.frozen_amount,
        total_bet: result.data.total_bet,
        period_bet_amount: result.data.period_bet_amount,
        periodNumber: result.data.time_info?.current_qishu
      })
    } else {
      console.error('获取用户信息失败:', result.msg)

      // ✅ 当API返回错误时,清空倒计时并显示等待提示
      accountInfo.value.periodNumber = '尚未开盘'
      currentQishu.value = ''

      // 停止倒计时定时器
      if (countdownTimer) {
        clearInterval(countdownTimer)
        countdownTimer = null
      }

      // 清空时间数据
      timeInfoData = null

      // 更新倒计时显示为等待状态
      timerInfo.value.primaryLabel = '尚未开盘，请耐心等待！'
      timerInfo.value.primaryTime = ''
      timerInfo.value.showKjTime = false
    }
  } catch (error) {
    console.error('获取用户信息API错误:', error)

    // ✅ API调用异常时也要清空倒计时
    accountInfo.value.periodNumber = '加载失败'
    currentQishu.value = ''

    // 停止倒计时定时器
    if (countdownTimer) {
      clearInterval(countdownTimer)
      countdownTimer = null
    }

    // 清空时间数据
    timeInfoData = null

    // 更新倒计时显示
    timerInfo.value.primaryLabel = '加载失败'
    timerInfo.value.primaryTime = ''
    timerInfo.value.showKjTime = false
  }
}

// fetchBalance 别名，保持向后兼容
const fetchBalance = fetchUserInfo

// 存储时间信息用于倒计时
let timeInfoData = null
let countdownTimer = null

// 更新倒计时状态
const updateTimerStatus = (timeInfo) => {
  if (!timeInfo) return

  // 保存时间信息
  timeInfoData = timeInfo

  // 清除旧的定时器
  if (countdownTimer) {
    clearInterval(countdownTimer)
  }

  // 启动倒计时
  updateCountdown()
  countdownTimer = setInterval(updateCountdown, 1000)
}

// 格式化秒数为时分秒
const formatSeconds = (totalSeconds) => {
  const hours = Math.floor(totalSeconds / 3600)
  const minutes = Math.floor((totalSeconds % 3600) / 60)
  const seconds = totalSeconds % 60
  return `${hours}时${String(minutes).padStart(2, '0')}分${String(seconds).padStart(2, '0')}秒`
}

// 更新倒计时显示
const updateCountdown = () => {
  if (!timeInfoData) {
    console.warn('⚠️ [倒计时] timeInfoData 为空')
    return
  }

  const status = timeInfoData.status
  let primarySeconds = 0
  let primaryLabel = ''

  console.log('⏱️ [倒计时更新]', {
    status: status,
    seconds_to_open: timeInfoData.seconds_to_open,
    seconds_to_close: timeInfoData.seconds_to_close,
    seconds_to_kj: timeInfoData.seconds_to_kj,
    seconds_to_draw: timeInfoData.seconds_to_draw
  })

  // 根据状态获取主要倒计时
  // 后端可能返回数字状态(1=开盘中, 2=封盘中, 0=等待开盘, 3=已结算)
  // 或字符串状态('betting', 'closed', 'before_open', 'settled')
  switch(status) {
    case 1:
    case 'betting':
      primarySeconds = timeInfoData.seconds_to_close
      primaryLabel = '距离封盘'
      break
    case 2:
    case 'closed':
      // 已封盘,等待开奖
      // 如果开奖时间已过,显示"等待开奖中",否则显示倒计时
      if (timeInfoData.seconds_to_kj <= 0 || timeInfoData.seconds_to_draw <= 0) {
        primarySeconds = 0
        primaryLabel = '等待开奖中'
      } else {
        primarySeconds = timeInfoData.seconds_to_kj || timeInfoData.seconds_to_draw
        primaryLabel = '距离开奖'
      }
      break
    case 0:
    case 'waiting':
    case 'before_open':
      primarySeconds = timeInfoData.seconds_to_open
      primaryLabel = '距离开盘'
      break
    case 3:
    case 'settled':
      primarySeconds = timeInfoData.seconds_to_open
      primaryLabel = '等待下期开盘'
      break
    case 'unknown':
      // 未知状态,根据倒计时自动判断
      if (timeInfoData.seconds_to_close > 0) {
        primarySeconds = timeInfoData.seconds_to_close
        primaryLabel = '距离封盘'
      } else if (timeInfoData.seconds_to_kj > 0) {
        primarySeconds = timeInfoData.seconds_to_kj
        primaryLabel = '距离开奖'
      } else {
        primarySeconds = timeInfoData.seconds_to_open
        primaryLabel = '距离开盘'
      }
      break
    default:
      // 默认也当作开盘中处理
      primarySeconds = timeInfoData.seconds_to_close || timeInfoData.seconds_to_open || 0
      primaryLabel = timeInfoData.seconds_to_close > 0 ? '距离封盘' : '距离开盘'
  }

  console.log('⏱️ [倒计时结果]', {
    primaryLabel,
    primarySeconds,
    formatted: formatSeconds(primarySeconds)
  })

  // 更新主倒计时
  timerInfo.value.primaryLabel = primaryLabel

  // 如果主倒计时小于等于0
  if (primarySeconds <= 0) {
    // 如果是"等待开奖中"状态,不显示倒计时,但仍然每30秒刷新
    if (primaryLabel === '等待开奖中') {
      timerInfo.value.primaryTime = ''  // 不显示倒计时
      timerInfo.value.showKjTime = false

      // 每30秒刷新一次,检查是否已开奖
      if (countdownTimer) {
        clearInterval(countdownTimer)
        countdownTimer = null
      }
      setTimeout(() => {
        fetchUserInfo()
      }, 30000)
      return
    }

    // 其他情况显示0时00分00秒
    timerInfo.value.primaryTime = '0时00分00秒'
    timerInfo.value.showKjTime = false

    if (countdownTimer) {
      clearInterval(countdownTimer)
      countdownTimer = null
    }

    const refreshInterval = (status === 3 || status === 'settled' || status === 'unknown') ? 30000 : 5000
    setTimeout(() => {
      fetchUserInfo()
    }, refreshInterval)
    return
  }

  timerInfo.value.primaryTime = formatSeconds(primarySeconds)

  // 判断是否需要显示开奖倒计时
  // 如果距离开奖时间不是负数，且不是已经显示开奖倒计时的状态，则显示
  const kjSeconds = timeInfoData.seconds_to_kj
  if (kjSeconds > 0 && status !== 2 && status !== 'closed') {
    timerInfo.value.showKjTime = true
    timerInfo.value.kjTime = formatSeconds(kjSeconds)
  } else {
    timerInfo.value.showKjTime = false
  }

  // 递减对应的秒数（为下一秒做准备）
  switch(timeInfoData.status) {
    case 1:
    case 'betting':
      timeInfoData.seconds_to_close--
      break
    case 2:
    case 'closed':
      timeInfoData.seconds_to_kj--
      break
    case 0:
    case 3:
    case 'waiting':
    case 'before_open':
    case 'settled':
      timeInfoData.seconds_to_open--
      break
  }

  // 开奖倒计时也需要递减
  if (timeInfoData.seconds_to_kj > 0) {
    timeInfoData.seconds_to_kj--
  }

  // 发送全局事件，通知其他组件封盘倒计时的变化
  window.dispatchEvent(new CustomEvent('bettingStatusChanged', {
    detail: {
      status: timeInfoData.status,
      seconds_to_close: timeInfoData.seconds_to_close,
      seconds_to_kj: timeInfoData.seconds_to_kj,
      seconds_to_open: timeInfoData.seconds_to_open
    }
  }))
}

// 获取盘口列表
const fetchPlateList = async () => {
  try {
    console.log('📊 开始获取盘口列表...')
    const result = await lotteryService.getPlateList(selectedLotteryType.value)
    console.log('📊 盘口列表API响应:', result)

    if (result.code === 1 && result.data && result.data.length > 0) {
      plateList.value = result.data.map(plate => ({
        code: plate.code,
        name: plate.name
      }))
      console.log('✅ 盘口列表加载成功:', plateList.value)

      // 如果当前选中的盘口不在列表中,选择第一个
      if (!plateList.value.find(p => p.code === marketInfo.value.type)) {
        marketInfo.value.type = plateList.value[0].code
      }
    } else {
      console.warn('⚠️ 盘口列表为空，使用默认值')
    }
  } catch (error) {
    console.error('❌ 获取盘口列表失败:', error)
  }
}

// 监听盘口切换
watch(() => marketInfo.value.type, (newPlate, oldPlate) => {
  if (newPlate === oldPlate) return

  console.log(`🔄 [盘口切换] ${oldPlate} → ${newPlate}`)

  // 同步到 localStorage
  localStorage.setItem('selectedPlateCode', newPlate)

  // 重新获取用户信息(包含倒计时数据和期号)
  console.log('🔄 盘口切换,重新获取用户信息')
  fetchUserInfo()

  // 触发全局事件,通知其他组件盘口已切换
  window.dispatchEvent(new CustomEvent('plateChanged', {
    detail: { plateCode: newPlate, oldPlateCode: oldPlate }
  }))
})

const fetchPlayList = async () => {
  try {
    isLoading.value = true
    // 传入彩种ID获取玩法列表
    const result = await lotteryService.getPlayList(selectedLotteryType.value)
    if (result.code === 1) {
      playList.value = result.data
      // 根据API响应更新betTypes选项
      if (result.data && result.data.list && result.data.list.length > 0) {
        // 将API响应转换为betTypes格式
        betTypes.value = result.data.list.map(item => ({
          value: item.id, // 使用pid作为value
          label: item.name,           // 使用name作为label
          pid: item.id,             // 保留原始pid
        }))

        // 自动选择第一个选项作为默认值
        const firstBetType = betTypes.value[0]
        if (firstBetType && !selectedBetType.value) {
          selectedBetType.value = firstBetType.value
        } else if (firstBetType && !betTypes.value.find(bt => bt.value === selectedBetType.value)) {
          // 如果当前选中的betType不在新的选项中，也选择第一个选项
          selectedBetType.value = firstBetType.value
        }
      }
    } else {
      console.error('获取玩法列表失败:', result.msg)
    }
  } catch (error) {
    console.error('获取玩法列表API错误:', error)
  } finally {
    isLoading.value = false
  }
}

// 全局状态管理函数
const saveGlobalState = () => {
  localStorage.setItem('selectedLotteryType', selectedLotteryType.value)
  localStorage.setItem('selectedBetType', selectedBetType.value)
}

const loadGlobalState = () => {
  selectedLotteryType.value = localStorage.getItem('selectedLotteryType') || '200'
  selectedBetType.value = localStorage.getItem('selectedBetType') || ''
}

// 监听选择变化并保存
watch(selectedLotteryType, () => {
  saveGlobalState()
  // 重新获取用户信息(包含期号和倒计时)
  fetchUserInfo()
  // 触发全局事件通知其他组件
  window.dispatchEvent(new CustomEvent('lotteryTypeChanged', {
    detail: {
      lotteryType: selectedLotteryType.value,
      lotteryData: currentLotteryType.value
    }
  }))
})

watch(selectedBetType, () => {
  saveGlobalState()
  // 触发全局事件通知其他组件
  const currentBetTypeData = betTypes.value.find(bt => bt.value === selectedBetType.value)
  window.dispatchEvent(new CustomEvent('betTypeChanged', {
    detail: {
      betType: selectedBetType.value,
      betTypeData: currentBetTypeData,
      playName: currentBetTypeData?.label || '特碼'
    }
  }))
})

// 提供全局访问函数
const getSelectedLotteryType = () => selectedLotteryType.value
const getSelectedBetType = () => selectedBetType.value
const getCurrentLotteryData = () => currentLotteryType.value

// 将函数和数据挂载到全局，方便其他组件调用
window.getLotteryState = {
  getSelectedLotteryType,
  getSelectedBetType,
  getCurrentLotteryData
}

// 暴露betTypes数据给其他组件
watch(betTypes, (newBetTypes) => {
  window.betTypes = newBetTypes
  console.log('更新全局betTypes:', newBetTypes)
}, { immediate: true })

// 方法
// 下注类型选择已改为直接使用 v-model

const formatNumber = (num) => {
  return num.toLocaleString('zh-CN')
}


// Computed properties
const pageTitle = computed(() => {
  const titles = {
    '/': '首页',
    '/about': '关于我们',
    '/users': '用户管理',
    '/betting': '号码下注',
    '/bet-status': '注单状态',
    '/results': '开奖结果',
    '/rules': '游戏规则',
    '/change-password': '修改密码'
  }
  return titles[route.path] || '应用'
})

// 监听主题变化
const updateTheme = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

// 监听全局主题变化事件
const handleThemeChange = (event) => {
  currentTheme.value = event.detail.theme || localStorage.getItem('appTheme') || 'gold'
}

// 监听投注事件，更新余额
const handleBetPlaced = (event) => {
  const { amount, success } = event.detail
  if (success) {
    // 重新获取用户余额，确保数据准确性
    fetchBalance()
    console.log('投注成功，金额:', amount)
  }
}

// 自动开奖函数
const triggerAutoDraw = async () => {
  try {
    // TODO: 替换为新后端API接口
    // 可以调用 /api/lottery/auto_draw 或类似接口
    // const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'
    // const response = await fetch(`${baseURL}/api/lottery/auto_draw`)
    // const result = await response.json()

    // if (result.code === 1 && result.data.success > 0) {
    //   console.log(`🎲 [自动开奖] 成功开奖 ${result.data.success} 个期号`)
    //   // 开奖后刷新用户信息
    //   fetchUserInfo()
    // }

    console.log('⏸️ [自动开奖] 功能已禁用,等待后端接口就绪')
  } catch (error) {
    // 静默失败,不影响用户体验
    console.error('自动开奖失败:', error)
  }
}

// 自动开奖定时器
let autoDrawTimer = null

onMounted(() => {
  // 加载全局状态
  loadGlobalState()

  // 恢复盘口选择(从localStorage)
  const savedPlate = localStorage.getItem('selectedPlateCode')
  if (savedPlate) {
    console.log('📍 恢复盘口选择:', savedPlate)
    marketInfo.value.type = savedPlate
  }

  // 获取用户账户信息、期号、盘口列表和玩法列表数据
  // 注意: fetchUserInfo 已经包含了期号和倒计时信息，不需要再调用 fetchCurrentQishu
  fetchUserInfo()
  fetchPlateList()  // 新增：获取盘口列表
  fetchPlayList()

  // 启动自动开奖轮询(每30秒检查一次)
  // TODO: 暂时禁用自动开奖,等待后端接口就绪
  // autoDrawTimer = setInterval(triggerAutoDraw, 30000)
  // 立即执行一次
  // triggerAutoDraw()

  // 监听 localStorage 变化
  window.addEventListener('storage', updateTheme)

  // 监听全局主题变化事件
  window.addEventListener('themeChanged', handleThemeChange)

  // 监听投注事件
  window.addEventListener('betPlaced', handleBetPlaced)

  // 同时监听同页面内的主题切换
  const observer = new MutationObserver(() => {
    updateTheme()
  })
  observer.observe(document.body, {
    attributes: true,
    attributeFilter: ['class', 'data-theme']
  })

  // 强制覆盖下拉选项的默认样式
  const forceSelectStyles = () => {
    const selects = document.querySelectorAll('header select')
    selects.forEach(select => {
      const options = select.querySelectorAll('option')
      options.forEach(option => {
        // 强制移除浏览器默认的选中样式
        option.style.backgroundColor = 'white'
        option.style.color = '#374151'

        // 添加事件监听器来动态设置颜色
        option.addEventListener('mouseenter', () => {
          if (!option.selected) {
            option.style.backgroundColor = currentTheme.value === 'gold' ? '#fbbf24' : '#c084fc'
            option.style.color = currentTheme.value === 'gold' ? '#78350f' : '#581c87'
          }
        })

        option.addEventListener('mouseleave', () => {
          if (!option.selected) {
            option.style.backgroundColor = 'white'
            option.style.color = '#374151'
          } else {
            option.style.backgroundColor = currentTheme.value === 'gold' ? '#f59e0b' : '#a855f7'
            option.style.color = 'white'
          }
        })

        // 设置选中状态的颜色
        if (option.selected) {
          option.style.backgroundColor = currentTheme.value === 'gold' ? '#f59e0b' : '#a855f7'
          option.style.color = 'white'
        }
      })

      // 监听变化事件
      select.addEventListener('change', () => {
        setTimeout(forceSelectStyles, 10) // 延迟执行以确保DOM更新
      })
    })
  }

  // 初始执行
  setTimeout(forceSelectStyles, 100)

  // 监听主题变化时重新应用样式
  const unwatchTheme = watch(currentTheme, () => {
    setTimeout(forceSelectStyles, 50)
  })


  // 暴露清理函数
  onUnmounted(() => {
    unwatchTheme()
  })
})

onUnmounted(() => {
  window.removeEventListener('storage', updateTheme)
  window.removeEventListener('themeChanged', handleThemeChange)
  window.removeEventListener('betPlaced', handleBetPlaced)

  // 清理倒计时定时器
  if (countdownTimer) {
    clearInterval(countdownTimer)
  }

  // 清理自动开奖定时器
  if (autoDrawTimer) {
    clearInterval(autoDrawTimer)
  }
})
</script>

<style scoped>
/* Additional styles for Tailwind */
.notification-dropdown {
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus styles for accessibility */
.action-button:focus {
  @apply ring-2 ring-primary-500 ring-offset-2;
}

.search-input:focus {
  @apply ring-2 ring-primary-500 ring-offset-2;
}

/* Theme-based select styles */
:global(.theme-gold) select option:checked {
  background-color: #f59e0b !important; /* amber-500 */
  color: white !important;
}

:global(.theme-gold) select option:hover,
:global(.theme-gold) select option:focus {
  background-color: #fbbf24 !important; /* amber-400 */
  color: #78350f !important;
}

:global(.theme-gold) select option:checked:hover {
  background-color: #f59e0b !important; /* amber-500 - 保持选中状态 */
  color: white !important;
}

:global(.theme-purple) select option:checked {
  background-color: #a855f7 !important; /* purple-500 */
  color: white !important;
}

:global(.theme-purple) select option:hover,
:global(.theme-purple) select option:focus {
  background-color: #c084fc !important; /* purple-400 */
  color: #581c87 !important;
}

:global(.theme-purple) select option:checked:hover {
  background-color: #a855f7 !important; /* purple-500 - 保持选中状态 */
  color: white !important;
}

/* Dynamic theme-based select styles */
.header select.theme-gold-dropdown option:checked {
  background-color: #f59e0b !important;
  color: white !important;
}

.header select.theme-gold-dropdown option:hover,
.header select.theme-gold-dropdown option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

.header select.theme-gold-dropdown option:checked:hover {
  background-color: #f59e0b !important;
  color: white !important;
}

.header select.theme-purple-dropdown option:checked {
  background-color: #a855f7 !important;
  color: white !important;
}

.header select.theme-purple-dropdown option:hover,
.header select.theme-purple-dropdown option:focus {
  background-color: #c084fc !important;
  color: #581c87 !important;
}

.header select.theme-purple-dropdown option:checked:hover {
  background-color: #a855f7 !important;
  color: white !important;
}

/* Enhanced select theme styling for better coverage */
.header[data-theme="gold"] select option:hover,
.header[data-theme="gold"] select option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

.header[data-theme="gold"] select option:checked:hover {
  background-color: #f59e0b !important;
  color: white !important;
}

.header[data-theme="purple"] select option:hover,
.header[data-theme="purple"] select option:focus {
  background-color: #c084fc !important;
  color: #581c87 !important;
}

.header[data-theme="purple"] select option:checked:hover {
  background-color: #a855f7 !important;
  color: white !important;
}

/* Universal theme-based hover states for select options */
body.theme-gold select option:hover,
body.theme-gold select option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

body.theme-gold select option:checked:hover {
  background-color: #f59e0b !important;
  color: white !important;
}

body.theme-purple select option:hover,
body.theme-purple select option:focus {
  background-color: #c084fc !important;
  color: #581c87 !important;
}

body.theme-purple select option:checked:hover {
  background-color: #a855f7 !important;
  color: white !important;
}

/* Most specific selectors for maximum compatibility */
header[data-theme="gold"] select.theme-gold-dropdown option:hover,
header[data-theme="gold"] select.theme-gold-dropdown option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

header[data-theme="gold"] select.theme-gold-dropdown option:checked:hover {
  background-color: #f59e0b !important;
  color: white !important;
}

header[data-theme="purple"] select.theme-purple-dropdown option:hover,
header[data-theme="purple"] select.theme-purple-dropdown option:focus {
  background-color: #c084fc !important;
  color: #581c87 !important;
}

header[data-theme="purple"] select.theme-purple-dropdown option:checked:hover {
  background-color: #a855f7 !important;
  color: white !important;
}

/* Force override browser default selection colors */
header select option {
  background-color: white !important;
  color: #374151 !important;
}

header select option:hover,
header select option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

header select option:checked {
  background-color: #f59e0b !important;
  color: white !important;
}

header select option:checked:hover,
header select option:checked:focus {
  background-color: #f59e0b !important;
  color: white !important;
}

/* Theme-specific force override */
header[data-theme="gold"] select option:hover,
header[data-theme="gold"] select option:focus {
  background-color: #fbbf24 !important;
  color: #78350f !important;
}

header[data-theme="gold"] select option:checked {
  background-color: #f59e0b !important;
  color: white !important;
}

header[data-theme="gold"] select option:checked:hover,
header[data-theme="gold"] select option:checked:focus {
  background-color: #f59e0b !important;
  color: white !important;
}

header[data-theme="purple"] select option:hover,
header[data-theme="purple"] select option:focus {
  background-color: #c084fc !important;
  color: #581c87 !important;
}

header[data-theme="purple"] select option:checked {
  background-color: #a855f7 !important;
  color: white !important;
}

header[data-theme="purple"] select option:checked:hover,
header[data-theme="purple"] select option:checked:focus {
  background-color: #a855f7 !important;
  color: white !important;
}

/* Webkit browsers specific override */
header select::-webkit-calendar-picker-indicator {
  background: transparent;
  bottom: 0;
  color: transparent;
  cursor: pointer;
  height: auto;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  width: auto;
}

/* Firefox specific override */
header select::-moz-focus-inner {
  border: 0;
}

/* CSS Variables approach for dynamic theme colors */
header select option {
  background-color: white !important;
  color: #374151 !important;
}

header select option:hover,
header select option:focus {
  background-color: var(--theme-secondary, #fbbf24) !important;
  color: var(--theme-text, #78350f) !important;
}

header select option:checked {
  background-color: var(--theme-primary, #f59e0b) !important;
  color: white !important;
}

header select option:checked:hover,
header select option:checked:focus {
  background-color: var(--theme-primary, #f59e0b) !important;
  color: white !important;
}

/* Override browser selection colors with CSS variables */
header select option::-moz-selection {
  background-color: var(--theme-primary, #f59e0b) !important;
  color: white !important;
}

header select option::selection {
  background-color: var(--theme-primary, #f59e0b) !important;
  color: white !important;
}

/* Additional override for stubborn browsers */
header select {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Hide default dropdown arrows completely */
header select::-ms-expand {
  display: none;
}

header select::-webkit-select-dropdown-indicator {
  display: none;
}

header select::-webkit-calendar-picker-indicator {
  display: none;
}

header select option {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background: white !important;
  color: #374151 !important;
}

header select option:checked {
  background: var(--theme-primary, #f59e0b) !important;
  color: white !important;
}

header select option:hover,
header select option:focus {
  background: var(--theme-secondary, #fbbf24) !important;
  color: var(--theme-text, #78350f) !important;
}
</style>
