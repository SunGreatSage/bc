<script setup>
import { computed, onMounted, onUnmounted, ref, watchEffect } from 'vue'
import { getPublicArticleList, getPublicExternalLottery, getPublicLotteryLatest } from '../api/article'

// 本地马彩按后台 API 时间走；澳彩和港彩按固定 21:30 / 21:35 节点走
// 自动扫描 src/assets/banners/ 目录下所有 jpg/jpeg 图片
const _bannerMods = import.meta.glob('../assets/banners/*.{jpg,jpeg}', { eager: true })
const bannerImages = Object.values(_bannerMods).map((m) => m.default)
const currentBanner = ref(0)
let bannerTimer

const now = ref(new Date())
const serverTimeOffsetMs = ref(0)
let timer
let lotteryTimer
let externalLotteryTimer
const BALL_REVEAL_MS = 3000
const SPECIAL_BALL_EXTRA_MS = 6000
const PRE_DRAW_WAIT_MS = 5 * 60 * 1000
const FLIP_REVEAL_MS = 780
const EXTERNAL_LOTTERY_POLL_MS = 3000
const EXTERNAL_PRE_DRAW_BG_CHARS = ['四', '九', '图', '库', '开', '奖', '快']
const EXTERNAL_PRE_DRAW_START_HOUR = 21
const EXTERNAL_PRE_DRAW_START_MINUTE = 30
const EXTERNAL_DRAW_START_HOUR = 21
const EXTERNAL_DRAW_START_MINUTE = 35
const latestLottery = ref(null)   // 始终是 API 最新数据（用于倒计时/下期信息）
const shownLottery = ref(null)    // 实际展示的开奖数据（只有开奖时间到了才更新）
const animatingLottery = ref(null) // 动画中的新期开奖数据，动画结束前不直接替换 shownLottery
const selectedLotteryKey = ref('local')
const externalLotteries = ref({
  macau: null,
  hongkong: null,
})

const getNowMs = () => Date.now() + serverTimeOffsetMs.value

const localDisplayLottery = computed(() => {
  if (localAnimationStartAt.value > 0 && animatingLottery.value) {
    return animatingLottery.value
  }
  return shownLottery.value
})

const lotterySources = computed(() => [
  {
    key: 'local',
    label: '马彩',
    data: localDisplayLottery.value, // 动画期间使用待揭晓结果，结束后再切换为 shownLottery
  },
  {
    key: 'macau',
    label: '澳彩',
    data: externalLotteries.value.macau,
  },
  {
    key: 'hongkong',
    label: '港彩',
    data: externalLotteries.value.hongkong,
  },
])

const activeLottery = computed(() => {
  const source = lotterySources.value.find((item) => item.key === selectedLotteryKey.value)
  return source ? source.data : shownLottery.value
})
const activeIsLocal = computed(() => selectedLotteryKey.value === 'local')
const historyRoute = computed(() => {
  if (selectedLotteryKey.value === 'macau') {
    return { path: '/lottery-history', query: { source: 'macau' } }
  }
  if (selectedLotteryKey.value === 'hongkong') {
    return { path: '/lottery-history', query: { source: 'hongkong' } }
  }
  return '/lottery-history'
})

const countdownTarget = computed(() => {
  // 完全依赖后台 API 返回的时间，不使用任何本地硬编码时间
  const nextDrawTime = Number(latestLottery.value?.nextDrawTime) || 0
  const currentDrawTime = Number(latestLottery.value?.drawTime) || 0
  const currentIssueKey = getLotteryIssueKey(latestLottery.value)
  const nowMs = now.value.getTime()
  const isAwaitingCurrentIssue =
    currentIssueKey > 0 &&
    currentDrawTime > 0 &&
    (pendingCurrentIssue === currentIssueKey ||
      (lastDrawnIssue > 0 && currentIssueKey > lastDrawnIssue))

  if (isAwaitingCurrentIssue) {
    return currentDrawTime > nowMs ? currentDrawTime : 0
  }

  // 当前期尚未开奖时，必须优先使用本期 drawTime，避免被下期 nextDrawTime 跳过。
  if (currentDrawTime > nowMs) return currentDrawTime
  // 当前期已到/已开后，再使用 nextDrawTime 展示下期开奖倒计时。
  if (nextDrawTime > nowMs) return nextDrawTime
  // 无法从 API 获得未来开奖时间，返回 0
  return 0
})

const countdown = computed(() => {
  const target = countdownTarget.value
  if (!target) return '--:--:--'
  const diff = Math.max(0, Math.floor((target - now.value.getTime()) / 1000))
  const hours = Math.floor(diff / 3600)
  const minutes = Math.floor((diff % 3600) / 60)
  const seconds = diff % 60
  return [hours, minutes, seconds].map((item) => String(item).padStart(2, '0')).join(':')
})

const drawBalls = [
  { number: '21', color: 'green', label: '狗/土' },
  { number: '16', color: 'green', label: '兔/木' },
  { number: '25', color: 'blue', label: '马/木' },
  { number: '29', color: 'red', label: '虎/土' },
  { number: '08', color: 'red', label: '猪/木' },
  { number: '07', color: 'red', label: '鼠/土' },
  { number: '04', color: 'blue', label: '兔/金' },
]

const apiDrawBalls = computed(() => {
  const balls = Array.isArray(activeLottery.value?.balls) ? activeLottery.value.balls : []
  if (!balls.length) return drawBalls
  return balls.map((ball) => ({
    number: String(ball.number ?? '').padStart(2, '0'),
    color: ball.color || 'red',
    label: ball.display || [ball.zodiac, ball.wuxing].filter(Boolean).join('/'),
  }))
})

