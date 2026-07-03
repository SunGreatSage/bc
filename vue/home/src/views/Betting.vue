<template>
  <div class="betting max-w-7xl mx-auto px-4">
    <!-- 号码选择区 -->
    <div class="max-w-4xl mx-auto mb-8">
      <div class="bg-gray-50 rounded-xl p-2 mb-6">
        <!-- 连肖玩法快速选择按钮 -->
        <div v-if="isLegacyMultiZodiacPlay" class="mb-4 p-3">
          <div class="flex items-center gap-4">
            <!-- 玩法标签 -->
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium"
                    :class="currentTheme === 'gold' ? 'text-amber-700' : 'text-purple-700'">
                {{ playName }}：
              </span>
            </div>
            <!-- 家禽和野兽按钮 -->
            <div class="flex items-center gap-2">
              <!-- 家禽按钮 -->
              <button
                @click="selectDomesticAnimals"
                :class="{
                  'border-2 border-amber-500 text-amber-700': isDomesticAnimalsSelected && currentTheme === 'gold',
                  'border-2 border-purple-500 text-purple-700': isDomesticAnimalsSelected && currentTheme === 'purple',
                  'border border-amber-200 text-amber-600 hover:border-amber-300': !isDomesticAnimalsSelected && currentTheme === 'gold',
                  'border border-purple-200 text-purple-600 hover:border-purple-300': !isDomesticAnimalsSelected && currentTheme === 'purple'
                }"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 bg-white"
              >
                家禽
              </button>
              <!-- 野兽按钮 -->
              <button
                @click="selectWildAnimals"
                :class="{
                  'border-2 border-amber-500 text-amber-700': isWildAnimalsSelected && currentTheme === 'gold',
                  'border-2 border-purple-500 text-purple-700': isWildAnimalsSelected && currentTheme === 'purple',
                  'border border-amber-200 text-amber-600 hover:border-amber-300': !isWildAnimalsSelected && currentTheme === 'gold',
                  'border border-purple-200 text-purple-600 hover:border-purple-300': !isWildAnimalsSelected && currentTheme === 'purple'
                }"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 bg-white"
              >
                野兽
              </button>
            </div>
          </div>
        </div>

        <!-- 号码类玩法的赔率显示行 -->
        <div v-if="playType === 'number' || playType === 'combo_number' || playType === 'option'" class="mb-4 p-3 bg-white rounded-lg border">
          <!-- 赔率显示 -->
          <div class="flex items-center gap-4 flex-wrap">
            <span class="text-sm text-gray-600">赔率:</span>
            <span class="text-lg font-bold"
                  :class="currentTheme === 'gold' ? 'text-amber-700' : 'text-purple-700'">
              {{ getAverageOdds('win') }}
            </span>
            <span v-if="playType === 'combo_number' || playType === 'zodiac_combo'" class="text-sm text-gray-600">
              {{ comboRuleText }}
            </span>
          </div>
        </div>

        <!-- 号码类玩法 (特碼、平码) -->
        <div v-if="playType === 'number' || playType === 'combo_number'" class="grid grid-cols-5 justify-items-center gap-2 mb-6">
          <button
            v-for="option in orderedBetNumbers"
            :key="option.value"
            @click="toggleNumber(option.value)"
            :class="{
              'bg-amber-100 border-2 border-amber-300 ring-2 ring-amber-400': selectedNumbers.includes(option.value) && currentTheme === 'gold',
              'bg-purple-100 border-2 border-purple-300 ring-2 ring-purple-400': selectedNumbers.includes(option.value) && currentTheme === 'purple',
              'bg-white border border-gray-200 hover:border-gray-300 hover:shadow': !selectedNumbers.includes(option.value)
            }"
            class="w-16 h-16 rounded-lg font-medium text-xs transition-all duration-200 flex flex-col items-center justify-center py-3"
          >
            <!-- 上方圆球（包含数字） -->
            <div
              :class="{
                [`bg-gradient-to-br ${getBallColor(option.value).selected} text-white border ${getBallColor(option.value).border} shadow-xl`]: selectedNumbers.includes(option.value),
                [`bg-gradient-to-br ${getBallColor(option.value).gradient} text-white border ${getBallColor(option.value).border} shadow-lg`]: !selectedNumbers.includes(option.value)
              }"
              class="w-8 h-8 rounded-full mb-1 transition-all duration-200 flex items-center justify-center font-bold text-sm flex-shrink-0"
              style="aspect-ratio: 1 / 1;"
            >
              {{ option.label }}
            </div>
            <!-- 下方不显示赔率(因为上方已有统一的中/不中赔率显示) -->
          </button>
        </div>

        <!-- 选项类玩法 -->
        <div v-else-if="playType === 'option'" class="grid grid-cols-2 sm:grid-cols-3 justify-items-center gap-3 mb-6">
          <button
            v-for="option in betNumbersData"
            :key="option.value"
            @click="toggleNumber(option.value)"
            :class="{
              'bg-amber-100 border-2 border-amber-300 ring-2 ring-amber-400 text-amber-900': selectedNumbers.includes(option.value) && currentTheme === 'gold',
              'bg-purple-100 border-2 border-purple-300 ring-2 ring-purple-400 text-purple-900': selectedNumbers.includes(option.value) && currentTheme === 'purple',
              'bg-white border border-gray-200 hover:border-gray-300 hover:shadow text-gray-900': !selectedNumbers.includes(option.value)
            }"
            class="w-32 h-16 rounded-lg font-medium text-sm transition-all duration-200 flex flex-col items-center justify-center py-3"
          >
            <div class="text-base font-bold text-center mb-1">{{ option.label }}</div>
            <div class="text-xs text-red-600 font-semibold">{{ option.odds_win || option.odds }}</div>
          </button>
        </div>

        <!-- 生肖类玩法 (包含"肖"的玩法显示生肖格式) -->
        <div v-else-if="playType === 'zodiac' || playType === 'zodiac_combo'" class="grid grid-cols-2 justify-items-center gap-4 mb-6">
          <button
            v-for="option in betNumbersData"
            :key="option.value"
            @click="toggleNumber(option.value)"
            :class="{
              'bg-amber-100 border-2 border-amber-300 ring-2 ring-amber-400 text-amber-900': selectedNumbers.includes(option.value) && currentTheme === 'gold',
              'bg-purple-100 border-2 border-purple-300 ring-2 ring-purple-400 text-purple-900': selectedNumbers.includes(option.value) && currentTheme === 'purple',
              'bg-white border border-gray-200 hover:border-gray-300 hover:shadow text-gray-900': !selectedNumbers.includes(option.value)
            }"
            class="w-40 h-16 rounded-lg font-medium text-sm transition-all duration-200 flex flex-col items-center justify-center py-3"
          >
            <!-- 生肖名称 - 上方居中显示 -->
            <div class="text-sm font-bold text-center mb-1">{{ option.label }}</div>

              <div v-if="isPingXiaoPlay" class="text-xs text-red-600 font-semibold mb-0.5">
                {{ option.odds_win || option.odds }}
              </div>
              <!-- 所有生肖玩法都显示号码列表 -->
              <div v-if="option.numbers && option.numbers.length > 0" class="text-xs text-center leading-tight">
                {{ option.numbers.join(',') }}
              </div>
          </button>
        </div>

        <!-- 默认数字格式 (其他所有玩法) -->
        <div v-else class="grid grid-cols-5 justify-items-center gap-2 mb-6">
          <button
            v-for="option in orderedBetNumbers"
            :key="option.value"
            @click="toggleNumber(option.value)"
            :class="{
              'bg-amber-100 border-2 border-amber-300 ring-2 ring-amber-400': selectedNumbers.includes(option.value) && currentTheme === 'gold',
              'bg-purple-100 border-2 border-purple-300 ring-2 ring-purple-400': selectedNumbers.includes(option.value) && currentTheme === 'purple',
              'bg-white border border-gray-200 hover:border-gray-300 hover:shadow': !selectedNumbers.includes(option.value)
            }"
            class="w-16 h-16 rounded-lg font-medium text-xs transition-all duration-200 flex flex-col items-center justify-center py-3"
          >
            <!-- 上方圆球（包含数字） -->
            <div
              :class="{
                [`bg-gradient-to-br ${getBallColor(option.value).selected} text-white border ${getBallColor(option.value).border} shadow-xl`]: selectedNumbers.includes(option.value),
                [`bg-gradient-to-br ${getBallColor(option.value).gradient} text-white border ${getBallColor(option.value).border} shadow-lg`]: !selectedNumbers.includes(option.value)
              }"
              class="w-8 h-8 rounded-full mb-1 transition-all duration-200 flex items-center justify-center font-bold text-sm flex-shrink-0"
              style="aspect-ratio: 1 / 1;"
            >
              {{ option.label }}
            </div>
          </button>
        </div>

        <!-- 加载状态 -->
        <div v-if="isLoading" class="flex items-center justify-center py-8">
          <div class="text-gray-500">加载中...</div>
        </div>

        <!-- 空数据状态 -->
        <div v-if="!isLoading && betNumbersData.length === 0" class="flex items-center justify-center py-8">
          <div class="text-gray-500">暂无可用数据</div>
        </div>
      </div>

      <!-- 赔率显示行 - 所有生肖玩法都显示 -->
      <div v-if="playType === 'zodiac' || playType === 'zodiac_combo'" class="mb-4 p-3 bg-white rounded-lg border">
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">赔率:</span>
          <span class="text-lg font-bold"
                :class="currentTheme === 'gold' ? 'text-amber-700' : 'text-purple-700'">
            {{ getAverageOdds('win') }}
          </span>
          <span v-if="isSixSpecialZodiacPlay" class="text-sm text-gray-600">
            选6个生肖，只看特码，命中任一所选生肖即中奖
          </span>
        </div>
      </div>

      <!-- 49号特殊规则提示 - 所有生肖玩法都显示 -->
      <div v-if="playType === 'zodiac' && !isPingXiaoPlay" class="mb-4 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
        <div class="flex items-start gap-2">
          <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <div class="text-sm text-gray-800">
            <strong class="text-gray-900">特殊规则：</strong>{{ isSixSpecialZodiacPlay ? '特码开49时视为' : '开奖号码中包含49号时视为' }}<strong class="text-red-700">和局</strong>，系统将退还投注金额。
          </div>
        </div>
      </div>

      <!-- 操作按钮区 - 根据seconds_to_close判断是否显示 -->
      <div v-if="canBet" class="border-t border-gray-200 pt-4">
        <div class="flex items-center justify-center gap-3">
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">¥</span>
            <input
              v-model="betAmount"
              type="text"
              class="w-24 pl-6 pr-2 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm text-gray-900"
              placeholder="下注金额"
            />
          </div>
          <button
            @click="confirmBet"
            :disabled="selectedNumbers.length === 0 || betAmount <= 0"
            class="px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-all duration-200 shadow-lg text-sm"
          >
            下单
          </button>
          <button
            @click="reverseSelection"
            class="px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all duration-200 text-sm shadow-sm"
          >
            反选
          </button>
          <button
            @click="clearSelection"
            class="px-3 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 text-sm shadow-sm"
          >
            重置
          </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 下注确认弹窗 -->
    <div v-if="showBetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div :class="currentTheme === 'gold' ? 'bg-amber-50' : 'bg-purple-50'" class="rounded-md shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-y-auto p-1">
        <!-- 弹窗标题 -->
        <div :class="currentTheme === 'gold' ? 'bg-amber-100 border-amber-200' : 'bg-purple-100 border-purple-200'" class="p-1 border-b flex items-center">
          <h3 class="text-xl font-bold text-gray-900">下注明细(请确认订单)</h3>
        </div>

        <!-- 弹窗内容 -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <!-- 表头 -->
            <thead>
              <tr :class="currentTheme === 'gold' ? 'bg-amber-700' : 'bg-purple-700'">
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-white">明细</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-white">号码</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-white">赔率</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold text-white">下注金额</th>
              </tr>
            </thead>
            <!-- 表格内容 -->
            <tbody>
              <!-- 连肖/数字组合类游戏：单行显示整组投注 -->
              <tr v-if="isGroupedBetPlay" class="hover:bg-gray-50">
                <!-- 明细列 -->
                <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">
                  {{ getDetailText() }}
                </td>
                <!-- 号码列：显示所有选中的生肖/号码，逗号分隔 -->
                <td class="border border-gray-300 px-4 py-2 text-sm font-medium text-gray-900">
                  {{ playType === 'combo_number' ? getSelectedNumberDisplay() : getSelectedZodiacDisplay() }}
                </td>
                <!-- 赔率列：显示平均赔率 -->
                <td class="border border-gray-300 px-4 py-2 text-sm text-red-600 font-medium">
                  {{ getAverageOdds('win') }}
                </td>
                <!-- 下注金额列 -->
                <td class="border border-gray-300 px-4 py-2">
                  <div class="flex items-center gap-2">
                    <div class="relative">
                      <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">¥</span>
                      <input
                        v-model="modalBetAmounts[0]"
                        type="text"
                        class="w-20 pl-5 pr-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-900"
                        placeholder="0"
                      />
                    </div>
                  </div>
                </td>
              </tr>
              <!-- 数字类、特肖、平肖：每个号码/生肖一行 -->
              <template v-else>
                <tr v-for="(num, index) in selectedNumbers" :key="num" class="hover:bg-gray-50">
                  <!-- 明细列 -->
                  <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">
                    {{ getDetailText() }}
                  </td>
                  <!-- 号码列 -->
                  <td class="border border-gray-300 px-4 py-2 text-sm font-medium text-gray-900">
                    {{ getNumberDisplay(num) }}
                  </td>
                  <!-- 赔率列 -->
                  <td class="border border-gray-300 px-4 py-2 text-sm text-red-600 font-medium">
                    {{ getOddsForModal(num) }}
                  </td>
                  <!-- 下注金额列 -->
                  <td class="border border-gray-300 px-4 py-2">
                    <div class="flex items-center gap-2">
                      <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">¥</span>
                        <input
                          v-model="modalBetAmounts[index]"
                          type="text"
                          class="w-20 pl-5 pr-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-gray-900"
                          placeholder="0"
                        />
                      </div>
                      <button
                        @click="removeBetItem(index)"
                        :class="currentTheme === 'gold' ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-red-100 hover:bg-red-200 text-red-600'"
                        class="p-1 rounded transition-all duration-200"
                        title="移除"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
            <!-- 统计行：显示注数和总金额 -->
            <tfoot>
              <tr :class="currentTheme === 'gold' ? 'bg-amber-100' : 'bg-purple-100'" class="font-semibold">
                <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900" colspan="2">
                  <span v-if="playType === 'zodiac' && isSingleZodiacPlay">注数: {{ selectedNumbers.length }}</span>
                  <span v-else-if="playType === 'zodiac' || playType === 'zodiac_combo'">组合: 1 注（已选 {{ selectedNumbers.length }} 个生肖）</span>
                  <span v-else-if="playType === 'combo_number'">组合: 1 注（已选 {{ selectedNumbers.length }} 个号码）</span>
                  <span v-else>注数: {{ selectedNumbers.length }}</span>
                </td>
                <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900" colspan="2">总金额: ¥{{ modalTotalAmount }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- 弹窗按钮 -->
        <div :class="currentTheme === 'gold' ? 'bg-amber-100/50 border-amber-200' : 'bg-purple-100/50 border-purple-200'" class="p-1 border-t flex justify-end gap-3">
          <button
            @click="closeBetModal"
            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 text-sm"
          >
            取消
          </button>
          <button
            @click="submitBet"
            :disabled="isLoading"
            class="px-6 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-200 text-sm"
          >
            {{ isLoading ? '下单中...' : '确认' }}
          </button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { lotteryService } from '@/services/api'

