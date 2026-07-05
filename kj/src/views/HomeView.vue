<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watchEffect } from 'vue'
import { getPublicRandomJumpDomains } from '../api/article'
import SectionBlock from '../components/SectionBlock.vue'
import { PREVIEW_LIMIT, loadPublicContent } from '../services/publicContent'

const currentYear = new Date().getFullYear()
const defaultSecurityButtons = [
  {
    id: 'security-link-1',
    label: '①线路→点击前往',
  },
  {
    id: 'security-link-2',
    label: '②线路→点击前往',
  },
  {
    id: 'security-link-3',
    label: '③线路→点击前往',
  },
]
const defaultSolutionLabel = '移动网络»防止移动端无法正常访问及防劫持解决方案'

const securityLinks = ref({
  urls: [],
  primaryUrl: '',
})
const securityChecked = ref(false)
const content = ref({
  state: 'loading',
  sourceMessage: '正在同步后端类目与文章数据，请稍候。',
  syncTimeLabel: '',
  sections: [],
  updates: [],
  totalArticleCount: 0,
})

let securityCheckTimer = 0

const currentSections = computed(() => content.value.sections ?? [])
const currentUpdates = computed(() => content.value.updates ?? [])

const securityButtons = computed(() =>
  defaultSecurityButtons.map((button, index) => ({
    ...button,
    url: securityLinks.value.urls[index] ?? '',
  })),
)

const primarySecurityButtons = computed(() => securityButtons.value)
const solutionSecurityButton = computed(() => ({
  id: 'security-link-solution',
  label: defaultSolutionLabel,
  url: securityLinks.value.primaryUrl || securityLinks.value.urls[0] || '',
}))

const filteredUpdates = computed(() => currentUpdates.value)

const filteredSections = computed(() =>
  currentSections.value
    .map((section) => ({
      ...section,
      items: section.items.slice(0, PREVIEW_LIMIT),
    }))
    .filter((section) => section.items.length),
)

const recentTip =
  '以下内容按最近发布时间整理，点击标题即可进入正文页。'

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const jumpToLink = (url) => {
  const target = `${url ?? ''}`.trim()
  if (!target) {
    return
  }

  window.location.assign(target)
}

const loadHomeContent = async () => {
  content.value = await loadPublicContent()
}

const loadSecurityLinks = async () => {
  try {
    const result = await getPublicRandomJumpDomains()
    const urls = Array.isArray(result?.urls)
      ? result.urls
      : Array.isArray(result?.domains)
        ? result.domains
        : []

    securityLinks.value = {
      urls: urls
        .map((item) => `${item ?? ''}`.trim())
        .filter(Boolean)
        .slice(0, 3),
      primaryUrl: `${result?.url ?? result?.domain ?? ''}`.trim(),
    }
  } catch (error) {
    securityLinks.value = {
      urls: [],
      primaryUrl: '',
    }
  }
}

onMounted(() => {
  loadHomeContent()
  loadSecurityLinks()
  securityCheckTimer = window.setTimeout(() => {
    securityChecked.value = true
  }, 1200)
})

onBeforeUnmount(() => {
  if (securityCheckTimer) {
    window.clearTimeout(securityCheckTimer)
  }
})

watchEffect(() => {
  document.title = '热门资讯网'
})
</script>

<template>
  <div id="top" class="legacy-home">
    <section class="security-entry" aria-label="安全检测">
      <div class="weui-safe-head" style="text-align:center;padding:30px 10px 20px 10px;">
        <i
          id="icon"
          :class="['icon', securityChecked ? 'weui-icon-success' : 'weui-icon-waiting']"
          aria-hidden="true"
        ></i>
        <p id="iconText" style="font-size:22px;color:#555;">已通过安全检测</p>
        <p id="subText" style="font-size:15px;color:#999;">请点击下方按钮，选择线路前往主页</p>
      </div>

      <div class="security-entry__actions">
        <a
          v-for="button in primarySecurityButtons"
          :key="button.id"
          :href="button.url || '#'"
          class="weui-btn weui-btn_primary"
          :class="{ 'is-disabled': !button.url }"
          @click.prevent="jumpToLink(button.url)"
        >
          {{ button.label }}
        </a>

        <a
          v-if="solutionSecurityButton"
          :href="solutionSecurityButton.url || '#'"
          class="weui-btn weui-btn_primary1 security-entry__solution"
          :class="{ 'is-disabled': !solutionSecurityButton.url }"
          @click.prevent="jumpToLink(solutionSecurityButton.url)"
        >
          {{ solutionSecurityButton.label }}
        </a>
      </div>

      <div class="footer security-entry__footer">
        <p>提供安全技术支持</p>
        <p>Copyright © 1998 - {{ currentYear }}. All Rights Reserved.</p>
      </div>
    </section>

    <div class="channels-wrap">
      <ul class="channels">
        <li><a href="#top">首页</a></li>
        <li v-for="section in currentSections" :key="section.id">
          <a :href="`#${section.id}`">{{ section.title }}</a>
        </li>
      </ul>
    </div>

    <div class="hr10"></div>

    <section class="modd">
      <div class="title TitA">
        <em></em>
        <h3>最近更新</h3>
      </div>

      <p class="block-tip">{{ recentTip }}</p>

      <ul v-if="filteredUpdates.length" class="article-hot article-hot--updates">
        <li v-for="article in filteredUpdates" :key="article.key">
          <span class="article-hot__dot">·</span>
          <RouterLink
            class="article-hot__link"
            :to="{ name: 'article-detail', params: { articleId: article.routeId } }"
          >
            {{ article.title }}
          </RouterLink>
          <span class="article-hot__tag">{{ article.section }}</span>
        </li>
      </ul>

      <div v-else class="block-empty">
        当前暂无最近更新内容。
      </div>
    </section>

    <template v-if="filteredSections.length">
      <div v-for="section in filteredSections" :key="section.id">
        <div class="hr10"></div>
        <SectionBlock :section="section" />
      </div>
    </template>

    <template v-else>
      <div class="hr10"></div>
      <section class="modd">
        <div class="title TitA">
          <em></em>
          <h3>暂无内容</h3>
        </div>
        <div class="block-empty">
          当前暂无可展示的首页文章内容。
        </div>
      </section>
    </template>

    <nav class="nav-foot">
      <ul>
        <li><a href="#top">首页</a></li>
        <li v-for="section in currentSections" :key="`footer-${section.id}`">
          <a :href="`#${section.id}`">{{ section.title }}</a>
        </li>
      </ul>
    </nav>

    <footer class="footer-min">
      <div class="app">
        <button type="button" class="pc" @click="scrollToTop">返回首页</button>
      </div>
      <div class="copyright">Copyright © 2002-2025 热门资讯网 版权所有 未经授权请勿转载</div>
    </footer>
  </div>
</template>
