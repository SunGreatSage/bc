<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getPublicArticleDetail } from '../api/article'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const article = ref(null)
const errorText = ref('')

const drawBalls = [
  { number: '21', color: 'green', label: '狗/土' },
  { number: '16', color: 'green', label: '兔/木' },
  { number: '25', color: 'blue', label: '马/木' },
  { number: '29', color: 'red', label: '虎/土' },
  { number: '08', color: 'red', label: '猪/木' },
  { number: '07', color: 'red', label: '鼠/土' },
  { number: '04', color: 'blue', label: '兔/金' },
]

const title = computed(() => article.value?.title || '帖子详情')
const contentHtml = computed(() => article.value?.content || '')
const likeCount = computed(() => {
  const id = Number(route.params.articleId) || 0
  return 12 + (id % 29)
})
const commentCount = computed(() => {
  const id = Number(route.params.articleId) || 0
  return 1 + (id % 5)
})

const loadDetail = async () => {
  const id = String(route.params.articleId ?? '')
  if (!id) {
    errorText.value = '帖子不存在'
    return
  }

  loading.value = true
  errorText.value = ''
  article.value = null
  try {
    article.value = await getPublicArticleDetail(id)
  } catch (error) {
    console.error('加载帖子详情失败:', error)
    errorText.value = '帖子加载失败，请稍后再试'
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  if (window.history.length > 1) {
    router.back()
    return
  }
  router.push('/find')
}

watch(
  () => route.params.articleId,
  () => {
    loadDetail()
  },
  { immediate: true },
)

onMounted(() => {
  document.title = `${title.value} - 金算盘论坛`
})
</script>

<template>
  <main class="find-detail">
    <header class="detail-topbar">
      <button type="button" class="back-button" @click="goBack">‹</button>
      <h1>{{ title }}</h1>
      <button type="button" class="more-button">•••</button>
    </header>

<!--    <section class="result-card">-->
<!--      <div class="result-head">-->
<!--        <span>第115期开奖结果</span>-->
<!--        <RouterLink to="/lottery-history">查看历史记录</RouterLink>-->
<!--      </div>-->

<!--      <div class="result-balls">-->
<!--        <template v-for="(ball, index) in drawBalls" :key="ball.number">-->
<!--          <div class="ball-item">-->
<!--            <strong :class="['ball', `ball&#45;&#45;${ball.color}`]">{{ ball.number }}</strong>-->
<!--            <span>{{ ball.label }}</span>-->
<!--          </div>-->
<!--          <b v-if="index === 5" class="plus">+</b>-->
<!--        </template>-->
<!--      </div>-->

<!--      <p>第116期 2026/04/26 21:32星期日</p>-->
<!--    </section>-->

    <section class="detail-body">
      <div v-if="loading" class="detail-state">加载中...</div>
      <div v-else-if="errorText" class="detail-state">{{ errorText }}</div>
      <article v-else class="content-card" v-html="contentHtml"></article>
    </section>

    <button class="float-home" type="button" @click="router.push('/home')">⌂</button>
    <button class="float-chat" type="button">•••</button>

    <footer class="detail-actions">
      <input type="text" readonly placeholder="想说点什么..." />
      <button type="button">
        <span>♡</span>
        <b>{{ likeCount }}</b>
      </button>
      <button type="button">
        <span>☵</span>
        <b>{{ commentCount }}</b>
      </button>
      <button type="button">
        <span>⌯</span>
        <b>分享</b>
      </button>
    </footer>
  </main>
</template>

<style scoped>
.find-detail {
  min-height: 100vh;
  padding-bottom: 70px;
  background: #fff;
  color: #101820;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

a {
  color: inherit;
  text-decoration: none;
}

.detail-topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  display: grid;
  grid-template-columns: 54px 1fr 54px;
  align-items: center;
  height: 64px;
  border-bottom: 1px solid #f0f0f0;
  background: #fff;
}

.detail-topbar h1 {
  margin: 0;
  overflow: hidden;
  color: #111827;
  font-size: 23px;
  font-weight: 900;
  text-align: center;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.back-button,
.more-button {
  border: 0;
  background: transparent;
  color: #001d35;
  font-size: 36px;
  line-height: 1;
}

.more-button {
  color: #06bd65;
  font-size: 24px;
  letter-spacing: -2px;
}

.result-card {
  margin: 18px 18px 12px;
  padding: 16px 16px 18px;
  border: 1px solid #0fc46f;
  border-radius: 4px;
  background: #fff;
}

.result-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #09b965;
  font-size: 22px;
}

.result-head a {
  color: #0abf68;
  font-size: 21px;
}

.result-balls {
  display: grid;
  grid-template-columns: repeat(6, minmax(0, 1fr)) auto minmax(0, 1fr);
  gap: 4px;
  align-items: start;
  margin-top: 16px;
}

.ball-item {
  display: grid;
  justify-items: center;
  min-width: 0;
}

.ball {
  display: grid;
  place-items: center;
  width: clamp(38px, 9.4vw, 58px);
  height: clamp(38px, 9.4vw, 58px);
  border-radius: 999px;
  background: #fff;
  color: #666;
  font-size: clamp(20px, 5.4vw, 31px);
  line-height: 1;
}

.ball--red {
  border: 5px solid #ff2f39;
}

.ball--blue {
  border: 5px solid #2f9cff;
}

.ball--green {
  border: 5px solid #10c760;
}

.ball-item span {
  margin-top: 8px;
  color: #202020;
  font-size: clamp(12px, 3.8vw, 18px);
  white-space: nowrap;
}

.plus {
  align-self: center;
  color: #777;
  font-size: 28px;
  font-weight: 500;
}

.result-card p {
  margin: 18px 0 0;
  color: #ff1010;
  font-size: 18px;
  text-align: center;
}

.detail-body {
  border-top: 3px solid #c54bea;
}

.content-card {
  background: #fff;
}

.content-card :deep(img) {
  display: block;
  width: 100%;
  height: auto;
  margin: 0 auto;
}

.content-card :deep(p) {
  margin: 0;
  color: #111;
  font-size: 18px;
  line-height: 1.65;
}

.content-card :deep(h1),
.content-card :deep(h2),
.content-card :deep(h3) {
  margin: 14px 12px 10px;
  color: #111827;
}

.detail-state {
  display: grid;
  place-items: center;
  min-height: 180px;
  color: #999;
  font-size: 16px;
}

.float-home {
  position: fixed;
  left: 6px;
  bottom: 118px;
  z-index: 20;
  display: grid;
  place-items: center;
  width: 58px;
  height: 58px;
  border: 0;
  border-radius: 999px;
  background: rgb(130 130 140 / 78%);
  color: #fff;
  font-size: 36px;
}

.float-chat {
  position: fixed;
  right: 18px;
  bottom: 116px;
  z-index: 20;
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  border: 0;
  border-radius: 999px;
  background: linear-gradient(135deg, #7db5ff, #7468ff);
  color: #fff;
  font-size: 20px;
  box-shadow: 0 2px 12px rgb(0 0 0 / 16%);
}

.detail-actions {
  position: fixed;
  left: 50%;
  bottom: 0;
  z-index: 40;
  display: grid;
  grid-template-columns: 1fr 44px 44px 54px;
  gap: 10px;
  align-items: center;
  width: min(100%, 600px);
  height: 64px;
  padding: 8px 16px;
  transform: translateX(-50%);
  border-top: 1px solid #eee;
  background: #fff;
  box-sizing: border-box;
}

.detail-actions input {
  height: 44px;
  padding: 0 20px;
  border: 0;
  border-radius: 999px;
  background: #f2f2f2;
  color: #aaa;
  font-size: 16px;
}

.detail-actions button {
  display: grid;
  justify-items: center;
  border: 0;
  background: transparent;
  color: #5f6b7a;
  font-size: 14px;
}

.detail-actions span {
  font-size: 24px;
  line-height: 1;
}

.detail-actions b {
  font-weight: 500;
}

@media (min-width: 601px) {
  .find-detail {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 430px) {
  .detail-topbar h1 {
    font-size: 21px;
  }

  .result-card {
    margin: 14px 18px 12px;
    padding: 14px 14px 16px;
  }

  .result-head {
    font-size: 19px;
  }

  .result-head a {
    font-size: 18px;
  }

  .ball {
    width: clamp(34px, 10.5vw, 50px);
    height: clamp(34px, 10.5vw, 50px);
    border-width: 4px;
    font-size: clamp(18px, 6vw, 27px);
  }

  .result-card p {
    font-size: 16px;
  }
}
</style>