const selectedNumbers = ref([])
const betAmount = ref(10)
const betMultiplier = ref(1)
const currentTheme = ref(localStorage.getItem('appTheme') || 'gold')
const showBetModal = ref(false)
const modalBetAmounts = ref([])
const betHistory = ref([])

// 投注相关状态
const currentQishu = ref('')
const isLoading = ref(false)
const currentPlayType = ref('特码') // 默认选择特码
const currentPid = ref(21401) // 默认特码PID
const secondsToClose = ref(0) // 距离封盘的秒数

// API数据相关状态
const betNumbersData = ref([])
const playName = ref('特碼') // 默认玩法名称
const playType = ref('number') // 玩法类型：number、zodiac 或 combo_number
const currentYear = ref(new Date().getFullYear()) // 当前年份
const comboSelectCount = ref(0)
const comboHitCount = ref(0)
const comboMode = ref('combo')
const lianXiaoDefaultOdds = ref('')
const lianXiaoHorseOdds = ref('')

// 六肖快速选择状态
const isDomesticAnimalsSelected = ref(false)
const isWildAnimalsSelected = ref(false)

// 家禽和野兽分类 - 使用繁体字
const domesticAnimals = ['牛', '馬', '羊', '雞', '狗', '豬']
const wildAnimals = ['鼠', '虎', '兔', '龍', '蛇', '猴']