// Cumulative reveal timestamps for each ball (relative to animationStartAt).
// Balls 0-5: each appears every BALL_REVEAL_MS (3 s).
// Ball 6 (special): appears SPECIAL_BALL_EXTRA_MS (6 s) after ball 5.
const BALL_REVEAL_TIMES = (() => {
  const times = []
  for (let i = 0; i < 6; i++) {
    times.push(i * BALL_REVEAL_MS)
  }
  // Special ball reveal time = after the 6th normal ball + extra delay
  times.push(5 * BALL_REVEAL_MS + SPECIAL_BALL_EXTRA_MS)
  return times
})()
const ANIMATION_TOTAL_MS = BALL_REVEAL_TIMES[6] + FLIP_REVEAL_MS + 300

// 本地动画触发时间戳（使用服务器校准后的当前时间）
const localAnimationStartAt = ref(0)
const waitingAnimationResult = ref(false)

const animationElapsed = computed(() => {
  // now.value 作为响应式依赖，驱动每 120ms 重算
  // getNowMs() 获得精确实时时间（不依赖 now.value 的刷新延迟）
  void now.value
  return localAnimationStartAt.value > 0
    ? Math.max(0, getNowMs() - localAnimationStartAt.value)
    : Number.MAX_SAFE_INTEGER
})

const getTodayTimeMs = (hour, minute) => {
  const date = new Date(now.value)
  date.setHours(hour, minute, 0, 0)
  return date.getTime()
}

const externalPreDrawStartAt = computed(() =>
  getTodayTimeMs(EXTERNAL_PRE_DRAW_START_HOUR, EXTERNAL_PRE_DRAW_START_MINUTE),
)
const externalDrawStartAt = computed(() =>
  getTodayTimeMs(EXTERNAL_DRAW_START_HOUR, EXTERNAL_DRAW_START_MINUTE),
)

const isDrawing = computed(
  () =>
    activeIsLocal.value &&
    localAnimationStartAt.value > 0 &&
    animationElapsed.value >= 0 &&
    animationElapsed.value < ANIMATION_TOTAL_MS,
)
const isPreDrawWaiting = computed(() => {
  if (!activeIsLocal.value || isDrawing.value) return false
  // waitingForNextDraw 只有在展示过某期后才会被设置，动画触发时会被消费
  // 所以这里表示“展示过上期开奖，并尔正在等待下期开奖时间到来”
  if (!waitingForNextDraw.value) return false
  const target = countdownTarget.value
  if (!target) return false
  const diff = target - now.value.getTime()
  return diff > 0 && diff <= PRE_DRAW_WAIT_MS
})
const isExternalPreDrawWaiting = computed(() => {
  if (activeIsLocal.value) return false
  const nowMs = getNowMs()
  return nowMs >= externalPreDrawStartAt.value && nowMs < externalDrawStartAt.value
})
const isExternalDrawing = computed(() => {
  if (activeIsLocal.value) return false
  const nowMs = getNowMs()
  return nowMs >= externalDrawStartAt.value && nowMs < externalDrawStartAt.value + ANIMATION_TOTAL_MS
})
const externalAnimationElapsed = computed(() => {
  if (!isExternalDrawing.value) return Number.MAX_SAFE_INTEGER
  return Math.max(0, getNowMs() - externalDrawStartAt.value)
})
const BG_CHARS = '福福福福福福福开奖'

const getDrawingBall = (ball, index, elapsedMs, bgChars = BG_CHARS) => {
  const flipStartAt = BALL_REVEAL_TIMES[index] ?? index * BALL_REVEAL_MS
  const flipEndAt = flipStartAt + FLIP_REVEAL_MS
  if (elapsedMs < flipStartAt)
    return {
      ...ball,
      number: '',
      label: '',
      color: 'gray',
      bgChar: bgChars[index] ?? '',
      cardBack: true,
    }
  if (elapsedMs < flipEndAt) return { ...ball, label: '翻牌中', bgChar: bgChars[index] ?? '', flipping: true }
  return ball
}

const displayBalls = computed(() =>
  apiDrawBalls.value.map((ball, index) => {
    if (isPreDrawWaiting.value)
      return {
        ...ball,
        number: '',
        label: '等待开奖',
        color: 'gray',
        bgChar: BG_CHARS[index] ?? '',
        cardBack: true,
      }
    if (isExternalPreDrawWaiting.value)
      return {
        ...ball,
        number: '',
        label: '',
        color: 'gray',
        bgChar: EXTERNAL_PRE_DRAW_BG_CHARS[index] ?? '',
        cardBack: true,
      }
    if (activeIsLocal.value && waitingAnimationResult.value && !animatingLottery.value) {
      if (index === 0)
        return {
          ...ball,
          number: '',
          label: '开奖中',
          color: 'gray',
          bgChar: BG_CHARS[index] ?? '',
          cardBack: true,
          shuffling: true,
        }
      return {
        ...ball,
        number: '',
        label: '',
        color: 'gray',
        bgChar: BG_CHARS[index] ?? '',
        cardBack: true,
      }
    }
    if (activeIsLocal.value && isDrawing.value) {
      return getDrawingBall(ball, index, animationElapsed.value)
    }
    if (!activeIsLocal.value && isExternalDrawing.value) {
      return getDrawingBall(ball, index, externalAnimationElapsed.value)
    }
    return ball
  }),
)

const formatIssueText = (lottery) => {
  const issueText = `${lottery?.issueText ?? ''}`.trim()
  if (issueText) return `第${issueText}期`
  const year = Number(lottery?.year) || new Date().getFullYear()
  const issue = Number(lottery?.issue) || 0
  if (!issue) return '暂无开奖'
  return `第${year}${String(issue).padStart(3, '0')}期`
}

const formatSourceSub = (source) => {
  if (source.data?.status === 'error') return '加载失败'
  const issueText = `${source.data?.issueText ?? ''}`.trim()
  const nextIssueText = `${source.data?.nextIssueText ?? ''}`.trim()
  if (issueText) {
    return nextIssueText ? `第${issueText}期 / 下期${nextIssueText}` : `第${issueText}期`
  }
  const issue = Number(source.data?.issue) || 0
  const nextIssue = Number(source.data?.nextIssue) || 0
  if (!issue) return '加载中'
  if (nextIssue) return `第${String(issue).padStart(3, '0')}期 / 下期${String(nextIssue).padStart(3, '0')}`
  return `第${String(issue).padStart(3, '0')}期`
}

