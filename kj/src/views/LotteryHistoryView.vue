<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getPublicExternalLotteryList, getPublicLotteryList } from '../api/article'

const router = useRouter()
const route = useRoute()
const currentYear = new Date().getFullYear()
const years = Array.from({ length: 12 }, (_, index) => currentYear - index)

const selectedYear = ref(currentYear)
const pickerYear = ref(currentYear)
const page = ref(1)
const pageSize = 10
const total = ref(0)
const records = ref([])
const loading = ref(false)
const finished = ref(false)
const showYearPicker = ref(false)
const sortMode = ref('default')
const showSortMenu = ref(false)
const activeTab = ref('records')
const baseMonthYear = currentYear
const baseMonth = new Date().getMonth() + 1
const currentMonthYear = ref(currentYear)
const currentMonth = ref(baseMonth)
const selectedDay = ref(1)
const serverNowMs = ref(Date.now())

const historySource = computed(() => {
  const source = String(route.query.source || '')
  if (source === 'macau' || source === 'hongkong') return source
  return 'local'
})

const sourceTitle = computed(() => {
  if (historySource.value === 'macau') return '澳彩'
  if (historySource.value === 'hongkong') return '港彩'
  return '马彩'
})

const externalLotteryType = computed(() => {
  if (historySource.value === 'macau') return 2
  if (historySource.value === 'hongkong') return 1
  return 0
})

const colorMap = {
  blue: 'blue',
  green: 'green',
  red: 'red',
}

const zodiacs = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪']
const wuxings = ['金', '木', '水', '火', '土']
const redNumbers = new Set([1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46])
const blueNumbers = new Set([3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48])

const seededRecords = [
  {
    issue: 1,
    date: '2026年01月01日',
    balls: [
      ['27', '兔', '土', 'green'],
      ['08', '狗', '木', 'red'],
      ['43', '猪', '水', 'green'],
      ['33', '鸡', '金', 'green'],
      ['42', '鼠', '金', 'blue'],
      ['11', '羊', '金', 'green'],
      ['29', '牛', '水', 'red'],
    ],
  },
  {
    issue: 2,
    date: '2026年01月02日',
    balls: [
      ['48', '马', '火', 'blue'],
      ['07', '猪', '木', 'red'],
      ['04', '虎', '金', 'blue'],
      ['03', '兔', '金', 'blue'],
      ['15', '兔', '木', 'blue'],
      ['11', '羊', '金', 'green'],
      ['22', '猴', '水', 'green'],
    ],
  },
  {
    issue: 3,
    date: '2026年01月03日',
    balls: [
      ['30', '鼠', '水', 'red'],
      ['44', '狗', '水', 'green'],
      ['07', '猪', '木', 'red'],
      ['15', '兔', '木', 'blue'],
      ['42', '鼠', '金', 'blue'],
      ['17', '牛', '火', 'green'],
      ['09', '鸡', '火', 'blue'],
    ],
  },
  {
    issue: 4,
    date: '2026年01月04日',
    balls: [
      ['22', '猴', '水', 'green'],
      ['19', '猪', '土', 'red'],
      ['07', '猪', '木', 'red'],
      ['35', '羊', '土', 'red'],
      ['49', '蛇', '土', 'green'],
      ['36', '马', '土', 'blue'],
      ['45', '鸡', '木', 'red'],
    ],
  },
  {
    issue: 5,
    date: '2026年01月05日',
    balls: [
      ['46', '猴', '木', 'red'],
      ['19', '猪', '土', 'red'],
      ['20', '狗', '土', 'blue'],
      ['36', '马', '土', 'blue'],
      ['13', '蛇', '水', 'red'],
      ['17', '牛', '火', 'green'],
      ['43', '猪', '水', 'green'],
    ],
  },
]

const getBallColor = (number) => {
  if (redNumbers.has(number)) return 'red'
  if (blueNumbers.has(number)) return 'blue'
  return 'green'
}

