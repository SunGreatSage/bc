import {
  getAllPublicArticles,
  getPublicArticleCategoryList,
} from '../api/article'
import {
  recentUpdates as fallbackRecentUpdates,
  sections as fallbackSections,
} from '../data/homeData'

export const PREVIEW_LIMIT = 8
export const RECENT_LIMIT = 14

const ARTICLE_PAGE_SIZE = 200

let cachedPayload = null
let inflightPromise = null

const pad = (value) => String(value).padStart(2, '0')

export const formatMonthDay = (timestamp) => {
  const value = Number(timestamp)
  if (!value) {
    return '--'
  }

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) {
    return '--'
  }

  return `${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

export const formatSyncTime = (date = new Date()) =>
  `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`

const escapeHtml = (value) =>
  String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')

export const stripHtml = (value) =>
  String(value ?? '')
    .replace(/<style[\s\S]*?<\/style>/gi, ' ')
    .replace(/<script[\s\S]*?<\/script>/gi, ' ')
    .replace(/<[^>]+>/g, ' ')
    .replace(/&nbsp;/gi, ' ')
    .replace(/&amp;/gi, '&')
    .replace(/&lt;/gi, '<')
    .replace(/&gt;/gi, '>')
    .replace(/&#39;/g, "'")
    .replace(/&quot;/g, '"')
    .replace(/\s+/g, ' ')
    .trim()

const buildFallbackContent = (title, summary, path) => {
  const copy =
    summary || `${title} 暂时只提供首页示例信息，正文内容会在接入真实文章接口后展示。`

  return [
    `<p>${escapeHtml(copy)}</p>`,
    '<p>当前详情页已经打通跳转逻辑；当后端实时文章可用时，这里会自动展示完整正文。</p>',
    path ? `<p class="article-inline-meta">原始路径：${escapeHtml(path)}</p>` : '',
  ]
    .filter(Boolean)
    .join('')
}

const sortSectionArticles = (left, right) => {
  const sortDiff = (left.sort ?? 0) - (right.sort ?? 0)
  if (sortDiff !== 0) {
    return sortDiff
  }

  const createdDiff = (right.createdAt ?? 0) - (left.createdAt ?? 0)
  if (createdDiff !== 0) {
    return createdDiff
  }

  return (right.id ?? 0) - (left.id ?? 0)
}

const sortRecentArticles = (left, right) => {
  const createdDiff = (right.createdAt ?? 0) - (left.createdAt ?? 0)
  if (createdDiff !== 0) {
    return createdDiff
  }

  const idDiff = (right.id ?? 0) - (left.id ?? 0)
  if (idDiff !== 0) {
    return idDiff
  }

  return (left.sort ?? 0) - (right.sort ?? 0)
}

const normalizeLiveArticle = (article) => {
  const category = `${article?.category ?? ''}`.trim() || '未分类'
  const title = `${article?.title ?? ''}`.trim() || '未命名文章'
  const summary = stripHtml(article?.content).slice(0, 120)

  return {
    key: `article-${article?.id ?? title}`,
    routeId: String(article?.id ?? title),
    id: Number(article?.id) || 0,
    section: category,
    date: formatMonthDay(article?.createdAt),
    title,
    summary: summary || '当前文章暂未提供摘要内容。',
    metaText: `类目：${category} · 排序 ${Number(article?.sort) || 0}`,
    createdAt: Number(article?.createdAt) || 0,
    sort: Number(article?.sort) || 0,
    source: 'api',
    contentHtml:
      article?.content ||
      `<p>${escapeHtml(summary || '当前文章暂未提供正文内容。')}</p>`,
  }
}

const normalizeFallbackArticle = (article, sectionId, sectionTitle, index) => {
  const title = article?.title || '未命名文章'
  const summary =
    article?.summary ||
    '当前展示的是首页示例内容，接口异常或没有数据时会自动回退到这里。'

  return {
    key: `fallback-${sectionId}-${index}`,
    routeId: `demo-${sectionId}-${index}`,
    id: 0,
    section: article?.section || sectionTitle,
    date: article?.date || '--',
    title,
    summary,
    metaText: article?.path
      ? `原始路径：${article.path}`
      : `类目：${article?.section || sectionTitle}`,
    createdAt: 0,
    sort: 0,
    source: 'fallback',
    contentHtml: buildFallbackContent(title, summary, article?.path),
  }
}

const createFallbackSections = () =>
  fallbackSections.map((section) => ({
    id: section.id,
    title: section.title,
    label: section.label,
    description: section.description,
    articleCount: section.items.length,
    items: section.items.map((article, index) =>
      normalizeFallbackArticle(article, section.id, section.title, index),
    ),
  }))

const buildFallbackPayload = (state, message) => {
  const sections = createFallbackSections()
  const allArticles = sections.flatMap((section) => section.items)
  const articleMap = new Map(
    allArticles.map((article) => [`${article.section}::${article.title}`, article]),
  )

  const updates = fallbackRecentUpdates.map((article, index) => {
    const matched = articleMap.get(
      `${article?.section || '最近更新'}::${article?.title || ''}`,
    )

    if (matched) {
      return matched
    }

    return normalizeFallbackArticle(
      article,
      'recent',
      article?.section || '最近更新',
      index,
    )
  })

  return {
    state,
    sourceMessage: message,
    syncTimeLabel: formatSyncTime(),
    sections,
    updates,
    allArticles,
    totalArticleCount: sections.reduce(
      (total, section) => total + section.articleCount,
      0,
    ),
  }
}

const buildLiveSections = (categories, articles) => {
  const articleMap = new Map()

  articles.forEach((article) => {
    const items = articleMap.get(article.section) ?? []
    items.push(article)
    articleMap.set(article.section, items)
  })

  return categories.map((category) => {
    const items = (articleMap.get(category.name) ?? []).slice().sort(sortSectionArticles)
    const previewCount = Math.min(PREVIEW_LIMIT, items.length)

    return {
      id: `channel-${category.id}`,
      title: category.name,
      label: '实时类目',
      description:
        category.articleCount > 0
          ? `当前类目共有 ${category.articleCount} 篇已发布文章，首页默认展示前 ${previewCount} 篇。`
          : '当前类目暂无已发布文章。',
      articleCount: Number(category.articleCount) || items.length,
      items,
    }
  })
}

const buildLivePayload = (categories, articleResult) => {
  const categoryList = Array.isArray(categories) ? categories : []
  const liveArticles = (articleResult?.items ?? []).map(normalizeLiveArticle)

  if (!categoryList.length || !liveArticles.length) {
    return null
  }

  return {
    state: 'live',
    sourceMessage:
      '首页和详情页都已切换到 gooze-vben-api 的实时类目与文章数据。',
    syncTimeLabel: formatSyncTime(),
    sections: buildLiveSections(categoryList, liveArticles),
    updates: liveArticles.slice().sort(sortRecentArticles).slice(0, RECENT_LIMIT),
    allArticles: liveArticles.slice().sort(sortRecentArticles),
    totalArticleCount:
      Number(articleResult?.total) ||
      categoryList.reduce(
        (total, category) => total + (Number(category.articleCount) || 0),
        0,
      ),
  }
}

export const resolveStatusTone = (state) => {
  switch (state) {
    case 'live':
      return 'success'
    case 'loading':
      return 'pending'
    default:
      return 'muted'
  }
}

export const resolveStatusLabel = (state) => {
  switch (state) {
    case 'live':
      return '类目 + 文章实时数据'
    case 'fallback-empty':
      return '接口暂无内容'
    case 'fallback-error':
      return '接口请求失败'
    default:
      return '接口同步中'
  }
}

export async function loadPublicContent(options = {}) {
  const force = Boolean(options.force)

  if (cachedPayload && !force) {
    return cachedPayload
  }

  if (inflightPromise && !force) {
    return inflightPromise
  }

  inflightPromise = (async () => {
    try {
      const [categories, articleResult] = await Promise.all([
        getPublicArticleCategoryList(),
        getAllPublicArticles({ pageSize: ARTICLE_PAGE_SIZE }),
      ])

      const livePayload = buildLivePayload(categories, articleResult)
      if (livePayload) {
        cachedPayload = livePayload
        return livePayload
      }

      cachedPayload = buildFallbackPayload(
        'fallback-empty',
        '类目接口或文章接口已经接通，但当前没有可展示的已发布内容，先显示本地示例数据。',
      )
      return cachedPayload
    } catch (error) {
      cachedPayload = buildFallbackPayload(
        'fallback-error',
        `类目或文章接口请求失败：${error?.message || '请确认后端服务已启动。'}，当前已自动回退到本地示例内容。`,
      )
      return cachedPayload
    } finally {
      inflightPromise = null
    }
  })()

  return inflightPromise
}

export function getFallbackArticleDetailByRouteId(routeId) {
  const payload =
    cachedPayload && cachedPayload.state !== 'live'
      ? cachedPayload
      : buildFallbackPayload(
          'fallback-empty',
          '当前详情页使用本地示例数据展示，因为实时文章接口暂不可用。',
        )

  const article = payload.allArticles.find(
    (item) => item.routeId === String(routeId),
  )

  if (!article) {
    return {
      payload,
      article: null,
      prevArticle: null,
      nextArticle: null,
    }
  }

  const section = payload.sections.find((item) => item.title === article.section)
  const items = section?.items ?? []
  const articleIndex = items.findIndex((item) => item.routeId === article.routeId)

  return {
    payload,
    article,
    prevArticle: articleIndex > 0 ? items[articleIndex - 1] : null,
    nextArticle:
      articleIndex >= 0 && articleIndex < items.length - 1
        ? items[articleIndex + 1]
        : null,
  }
}