// 投注类型固定为'win'(用户只能投注"中")
// const betType = ref('中') // 已移除,所有投注默认为'win'

// 判断是否可以投注（seconds_to_close > 0 时可以投注）
const canBet = computed(() => secondsToClose.value > 0)
const isPingXiaoPlay = computed(() => playType.value === 'zodiac' && playName.value.includes('平肖'))
const isSingleZodiacPlay = computed(() => playType.value === 'zodiac' && (playName.value === '特肖' || isPingXiaoPlay.value))
const isSixSpecialZodiacPlay = computed(() => {
  const name = playName.value || ''
  return playType.value === 'zodiac' && (name.includes('6肖中特') || name.includes('六肖中特') || name.includes('六肖') || name.includes('6肖'))
})
const isLegacyMultiZodiacPlay = computed(() => playType.value === 'zodiac' && playName.value && (isSixSpecialZodiacPlay.value || playName.value.includes('五肖') || playName.value.includes('四肖') || playName.value.includes('三肖')))
const isGroupedBetPlay = computed(() => playType.value === 'combo_number' || playType.value === 'zodiac_combo' || (playType.value === 'zodiac' && !isSingleZodiacPlay.value))
const comboRuleText = computed(() => {
  if (playType.value === 'zodiac_combo') {
    return `选${comboSelectCount.value}个生肖，7个开奖号码中每个生肖都出现即中奖`
  }
  if (comboMode.value === 'miss' || comboHitCount.value === 0) {
    return `选${comboSelectCount.value}个号码，7个开奖号码全部不中即中奖`
  }
  return `选${comboSelectCount.value}个号码，中${comboHitCount.value}个即中奖（只算正码）`
})