const getLotteryIssueKey = (lottery) => {
  const issueKey = Number(lottery?.issueKey) || 0
  if (issueKey > 0) return issueKey
  const year = Number(lottery?.year) || 0
  const issue = Number(lottery?.issue) || 0
  if (issue > 100000) return issue
  if (!year || !issue) return 0
  return year * 1000 + issue
}

const currentIssueText = computed(() => formatIssueText(activeLottery.value))

const activeCountdownText = computed(() => {
  if (activeIsLocal.value) return `距下期开奖：${countdown.value}`
  return activeLottery.value?.title || '最新开奖信息'
})

const nextIssueText = computed(() => {
  const pad = (value) => String(value).padStart(2, '0')

  if (!activeIsLocal.value) {
    const issue = Number(activeLottery.value?.nextIssue) || 0
    const nextDrawTime = Number(activeLottery.value?.nextDrawTime) || 0
    const date = nextDrawTime ? new Date(nextDrawTime) : null
    const dateText = date
      ? `${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())}`
      : ''
    if (!issue) return activeLottery.value?.title || '等待开奖信息'
    return `下期第${String(issue).padStart(3, '0')}期${dateText ? ` ${dateText}` : ''}`
  }

  const rawNextIssueText = `${latestLottery.value?.nextIssueText ?? ''}`.trim()
  const issue = Number(latestLottery.value?.nextIssue) || Number(latestLottery.value?.issue || 115) + 1
  const issueText = rawNextIssueText
    ? `第${rawNextIssueText}期`
    : `第${new Date().getFullYear()}${String(issue).padStart(3, '0')}期`
  if (!countdownTarget.value) return issueText
  const date = new Date(countdownTarget.value)
  return `${issueText} ${date.getFullYear()}/${pad(date.getMonth() + 1)}/${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`
})

const siteLinks = [
  { name: '金牌谜语', url: 'https://imgs.hink.mom/' },
  { name: '王中王', url: '#' },
  { name: '神算子', url: '#' },
  { name: '铁算盘', url: '#' },
  { name: '鬼谷子', url: '#' },
  { name: '刘伯温', url: '#' },
]

const siteDomains = [
  'www.xxx.shop',
  'www.xxxxx.mom',
  'www.xxx.eu.cc',
  'my.xxx.cc',
  'my.xxxx.eu.cc',
  'my.xxx.mom',
]

const siteDomainRows = computed(() => {
  const rows = []
  for (let index = 0; index < siteDomains.length; index += 2) {
    rows.push(siteDomains.slice(index, index + 2))
  }
  return rows
})

const featureItems = [
  { label: '开奖现场', icon: '▶', color: '#38dca1' },
  { label: '资料大全', icon: '▥', color: '#35a8e8' },
  { label: '资讯统计', icon: '◔', color: '#ff8f1f' },
  { label: '查询助手', icon: '▣', color: '#63dc3c' },
  { label: '天线宝宝', icon: '☻', color: '#31b7e8' },
  { label: '开奖记录', icon: '◆', color: '#d84ff4' },
  { label: '热门榜单', icon: '■', color: '#ff656d' },
  { label: '高手之家', icon: '⌂', color: '#705dff', hot: true },
  { label: '高手论坛', icon: '○', color: '#34c6da' },
  { label: '我的收藏', icon: '●', color: '#ff7067' },
]

const bottomTabs = [
  { label: '马彩', icon: '⌂', active: true },
  { label: '发现', icon: '♁' },
  { label: '高手之家', icon: '⌂', center: true },
  { label: '寻宝', icon: '▱' },
  { label: '我的', icon: '♧' },
]

// imagePool 已移除（未使用）

const articlePage = ref(1)
const articlePageSize = 8
const articleMaxCount = 56
const articleTotal = ref(0)
const homeImages = ref([])
const loadingMore = ref(false)
const articleErrorText = ref('')

const stripHtml = (html) => `${html ?? ''}`.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()

const extractImage = (html) => {
  const match = `${html ?? ''}`.match(/<img[^>]+src=["']([^"']+)["']/i)
  return match?.[1] || ''
}

const normalizeHomeArticle = (article) => {
  const id = article?.id ?? article?.title ?? Math.random()
  const title = `${article?.title ?? '未命名文章'}`.trim()
  const src =
    article?.cover ||
    article?.coverImage ||
    article?.image ||
    article?.thumbnail ||
    extractImage(article?.content)

  return {
    id: String(id),
    title,
    src,
    summary: stripHtml(article?.content).slice(0, 48),
  }
}

const leftImages = computed(() => homeImages.value.filter((_, index) => index % 2 === 0))
const rightImages = computed(() => homeImages.value.filter((_, index) => index % 2 === 1))
const maxHomeImageCount = computed(() => {
  if (articleTotal.value > 0) {
    return Math.min(articleTotal.value, articleMaxCount)
  }
  return articleMaxCount
})
const hasMoreImages = computed(() => homeImages.value.length < maxHomeImageCount.value)

const refreshPage = () => {
  window.location.reload()
}

const loadMoreImages = () => {
  if (loadingMore.value || !hasMoreImages.value) return
  loadHomeArticles()
}

const handleScroll = () => {
  const scrollBottom = window.innerHeight + window.scrollY
  const documentHeight = document.documentElement.scrollHeight
  if (documentHeight - scrollBottom < 180) {
    loadMoreImages()
  }
}

