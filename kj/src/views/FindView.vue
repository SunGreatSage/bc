<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { getPublicArticleList } from '../api/article'

const tabs = [
  { key: 'all', label: '全部' },
  { key: 'latest', label: '最新帖' },
  { key: 'history', label: '历史帖' },
]

const authorNames = ['金算盘', '港澳老友', '铁算盘', '六合小师妹', '老陈说彩', '阿九', '澳彩观察', '图文精选']
const avatarColors = ['#d7a463', '#6aa9d8', '#7fd1ac', '#d77a85', '#9b83e6', '#efa045', '#7c9ec8', '#d6bd78']

const activeTab = ref('all')
const route = useRoute()
const keyword = ref('')
const page = ref(1)
const pageSize = 10
const total = ref(0)
const articles = ref([])
const loading = ref(false)
const finished = ref(false)
const errorText = ref('')

const hashNumber = (value) => {
  const text = `${value ?? ''}`
  let hash = 0
  for (let index = 0; index < text.length; index += 1) {
    hash = (hash * 31 + text.charCodeAt(index)) >>> 0
  }
  return hash
}

const stripHtml = (html) => `${html ?? ''}`.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()

const extractImage = (html, id) => {
  const match = `${html ?? ''}`.match(/<img[^>]+src=["']([^"']+)["']/i)
  if (match?.[1]) return match[1]
  return ''
}

const normalizeArticle = (article) => {
  const id = article?.id ?? article?.title ?? Math.random()
  const hash = hashNumber(id)
  const title = `${article?.title ?? '未命名帖子'}`.trim()
  return {
    id,
    routeId: String(id),
    title,
    createdAt: Number(article?.createdAt) || 0,
    content: article?.content ?? '',
    summary: stripHtml(article?.content).slice(0, 80),
    image: extractImage(article?.content, id),
    author: authorNames[hash % authorNames.length],
    avatarText: authorNames[hash % authorNames.length].slice(0, 1),
    avatarColor: avatarColors[hash % avatarColors.length],
    likes: 10 + (hash % 28),
  }
}

const sortedArticles = computed(() => {
  const list = articles.value.slice()
  const today = new Date()
  const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime()
  const startOfTomorrow = startOfToday + 24 * 60 * 60 * 1000

  if (activeTab.value === 'latest') {
    return list
      .filter((item) => item.createdAt >= startOfToday && item.createdAt < startOfTomorrow)
      .sort((left, right) => right.createdAt - left.createdAt)
  }
  if (activeTab.value === 'history') {
    return list
      .filter((item) => item.createdAt < startOfToday || item.createdAt >= startOfTomorrow)
      .sort((left, right) => right.createdAt - left.createdAt)
  }
  return list.sort((left, right) => right.createdAt - left.createdAt)
})

const filteredArticles = computed(() => {
  const text = keyword.value.trim()
  if (!text) return sortedArticles.value
  return sortedArticles.value.filter((item) => {
    return item.title.includes(text) || item.author.includes(text) || item.summary.includes(text)
  })
})

const leftArticles = computed(() => filteredArticles.value.filter((_, index) => index % 2 === 0))
const rightArticles = computed(() => filteredArticles.value.filter((_, index) => index % 2 === 1))

