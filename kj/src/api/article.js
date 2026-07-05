const runtimeEnv = window.__APP_ENV__ ?? {}
const rawApiBaseUrl = `${runtimeEnv.VITE_API_BASE_URL ?? import.meta.env.VITE_API_BASE_URL ?? '/api/v1'}`.trim()
const apiBaseUrl = rawApiBaseUrl.replace(/\/$/, '')
const rawHomeApiBaseUrl = `${
  runtimeEnv.VITE_HOME_API_BASE_URL ??
  import.meta.env.VITE_HOME_API_BASE_URL ??
  'http://8.220.222.43:8666'
}`.trim()
const homeApiBaseUrl = rawHomeApiBaseUrl.replace(/\/$/, '')
const EXTERNAL_LOTTERY_TIMEOUT_MS = 35000
const HOME_LOTTERY_GID = 200
const HOME_LOTTERY_PLATE_CODE = 'A'

const redNumbers = new Set([1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46])
const blueNumbers = new Set([3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48])

const getBallColor = (number) => {
  const value = Number(number)
  if (redNumbers.has(value)) return 'red'
  if (blueNumbers.has(value)) return 'blue'
  return 'green'
}

const createUrl = (path, params = {}, baseUrl = apiBaseUrl) => {
  const url = new URL(`${baseUrl}${path}`, window.location.origin)

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null || value === '') {
      return
    }

    url.searchParams.set(key, value)
  })

  return url
}

const readJson = async (path, params = {}, options = {}) => {
  const timeoutMs = Number(options.timeoutMs) > 0 ? Number(options.timeoutMs) : 0
  const controller = timeoutMs > 0 ? new AbortController() : null
  const timeoutId =
    controller && timeoutMs > 0
      ? window.setTimeout(() => {
          controller.abort()
        }, timeoutMs)
      : 0

  let response
  try {
    response = await fetch(createUrl(path, params), {
      cache: 'no-store',
      headers: {
        Accept: 'application/json',
      },
      signal: controller?.signal,
    })
  } catch (error) {
    if (error?.name === 'AbortError') {
      throw new Error(`Request timeout after ${timeoutMs}ms`)
    }
    throw error
  } finally {
    if (timeoutId) {
      window.clearTimeout(timeoutId)
    }
  }

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`)
  }

  const payload = await response.json()

  if (payload?.code !== 200) {
    throw new Error(payload?.msg || 'API request failed')
  }

  const data = payload?.data
  if (data && typeof data === 'object' && !Array.isArray(data)) {
    const serverNowMs =
      Number(payload?.serverTime) ||
      (Number(payload?.nowTime) > 0 ? Number(payload.nowTime) * 1000 : 0)
    if (serverNowMs > 0) {
      data._serverNowMs = serverNowMs
    }
  }

  return data
}

const readHomeJson = async (path, params = {}) => {
  const response = await fetch(createUrl(path, params, homeApiBaseUrl), {
    cache: 'no-store',
    headers: {
      Accept: 'application/json',
    },
  })

  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`)
  }

  const payload = await response.json()
  if (payload?.code !== 1) {
    throw new Error(payload?.msg || 'Home lottery API request failed')
  }

  return payload?.data
}

const parseHomeTime = (value) => {
  const text = `${value ?? ''}`.trim()
  if (!text) return 0
  const normalized = text.includes('T') ? text : text.replace(' ', 'T')
  const time = new Date(normalized).getTime()
  return Number.isNaN(time) ? 0 : time
}

const normalizeHomeBall = (item) => {
  const number = `${item?.num ?? item?.number ?? item ?? ''}`.padStart(2, '0')
  const zodiac = `${item?.zodiac ?? ''}`.trim()
  const wuxing = `${item?.wuxing ?? ''}`.trim()
  const display = [zodiac, wuxing].filter(Boolean).join('/')

  return {
    number,
    zodiac,
    wuxing,
    display,
    color: item?.color || getBallColor(number),
  }
}

const normalizeHomeLotteryRecord = (item) => {
  const qishu = `${item?.qishu ?? item?.issue ?? ''}`.trim()
  const drawTime = parseHomeTime(item?.draw_time || item?.kj_time || item?.date)
  const numbers = Array.isArray(item?.display_numbers) && item.display_numbers.length > 0
    ? item.display_numbers
    : item?.numbers

  return {
    id: qishu || item?.id,
    issue: Number(qishu) || 0,
    issueText: qishu,
    date: item?.date_display || item?.date || '',
    balls: Array.isArray(numbers) ? numbers.map(normalizeHomeBall) : [],
    drawTime,
    status: item?.has_result === false ? 'pending' : 'drawn',
  }
}

const getHomeServerNowMs = (currentPeriod, currentResult, drawTime) => {
  const seconds = Number(
    currentResult?.seconds_to_kj ??
      currentResult?.seconds_to_draw ??
      currentPeriod?.seconds_to_kj ??
      currentPeriod?.seconds_to_draw,
  )
  if (Number.isFinite(seconds) && seconds >= 0 && drawTime > 0) {
    return drawTime - seconds * 1000
  }
  return Date.now()
}

export async function getPublicArticleList(params = {}) {
  return (await readJson('/public/article/list', params)) ?? {
    items: [],
    total: 0,
  }
}

export async function getPublicArticleDetail(articleId) {
  return (
    await readJson(`/public/article/detail/${encodeURIComponent(articleId)}`)
  )
}

export async function getPublicArticleCategoryList() {
  return (await readJson('/public/article/category/list')) ?? []
}

