<template>
  <div class="results">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <!-- 历史开奖 -->
      <div class="mb-6">
        <!-- 标题和页码导航 -->
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-bold text-gray-900">开奖结果</h2>
          <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600">第 {{ currentPage }} 页</span>
            <span class="text-sm text-gray-600">/</span>
            <span class="text-sm text-gray-600">共 {{ totalPages }} 页</span>
            <button
              @click="previousPage"
              :disabled="currentPage === 1 || loading"
              class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed"
            >
              上一页
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages || loading"
              class="text-sm text-blue-600 hover:text-blue-800 disabled:text-gray-400 disabled:cursor-not-allowed"
            >
              下一页
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full border-collapse border border-gray-300">
            <thead>
              <tr
                :class="[
                  'border-b border-gray-300',
                  currentTheme === 'gold' ? 'bg-amber-700' : 'bg-purple-700'
                ]"
              >
                <th class="px-2 py-2 text-left text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">开奖日期</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">开奖号码</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">总分</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">特码</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">特码生肖</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">特码单双</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">特码大小</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">特码合数</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">合数单双</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">总数单双</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">总数大小</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">一肖量</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap border-r border-amber-600">尾数量</th>
                <th class="px-2 py-2 text-center text-sm font-semibold text-white whitespace-nowrap">五行</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="result in paginatedResults"
                :key="result.qishu"
                class="border-b border-gray-300 hover:bg-gray-50 transition-colors duration-200"
              >
                <!-- 开奖日期显示 -->
                <td class="px-2 py-2 text-sm text-gray-900 whitespace-nowrap border-r border-gray-300">
                  <div class="font-medium">{{ result.date_display }}</div>
                </td>

                <!-- 开奖号码 -->
                <td class="px-2 py-2 border-r border-gray-300">
                  <div class="flex gap-2 justify-center items-center whitespace-nowrap">
                    <div
                      v-for="(item, index) in getDisplayNumbers(result)"
                      :key="index"
                      class="flex items-center gap-1"
                    >
                      <span
                        :class="[
                          'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-lg',
                          item.is_special
                            ? 'bg-gradient-to-br from-red-400 via-red-500 to-red-700 text-white'
                            : `bg-gradient-to-br ${getBallColor(item.num).gradient} ${getBallColor(item.num).text}`
                        ]"
                        style="box-shadow: inset -2px -2px 4px rgba(0,0,0,0.2), inset 2px 2px 4px rgba(255,255,255,0.3), 0 4px 8px rgba(0,0,0,0.3);"
                      >
                        {{ item.num }}
                      </span>
                      <span class="text-xs text-gray-600">{{ item.zodiac }}</span>
                    </div>
                  </div>
                </td>

                <!-- 总分 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-lg font-bold text-gray-900">{{ result.total_score }}</span>
                </td>

                <!-- 特码 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-lg font-bold text-red-600">{{ result.special_num }}</span>
                </td>

                <!-- 特码生肖 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-sm text-gray-700">{{ result.special_zodiac }}</span>
                </td>

                <!-- 特码单双 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-800 rounded text-xs">
                    {{ result.special_odd_even }}
                  </span>
                </td>

                <!-- 特码大小 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="inline-block px-2 py-0.5 bg-green-100 text-green-800 rounded text-xs">
                    {{ result.special_big_small }}
                  </span>
                </td>

                <!-- 特码合数 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-sm font-medium text-gray-700">{{ result.special_hesu }}</span>
                </td>

                <!-- 特码合数单双 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                    {{ result.special_hesu_odd_even }}
                  </span>
                </td>

                <!-- 总数单双 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="inline-block px-2 py-0.5 bg-orange-100 text-orange-800 rounded text-xs">
                    {{ result.total_odd_even }}
                  </span>
                </td>

                <!-- 总数大小 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="inline-block px-2 py-0.5 bg-teal-100 text-teal-800 rounded text-xs">
                    {{ result.total_big_small }}
                  </span>
                </td>

                <!-- 一肖量 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-sm font-medium text-gray-700">{{ result.one_zodiac_count }}</span>
                </td>

                <!-- 尾数量 -->
                <td class="px-2 py-2 text-center border-r border-gray-300">
                  <span class="text-sm font-medium text-gray-700">{{ result.tail_count }}</span>
                </td>

                <!-- 五行 -->
                <td class="px-2 py-2 text-center">
                  <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                    {{ result.wuxing }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { lotteryService } from '@/services/api'

const currentPage = ref(1)
const pageSize = 10
const loading = ref(false)
const totalRecords = ref(0)
const currentTheme = ref(localStorage.getItem('appTheme') || 'gold')

// 历史开奖数据
const historyResults = ref([])

// 获取当前选中的盘口代码
const getSelectedPlateCode = () => {
  return localStorage.getItem('selectedPlateCode') || 'A'
}

// 从API获取开奖结果
const fetchResults = async () => {
  try {
    loading.value = true
    const plateCode = getSelectedPlateCode()
    console.log('📊 获取开奖结果, 盘口:', plateCode)
    const response = await lotteryService.getResultList({
      gid: 200,
      page: currentPage.value,
      limit: pageSize,
      plate_code: plateCode  // 新增：传递盘口参数
    })

    if (response.code === 1 && response.data) {
      historyResults.value = response.data.list || []
      totalRecords.value = response.data.total || 0
    }
  } catch (error) {
    console.error('获取开奖结果失败:', error)
  } finally {
    loading.value = false
  }
}

const paginatedResults = computed(() => {
  return historyResults.value
})

const getDisplayNumbers = (result) => {
  if (Array.isArray(result?.display_numbers) && result.display_numbers.length > 0) {
    return result.display_numbers
  }
  return Array.isArray(result?.numbers) ? result.numbers : []
}

const totalPages = computed(() => {
  return Math.ceil(totalRecords.value / pageSize)
})

// 获取圆球颜色 - 按照从右上角开始的斜向排列规律
const getBallColor = (number) => {
  // 将号码转换为数字
  const num = parseInt(number)

  // 根据5列布局计算号码在纵向排列中的位置
  const rows = Math.ceil(49 / 5) // 10行
  const cols = 5 // 5列

  // 计算在纵向排列中的位置
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
        text: 'text-white'
      }
    case 'teal':
      return {
        gradient: 'from-teal-500 via-teal-700 to-teal-900',
        text: 'text-white'
      }
    case 'yellow':
      return {
        gradient: 'from-yellow-500 via-yellow-700 to-yellow-900',
        text: 'text-white'
      }
    default:
      // 默认红色系
      return {
        gradient: 'from-red-400 via-red-500 to-red-700',
        text: 'text-white'
      }
  }
}

const previousPage = async () => {
  if (currentPage.value > 1) {
    currentPage.value--
    await fetchResults()
  }
}

const nextPage = async () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    await fetchResults()
  }
}

// 监听主题变化
const updateTheme = () => {
  currentTheme.value = localStorage.getItem('appTheme') || 'gold'
}

// 监听盘口切换事件
const handlePlateChanged = (event) => {
  console.log('🔄 开奖结果页面收到盘口切换事件:', event.detail.plateCode)
  // 重置到第一页并刷新数据
  currentPage.value = 1
  fetchResults()
}

onMounted(async () => {
  await fetchResults()

  // 监听主题变化事件
  window.addEventListener('storage', updateTheme)
  window.addEventListener('themeChanged', updateTheme)
  window.addEventListener('plateChanged', handlePlateChanged)
})

onUnmounted(() => {
  window.removeEventListener('storage', updateTheme)
  window.removeEventListener('themeChanged', updateTheme)
  window.removeEventListener('plateChanged', handlePlateChanged)
})
</script>