// 获取全局选中的彩种ID
const getSelectedLotteryType = () => {
  return localStorage.getItem('selectedLotteryType') || '200'
}

const getSelectedBetType = () => {
  return localStorage.getItem('selectedBetType') || 'special-code'
}

// 获取当前选中的盘口代码
const getSelectedPlateCode = () => {
  return localStorage.getItem('selectedPlateCode') || 'A'
}

// API 相关方法
const fetchCurrentQishu = async () => {
  try {
    isLoading.value = true
    // 使用全局选中的彩种ID和盘口获取期号
    const lotteryType = getSelectedLotteryType()
    const plateCode = getSelectedPlateCode()
    console.log(`📊 获取期号: 彩种=${lotteryType}, 盘口=${plateCode}`)
    const result = await lotteryService.getCurrentPeriod(lotteryType, plateCode)
    if (result.code === 1) {
      currentQishu.value = result.data.qishu
      console.log(`✅ 当前期号: ${currentQishu.value}`)
    } else {
      console.error('获取期号失败:', result.msg)
    }
  } catch (error) {
    console.error('获取期号API错误:', error)
  } finally {
    isLoading.value = false
  }
}

// 获取投注号码数据
const fetchBetNumbers = async () => {
  try {
    isLoading.value = true
    const lotteryId = getSelectedLotteryType()
    const plateCode = getSelectedPlateCode()  // 获取当前选中的盘口
    const result = await lotteryService.getBetNumbers(playName.value, lotteryId, currentYear.value, plateCode)
    if (result.code === 1 && result.data) {
      betNumbersData.value = result.data.options || []
      console.log('获取到的生肖数据:', result.data.options)

      // 优先使用后端返回的玩法类型，兼容旧接口再按名称判断生肖玩法
      const responsePlayType = result.data.play_type || ''
      const isZodiacGame = result.data.play_name && result.data.play_name.includes('肖')
      playType.value = responsePlayType || (isZodiacGame ? 'zodiac' : 'number')
      comboSelectCount.value = parseInt(result.data.select_count || 0)
      comboHitCount.value = parseInt(result.data.hit_count || 0)
      comboMode.value = result.data.combo_mode || 'combo'
      lianXiaoDefaultOdds.value = result.data.odds || ''
      lianXiaoHorseOdds.value = result.data.with_horse_odds || ''

      // 如果是生肖玩法，清空选中的号码和快速选择状态
      if (playType.value === 'zodiac' || playType.value === 'zodiac_combo' || playType.value === 'combo_number' || playType.value === 'option') {
        selectedNumbers.value = []
        isDomesticAnimalsSelected.value = false
        isWildAnimalsSelected.value = false
      }
    } else {
      console.error('获取投注号码失败:', result.msg)
      betNumbersData.value = []
    }
  } catch (error) {
    console.error('获取投注号码API错误:', error)
    betNumbersData.value = []
  } finally {
    isLoading.value = false
  }
}

const totalAmount = computed(() => {
  return selectedNumbers.value.length * betAmount.value * betMultiplier.value
})

const modalTotalAmount = computed(() => {
  if (playType.value === 'zodiac_combo') {
    const amount = parseFloat(modalBetAmounts.value[0]) || 0
    return amount
  } else if (playType.value === 'zodiac') {
    if (isSingleZodiacPlay.value) {
      return modalBetAmounts.value.reduce((total, amount) => {
        return total + (parseFloat(amount) || 0)
      }, 0)
    }
    // 多肖/6肖中特：整组生肖为一注
    const amount = parseFloat(modalBetAmounts.value[0]) || 0
    return amount
  } else if (playType.value === 'combo_number') {
    // 数字连码：整组号码为一注
    const amount = parseFloat(modalBetAmounts.value[0]) || 0
    return amount
  } else {
    // 数字类游戏：累加所有输入框金额
    return modalBetAmounts.value.reduce((total, amount) => {
      return total + (parseFloat(amount) || 0)
    }, 0)
  }
})


// 从API数据中获取赔率
const getOdds = (value) => {
  const option = betNumbersData.value.find(item => item.value === value)
  return option ? option.odds : '0.0000'
}