// ── 动画状态机（双变量精确控制）───────────────────────────────────
//
// pendingCurrentIssue：页面加载时当期尚未开奖，等到开奖后播放动画
// waitingForNextDraw：已展示某期，当前在等待下一期开奖（开奖前 5 分钟内显示灰色球）
// lastDrawnIssue：已在 UI 展示过的期号
//
// 场景覆盖：
//   ① 页面刷新时数据已开奖        → lastDrawnIssue=0, pendingCurrentIssue=0 → 直接显示
//   ② 开奖前加载页面，等到开奖  → pendingCurrentIssue=X → 动画
//   ③ 已展示上期，新期开奖        → waitingForNextDraw=true → 动画
let pendingCurrentIssue = 0     // 当期开奖前记录，用于开奖后触发动画（year * 1000 + issue）
let lastDrawnIssue = 0          // 已展示期号（year * 1000 + issue）
let lastAnimatedIssue = 0       // 已播放过动画的期号，防止同一期重复开奖
let latestLotteryRequestSeq = 0
let latestLotteryLoading = false
let externalLotteryLoading = false
// waitingForNextDraw 必须是响应式，让 isPreDrawWaiting computed 能跟踪它
const waitingForNextDraw = ref(false)

const completeLotteryAnimationIfNeeded = () => {
  if (!localAnimationStartAt.value) return
  if (waitingAnimationResult.value && !animatingLottery.value) return
  if (animationElapsed.value < ANIMATION_TOTAL_MS) return
  if (animatingLottery.value) {
    shownLottery.value = animatingLottery.value
  }
  localAnimationStartAt.value = 0
  animatingLottery.value = null
  waitingAnimationResult.value = false
}

const primeDrawAnimationIfNeeded = () => {
  if (localAnimationStartAt.value > 0 || waitingAnimationResult.value || animatingLottery.value) return
  if (!(pendingCurrentIssue > 0 || waitingForNextDraw.value)) return
  if (countdownTarget.value > 0) return

  waitingAnimationResult.value = true
  now.value = new Date(getNowMs())
  localAnimationStartAt.value = getNowMs()
}

const loadLatestLottery = async () => {
  if (latestLotteryLoading) return

  latestLotteryLoading = true
  const requestSeq = ++latestLotteryRequestSeq

  try {
    const result = await getPublicLotteryLatest()
    if (requestSeq !== latestLotteryRequestSeq) return

    const newIssue = Number(result?.issue) || 0
    const newIssueKey = getLotteryIssueKey(result)
    // 丢弃乱序或回退响应，避免开奖临界点把 UI 回滚后又重新开奖一次。
    if (newIssueKey > 0 && lastDrawnIssue > 0 && newIssueKey < lastDrawnIssue) return

    const serverNowMs = Number(result?._serverNowMs) || 0
    if (serverNowMs > 0) {
      serverTimeOffsetMs.value = serverNowMs - Date.now()
      now.value = new Date(getNowMs())
    }
    // 始终更新 latestLottery，用于倒计时和下期信息
    latestLottery.value = result

    const hasBalls = Array.isArray(result?.balls) && result.balls.length > 0
    const drawTimeMs = Number(result?.drawTime) || 0
    const nextDrawTimeMs = Number(result?.nextDrawTime) || 0
    const nowMs = getNowMs()
    const drawTimePassed = drawTimeMs > 0 && drawTimeMs <= nowMs
    const isNewIssue = newIssueKey > 0 && newIssueKey !== lastDrawnIssue
    const currentIssueDrawTimePending = drawTimeMs > nowMs

    // ① 开奖前记录待开奖期号（用于开奖后触发动画）
    //    条件：当期尚未开奖，且 lastDrawnIssue=0（页面还没展示过任何期）
    if (newIssueKey > 0 && !drawTimePassed && lastDrawnIssue === 0) {
      pendingCurrentIssue = newIssueKey
    }

    // ② 已展示过上期，并且当前返回期号有未来 drawTime → 标记正在等待本期开奖
    //    nextDrawTime 只作为兼容兜底，不能覆盖当前期 drawTime。
    if (
      lastDrawnIssue > 0 &&
      (isNewIssue ? currentIssueDrawTimePending : nextDrawTimeMs > nowMs)
    ) {
      waitingForNextDraw.value = true
    }

    // ③ 到了后台开奖时间但号码尚未返回时，先进入占位摇奖态，等真实结果到达后重播。
    if (isNewIssue && !hasBalls && drawTimePassed) {
      if (lastDrawnIssue > 0) {
        waitingForNextDraw.value = true
      } else {
        pendingCurrentIssue = newIssueKey
      }
    }

    // ④ 无号码或开奖时间未到：不更新展示数据，等待本期 drawTime 或开奖结果
    if (!hasBalls || !newIssue || !drawTimePassed) return

    // ⑤ 有号码且 drawTime 已过，且是新期号 → 决定展示方式
    if (newIssueKey !== lastDrawnIssue) {
      lastDrawnIssue = newIssueKey

      // 播放动画的条件（满足任一即可）：
      //   A. 页面在当期开奖前加载，pendingCurrentIssue 匹配（等到了这期开奖）
      //   B. 已展示过上期并在等待下期（waitingForNextDraw = true）
      const shouldAnimate =
        newIssueKey !== lastAnimatedIssue &&
        (pendingCurrentIssue === newIssueKey || waitingForNextDraw.value)

      // 消费两个标记
      pendingCurrentIssue = 0
      waitingForNextDraw.value = false

      if (shouldAnimate) {
        // 到开奖时间后，本地会先进入占位摇球态；拿到真实结果后再重启动画，
        // 保证不会先闪出静态号码。
        lastAnimatedIssue = newIssueKey
        waitingAnimationResult.value = false
        animatingLottery.value = result
        now.value = new Date(getNowMs())
        localAnimationStartAt.value = getNowMs()
      } else {
        // 首次加载：页面打开时已开奖，直接显示历史数据，不播放动画
        waitingAnimationResult.value = false
        animatingLottery.value = null
        localAnimationStartAt.value = 0
        shownLottery.value = result
      }
    }
  } catch (error) {
    if (requestSeq !== latestLotteryRequestSeq) return
    console.warn('load latest lottery failed', error)
  } finally {
    latestLotteryLoading = false
  }
}

