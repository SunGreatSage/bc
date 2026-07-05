<script setup>
import { computed, ref, watch, watchEffect } from 'vue'
import { useRoute } from 'vue-router'
import { getPublicArticleDetail } from '../api/article'
import {
  formatMonthDay,
  formatSyncTime,
  getFallbackArticleDetailByRouteId,
  resolveStatusLabel,
  resolveStatusTone,
  stripHtml,
} from '../services/publicContent'

const route = useRoute()

const loading = ref(true)
const statusState = ref('loading')
const sourceMessage = ref('正在加载文章详情。')
const syncTimeLabel = ref('')
const article = ref(null)
const prevArticle = ref(null)
const nextArticle = ref(null)

const normalizeNavigationArticle = (item) => {
  if (!item) {
    return null
  }

  const category = `${item?.category ?? item?.section ?? ''}`.trim() || '未分类'

  return {
    routeId: String(item?.id ?? item?.routeId ?? ''),
    title: `${item?.title ?? ''}`.trim() || '未命名文章',
    section: category,
    date: formatMonthDay(item?.createdAt),
  }
}

const normalizeDetailArticle = (detail) => {
  const category = `${detail?.category ?? ''}`.trim() || '未分类'
  const summary = stripHtml(detail?.content).slice(0, 140)

  return {
    routeId: String(detail?.id ?? ''),
    id: Number(detail?.id) || 0,
    section: category,
    date: formatMonthDay(detail?.createdAt),
    title: `${detail?.title ?? ''}`.trim() || '未命名文章',
    summary: summary || '当前文章暂未提供摘要内容。',
    metaText: `类目：${category} · 排序 ${Number(detail?.sort) || 0}`,
    contentHtml: detail?.content || '<p>当前文章暂未提供正文内容。</p>',
    source: 'api',
  }
}

const loadArticle = async () => {
  loading.value = true
  article.value = null
  prevArticle.value = null
  nextArticle.value = null

  const routeId = String(route.params.articleId ?? '')

  if (!routeId) {
    statusState.value = 'fallback-error'
    sourceMessage.value = '当前详情链接无效。'
    syncTimeLabel.value = formatSyncTime()
    loading.value = false
    return
  }

  if (routeId.startsWith('demo-')) {
    const result = getFallbackArticleDetailByRouteId(routeId)
    statusState.value = result.payload.state
    sourceMessage.value =
      '当前详情页显示的是本地示例正文，因为实时文章接口暂不可用。'
    syncTimeLabel.value = result.payload.syncTimeLabel
    article.value = result.article
    prevArticle.value = result.prevArticle
    nextArticle.value = result.nextArticle
    loading.value = false
    return
  }

  try {
    const detail = await getPublicArticleDetail(routeId)

    article.value = normalizeDetailArticle(detail)
    prevArticle.value = normalizeNavigationArticle(detail?.prev)
    nextArticle.value = normalizeNavigationArticle(detail?.next)
    statusState.value = 'live'
    sourceMessage.value = '正文通过公开文章详情接口独立加载，不再依赖文章列表缓存。'
    syncTimeLabel.value = formatSyncTime()
  } catch (error) {
    statusState.value = 'fallback-error'
    sourceMessage.value = `文章详情接口请求失败：${error?.message || '请稍后重试。'}`
    syncTimeLabel.value = formatSyncTime()
  } finally {
    loading.value = false
  }
}

watch(
  () => route.params.articleId,
  () => {
    loadArticle()
  },
  { immediate: true },
)

watchEffect(() => {
  document.title = article.value
    ? `${article.value.title} - 热门资讯网`
    : '文章详情 - 热门资讯网'
})

const notFound = computed(() => !loading.value && !article.value)
const statusTone = computed(() => resolveStatusTone(statusState.value))
const statusLabel = computed(() => resolveStatusLabel(statusState.value))
const sourceBadge = computed(() =>
  article.value?.source === 'api' ? '实时正文' : '示例正文',
)
const adjacentCount = computed(() =>
  [prevArticle.value, nextArticle.value].filter(Boolean).length,
)
</script>