// 计算中/不中的平均赔率 - 使用新的API数据结构
const getAverageOdds = (type = 'win') => {
  if (betNumbersData.value.length === 0) return '0.0000'

  if (isPingXiaoPlay.value && selectedNumbers.value.length === 0) {
    return '马1.8000 / 其他2.0000'
  }

  if (playType.value === 'zodiac_combo') {
    const hasHorse = selectedNumbers.value.some(value => {
      const option = betNumbersData.value.find(item => item.value === value)
      return value === '马' || option?.label === '马'
    })
    if (selectedNumbers.value.length === 0 && lianXiaoHorseOdds.value) {
      return `带马${lianXiaoHorseOdds.value} / 不带马${lianXiaoDefaultOdds.value || '0.0000'}`
    }
    return hasHorse ? (lianXiaoHorseOdds.value || '0.0000') : (lianXiaoDefaultOdds.value || '0.0000')
  }

  const oddsSource = selectedNumbers.value.length > 0
    ? betNumbersData.value.filter(option => selectedNumbers.value.includes(option.value))
    : betNumbersData.value

  if (oddsSource.length === 0) return '0.0000'

  const totalOdds = oddsSource.reduce((sum, option) => {
    // 只使用 odds_win 字段,因为用户只能投注"中"
    const odds = parseFloat(option.odds_win) || parseFloat(option.odds) || 0
    return sum + odds
  }, 0)

  return (totalOdds / oddsSource.length).toFixed(4)
}

// 按纵向顺序重新排列号码数据
const orderedBetNumbers = computed(() => {
  if (betNumbersData.value.length === 0) return []

  // 将API数据转换为Map以便快速查找
  const dataMap = new Map()
  betNumbersData.value.forEach(item => {
    dataMap.set(item.value, item)
  })

  // 创建纵向排序的数据
  const ordered = []
  const columns = 5
  const rows = Math.ceil(betNumbersData.value.length / columns)

  for (let row = 0; row < rows; row++) {
    for (let col = 0; col < columns; col++) {
      const index = col * rows + row
      if (index < betNumbersData.value.length) {
        const originalItem = betNumbersData.value[index]
        if (originalItem && dataMap.has(originalItem.value)) {
          ordered.push(dataMap.get(originalItem.value))
        }
      }
    }
  }

  return ordered
})

// 获取明细文本
const getDetailText = () => {
  // ✅ 用户只能投注"中",不再显示类型后缀
  if (playType.value === 'zodiac' || playType.value === 'zodiac_combo' || playType.value === 'option') {
    return playName.value
  } else {
    return currentPlayType.value || playName.value
  }
}

// 获取号码显示文本
const getNumberDisplay = (num) => {
  if (playType.value === 'zodiac' || playType.value === 'zodiac_combo' || playType.value === 'option') {
    // 生肖游戏显示动物名称
    const option = betNumbersData.value.find(item => item.value === num)
    return option ? option.label : num
  } else {
    // 数字游戏显示格式化的号码
    return num.toString().padStart(2, '0')
  }
}

// 获取数字组合显示文本
const getSelectedNumberDisplay = () => {
  if (selectedNumbers.value.length === 0) return '无'
  return selectedNumbers.value
    .map(num => num.toString().padStart(2, '0'))
    .join('，')
}

// 获取赔率显示
const getOddsForModal = (num) => {
  const option = betNumbersData.value.find(item => item.value === num)
  if (!option) return '0.0000'

  // ✅ 用户只能投注"中",固定使用win赔率
  return option.odds_win || option.odds || '0.0000'
}

// 获取选中的生肖显示文本
const getSelectedZodiacDisplay = () => {
  if (selectedNumbers.value.length === 0) return '无'

  const zodiacNames = selectedNumbers.value.map(num => {
    const option = betNumbersData.value.find(item => item.value === num)
    return option ? option.label : num
  })

  return zodiacNames.join('，')
}

// 清空生肖选择
const clearZodiacSelection = () => {
  selectedNumbers.value = []
  modalBetAmounts.value = []
  showBetModal.value = false
}

// 获取圆球颜色 - 按照从右上角开始的斜向排列规律
const getBallColor = (number) => {
  // 将号码转换为数字
  const num = parseInt(number)

  // 根据5列布局计算号码在纵向排列中的位置
  const rows = Math.ceil(49 / 5) // 10行
  const cols = 5 // 5列

  // 计算在纵向排列中的位置
  // 纵向排列：第一列01,02,03... 第二列11,12,13... 等
  let row, col
  if (num <= 10) {
    row = num - 1      // 行: 0-9
    col = 0            // 列: 0 (第一列)
  } else if (num <= 20) {
    row = num - 11     // 行: 0-9
    col = 1            // 列: 1 (第二列)
  } else if (num <= 30) {
    row = num - 21     // 行: 0-9
    col = 2            // 列: 2 (第三列)
  } else if (num <= 40) {
    row = num - 31     // 行: 0-9
    col = 3            // 列: 3 (第四列)
  } else {
    row = num - 41     // 行: 0-8 (41-49)
    col = 4            // 列: 4 (第五列)
  }

  // 计算斜向索引：从右上角开始斜向排列
  // 右上角的41是起点，其坐标为(row=0, col=4)
  // 斜向索引 = (行 + (最大列数-1-列)) % 12，每12个一循环
  const maxCol = cols - 1  // 最大列索引 = 4
  const diagonalIndex = (row + (maxCol - col)) % 12

  // 颜色规律：第一二列蓝色，第三四列藏青绿色，第五六列黄褐色，循环重复
  const colorGroups = [
    { color: 'blue', group: 1 },    // 0: 第一列 - 蓝色
    { color: 'blue', group: 2 },    // 1: 第二列 - 蓝色
    { color: 'teal', group: 3 },    // 2: 第三列 - 藏青绿色
    { color: 'teal', group: 4 },    // 3: 第四列 - 藏青绿色
    { color: 'yellow', group: 5 },  // 4: 第五列 - 黄褐色
    { color: 'yellow', group: 6 },  // 5: 第六列 - 黄褐色
    { color: 'blue', group: 7 },    // 6: 第七列 - 蓝色
    { color: 'blue', group: 8 },    // 7: 第八列 - 蓝色
    { color: 'teal', group: 9 },    // 8: 第九列 - 藏青绿色
    { color: 'teal', group: 10 },   // 9: 第十列 - 藏青绿色
    { color: 'yellow', group: 11 }, // 10: 第十一列 - 黄褐色
    { color: 'yellow', group: 12 }  // 11: 第十二列 - 黄褐色
  ]

  const colorConfig = colorGroups[diagonalIndex]

  switch(colorConfig.color) {
    case 'blue':
      return {
        gradient: 'from-blue-300 via-blue-500 to-blue-700',
        shadow: 'shadow-blue-900/50',
        border: 'border-blue-400',
        selected: 'from-blue-400 via-blue-600 to-blue-800',
        text: 'text-white'
      }
    case 'teal':
      return {
        gradient: 'from-teal-500 via-teal-700 to-teal-900',
        shadow: 'shadow-teal-950/50',
        border: 'border-teal-600',
        selected: 'from-teal-600 via-teal-800 to-teal-950',
        text: 'text-white'
      }
    case 'yellow':
      return {
        gradient: 'from-yellow-500 via-yellow-700 to-yellow-900',
        shadow: 'shadow-yellow-950/50',
        border: 'border-yellow-600',
        selected: 'from-yellow-600 via-yellow-800 to-yellow-950',
        text: 'text-white'
      }
    default:
      // 默认红色系
      return {
        gradient: 'from-red-400 via-red-500 to-red-700',
        shadow: 'shadow-red-900/50',
        border: 'border-red-500',
        selected: 'from-red-500 via-red-600 to-red-800',
        text: 'text-white'
      }
  }
}