const makeBall = (number, offset = 0) => ({
  number: String(number).padStart(2, '0'),
  zodiac: zodiacs[(number + offset) % zodiacs.length],
  wuxing: wuxings[(number + offset) % wuxings.length],
  color: getBallColor(number),
})

const makeFakeRecords = (year) => {
  const seeded = seededRecords.map((record) => ({
    id: `fake-${year}-${record.issue}`,
    issue: record.issue,
    date: record.date.replace('2026年', `${year}年`),
    balls: record.balls.map(([number, zodiac, wuxing, color]) => ({
      number,
      zodiac,
      wuxing,
      color,
    })),
  }))

  const generated = Array.from({ length: 75 }, (_, index) => {
    const issue = index + seeded.length + 1
    const date = new Date(year, 0, issue)
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const numbers = Array.from({ length: 7 }, (__, ballIndex) => {
      const value = ((issue * 7 + ballIndex * 11 + year) % 49) + 1
      return value
    })
    const uniqueNumbers = numbers.map((number, ballIndex) => ((number + ballIndex * 3 - 1) % 49) + 1)

    return {
      id: `fake-${year}-${issue}`,
      issue,
      date: `${date.getFullYear()}年${month}月${day}日`,
      balls: uniqueNumbers.map((number, ballIndex) => makeBall(number, issue + ballIndex)),
    }
  })

  return [...seeded, ...generated]
}

const getFakePage = (currentPage) => {
  const allItems = makeFakeRecords(selectedYear.value)
  const start = (currentPage - 1) * pageSize
  const items = allItems.slice(start, start + pageSize)
  return {
    items,
    total: allItems.length,
  }
}

const sortLabel = computed(() => {
  if (sortMode.value === 'asc') return '平码升序'
  if (sortMode.value === 'desc') return '平码降序'
  return '默认'
})

const calendarTitle = computed(() => {
  return `${currentMonthYear.value}-${currentMonth.value}`
})

const monthDays = computed(() => {
  const days = new Date(currentMonthYear.value, currentMonth.value, 0).getDate()
  return Array.from({ length: days }, (_, index) => index + 1)
})

const selectedDateText = computed(() => {
  if (!selectedDay.value) return ''
  const month = String(currentMonth.value).padStart(2, '0')
  const day = String(selectedDay.value).padStart(2, '0')
  return `${currentMonthYear.value}-${month}-${day} 是开奖日期`
})

const monthOffset = computed(() => (currentMonthYear.value - baseMonthYear) * 12 + currentMonth.value - baseMonth)
const canPrevMonth = computed(() => monthOffset.value > 0)
const canNextMonth = computed(() => monthOffset.value < 1)