<template>
  <div class="page-shell article-shell">
    <header class="article-hero">
      <RouterLink class="back-link" :to="{ name: 'home' }">
        ← 返回首页
      </RouterLink>

      <div class="article-hero__meta">
        <span class="article-badge">{{ article?.section || '文章详情' }}</span>
        <span class="article-badge article-badge--soft">{{ sourceBadge }}</span>
      </div>

      <h1>{{ article?.title || '文章详情' }}</h1>
      <p class="article-hero__summary">
        {{
          article?.summary ||
          '点击首页标题后会来到这里，正文会优先通过独立的文章详情接口加载。'
        }}
      </p>

      <div class="article-hero__facts">
        <div>
          <span>{{ article?.date || '--' }}</span>
          <small>发布日期</small>
        </div>
        <div>
          <span>{{ adjacentCount }}</span>
          <small>相邻文章</small>
        </div>
        <div>
          <span>{{ article ? 'OK' : '--' }}</span>
          <small>详情状态</small>
        </div>
      </div>
    </header>

    <section class="status-strip" :class="`is-${statusTone}`">
      <div>
        <p class="eyebrow">Article Status</p>
        <h2>正文详情页</h2>
      </div>
      <div class="status-chip" :class="`is-${statusTone}`">{{ statusLabel }}</div>
      <p class="status-copy">
        {{ sourceMessage }}
        <span v-if="syncTimeLabel">最近同步：{{ syncTimeLabel }}</span>
      </p>
    </section>

    <section v-if="loading" class="empty-state empty-state--large">
      <h3>正在加载正文</h3>
      <p>请稍候，正在从公开文章详情接口读取完整内容。</p>
    </section>

    <section v-else-if="notFound" class="empty-state empty-state--large">
      <h3>文章不存在</h3>
      <p>这篇文章可能已下线，或者当前详情链接无效。</p>
    </section>

    <div v-else class="article-layout">
      <article class="article-body">
        <div class="article-body__head">
          <p class="eyebrow">Article Detail</p>
          <p class="article-body__meta">{{ article.metaText }}</p>
        </div>
        <div class="article-content" v-html="article.contentHtml"></div>

        <nav class="article-pager">
          <RouterLink
            v-if="prevArticle"
            class="article-pager-link"
            :to="{ name: 'article-detail', params: { articleId: prevArticle.routeId } }"
          >
            <span class="article-pager-link__label">上一篇</span>
            <strong class="article-pager-link__title">{{ prevArticle.title }}</strong>
            <span class="article-pager-link__meta">
              {{ prevArticle.section }} · {{ prevArticle.date }}
            </span>
          </RouterLink>

          <div v-else class="article-pager-link article-pager-link--empty">
            <span class="article-pager-link__label">上一篇</span>
            <strong class="article-pager-link__title">已经到当前类目的第一篇</strong>
            <span class="article-pager-link__meta">没有更靠前的文章了</span>
          </div>

          <RouterLink
            v-if="nextArticle"
            class="article-pager-link"
            :to="{ name: 'article-detail', params: { articleId: nextArticle.routeId } }"
          >
            <span class="article-pager-link__label">下一篇</span>
            <strong class="article-pager-link__title">{{ nextArticle.title }}</strong>
            <span class="article-pager-link__meta">
              {{ nextArticle.section }} · {{ nextArticle.date }}
            </span>
          </RouterLink>

          <div v-else class="article-pager-link article-pager-link--empty">
            <span class="article-pager-link__label">下一篇</span>
            <strong class="article-pager-link__title">已经到当前类目的最后一篇</strong>
            <span class="article-pager-link__meta">没有更靠后的文章了</span>
          </div>
        </nav>
      </article>

      <aside class="article-side">
        <section class="article-panel">
          <p class="eyebrow">Reading Note</p>
          <h2>阅读信息</h2>
          <p>
            当前正文通过独立的公开文章详情接口按文章 ID 加载，
            不再依赖首页文章列表缓存，直接刷新详情页也可以正常打开。
          </p>
        </section>

        <section class="article-panel">
          <p class="eyebrow">Navigation</p>
          <h2>上下篇导航</h2>
          <ul class="article-side-list">
            <li>
              <span>上一篇</span>
              <RouterLink
                v-if="prevArticle"
                :to="{ name: 'article-detail', params: { articleId: prevArticle.routeId } }"
              >
                {{ prevArticle.title }}
              </RouterLink>
              <span v-else>当前已经是本类目的第一篇</span>
            </li>
            <li>
              <span>下一篇</span>
              <RouterLink
                v-if="nextArticle"
                :to="{ name: 'article-detail', params: { articleId: nextArticle.routeId } }"
              >
                {{ nextArticle.title }}
              </RouterLink>
              <span v-else>当前已经是本类目的最后一篇</span>
            </li>
          </ul>
        </section>
      </aside>
    </div>
  </div>
</template>