const toggleNumber = (num) => {
  const index = selectedNumbers.value.indexOf(num)
  if (index > -1) {
    selectedNumbers.value.splice(index, 1)
  } else {
    if (playType.value === 'option') {
      selectedNumbers.value = [num]
      return
    }
    if (playType.value === 'combo_number' && comboSelectCount.value > 0 && selectedNumbers.value.length >= comboSelectCount.value) {
      alert(`${playName.value}玩法只能选择${comboSelectCount.value}个号码`)
      return
    }
    if (playType.value === 'zodiac_combo' && comboSelectCount.value > 0 && selectedNumbers.value.length >= comboSelectCount.value) {
      alert(`${playName.value}玩法只能选择${comboSelectCount.value}个生肖`)
      return
    }
    selectedNumbers.value.push(num)
  }

  // 手动选择时重置快速选择状态
  isDomesticAnimalsSelected.value = false
  isWildAnimalsSelected.value = false
}

const selectQuick = (type) => {
  selectedNumbers.value = []

  switch (type) {
    case 'odd':
      for (let i = 1; i <= 49; i += 2) {
        selectedNumbers.value.push(i)
      }
      break
    case 'even':
      for (let i = 2; i <= 49; i += 2) {
        selectedNumbers.value.push(i)
      }
      break
    case 'small':
      for (let i = 1; i <= 24; i++) {
        selectedNumbers.value.push(i)
      }
      break
    case 'big':
      for (let i = 25; i <= 49; i++) {
        selectedNumbers.value.push(i)
      }
      break
    case 'all':
      for (let i = 1; i <= 49; i++) {
        selectedNumbers.value.push(i)
      }
      break
  }
}

const clearSelection = () => {
  selectedNumbers.value = []
  isDomesticAnimalsSelected.value = false
  isWildAnimalsSelected.value = false
}

const reverseSelection = () => {
  // 获取所有可用的号码
  const allAvailableNumbers = orderedBetNumbers.value.map(option => option.value)

  // 反选：选择所有未选中的号码
  const reversed = allAvailableNumbers.filter(num => !selectedNumbers.value.includes(num))
  if (playType.value === 'option') {
    selectedNumbers.value = reversed.slice(0, 1)
    return
  }
  if (playType.value === 'combo_number' && comboSelectCount.value > 0) {
    selectedNumbers.value = reversed.slice(0, comboSelectCount.value)
    return
  }
  if (playType.value === 'zodiac_combo' && comboSelectCount.value > 0) {
    selectedNumbers.value = reversed.slice(0, comboSelectCount.value)
    return
  }
  selectedNumbers.value = reversed
}

// 选择家禽
const selectDomesticAnimals = () => {
  selectedNumbers.value = []
  const domesticAnimalOptions = betNumbersData.value.filter(option => {
    // 优先使用API提供的category，fallback到 hardcoded 列表
    if (option.category) {
      console.log(`${option.label} API分类: ${option.category}`)
      return option.category === 'domestic'
    }
    const isDomestic = domesticAnimals.includes(option.label)
    console.log(`${option.label} 本地分类: ${isDomestic ? '家禽' : '野兽'}`)
    return isDomestic
  })
  selectedNumbers.value = domesticAnimalOptions.map(option => option.value)

  console.log('选择的家禽:', domesticAnimalOptions.map(o => o.label))

  // 更新选择状态
  isDomesticAnimalsSelected.value = true
  isWildAnimalsSelected.value = false
}

// 选择野兽
const selectWildAnimals = () => {
  selectedNumbers.value = []
  const wildAnimalOptions = betNumbersData.value.filter(option => {
    // 优先使用API提供的category，fallback到 hardcoded 列表
    if (option.category) {
      console.log(`${option.label} API分类: ${option.category}`)
      return option.category === 'wild'
    }
    const isWild = wildAnimals.includes(option.label)
    console.log(`${option.label} 本地分类: ${isWild ? '野兽' : '家禽'}`)
    return isWild
  })
  selectedNumbers.value = wildAnimalOptions.map(option => option.value)

  console.log('选择的野兽:', wildAnimalOptions.map(o => o.label))

  // 更新选择状态
  isWildAnimalsSelected.value = true
  isDomesticAnimalsSelected.value = false
}