const loadExternalLotteries = async () => {
  if (externalLotteryLoading) return

  externalLotteryLoading = true
  const [macauResult, hongkongResult] = await Promise.allSettled([
    getPublicExternalLottery(2),
    getPublicExternalLottery(1),
  ])

  try {
    externalLotteries.value = {
      macau:
        macauResult.status === 'fulfilled'
          ? macauResult.value
          : {
              ...(externalLotteries.value.macau || {}),
              status: 'error',
              title: '澳彩加载失败',
            },
      hongkong:
        hongkongResult.status === 'fulfilled'
          ? hongkongResult.value
          : {
              ...(externalLotteries.value.hongkong || {}),
              status: 'error',
              title: '港彩加载失败',
            },
    }

    if (macauResult.status === 'rejected') {
      console.warn('load macau lottery failed', macauResult.reason)
    }
    if (hongkongResult.status === 'rejected') {
      console.warn('load hongkong lottery failed', hongkongResult.reason)
    }
  } finally {
    externalLotteryLoading = false
  }
}

const refreshLotteryData = () => {
  loadLatestLottery()
}

const refreshExternalLotteries = () => {
  loadExternalLotteries()
}

const loadHomeArticles = async (reset = false) => {
  if (loadingMore.value && !reset) return
  if (!reset && homeImages.value.length >= articleMaxCount) return

  loadingMore.value = true
  articleErrorText.value = ''

  try {
    const currentPage = reset ? 1 : articlePage.value
    const result = await getPublicArticleList({
      page: currentPage,
      pageSize: articlePageSize,
      category: '首页',
    })
    const items = Array.isArray(result?.items) ? result.items.map(normalizeHomeArticle) : []

    articleTotal.value = Number(result?.total) || 0
    const nextItems = reset ? items : [...homeImages.value, ...items]
    homeImages.value = nextItems.slice(0, articleMaxCount)
    articlePage.value = currentPage + 1
  } catch (error) {
    console.error('加载首页瀑布流失败:', error)
    articleErrorText.value = '首页内容加载失败，请稍后再试'
  } finally {
    loadingMore.value = false
  }
}

watchEffect(() => {
  document.title = '马彩 - 金算盘论坛'
})

onMounted(() => {
  refreshLotteryData()
  refreshExternalLotteries()
  loadHomeArticles(true)
  timer = window.setInterval(() => {
    now.value = new Date(getNowMs())
    primeDrawAnimationIfNeeded()
    completeLotteryAnimationIfNeeded()
  }, 120)
  lotteryTimer = window.setInterval(refreshLotteryData, 3000)
  externalLotteryTimer = window.setInterval(refreshExternalLotteries, EXTERNAL_LOTTERY_POLL_MS)
  window.addEventListener('scroll', handleScroll, { passive: true })
  // 轮播定时器：每 4 秒切换一张
  if (bannerImages.length > 1) {
    bannerTimer = window.setInterval(() => {
      currentBanner.value = (currentBanner.value + 1) % bannerImages.length
    }, 4000)
  }
})

