<template>
  <div class="bet-status">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-900">注单状态</h2>
        <button
          @click="refreshData"
          :disabled="isLoading"
          class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
        >
          {{ isLoading ? '加载中...' : '刷新' }}
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">中奖状态</label>
          <select
            v-model="selectedStatus"
            class="status-filter-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            @change="handleFilterChange"
          >
            <option value="">全部</option>
            <option value="1">已中奖</option>
            <option value="2">未中奖</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">开始日期</label>
          <input
            v-model="startDate"
            type="date"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            @change="handleFilterChange"
          />
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">结束日期</label>
          <input
            v-model="endDate"
            type="date"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            @change="handleFilterChange"
          />
        </div>
        <div class="flex items-end gap-2">
          <button
            @click="setTodayRange"
            class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm"
          >
            今天
          </button>
          <button
            @click="resetFilters"
            class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm"
          >
            重置
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
          <div class="text-xs text-gray-500">总投注额</div>
          <div class="mt-1 text-lg font-semibold text-gray-900">¥{{ formatMoney(summary.totalAmount) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
          <div class="text-xs text-gray-500">派奖总额</div>
          <div class="mt-1 text-lg font-semibold text-green-600">¥{{ formatMoney(summary.totalPrizeAmount) }}</div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
          <div class="text-xs text-gray-500">盈利</div>
          <div class="mt-1 text-lg font-semibold" :class="summary.totalProfitAmount >= 0 ? 'text-green-600' : 'text-red-600'">
            ¥{{ formatMoney(summary.totalProfitAmount) }}
          </div>
        </div>
      </div>

      <!-- 分页 -->
      <div class="flex items-center gap-3 mb-4">
        <span class="text-sm text-gray-600">第 {{ currentPage }} 页</span>
        <span class="text-sm text-gray-600">/</span>
        <span class="text-sm text-gray-600">共 {{ totalPages }} 页</span>
        <button
          @click="previousPage"
          :disabled="currentPage === 1"
          class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed"
        >
          上一页
        </button>
        <button
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed"
        >
          下一页
        </button>
      </div>

      <!-- 注单列表 -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
              <th class="px-6 py-3 whitespace-nowrap">注单号</th>
              <th class="px-6 py-3 whitespace-nowrap">期号</th>
              <th class="px-6 py-3 whitespace-nowrap">游戏</th>
              <th class="px-6 py-3 whitespace-nowrap">玩法</th>
              <th class="px-6 py-3 whitespace-nowrap">类型</th>
              <th class="px-6 py-3 whitespace-nowrap">投注内容</th>
              <th class="px-6 py-3 whitespace-nowrap">下注金额</th>
              <th class="px-6 py-3 whitespace-nowrap">赔率</th>
              <th class="px-6 py-3 whitespace-nowrap">预期奖金</th>
              <th class="px-6 py-3 whitespace-nowrap">状态</th>
              <th class="px-6 py-3 whitespace-nowrap">中奖金额</th>
              <th class="px-6 py-3 whitespace-nowrap">盈利</th>
              <th class="px-6 py-3 whitespace-nowrap">时间</th>
            </tr>
          </thead>
          <tbody>
            <!-- 加载状态 -->
            <tr v-if="isLoading">
              <td colspan="13" class="px-6 py-8 text-center text-gray-500">
                <div class="flex items-center justify-center gap-2">
                  <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-gray-900"></div>
                  加载中...
                </div>
              </td>
            </tr>
            <!-- 错误状态 -->
            <tr v-else-if="apiError">
              <td colspan="13" class="px-6 py-8 text-center text-red-500">
                {{ apiError }}
                <div class="mt-2">
                  <button @click="refreshData" class="text-blue-600 hover:text-blue-800 underline">
                    重试
                  </button>
                </div>
              </td>
            </tr>
            <!-- 无数据状态 -->
            <tr v-else-if="filteredBets.length === 0">
              <td colspan="13" class="px-6 py-8 text-center text-gray-500">
                暂无注单记录
              </td>
            </tr>
            <tr
              v-for="bet in filteredBets"
              :key="bet.id"
              class="bg-white border-b hover:bg-gray-50"
            >
              <!-- 注单号 -->
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                {{ bet.sn || bet.id }}
              </td>
              <!-- 期号 -->
              <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                {{ bet.qishu }}
              </td>
              <!-- 游戏名称 -->
              <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                {{ bet.gameName }}
              </td>
              <!-- 玩法显示 -->
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                {{ bet.methodName || bet.playName || '未知玩法' }}
              </td>
              <!-- 投注类型 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'px-2 py-1 rounded-full text-xs font-medium': true,
                    'bg-blue-100 text-blue-800': bet.betType === 'win',
                    'bg-purple-100 text-purple-800': bet.betType === 'not_win'
                  }"
                >
                  {{ bet.betTypeText || '中' }}
                </span>
              </td>
              <!-- 投注内容 -->
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                {{ bet.content }}
              </td>
              <!-- 下注金额 -->
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                ¥{{ bet.amount }}
              </td>
              <!-- 赔率 -->
              <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                {{ bet.peilv }}
              </td>
              <!-- 预期奖金 -->
              <td class="px-6 py-4 font-medium text-blue-600 whitespace-nowrap">
                ¥{{ bet.expectedPrize }}
              </td>
              <!-- 状态 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'px-2 py-1 rounded-full text-xs font-medium': true,
                    'bg-yellow-100 text-yellow-800': bet.status === 'pending',
                    'bg-green-100 text-green-800': bet.status === 'won',
                    'bg-red-100 text-red-800': bet.status === 'lost'
                  }"
                >
                  {{ bet.statusText }}
                </span>
              </td>
              <!-- 中奖金额 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="bet.prize > 0" class="font-medium text-green-600">
                  ¥{{ bet.prize }}
                </span>
                <span v-else class="text-gray-400">
                  -
                </span>
              </td>
              <!-- 盈利 -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="font-medium" :class="bet.profitAmount >= 0 ? 'text-green-600' : 'text-red-600'">
                  ¥{{ formatMoney(bet.profitAmount) }}
                </span>
              </td>
              <!-- 时间 -->
              <td class="px-6 py-4 text-gray-600 text-xs whitespace-nowrap">
                {{ formatDateTime(bet.time) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { lotteryService } from '@/services/api'

const currentPage = ref(1)
const pageSize = 20  // 设置为API默认的每页数量
const isLoading = ref(false)
const totalRecords = ref(0)
const selectedStatus = ref('')
const startDate = ref('')
const endDate = ref('')

// 投注记录数据
const bets = ref([])
const apiError = ref('')
const summary = ref({
  totalAmount: 0,
  totalPrizeAmount: 0,
  totalProfitAmount: 0
})

// 获取全局选中的彩种ID
const getSelectedLotteryType = () => {
  return localStorage.getItem('selectedLotteryType') || '200'
}

// 获取当前选中的盘口代码
const getSelectedPlateCode = () => {
  return localStorage.getItem('selectedPlateCode') || 'A'
}

// 状态映射函数 - 根据API返回的status值映射到前端状态（新表）
const mapStatusFromApi = (statusValue) => {
  switch(Number(statusValue)) {
    case 0:
      return 'pending'  // 未开奖
    case 1:
      return 'won'      // 已中奖
    case 2:
      return 'lost'     // 未中奖
    case 3:
      return 'cancelled' // 已撤单
    default:
      return 'pending'
  }
}

// 获取投注记录
const fetchBetList = async () => {
  try {
    isLoading.value = true
    apiError.value = ''

    const params = {
      page: currentPage.value,
      limit: pageSize
    }

    // 传入彩种ID和盘口代码
    const gid = getSelectedLotteryType()
    if (gid) {
      params.gid = parseInt(gid)
    }

    const plateCode = getSelectedPlateCode()
    if (plateCode) {
      params.plate_code = plateCode
    }

    if (selectedStatus.value) {
      params.z = selectedStatus.value
    }

    if (startDate.value) {
      params.start_date = startDate.value
    }

    if (endDate.value) {
      params.end_date = endDate.value
    }

    console.log('获取投注记录，参数:', params)
    const result = await lotteryService.getBetList(params)

    if (result.code === 1 && result.data) {
      // 映射API数据到前端数据结构（使用新表字段）
      bets.value = result.data.list.map(item => ({
        id: item.id,                               // 注单ID（新表）
        sn: item.sn,                               // 注单号（新表）
        qishu: item.issue,                         // 期号（新表字段：issue）
        gid: item.game_id,                         // 游戏ID（新表字段：game_id）
        plateCode: item.plate_code,                // 盘口代码（新表）
        methodId: item.method_id,                  // 玩法ID（新表字段：method_id）
        methodName: item.method_name,              // 玩法名称（新表字段：method_name）
        gameName: item.game_name || '未知游戏',      // 游戏名称
        playName: item.play_name || '',            // 玩法名称
        playDisplay: item.play_display || item.method_name || '未知玩法',  // 组合显示名称
        betType: item.bet_type,                    // 投注类型（新增：win/not_win）
        betTypeText: item.bet_type_text || '中',    // 投注类型文本（新增：中/不中）
        content: item.content || item.bet_content, // 投注内容（新表字段：bet_content）
        amount: parseFloat(item.bet_amount),       // 下注金额（新表字段：bet_amount）
        peilv: item.odds,                          // 赔率（新表字段：odds）
        expectedPrize: parseFloat(item.expected_prize) || 0,  // 预期中奖金额
        status: mapStatusFromApi(item.status),     // 状态映射（新表字段：status，0=未开奖,1=已中奖,2=未中奖）
        statusText: item.status_text || getStatusText(mapStatusFromApi(item.status)),  // 状态文本
        prize: parseFloat(item.prize) || 0,        // 实际中奖金额
        profitAmount: parseFloat(item.profit_amount) || 0,
        time: item.time,                           // 时间
        rawStatus: item.status                     // 原始状态值（新表）
      }))

      totalRecords.value = result.data.total || 0
      summary.value = {
        totalAmount: parseFloat(result.data.summary?.total_amount) || 0,
        totalPrizeAmount: parseFloat(result.data.summary?.total_prize_amount) || 0,
        totalProfitAmount: parseFloat(result.data.summary?.total_profit_amount) || 0
      }
      console.log('获取投注记录成功:', bets.value.length, '条记录')
    } else {
      apiError.value = result.msg || '获取投注记录失败'
      bets.value = []
      totalRecords.value = 0
      resetSummary()
    }
  } catch (error) {
    console.error('获取投注记录API错误:', error)
    apiError.value = '网络错误，请稍后重试'
    bets.value = []
    totalRecords.value = 0
    resetSummary()
  } finally {
    isLoading.value = false
  }
}

// 由于现在API支持分页，filteredBets直接返回当前页数据
const filteredBets = computed(() => {
  return bets.value
})

// 计算总页数
const totalPages = computed(() => {
  return Math.max(1, Math.ceil(totalRecords.value / pageSize))
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * pageSize + 1
})

const endIndex = computed(() => {
  return Math.min(currentPage.value * pageSize, totalRecords.value)
})

const getStatusText = (status) => {
  const statusMap = {
    'pending': '待开奖',
    'won': '中奖',
    'lost': '未中奖'
  }
  return statusMap[status] || status
}

const formatDateTime = (time) => {
  return new Date(time).toLocaleString('zh-CN')
}

const formatMoney = (amount) => {
  return Number(amount || 0).toFixed(2)
}

const getTodayString = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const resetSummary = () => {
  summary.value = {
    totalAmount: 0,
    totalPrizeAmount: 0,
    totalProfitAmount: 0
  }
}

// 刷新数据
const refreshData = () => {
  fetchBetList()
}

const handleFilterChange = () => {
  currentPage.value = 1
  fetchBetList()
}

const setTodayRange = () => {
  const today = getTodayString()
  startDate.value = today
  endDate.value = today
  handleFilterChange()
}

const resetFilters = () => {
  selectedStatus.value = ''
  startDate.value = ''
  endDate.value = ''
  handleFilterChange()
}

const cancelBet = (betId) => {
  // TODO: 实现取消注单的API调用
  alert('取消注单功能暂未实现')
}

const viewDetails = (bet) => {
  const details = `
========== 注单详情 ==========

注单号: ${bet.id}
期号: ${bet.qishu}
游戏: ${bet.gameName}
玩法: ${bet.playDisplay}
投注内容: ${bet.content}
时间: ${formatDateTime(bet.time)}

---------- 金额信息 ----------
下注金额: ¥${bet.amount}
赔率: ${bet.peilv}
预期奖金: ¥${bet.expectedPrize}

	---------- 开奖结果 ----------
	状态: ${bet.statusText}
	${bet.prize > 0 ? `实际中奖金额: ¥${bet.prize}` : '尚未开奖或未中奖'}
	盈利: ¥${formatMoney(bet.profitAmount)}

	=============================
  `
  alert(details)
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchBetList()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchBetList()
  }
}

onMounted(() => {
  // 页面加载时获取投注记录
  fetchBetList()

  // 监听盘口切换事件
  const handlePlateChanged = (event) => {
    console.log('🔄 投注记录页面收到盘口切换事件:', event.detail.plateCode)
    // 重置到第一页并刷新数据
    currentPage.value = 1
    fetchBetList()
  }

  window.addEventListener('plateChanged', handlePlateChanged)
})
</script>

<style scoped>
.status-filter-select {
  color: #111827 !important;
  background-color: #ffffff !important;
  -webkit-text-fill-color: #111827;
}

.status-filter-select option {
  color: #111827 !important;
  background-color: #ffffff !important;
  -webkit-text-fill-color: #111827;
}

.status-filter-select option:checked {
  color: #111827 !important;
  background-color: #e5e7eb !important;
  -webkit-text-fill-color: #111827;
}
</style>