const confirmBet = () => {
  if (selectedNumbers.value.length === 0 || betAmount.value <= 0) {
    return
  }

  // 验证连肖玩法的选择数量
  if (playType.value === 'zodiac' && !isSingleZodiacPlay.value) {
    const requiredCount = {
      '六肖': 6,
      '6肖': 6,
      '6肖中特': 6,
      '六肖中特': 6,
      '五肖': 5,
      '四肖': 4,
      '三肖': 3
    }

    const required = comboSelectCount.value || requiredCount[playName.value]
    if (required && selectedNumbers.value.length !== required) {
      alert(`${playName.value}玩法必须选择${required}个生肖！当前已选择${selectedNumbers.value.length}个。`)
      return
    }
  }

  if (playType.value === 'combo_number') {
    const required = comboSelectCount.value || 0
    if (required && selectedNumbers.value.length !== required) {
      alert(`${playName.value}玩法必须选择${required}个号码！当前已选择${selectedNumbers.value.length}个。`)
      return
    }
  }

  if (playType.value === 'zodiac_combo') {
    const required = comboSelectCount.value || 0
    if (required && selectedNumbers.value.length !== required) {
      alert(`${playName.value}玩法必须选择${required}个生肖！当前已选择${selectedNumbers.value.length}个。`)
      return
    }
  }

  if (isGroupedBetPlay.value) {
    // 连肖/数字组合：单行显示，只有一个金额输入
    modalBetAmounts.value = [betAmount.value.toString()]
  } else {
    // 数字、特肖、平肖：每个号码/生肖一行
    modalBetAmounts.value = selectedNumbers.value.map(() => betAmount.value.toString())
  }

  showBetModal.value = true
}

const closeBetModal = () => {
  showBetModal.value = false
}

const removeBetItem = (index) => {
  selectedNumbers.value.splice(index, 1)
  modalBetAmounts.value.splice(index, 1)

  // 如果没有选中项目了，关闭弹窗
  if (selectedNumbers.value.length === 0) {
    showBetModal.value = false
  }
}

const submitBet = async () => {
  const totalBetAmount = modalTotalAmount.value
  if (totalBetAmount <= 0) {
    alert('下注金额必须大于0！')
    return
  }

  try {
    isLoading.value = true
    const gid = getSelectedLotteryType()  // 获取当前选中的彩种ID

    console.log('开始下注，彩种ID:', gid, '期号:', currentQishu.value)

    // 构建批量订单数组
    const orders = []

    if (playType.value === 'zodiac' || playType.value === 'zodiac_combo') {
      // 生肖类游戏：为每个选中的生肖创建一个订单
      const betAmount = parseFloat(modalBetAmounts.value[0]) || 0
      if (betAmount <= 0) {
        alert('下注金额必须大于0！')
        return
      }

      if (isSingleZodiacPlay.value) {
        // 特肖/平肖：为每个选中的生肖单独创建订单
        selectedNumbers.value.forEach((zodiacValue, index) => {
          const itemAmount = parseFloat(modalBetAmounts.value[index]) || 0
          if (itemAmount <= 0) {
            return
          }
          const option = betNumbersData.value.find(item => item.value === zodiacValue)
          const zodiacName = option ? option.label : zodiacValue

          orders.push({
            pid: currentPid.value,
            bet_content: zodiacName,  // 生肖名称，如"鼠"
            bet_amount: parseInt(itemAmount),
            bet_type: 'win'  // 固定为'win',用户只能投注"中"
          })
        })
      } else {
        // 连肖玩法：提交生肖组合
        const zodiacNames = selectedNumbers.value.map(zodiacValue => {
          const option = betNumbersData.value.find(item => item.value === zodiacValue)
          return option ? option.label : zodiacValue
        })

        // bet_content 格式：生肖组合，如 "鼠,牛,虎,兔,龙,蛇"
        const betContent = zodiacNames.join(',')

        orders.push({
          pid: currentPid.value,
          bet_content: betContent,
          bet_amount: parseInt(betAmount),
          bet_type: 'win'  // 固定为'win',用户只能投注"中"
        })
      }

    } else if (playType.value === 'option') {
      for (let i = 0; i < selectedNumbers.value.length; i++) {
        const optionValue = selectedNumbers.value[i]
        const amount = parseFloat(modalBetAmounts.value[i]) || 0
        if (amount <= 0) {
          continue
        }
        const option = betNumbersData.value.find(item => item.value === optionValue)
        orders.push({
          pid: currentPid.value,
          bet_content: option ? option.label : optionValue,
          bet_amount: parseInt(amount),
          bet_type: 'win'
        })
      }

    } else if (playType.value === 'combo_number') {
      const amount = parseFloat(modalBetAmounts.value[0]) || 0
      if (amount <= 0) {
        alert('下注金额必须大于0！')
        return
      }

      const required = comboSelectCount.value || 0
      if (required && selectedNumbers.value.length !== required) {
        alert(`${playName.value}玩法必须选择${required}个号码！当前已选择${selectedNumbers.value.length}个。`)
        return
      }

      const betContent = selectedNumbers.value
        .map(number => number.toString().padStart(2, '0'))
        .join(',')

      orders.push({
        pid: currentPid.value,
        bet_content: betContent,
        bet_amount: parseInt(amount),
        bet_type: 'win'
      })

    } else {
      // 数字类游戏：为每个号码创建一个订单
      for (let i = 0; i < selectedNumbers.value.length; i++) {
        const number = selectedNumbers.value[i]
        const amount = parseFloat(modalBetAmounts.value[i]) || 0

        if (amount <= 0) {
          continue // 跳过金额为0的投注
        }

        // 格式化为两位数
        const formattedNumber = number.toString().padStart(2, '0')

        orders.push({
          pid: currentPid.value,
          bet_content: formattedNumber,  // 只传递号码，如 "08"
          bet_amount: parseInt(amount),
          bet_type: 'win'  // 固定为'win',用户只能投注"中"
        })
      }
    }

    // 检查是否有有效订单
    if (orders.length === 0) {
      alert('没有有效的投注订单！')
      return
    }

    console.log('批量投注订单:', orders)

    // 获取当前盘口
    const plateCode = getSelectedPlateCode()
    console.log('当前盘口:', plateCode)

    // 批量提交投注
    const result = await lotteryService.placeBet(
      parseInt(gid),
      currentQishu.value,
      orders,
      plateCode  // 新增：传递盘口参数
    )

    console.log('投注响应:', result)

    // 处理投注结果
    if (result.code === 1 && result.data) {
      const { success_count, total_amount, balance, results } = result.data

      // 后端只返回 success_count，没有 fail_count
      // 根据 success_count > 0 判断是否成功
      if (success_count > 0) {
        // 投注成功
        alert(`下注成功！共投注${success_count}注，总金额：¥${total_amount}\n剩余余额：¥${balance}`)

        // 清空选择
        selectedNumbers.value = []
        betAmount.value = 10
        betMultiplier.value = 1
        modalBetAmounts.value = []
        showBetModal.value = false

        // 触发事件更新余额等信息
        window.dispatchEvent(new CustomEvent('betPlaced', {
          detail: {
            amount: parseFloat(total_amount),
            success: true,
            qishu: currentQishu.value,
            gid: parseInt(gid)
          }
        }))

      } else {
        // 投注失败（success_count = 0）
        alert(`下注失败！请稍后重试`)
      }

    } else {
      // API返回失败
      alert(`下注失败：${result.msg || '未知错误'}`)
    }

  } catch (error) {
    console.error('下注API错误:', error)
    const errorMsg = error.response?.data?.msg || error.message || '网络错误'
    alert(`下注失败：${errorMsg}`)
  } finally {
    isLoading.value = false
  }
}