onUnmounted(() => {
  window.clearInterval(timer)
  window.clearInterval(lotteryTimer)
  window.clearInterval(externalLotteryTimer)
  window.clearInterval(bannerTimer)
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <main class="gallery-page">
    <header class="topbar">
      <div class="backup">
        <strong>福字专用网站</strong>
      </div>
      <h1>马彩</h1>
      <div class="tools">
        <a href="#"><span>⌕</span>搜索</a>
        <a href="#"><span>↗</span>分享</a>
        <a href="#"><span>◔</span>客服</a>
      </div>
    </header>

    <!-- 轮播图：将图片放入 src/assets/banners/ 目录后重新编译即可自动显示 -->
    <section v-if="bannerImages.length" class="banner-carousel">
      <div class="banner-track" :style="{ transform: `translateX(-${currentBanner * 100}%)` }">
        <img
          v-for="(src, i) in bannerImages"
          :key="i"
          :src="src"
          class="banner-slide"
          :alt="`轮播图${i + 1}`"
        />
      </div>
      <div v-if="bannerImages.length > 1" class="banner-dots">
        <span
          v-for="(_, i) in bannerImages"
          :key="i"
          :class="['banner-dot', { active: currentBanner === i }]"
          @click="currentBanner = i"
        />
      </div>
    </section>



    <div class="red-strip">49彩票49.com由马彩49TK.com全程担保，请广大彩民放心投注。</div>

    <section class="notice">
      <span>🔊 公告：</span>
      <div class="notice-marquee">
        <p>1.首页新增高手之家。　2.首页新增开奖直播。　3.修复部分资料显示。</p>
      </div>
    </section>

    <section class="market-tabs">
      <button
        v-for="source in lotterySources"
        :key="source.key"
        :class="{ active: selectedLotteryKey === source.key }"
        type="button"
        @click="selectedLotteryKey = source.key"
      >
        <strong>{{ source.label }}</strong>
        <span>{{ formatSourceSub(source) }}</span>
      </button>
    </section>

    <button class="live-toggle" type="button">展开直播</button>

    <section class="draw-card">
      <div class="draw-head">
        <span>{{ currentIssueText }}</span>
        <strong>{{ activeCountdownText }}</strong>
        <RouterLink :to="historyRoute">查看历史记录</RouterLink>
      </div>

      <div class="balls-line">
        <template v-for="(ball, index) in displayBalls" :key="index">
          <div class="ball-wrap">
            <div
              :class="[
                'ball',
                `ball--${ball.color}`,
                {
                  'is-card-back': ball.cardBack,
                  'is-flipping': ball.flipping,
                  'is-shuffling': ball.shuffling,
                },
              ]"
            >
              <div class="ball-card">
                <div class="ball-face ball-face--back">
                  <span class="ball-bg-char">{{ ball.bgChar || '开' }}</span>
                </div>
                <div class="ball-face ball-face--front">
                  <span class="ball-num">{{ ball.number }}</span>
                </div>
              </div>
            </div>
            <span v-if="ball.label">{{ ball.label }}</span>
          </div>
          <b v-if="index === 5" class="plus">+</b>
        </template>
      </div>

      <p class="next-issue">{{ nextIssueText }}</p>
    </section>

    <section class="website-links">
      <div class="website-panel">
        <h2 class="website-title">马彩预测网</h2>

        <section class="website-body clearfix">
          <a
            v-for="item in siteLinks"
            :key="item.name"
            class="website-item"
            :href="item.url || undefined"
            target="_blank"
            rel="noopener noreferrer"
          >
            <div>
              <p>{{ item.name }}</p>
            </div>
          </a>
        </section>

        <div class="website-domain-box">
          <p class="website-domain-label">请记住以下网站域名</p>
          <p
            v-for="(row, index) in siteDomainRows"
            :key="index"
            class="website-domain-row"
          >
            <span
              v-for="domain in row"
              :key="domain"
              class="website-domain-item"
            >
              {{ domain }}
            </span>
          </p>
        </div>
      </div>
    </section>

    <button class="add-button" type="button">+</button>

    <section class="feature-grid">
      <a
        v-for="item in featureItems"
        :key="item.label"
        class="feature-item"
        :href="item.label === '高手之家' ? '/gszj' : '#'"
      >
        <span class="feature-icon" :style="{ background: item.color }">
          {{ item.icon }}
          <i v-if="item.hot">热</i>
        </span>
        <strong>{{ item.label }}</strong>
      </a>
    </section>

    <section role="feed" class="van-list waterfall-feed">
      <div class="body-image-bd">
        <div class="waterfall van-row">
          <div class="van-col van-col--12 waterfall-col waterfall-col--left">
            <RouterLink
              v-for="item in leftImages"
              :key="item.id"
              class="image-item"
              :to="{ name: 'find-detail', params: { articleId: item.id } }"
            >
              <div class="image-item-image">
                <div v-if="item.src" class="van-image">
                  <img class="van-image__img" :src="item.src" :alt="item.title" loading="lazy" />
                </div>
                <div v-else class="van-image image-fallback">
                  <strong>{{ item.title }}</strong>
                  <span>{{ item.summary || '金算盘论坛' }}</span>
                </div>
              </div>
              <div class="image-item-title">{{ item.title }}</div>
            </RouterLink>
          </div>

          <div class="van-col van-col--12 waterfall-col waterfall-col--right">
            <RouterLink
              v-for="item in rightImages"
              :key="item.id"
              class="image-item"
              :to="{ name: 'find-detail', params: { articleId: item.id } }"
            >
              <div class="image-item-image">
                <div v-if="item.src" class="van-image">
                  <img class="van-image__img" :src="item.src" :alt="item.title" loading="lazy" />
                </div>
                <div v-else class="van-image image-fallback">
                  <strong>{{ item.title }}</strong>
                  <span>{{ item.summary || '金算盘论坛' }}</span>
                </div>
              </div>
              <div class="image-item-title">{{ item.title }}</div>
            </RouterLink>
          </div>
        </div>
      </div>

      <div class="van-list__placeholder">
        <span v-if="articleErrorText">{{ articleErrorText }}</span>
        <span v-else-if="loadingMore">加载中...</span>
        <span v-else-if="!homeImages.length">暂无首页内容</span>
        <span v-else-if="hasMoreImages">下拉加载更多</span>
        <span v-else>没有更多了</span>
      </div>
    </section>

    <nav class="bottom-nav">
      <component
        v-for="(item, index) in bottomTabs"
        :is="index === 1 || index === 2 || index === 3 || index === 4 ? 'RouterLink' : 'a'"
        :key="item.label"
        :to="index === 1 ? '/find' : index === 2 ? '/gszj' : index === 3 ? '/treasure' : index === 4 ? '/my' : undefined"
        :class="{ active: item.active, center: item.center }"
        :href="index === 1 || index === 2 || index === 3 || index === 4 ? undefined : '#'"
      >
        <span>{{ item.icon }}</span>
        <strong>{{ item.label }}</strong>
      </component>
    </nav>
  </main>
</template>

<style scoped>
.banner-carousel {
  position: relative;
  overflow: hidden;
  width: 100%;
  background: #000;
}

.banner-track {
  display: flex;
  transition: transform 0.5s ease;
  will-change: transform;
}

.banner-slide {
  flex: 0 0 100%;
  width: 100%;
  height: clamp(200px, 56vw, 420px);
  object-fit: cover;
  display: block;
}

.banner-dots {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
}

.banner-dot {
  width: 7px;
  height: 7px;
  border-radius: 999px;
  background: rgb(255 255 255 / 55%);
  cursor: pointer;
  transition: background 0.3s, width 0.3s;
}

.banner-dot.active {
  width: 18px;
  background: #fff;
}

.gallery-page {
  min-height: 100vh;
  padding-bottom: 80px;
  background: #f5f5f5;
  color: #222;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

a {
  color: inherit;
  text-decoration: none;
}

.topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  display: grid;
  grid-template-columns: 122px 1fr 152px;
  align-items: center;
  height: 70px;
  padding: 0 12px;
  background: #fff;
  box-shadow: 0 1px 0 rgb(0 0 0 / 8%);
}

.backup {
  display: grid;
  justify-items: center;
  width: 118px;
  padding: 4px 0;
  border-radius: 5px;
  background: #08c76b;
  color: #fff;
  line-height: 1.25;
}

.backup strong {
  font-size: 18px;
}

.backup span {
  font-size: 14px;
  font-weight: 700;
}

.topbar h1 {
  margin: 0;
  font-size: 26px;
  font-weight: 500;
  text-align: center;
}

.tools {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.tools a {
  display: grid;
  justify-items: center;
  color: #050505;
  font-size: 13px;
  line-height: 1;
}

.tools span {
  margin-bottom: 4px;
  font-size: 24px;
  line-height: 1;
}



.red-strip {
  padding: 5px 8px;
  background: #ff2832;
  color: #fff;
  font-size: 14px;
  font-weight: 900;
  text-align: center;
}

.notice {
  display: flex;
  align-items: center;
  height: 38px;
  padding: 0 12px;
  background: #fff;
  color: #444;
  font-size: 14px;
  white-space: nowrap;
}

.notice span {
  color: #555;
  flex: none;
}

.notice-marquee {
  overflow: hidden;
}

.notice-marquee p {
  margin: 0;
  animation: marquee 18s linear infinite;
}

.market-tabs {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 8px;
  padding: 10px 12px 0;
  background: #f5f5f5;
}

.market-tabs button {
  display: grid;
  place-items: center;
  height: 68px;
  min-width: 0;
  border: 0;
  border-radius: 8px 8px 0 0;
  background: #fff;
  color: #333;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
}

.market-tabs button.active {
  background: #08c76b;
  color: #fff;
}

.market-tabs strong,
.market-tabs span {
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.market-tabs span {
  margin-top: 5px;
  font-size: 12px;
  font-weight: 500;
}

.live-toggle {
  display: block;
  width: 164px;
  height: 38px;
  margin: 14px auto 0;
  border: 0;
  background: #09c86d;
  color: #fff;
  clip-path: polygon(0 0, 100% 0, 84% 100%, 16% 100%);
  font-size: 16px;
}

.draw-card {
  position: relative;
  margin: 0 12px;
  padding: 12px 8px 10px;
  border: 1px solid #13c46f;
  border-radius: 3px;
  background: #fff;
  overflow: hidden;
}

.draw-head {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 10px;
  align-items: center;
  font-size: 16px;
}

.draw-head span {
  color: #08b861;
}

.draw-head strong {
  color: #ff1111;
  font-weight: 500;
}

.draw-head a {
  color: #0abb65;
}

.balls-line {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr)) auto minmax(0, 1fr);
  align-items: flex-start;
  gap: clamp(2px, 1.2vw, 8px);
  margin-top: 16px;
  width: 100%;
  min-width: 0;
}

.ball-wrap {
  display: grid;
  justify-items: center;
  gap: 4px;
  min-width: 0;
}

.ball {
  position: relative;
  display: grid;
  place-items: center;
  width: clamp(44px, 11.5vw, 64px);
  height: clamp(58px, 15vw, 84px);
  perspective: 520px;
  color: #666;
  font-size: clamp(24px, 6vw, 36px);
  font-weight: 900;
  line-height: 1;
  letter-spacing: 0;
  box-sizing: border-box;
}

.ball-card {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform 0.72s cubic-bezier(0.2, 0.78, 0.22, 1);
}

.ball.is-card-back .ball-card {
  transform: rotateY(180deg);
}

.ball.is-flipping .ball-card {
  animation: flip-card 0.78s cubic-bezier(0.2, 0.78, 0.22, 1) both;
}

.ball.is-shuffling .ball-card {
  animation: card-shuffle 0.72s ease-in-out infinite;
}

.ball-face {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 4px solid #bbb;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 6px 12px rgb(0 0 0 / 12%);
  backface-visibility: hidden;
  box-sizing: border-box;
}

.ball-face--front {
  color: #666;
}

.ball-face--back {
  border-color: #13c46f;
  background:
    linear-gradient(135deg, rgb(255 255 255 / 18%) 25%, transparent 25%) 0 0 / 10px 10px,
    linear-gradient(225deg, rgb(255 255 255 / 16%) 25%, transparent 25%) 0 0 / 10px 10px,
    linear-gradient(145deg, #16cc72, #07994e);
  color: #fff;
  transform: rotateY(180deg);
}

.ball-num {
  position: relative;
  z-index: 1;
  width: 100%;
  text-align: center;
}

.ball-bg-char {
  font-size: clamp(20px, 5.2vw, 32px);
  font-weight: 900;
  color: #fff;
  text-shadow: 0 2px 4px rgb(0 0 0 / 18%);
  pointer-events: none;
  user-select: none;
  letter-spacing: 0;
}

.ball--red .ball-face--front {
  border: 4px solid #ff313b;
}

.ball--blue .ball-face--front {
  border: 4px solid #2e99ff;
}

.ball--green .ball-face--front {
  border: 4px solid #13c65d;
}

.ball--gray .ball-face--front {
  border: 4px solid #bbb;
  color: #bbb;
}

.ball-wrap > span {
  max-width: 100%;
  overflow: hidden;
  font-size: clamp(11px, 3vw, 15px);
  line-height: 1.1;
  text-align: center;
  white-space: nowrap;
}

@keyframes flip-card {
  0% {
    transform: rotateY(180deg) scale(0.96);
  }
  48% {
    transform: rotateY(90deg) scale(1.08);
  }
  100% {
    transform: rotateY(0) scale(1);
  }
}

@keyframes card-shuffle {
  0%,
  100% {
    transform: rotateY(180deg) translateY(0);
  }
  50% {
    transform: rotateY(180deg) translateY(-4px);
  }
}

.plus {
  align-self: center;
  margin-top: clamp(10px, 3vw, 17px);
  color: #777;
  font-size: clamp(18px, 5vw, 28px);
  font-weight: 500;
}

.next-issue {
  margin: 6px 0 0;
  color: #ff1111;
  font-size: 14px;
  text-align: center;
}



.website-links {
  padding: 14px 12px 0;
  background: #f5f5f5;
}

.website-panel {
  overflow: hidden;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 1px 0 rgb(0 0 0 / 2%);
}

.website-title {
  margin: 0;
  padding: 14px 14px 10px;
  color: #c62828;
  font-size: clamp(18px, 4.8vw, 24px);
  font-weight: 700;
  line-height: 1.2;
  text-align: center;
}

.website-domain-box {
  margin: 0 14px 14px;
  padding: 12px 14px;
  border-radius: 6px;
  border: 1px solid #f0f0f0;
  background: #fafafa;
  text-align: center;
}

.website-domain-label {
  margin: 0 0 8px;
  color: #666;
  font-size: 14px;
  line-height: 1.5;
}

.website-domain-row {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin: 0;
  color: #c62828;
  font-size: 15px;
  line-height: 1.8;
}

.website-domain-item {
  min-width: min(42vw, 180px);
  flex: 0 1 auto;
  font-weight: 600;
  text-align: center;
}

.website-body {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: clamp(12px, 3.8vw, 24px) clamp(8px, 2.8vw, 16px);
  padding: 0 14px 14px;
}

.website-item {
  display: block;
  min-width: 0;
}

.website-item > div {
  display: grid;
  place-items: center;
  height: clamp(40px, 11vw, 48px);
  min-width: 0;
  border-radius: 6px;
  border: 1px solid #f0f0f0;
  background: #fafafa;
}

.website-item p {
  max-width: 100%;
  margin: 0;
  overflow: hidden;
  color: #333;
  font-size: clamp(13px, 4vw, 18px);
  line-height: 1.1;
  text-align: center;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.add-button {
  position: fixed;
  right: 16px;
  bottom: 92px;
  z-index: 20;
  width: 52px;
  height: 52px;
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #14c878;
  font-size: 34px;
  box-shadow: 0 2px 14px rgb(0 0 0 / 15%);
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 26px 0;
  padding: 26px 8px 10px;
  border-top: 7px solid #efefef;
  background: #fff;
}

.feature-item {
  display: grid;
  justify-items: center;
  gap: 12px;
}

.feature-icon {
  position: relative;
  display: grid;
  place-items: center;
  width: 62px;
  height: 62px;
  border-radius: 999px;
  color: #fff;
  font-size: 27px;
  font-weight: 900;
}

.feature-icon i {
  position: absolute;
  right: -4px;
  top: 10px;
  display: grid;
  place-items: center;
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: #ff6a2a;
  font-size: 12px;
  font-style: normal;
}

.feature-item strong {
  color: #4a4a4a;
  font-size: 15px;
  font-weight: 500;
}

.waterfall-feed {
  padding: 10px 10px 20px;
  background: #f5f5f5;
}

.body-image-bd {
  width: 100%;
}

.waterfall {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.waterfall-col {
  display: grid;
  align-content: start;
  gap: 10px;
  min-width: 0;
}

.image-item {
  display: block;
  overflow: hidden;
  border-radius: 7px;
  background: #fff;
  box-shadow: 0 1px 6px rgb(0 0 0 / 8%);
}

.image-item-image {
  overflow: hidden;
  background: linear-gradient(135deg, #f1f1f1, #ffffff);
}

.van-image {
  width: 100%;
}

.van-image__img {
  display: block;
  width: 100%;
  min-height: 132px;
  object-fit: cover;
}

.image-fallback {
  display: grid;
  align-content: center;
  min-height: 132px;
  padding: 14px;
  background: linear-gradient(135deg, #fff0d2, #eaf7ff);
  color: #d60012;
  text-align: center;
}

.image-fallback strong {
  font-size: 18px;
  line-height: 1.25;
}

.image-fallback span {
  margin-top: 8px;
  color: #777;
  font-size: 13px;
  line-height: 1.35;
}

.image-item-title {
  padding: 8px 6px 9px;
  color: #333;
  font-size: clamp(15px, 4.2vw, 18px);
  line-height: 1.15;
  text-align: center;
  white-space: nowrap;
}

.van-list__placeholder {
  display: grid;
  place-items: center;
  min-height: 42px;
  color: #999;
  font-size: 14px;
}

.bottom-nav {
  position: fixed;
  left: 50%;
  bottom: 0;
  z-index: 40;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  width: min(100%, 600px);
  height: 78px;
  transform: translateX(-50%);
  border-top: 1px solid #e5e5e5;
  background: #fff;
  box-shadow: 0 -1px 8px rgb(0 0 0 / 6%);
}

.bottom-nav a {
  position: relative;
  display: grid;
  align-content: center;
  justify-items: center;
  gap: 4px;
  color: #777;
  font-size: 16px;
}

.bottom-nav span {
  color: #16c878;
  font-size: 26px;
  line-height: 1;
}

.bottom-nav strong {
  font-weight: 500;
}

.bottom-nav .active {
  color: #10c873;
}

.bottom-nav .center span {
  display: grid;
  place-items: center;
  width: 56px;
  height: 56px;
  margin-top: -28px;
  border: 6px solid #fff;
  border-radius: 999px;
  background: #15c872;
  color: #fff;
  font-size: 30px;
  box-shadow: 0 -2px 7px rgb(0 0 0 / 9%);
}

@keyframes marquee {
  from {
    transform: translateX(40%);
  }
  to {
    transform: translateX(-100%);
  }
}

@media (min-width: 601px) {
  .gallery-page {
    max-width: 600px;
    margin: 0 auto;
  }

  .add-button {
    right: calc((100vw - 600px) / 2 + 16px);
  }
}

@media (max-width: 560px) {

  .topbar {
    grid-template-columns: 130px 1fr 152px;
    height: 70px;
  }
}

@media (max-width: 430px) {
  .topbar {
    grid-template-columns: 106px 1fr 126px;
    padding: 0 8px;
  }

  .backup {
    width: 100px;
  }

  .backup strong {
    font-size: 14px;
  }

  .backup span,
  .tools a {
    font-size: 11px;
  }

  .topbar h1 {
    font-size: 22px;
  }

  .tools {
    gap: 8px;
  }

  .tools span {
    font-size: 21px;
  }



  .draw-head {
    font-size: 14px;
  }

  .ball {
    width: clamp(34px, 9vw, 42px);
    height: clamp(46px, 12vw, 56px);
    font-size: clamp(17px, 4.8vw, 22px);
  }

  .ball-wrap span {
    font-size: clamp(10px, 3.3vw, 13px);
  }

  .website-body {
    gap: clamp(10px, 5.5vw, 24px) clamp(8px, 3.8vw, 16px);
    padding-inline: clamp(10px, 4.8vw, 18px);
  }

  .website-item > div {
    height: clamp(38px, 12vw, 48px);
  }

  .website-item p {
    font-size: clamp(12px, 4vw, 16px);
  }

  .feature-icon {
    width: 54px;
    height: 54px;
  }

  .feature-item strong {
    font-size: 13px;
  }
}
</style>