export async function getPublicLotteryList(params = {}) {
  const currentPage = Number(params.page ?? params.currentPage) > 0 ? Number(params.page ?? params.currentPage) : 1
  const pageSize = Number(params.pageSize ?? params.limit) > 0 ? Number(params.pageSize ?? params.limit) : 10
  const result = await readHomeJson('/api/lottery_result/getResultList', {
    gid: HOME_LOTTERY_GID,
    page: currentPage,
    limit: pageSize,
    plate_code: HOME_LOTTERY_PLATE_CODE,
  })

  return {
    items: Array.isArray(result?.list) ? result.list.map(normalizeHomeLotteryRecord) : [],
    total: Number(result?.total) || 0,
    _serverNowMs: Date.now(),
  }
}

export async function getLegacyPublicLotteryList(params = {}) {
  return (await readJson('/public/lottery/list', params)) ?? {
    items: [],
    total: 0,
  }
}

export async function getPublicLotteryLatest() {
  const currentPeriod = await readHomeJson('/api/lottery_bet/getCurrentQishu', {
    gid: HOME_LOTTERY_GID,
    plate_code: HOME_LOTTERY_PLATE_CODE,
  })
  const currentQishu = `${currentPeriod?.qishu ?? ''}`.trim()
  const currentDrawTime = parseHomeTime(currentPeriod?.draw_time || currentPeriod?.kj_time)

  const [currentResult, latestList] = await Promise.all([
    currentQishu
      ? readHomeJson('/api/lottery_bet/getKjResult', {
          gid: HOME_LOTTERY_GID,
          qishu: currentQishu,
          plate_code: HOME_LOTTERY_PLATE_CODE,
        }).catch(() => null)
      : Promise.resolve(null),
    readHomeJson('/api/lottery_result/getResultList', {
      gid: HOME_LOTTERY_GID,
      page: 1,
      limit: 1,
      plate_code: HOME_LOTTERY_PLATE_CODE,
    }).catch(() => null),
  ])

  const currentNumbers =
    Array.isArray(currentResult?.display_numbers) && currentResult.display_numbers.length > 0
      ? currentResult.display_numbers
      : currentResult?.numbers
  const currentBalls = Array.isArray(currentNumbers)
    ? currentNumbers.map(normalizeHomeBall)
    : []
  const latestRecord = Array.isArray(latestList?.list) && latestList.list.length
    ? normalizeHomeLotteryRecord(latestList.list[0])
    : null

  const hasCurrentResult = currentBalls.length > 0
  const currentDetailBalls =
    hasCurrentResult && latestRecord?.issueText === currentQishu && latestRecord.balls.length
      ? latestRecord.balls
      : currentBalls
  const issueText = hasCurrentResult ? currentQishu : latestRecord?.issueText || currentQishu
  const drawTime = hasCurrentResult
    ? parseHomeTime(currentResult?.draw_time || currentResult?.kj_time || currentPeriod?.draw_time)
    : Number(latestRecord?.drawTime) || 0
  const nextIssueText = hasCurrentResult ? '' : currentQishu
  const nextDrawTime = hasCurrentResult ? 0 : currentDrawTime

  return {
    balls: hasCurrentResult ? currentDetailBalls : latestRecord?.balls || [],
    issue: Number(issueText) || 0,
    issueText,
    issueKey: Number(issueText) || 0,
    nextIssue: Number(nextIssueText) || 0,
    nextIssueText,
    nextDrawTime,
    drawTime,
    status: hasCurrentResult || latestRecord?.balls?.length ? 'drawn' : 'empty',
    updatedAt: Date.now(),
    year: 0,
    _serverNowMs: getHomeServerNowMs(currentPeriod, currentResult, currentDrawTime),
  }
}

export async function getPublicExternalLottery(lotteryType) {
  return (
    (await readJson('/public/lottery/external', { lotteryType }, { timeoutMs: EXTERNAL_LOTTERY_TIMEOUT_MS })) ?? {
      balls: [],
      issue: 0,
      status: 'empty',
      updatedAt: 0,
      year: new Date().getFullYear(),
    }
  )
}

export async function getPublicExternalLotteryList(params = {}) {
  return (await readJson('/public/lottery/external/list', params, { timeoutMs: EXTERNAL_LOTTERY_TIMEOUT_MS })) ?? {
    items: [],
    total: 0,
  }
}

export async function getPublicRandomJumpDomains() {
  return (
    (await readJson('/public/config/jump-domain/random')) ?? {
      domain: '',
      url: '',
      domains: [],
      urls: [],
      count: 0,
    }
  )
}

export async function getAllPublicArticles(options = {}) {
  const pageSize = Number(options.pageSize) > 0 ? Number(options.pageSize) : 200
  const firstPage = await getPublicArticleList({
    page: 1,
    pageSize,
  })

  const total = Number(firstPage?.total) || 0
  const items = Array.isArray(firstPage?.items) ? [...firstPage.items] : []
  const totalPages = total > 0 ? Math.ceil(total / pageSize) : 1

  if (totalPages <= 1) {
    return {
      total: total || items.length,
      items,
    }
  }

  const remainingPages = []
  for (let page = 2; page <= totalPages; page += 1) {
    remainingPages.push(
      getPublicArticleList({
        page,
        pageSize,
      }),
    )
  }

  const pageResults = await Promise.all(remainingPages)
  pageResults.forEach((result) => {
    if (Array.isArray(result?.items)) {
      items.push(...result.items)
    }
  })

  return {
    total: total || items.length,
    items,
  }
}