const getStatusText = (status) => {
  const statusMap = {
    'pending': '待开奖',
    'won': '中奖',
    'lost': '未中奖'
  }
  return statusMap[status] || status
}

const formatTime = (time) => {
  return new Date(time).toLocaleString('zh-CN')
}

// 监听主题变化
const updateTheme = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

onMounted(() => {
  // 获取期号数据
  fetchCurrentQishu()

  // 监听 localStorage 变化
  window.addEventListener('storage', updateTheme)

  // 监听全局主题变化事件
  window.addEventListener('themeChanged', () => {
    updateTheme()
  })

  // 监听彩种变化事件
  const handleLotteryTypeChanged = (event) => {
    console.log('彩种已变化:', event.detail)
    // 重新获取期号
    fetchCurrentQishu()
  }

  // 监听投注类型变化事件
  const handleBetTypeChanged = (event) => {
    console.log('投注类型已变化:', event.detail)
    // 更新玩法名称和数据
    const betTypeData = event.detail.betTypeData
    if (betTypeData) {
      playName.value = event.detail.playName
      currentPid.value = betTypeData.pid || 21401
      currentPlayType.value = betTypeData.label || '特码'

      // 重新获取投注号码数据
      fetchBetNumbers()
    }
  }

  // 监听封盘倒计时状态变化事件
  const handleBettingStatusChanged = (event) => {
    const { seconds_to_close } = event.detail
    secondsToClose.value = seconds_to_close || 0
  }

  // 监听盘口切换事件
  const handlePlateChanged = (event) => {
    console.log('🔄 投注页面收到盘口切换事件:', event.detail.plateCode)
    // 重新获取期号和投注数据
    fetchCurrentQishu()
    // 如果已选择玩法,重新获取投注号码
    if (playName.value) {
      fetchBetNumbers()
    }
  }

  window.addEventListener('lotteryTypeChanged', handleLotteryTypeChanged)
  window.addEventListener('betTypeChanged', handleBetTypeChanged)
  window.addEventListener('bettingStatusChanged', handleBettingStatusChanged)
  window.addEventListener('plateChanged', handlePlateChanged)

  // 延迟初始化投注类型，确保Header组件已经加载完成
  setTimeout(() => {
    initializeBetType()
  }, 100)

  // 清理函数
  onUnmounted(() => {
    window.removeEventListener('lotteryTypeChanged', handleLotteryTypeChanged)
    window.removeEventListener('betTypeChanged', handleBetTypeChanged)
    window.removeEventListener('bettingStatusChanged', handleBettingStatusChanged)
  })
})

// 初始化投注类型
const initializeBetType = () => {
  const currentBetTypeValue = getSelectedBetType()
  console.log('初始化投注类型:', currentBetTypeValue)

  // 检查是否有全局的betTypes数据
  if (window.betTypes && window.betTypes.length > 0) {
    console.log('找到全局betTypes:', window.betTypes)
    const currentBetTypeData = window.betTypes.find(bt => bt.value === currentBetTypeValue)

    if (currentBetTypeData) {
      playName.value = currentBetTypeData.label || '特碼'
      currentPid.value = currentBetTypeData.pid || 21401
      currentPlayType.value = currentBetTypeData.label || '特码'

      // 获取对应的数据
      fetchBetNumbers()
    } else if (window.betTypes.length > 0) {
      // 如果没找到匹配的，使用第一个
      const firstBetType = window.betTypes[0]
      playName.value = firstBetType.label || '特碼'
      currentPid.value = firstBetType.pid || 21401
      currentPlayType.value = firstBetType.label || '特码'

      // 更新全局选择
      localStorage.setItem('selectedBetType', firstBetType.value)

      fetchBetNumbers()
    } else {
      // 默认使用特码
      playName.value = '特碼'
      currentPid.value = 21401
      currentPlayType.value = '特码'
      fetchBetNumbers()
    }
  } else {
    // 如果没有全局数据，等待更长时间再重试
    console.log('全局betTypes尚未就绪，延迟重试...')
    setTimeout(() => {
      initializeBetType()
    }, 500)
  }
}
</script>

<style scoped>
/* Additional styles can be added here if needed */
</style>