const formatDate = (time) => {
  const date = new Date(Number(time))
  if (Number.isNaN(date.getTime())) return ''
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${date.getFullYear()}年${month}月${day}日`
}

const orderedRecords = computed(() => {
  if (sortMode.value === 'default') return records.value

  return records.value.map((record) => {
    const normalBalls = record.balls.slice(0, 6).sort((left, right) => {
      const diff = Number(left.number) - Number(right.number)
      return sortMode.value === 'asc' ? diff : -diff
    })
    return {
      ...record,
      balls: [...normalBalls, ...record.balls.slice(6)],
    }
  })
})

const normalizeRecord = (item) => ({
  id: item?.id,
  issue: Number(item?.issue) || 0,
  issueText: `${item?.issueText ?? item?.qishu ?? item?.issue ?? ''}`.trim(),
  date: item?.date || item?.lotteryTime || formatDate(item?.drawTime || item?.createdAt),
  balls: Array.isArray(item?.balls) ? item.balls : [],
  drawTimeMs: Number(item?.drawTime) || 0,
})

const isVisibleRecord = (record) => {
  const drawTimeMs = Number(record?.drawTimeMs) || 0
  if (drawTimeMs > 0 && drawTimeMs > serverNowMs.value) return false
  return true
}

const loadRecords = async (reset = false) => {
  if (loading.value || (finished.value && !reset)) return

  loading.value = true
  try {
    const currentPage = reset ? 1 : page.value
    let result
    let items = []
    let rawItemCount = 0

    try {
      if (historySource.value === 'local') {
        result = await getPublicLotteryList({
          page: currentPage,
          pageSize,
          year: selectedYear.value,
        })
      } else {
        result = await getPublicExternalLotteryList({
          lotteryType: externalLotteryType.value,
          page: currentPage,
          pageSize,
          sort: 1,
          year: selectedYear.value,
        })
      }
      serverNowMs.value = Number(result?._serverNowMs) || Date.now()
      rawItemCount = Array.isArray(result?.items) ? result.items.length : 0
      items = Array.isArray(result?.items) ? result.items.map(normalizeRecord).filter(isVisibleRecord) : []
    } catch {
      result = null
    }

    if (!items.length && historySource.value === 'local') {
      result = getFakePage(currentPage)
      items = result.items
    }

    total.value = Number(result?.total) || 0
    records.value = reset ? items : [...records.value, ...items]
    page.value = currentPage + 1
    finished.value = total.value > 0 ? currentPage * pageSize >= total.value : rawItemCount < pageSize
  } finally {
    loading.value = false
  }
}

const resetAndLoad = () => {
  page.value = 1
  total.value = 0
  records.value = []
  finished.value = false
  loadRecords(true)
}

const openYearPicker = () => {
  pickerYear.value = selectedYear.value || currentYear
  showYearPicker.value = true
}

const confirmYear = () => {
  selectedYear.value = pickerYear.value
  showYearPicker.value = false
}

const selectSort = (mode) => {
  sortMode.value = mode
  showSortMenu.value = false
}

const switchTab = (tab) => {
  activeTab.value = tab
  showSortMenu.value = false
}

const changeMonth = (step) => {
  if (step < 0 && !canPrevMonth.value) return
  if (step > 0 && !canNextMonth.value) return

  const nextMonth = currentMonth.value + step
  if (nextMonth > 12) {
    currentMonthYear.value += 1
    currentMonth.value = 1
  } else if (nextMonth < 1) {
    currentMonthYear.value -= 1
    currentMonth.value = 12
  } else {
    currentMonth.value = nextMonth
  }
  selectedDay.value = null
}

const selectDay = (day) => {
  selectedDay.value = day
}

const updateDocumentTitle = () => {
  document.title = `${sourceTitle.value}历史开奖 - 金算盘论坛`
}

const handleScroll = () => {
  if (activeTab.value !== 'records') return
  const bottom = window.innerHeight + window.scrollY
  const height = document.documentElement.scrollHeight
  if (height - bottom < 220) {
    loadRecords()
  }
}

watch([selectedYear, historySource], resetAndLoad)
watch(sourceTitle, updateDocumentTitle)

onMounted(() => {
  updateDocumentTitle()
  resetAndLoad()
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <main class="history-page">
    <header class="history-topbar">
      <button type="button" class="back-button" @click="router.back()">‹</button>
      <h1>{{ sourceTitle }}历史开奖</h1>
      <button type="button" class="calendar-button" @click="openYearPicker">▣</button>
    </header>

    <nav class="history-tabs">
      <button :class="{ active: activeTab === 'records' }" type="button" @click="switchTab('records')">开奖记录</button>
      <button :class="{ active: activeTab === 'dates' }" type="button" @click="switchTab('dates')">开奖日期</button>
    </nav>

    <section v-if="activeTab === 'records'" class="history-filter">
      <strong>{{ sourceTitle }} {{ selectedYear }}年历史开奖记录</strong>
      <button type="button" @click="selectSort(sortMode === 'asc' ? 'default' : 'asc')">升序</button>
      <button class="active" type="button">五行</button>
      <button type="button" @click="showSortMenu = !showSortMenu">{{ sortLabel }}</button>

      <div v-if="showSortMenu" class="sort-popover">
        <button type="button" @click="selectSort('default')">默认</button>
        <button type="button" @click="selectSort('asc')">平码升序</button>
        <button type="button" @click="selectSort('desc')">平码降序</button>
      </div>
    </section>

    <section v-if="activeTab === 'records'" class="record-list">
      <article v-for="record in orderedRecords" :key="record.id || record.issue" class="record-item">
        <div class="record-title">
          <h2>第{{ record.issueText || String(record.issue).padStart(3, '0') }}期最新开奖结果</h2>
          <!-- 暂时屏蔽马彩日期显示 -->
          <time v-if="historySource !== 'local'">{{ record.date }}</time>
        </div>

        <div class="record-card">
          <template v-for="(ball, index) in record.balls" :key="`${record.issue}-${index}`">
            <div class="ball-wrap">
              <strong :class="['ball', `ball--${colorMap[ball.color] || ball.color || 'red'}`]">
                {{ ball.number }}
              </strong>
              <span>{{ ball.display || [ball.zodiac, ball.wuxing].filter(Boolean).join('/') }}</span>
            </div>
            <b v-if="index === 5" class="plus">+</b>
          </template>
        </div>
      </article>
    </section>

    <template v-if="activeTab === 'records'">
      <p v-if="loading" class="history-state">加载中...</p>
      <p v-else-if="!records.length" class="history-state">暂无开奖记录</p>
      <p v-else-if="finished" class="history-state">没有更多了</p>
    </template>

    <section v-else class="date-panel">
      <div class="date-intro">
        <h2>简介</h2>
        <p>搅珠日期对照表，可查看当月及下一个月的搅珠开奖日期</p>
      </div>

      <div class="month-bar">
        <button :disabled="!canPrevMonth" type="button" aria-label="上个月" @click="changeMonth(-1)">‹</button>
        <strong>{{ calendarTitle }}</strong>
        <button :disabled="!canNextMonth" type="button" aria-label="下个月" @click="changeMonth(1)">›</button>
      </div>

      <div class="day-grid">
        <button
          v-for="day in monthDays"
          :key="day"
          :class="{ selected: selectedDay === day }"
          type="button"
          @click="selectDay(day)"
        >
          {{ day }}
        </button>
      </div>

      <p v-if="selectedDateText" class="selected-date">{{ selectedDateText }}</p>
    </section>

    <button class="float-home" type="button" @click="router.push('/home')">⌂</button>

    <div v-if="showYearPicker" class="year-mask" @click.self="showYearPicker = false">
      <div class="year-panel">
        <div class="year-toolbar">
          <button type="button" @click="showYearPicker = false">取消</button>
          <h3>选择年份</h3>
          <button type="button" @click="confirmYear">确定</button>
        </div>
        <div class="year-wheel">
          <button
            v-for="year in years"
            :key="year"
            :class="{ active: pickerYear === year }"
            type="button"
            @click="pickerYear = year"
          >
            {{ year }}年
          </button>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.history-page {
  min-height: 100vh;
  background: #f6f6f6;
  color: #333;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

.history-topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  display: grid;
  grid-template-columns: 48px 1fr 48px;
  align-items: center;
  height: 60px;
  border-bottom: 1px solid #efefef;
  background: #fff;
}

.history-topbar h1 {
  margin: 0;
  color: #111827;
  font-size: 21px;
  font-weight: 500;
  text-align: center;
}

.back-button,
.calendar-button {
  border: 0;
  background: transparent;
  color: #0a1d2f;
  font-size: 31px;
}

.calendar-button {
  color: #08c76b;
  font-size: 19px;
}

.history-tabs {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  height: 56px;
  background: #fff;
}

.history-tabs button {
  display: grid;
  place-items: center;
  border: 0;
  background: transparent;
  color: #222;
  font-size: 17px;
}

.history-tabs .active {
  color: #08c76b;
}

.history-filter {
  position: sticky;
  top: 60px;
  z-index: 20;
  display: grid;
  grid-template-columns: 1fr 54px 54px 86px;
  gap: 7px;
  align-items: center;
  min-height: 52px;
  padding: 0 12px;
  background: #e9e9e9;
}

.history-filter strong {
  min-width: 0;
  overflow: hidden;
  font-size: 17px;
  font-weight: 500;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.history-filter button {
  height: 28px;
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #333;
  font-size: 14px;
}

.history-filter button.active {
  background: #08c76b;
  color: #fff;
}

.sort-popover {
  position: absolute;
  top: 52px;
  right: 0;
  display: grid;
  width: 148px;
  border-radius: 10px 0 0 10px;
  background: #fff;
  box-shadow: 0 6px 18px rgb(0 0 0 / 12%);
}

.sort-popover::before {
  content: "";
  position: absolute;
  top: -9px;
  right: 48px;
  border-right: 9px solid transparent;
  border-bottom: 9px solid #fff;
  border-left: 9px solid transparent;
}

.sort-popover button {
  height: 54px;
  border-radius: 0;
  border-bottom: 1px solid #eee;
  font-size: 15px;
}

.record-list {
  padding: 12px 10px 28px;
}

.record-item {
  margin-bottom: 20px;
}

.record-title {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  margin: 0 4px 7px;
}

.record-title h2 {
  margin: 0;
  color: #444;
  font-size: 17px;
  font-weight: 500;
}

.record-title time {
  color: #8e8e8e;
  font-size: 15px;
}

.record-card {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr)) auto minmax(0, 1fr);
  gap: 3px;
  align-items: start;
  padding: 9px 7px 12px;
  border: 1px solid #0fc46f;
  border-radius: 4px;
  background: #fff;
  overflow: hidden;
}

.ball-wrap {
  display: grid;
  justify-items: center;
  min-width: 0;
}

.ball {
  display: grid;
  place-items: center;
  width: clamp(30px, 8.6vw, 46px);
  height: clamp(30px, 8.6vw, 46px);
  border-radius: 999px;
  background: #fff;
  color: #666;
  font-size: clamp(15px, 4.6vw, 23px);
  font-weight: 900;
  line-height: 1;
  box-sizing: border-box;
}

.ball--red {
  border: clamp(3px, 0.9vw, 5px) solid #ff2f39;
}

.ball--blue {
  border: clamp(3px, 0.9vw, 5px) solid #2f9cff;
}

.ball--green {
  border: clamp(3px, 0.9vw, 5px) solid #10c760;
}

.ball-wrap span {
  margin-top: 6px;
  color: #222;
  font-size: clamp(10px, 3vw, 14px);
  white-space: nowrap;
}

.plus {
  align-self: center;
  color: #777;
  font-size: 22px;
  font-weight: 500;
}

.history-state {
  padding: 0 0 30px;
  color: #999;
  font-size: 14px;
  text-align: center;
}

.date-panel {
  padding: 16px 16px 40px;
  background: #fff;
}

.date-intro {
  position: relative;
  margin: 0 0 34px;
  padding: 18px 14px 20px;
  border: 2px solid #ff6849;
  background: #fff;
  color: #444;
  text-align: center;
}

.date-intro::before,
.date-intro::after {
  content: "";
  position: absolute;
  left: 10px;
  right: 10px;
  height: 4px;
  border-inline: 2px solid #ff6849;
}

.date-intro::before {
  top: -5px;
}

.date-intro::after {
  bottom: -5px;
}

.date-intro h2 {
  margin: 0 0 20px;
  font-size: 18px;
  font-weight: 500;
}

.date-intro p {
  margin: 0;
  font-size: 16px;
  line-height: 1.5;
}

.month-bar {
  display: grid;
  grid-template-columns: 56px 1fr 56px;
  align-items: center;
  height: 72px;
  background: #08c76b;
  color: #fff;
}

.month-bar strong {
  font-size: 27px;
  font-weight: 500;
  text-align: center;
}

.month-bar button {
  height: 100%;
  border: 0;
  background: transparent;
  color: #fff;
  font-size: 42px;
  line-height: 1;
}

.month-bar button:disabled {
  opacity: 0.35;
}

.day-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 16px 22px;
  padding: 32px 16px 52px;
}

.day-grid button {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  justify-self: center;
  border: 1px solid #08c76b;
  border-radius: 999px;
  background: #08c76b;
  color: #fff;
  font-size: 18px;
}

.day-grid button.selected {
  background: #fff;
  color: #08c76b;
}

.selected-date {
  margin: 0;
  color: #333;
  font-size: 22px;
  text-align: center;
}

.float-home {
  position: fixed;
  left: 6px;
  bottom: 98px;
  z-index: 25;
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  border: 0;
  border-radius: 999px;
  background: rgb(130 130 140 / 78%);
  color: #fff;
  font-size: 31px;
}

.year-mask {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: grid;
  place-items: end center;
  background: rgb(0 0 0 / 34%);
}

.year-panel {
  width: min(100%, 600px);
  border-radius: 18px 18px 0 0;
  background: #f8f8f8;
  box-shadow: 0 -8px 26px rgb(0 0 0 / 16%);
  overflow: hidden;
}

.year-toolbar {
  display: grid;
  grid-template-columns: 72px 1fr 72px;
  align-items: center;
  height: 48px;
  border-bottom: 1px solid #e5e5e5;
  background: #fff;
}

.year-toolbar h3 {
  margin: 0;
  color: #111;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
}

.year-toolbar button {
  border: 0;
  background: transparent;
  color: #08c76b;
  font-size: 15px;
}

.year-wheel {
  position: relative;
  height: 220px;
  padding: 72px 0;
  overflow-y: auto;
  background:
    linear-gradient(180deg, #f8f8f8 0%, rgb(248 248 248 / 82%) 28%, transparent 50%, rgb(248 248 248 / 82%) 72%, #f8f8f8 100%);
  scroll-snap-type: y mandatory;
  box-sizing: border-box;
}

.year-wheel::before,
.year-wheel::after {
  content: "";
  position: sticky;
  left: 0;
  z-index: 2;
  display: block;
  height: 1px;
  margin: 0 18px;
  background: #d7d7d7;
}

.year-wheel::before {
  top: 72px;
}

.year-wheel::after {
  bottom: 72px;
}

.year-wheel button {
  display: block;
  width: 100%;
  height: 44px;
  border: 0;
  background: transparent;
  color: #888;
  font-size: 18px;
  scroll-snap-align: center;
}

.year-wheel button.active {
  color: #111;
  font-size: 22px;
  font-weight: 600;
}

@media (min-width: 601px) {
  .history-page {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 430px) {
  .history-filter {
    grid-template-columns: 1fr 50px 50px 80px;
    padding-inline: 10px;
  }

  .history-filter strong,
  .record-title h2 {
    font-size: 16px;
  }

  .record-title time {
    font-size: 14px;
  }

  .ball {
    width: clamp(29px, 9.8vw, 42px);
    height: clamp(29px, 9.8vw, 42px);
    font-size: clamp(14px, 5vw, 21px);
  }

  .date-panel {
    padding-inline: 16px;
  }

  .date-intro {
    margin-bottom: 34px;
    padding: 17px 10px 18px;
  }

  .date-intro h2 {
    font-size: 17px;
  }

  .date-intro p {
    font-size: 15px;
  }

  .month-bar {
    height: 72px;
  }

  .day-grid {
    grid-template-columns: repeat(5, 1fr);
    gap: 16px 19px;
    padding: 32px 16px 52px;
  }

  .day-grid button {
    width: 48px;
    height: 48px;
    font-size: 18px;
  }

  .selected-date {
    font-size: 22px;
  }
}
</style>