const loadArticles = async (reset = false) => {
  if (loading.value || (finished.value && !reset)) return

  loading.value = true
  errorText.value = ''
  try {
    const currentPage = reset ? 1 : page.value
    const result = await getPublicArticleList({
      page: currentPage,
      pageSize,
      category: '发现',
    })
    const items = Array.isArray(result?.items) ? result.items.map(normalizeArticle) : []

    total.value = Number(result?.total) || 0
    articles.value = reset ? items : [...articles.value, ...items]
    page.value = currentPage + 1
    finished.value = articles.value.length >= total.value || items.length < pageSize
  } catch (error) {
    console.error('加载发现列表失败:', error)
    errorText.value = '内容加载失败，请稍后再试'
    finished.value = true
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleScroll = () => {
  const bottom = window.innerHeight + window.scrollY
  const height = document.documentElement.scrollHeight
  if (height - bottom < 220) {
    loadArticles()
  }
}

const resetAndLoad = () => {
  page.value = 1
  total.value = 0
  articles.value = []
  finished.value = false
  errorText.value = ''
  loadArticles(true)
}

watch(activeTab, () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

watch(
  () => route.fullPath,
  () => {
    if (route.name === 'find') {
      resetAndLoad()
    }
  },
)

onMounted(() => {
  document.title = '发现 - 金算盘论坛'
  resetAndLoad()
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <main class="find-page">
    <header class="find-tabs">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        :class="{ active: activeTab === tab.key }"
        type="button"
        @click="activeTab = tab.key"
      >
        {{ tab.label }}
      </button>
    </header>

    <section class="find-search">
      <input v-model="keyword" type="search" placeholder="点击搜索作者或标题..." @keyup.enter="handleSearch" />
      <button type="button" @click="handleSearch">搜索</button>
    </section>

    <section role="feed" class="find-waterfall">
      <div class="find-column">
        <RouterLink
          v-for="item in leftArticles"
          :key="item.routeId"
          class="post-card"
          :to="{ name: 'find-detail', params: { articleId: item.routeId } }"
        >
          <div v-if="item.image" class="post-image">
            <img :src="item.image" :alt="item.title" loading="lazy" />
          </div>
          <div v-else class="post-image post-image--fallback">
            <strong>{{ item.title }}</strong>
            <span>金算盘论坛</span>
          </div>
          <div class="post-info">
            <span class="avatar" :style="{ background: item.avatarColor }">{{ item.avatarText }}</span>
            <p>{{ item.title }}</p>
            <span class="likes">赞 {{ item.likes }}</span>
          </div>
        </RouterLink>
      </div>

      <div class="find-column">
        <RouterLink
          v-for="item in rightArticles"
          :key="item.routeId"
          class="post-card"
          :to="{ name: 'find-detail', params: { articleId: item.routeId } }"
        >
          <div v-if="item.image" class="post-image">
            <img :src="item.image" :alt="item.title" loading="lazy" />
          </div>
          <div v-else class="post-image post-image--fallback">
            <strong>{{ item.title }}</strong>
            <span>金算盘论坛</span>
          </div>
          <div class="post-info">
            <span class="avatar" :style="{ background: item.avatarColor }">{{ item.avatarText }}</span>
            <p>{{ item.title }}</p>
            <span class="likes">赞 {{ item.likes }}</span>
          </div>
        </RouterLink>
      </div>
    </section>

    <p v-if="errorText" class="find-state">{{ errorText }}</p>
    <p v-else-if="loading" class="find-state">加载中...</p>
    <p v-else-if="!filteredArticles.length" class="find-state">暂无发现内容</p>
    <p v-else-if="finished" class="find-state">没有更多了</p>

    <button class="float-publish" type="button">
      <span>+</span>
      <small>发布</small>
    </button>

    <nav class="find-bottom-nav">
      <RouterLink to="/home">
        <span>⌂</span>
        <strong>49图库</strong>
      </RouterLink>
      <RouterLink class="active" to="/find">
        <span>♣</span>
        <strong>发现</strong>
      </RouterLink>
      <RouterLink class="center" to="/gszj">
        <span>⌘</span>
        <strong>高手之家</strong>
      </RouterLink>
      <RouterLink to="/treasure">
        <span>▣</span>
        <strong>寻宝</strong>
      </RouterLink>
      <RouterLink to="/my">
        <span>♙</span>
        <strong>我的</strong>
      </RouterLink>
    </nav>
  </main>
</template>

<style scoped>
.find-page {
  min-height: 100vh;
  padding-bottom: 82px;
  background: #f4f4f4;
  color: #222;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

a {
  color: inherit;
  text-decoration: none;
}

.find-tabs {
  position: sticky;
  top: 0;
  z-index: 20;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  height: 76px;
  background: #fff;
  box-shadow: 0 1px 0 rgb(0 0 0 / 5%);
}

.find-tabs button {
  border: 0;
  background: transparent;
  color: #000;
  font-size: 18px;
  font-weight: 800;
}

.find-tabs button.active {
  color: #08c76b;
}

.find-search {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 16px;
  align-items: center;
  padding: 14px 24px 20px;
  background: #f4f4f4;
}

.find-search input {
  width: 100%;
  height: 60px;
  padding: 0 24px;
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #333;
  font-size: 21px;
  outline: none;
}

.find-search input::placeholder {
  color: #8b8b8b;
}

.find-search button {
  border: 0;
  background: transparent;
  color: #08c76b;
  font-size: 22px;
  font-weight: 900;
}

.find-waterfall {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
  padding: 0 14px 10px;
}

.find-column {
  display: grid;
  align-content: start;
  gap: 16px;
  min-width: 0;
}

.post-card {
  overflow: hidden;
  border-radius: 2px;
  background: #fff;
  box-shadow: 0 2px 7px rgb(0 0 0 / 15%);
}

.post-image {
  overflow: hidden;
  background: #eee;
}

.post-image img {
  display: block;
  width: 100%;
  min-height: 184px;
  object-fit: cover;
}

.post-image--fallback {
  display: grid;
  align-content: center;
  min-height: 190px;
  padding: 18px;
  background:
    linear-gradient(90deg, rgb(255 255 255 / 72%) 0 1px, transparent 1px 100%),
    linear-gradient(180deg, #ffe6f6, #eef7ff 48%, #fff6db);
  background-size: 24px 24px, auto;
  color: #222;
}

.post-image--fallback strong {
  color: #e50022;
  font-size: 21px;
  line-height: 1.25;
}

.post-image--fallback span {
  margin-top: 12px;
  color: rgb(0 0 0 / 28%);
  font-size: 28px;
  font-weight: 900;
}

.post-info {
  display: grid;
  grid-template-columns: 40px 1fr auto;
  gap: 8px;
  align-items: center;
  min-height: 56px;
  padding: 8px;
}

.avatar {
  display: grid;
  place-items: center;
  width: 36px;
  height: 36px;
  border-radius: 999px;
  color: #fff;
  font-size: 18px;
  font-weight: 800;
}

.post-info p {
  min-width: 0;
  margin: 0;
  overflow: hidden;
  color: #222;
  font-size: 20px;
  line-height: 1.2;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.likes {
  color: #bfbfbf;
  font-size: 16px;
  white-space: nowrap;
}

.find-state {
  margin: 12px 0 0;
  color: #999;
  font-size: 15px;
  text-align: center;
}

.float-publish {
  position: fixed;
  right: 14px;
  bottom: 86px;
  z-index: 25;
  display: grid;
  place-items: center;
  width: 68px;
  height: 82px;
  border: 0;
  border-radius: 12px;
  background: #fff;
  color: #333;
  box-shadow: 0 2px 16px rgb(0 0 0 / 18%);
}

.float-publish span {
  display: grid;
  place-items: center;
  width: 50px;
  height: 50px;
  border-radius: 999px;
  background: #09c96d;
  color: #fff;
  font-size: 42px;
  line-height: 1;
}

.float-publish small {
  font-size: 16px;
}

.find-bottom-nav {
  position: fixed;
  left: 50%;
  bottom: 0;
  z-index: 40;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  width: min(100%, 600px);
  height: 78px;
  transform: translateX(-50%);
  border-top: 1px solid #e4e4e4;
  background: #fff;
}

.find-bottom-nav a {
  position: relative;
  display: grid;
  align-content: center;
  justify-items: center;
  gap: 4px;
  color: #777;
  font-size: 15px;
}

.find-bottom-nav span {
  color: #12c878;
  font-size: 25px;
  line-height: 1;
}

.find-bottom-nav strong {
  font-weight: 500;
}

.find-bottom-nav .active {
  color: #0ac66d;
}

.find-bottom-nav .center span {
  display: grid;
  place-items: center;
  width: 56px;
  height: 56px;
  margin-top: -28px;
  border: 6px solid #fff;
  border-radius: 999px;
  background: #14c873;
  color: #fff;
  font-size: 28px;
  box-shadow: 0 -2px 8px rgb(0 0 0 / 10%);
}

@media (min-width: 601px) {
  .find-page {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 430px) {
  .find-tabs {
    height: 70px;
  }

  .find-search {
    padding-inline: 18px;
  }

  .find-search input {
    height: 54px;
    font-size: 18px;
  }

  .find-search button {
    font-size: 20px;
  }

  .find-waterfall {
    gap: 10px;
    padding-inline: 10px;
  }

  .post-image img,
  .post-image--fallback {
    min-height: 172px;
  }

  .post-info {
    grid-template-columns: 34px 1fr auto;
  }

  .avatar {
    width: 32px;
    height: 32px;
  }

  .post-info p {
    font-size: 18px;
  }
}
</style>
