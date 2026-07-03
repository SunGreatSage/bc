<template>
  <div class="rules-page custom-scrollbar">
    <header class="hero">
      <p class="hero-tag">官方规则</p>
      <h1>新澳门六合彩客户规则</h1>
      <p class="hero-desc">
        以下内容仅展示客户在前端可见的投注信息，涵盖新澳门六合彩的核心玩法、生肖对照以及开奖与派奖流程，确保阅读体验与当前主题配色保持一致。
      </p>
      <div class="hero-meta">
        <div class="meta-item">
          <span class="meta-label">最新更新时间</span>
          <span class="meta-value">{{ lastUpdated }}</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">适用页面</span>
          <span class="meta-value">客户前端</span>
        </div>
      </div>
    </header>

    <div class="quick-nav" role="navigation" aria-label="规则导航">
      <button
        v-for="section in sections"
        :key="section.id"
        type="button"
        class="quick-nav-btn"
        @click="scrollToSection(section.id)"
      >
        {{ section.title }}
      </button>
    </div>

    <section id="basic-info" class="rule-card">
      <div class="section-head">
        <div>
          <p class="section-badge">规则总览</p>
          <h2>客户需知的基础信息</h2>
        </div>
        <p>了解号码范围、开奖方式、投注门槛与玩法限制，确保下单更安心。</p>
      </div>
      <div class="info-grid">
        <div v-for="item in coreInfo" :key="item.label" class="info-item">
          <p class="info-label">{{ item.label }}</p>
          <p class="info-value">{{ item.value }}</p>
        </div>
      </div>
      <div class="note-card">
        <h3>玩家须知</h3>
        <ul>
          <li v-for="note in playerNotes" :key="note">
            <i class="fas fa-check-circle"></i>
            <span>{{ note }}</span>
          </li>
        </ul>
      </div>
    </section>

    <section id="play-rules" class="rule-card">
      <div class="section-head">
        <div>
          <p class="section-badge">玩法分类</p>
          <h2>客户可参与的投注类型</h2>
        </div>
        <p>所有玩法均为“中”类投注，聚焦客户真正可见、可用的内容，便于快速对比。</p>
      </div>
      <div class="play-grid">
        <article
          v-for="type in playTypes"
          :key="type.title"
          class="play-card"
          :class="type.accent"
        >
          <div class="play-card-head">
            <div>
              <h3>{{ type.title }}</h3>
              <p class="play-summary">{{ type.summary }}</p>
            </div>
            <span class="badge">{{ type.badge }}</span>
          </div>
          <p class="play-win">
            <strong>中奖条件：</strong>{{ type.winText }}
          </p>
          <p v-if="type.subText" class="play-sub">{{ type.subText }}</p>
          <ul class="play-list" v-if="type.bullets && type.bullets.length">
            <li v-for="line in type.bullets" :key="line">
              <i class="fas fa-dot-circle"></i>
              <span>{{ line }}</span>
            </li>
          </ul>
          <div class="example-box" v-if="type.examples && type.examples.length">
            <p class="example-title">
              <i class="fas fa-lightbulb"></i>
              示例如下
            </p>
            <p v-for="example in type.examples" :key="example">{{ example }}</p>
          </div>
        </article>
      </div>
    </section>

    <section id="zodiac-guide" class="rule-card">
      <div class="section-head">
        <div>
          <p class="section-badge">生肖参考</p>
          <h2>生肖与号码对照表</h2>
        </div>
        <p>以下表格为客户投注时需要的公开信息，每年随农历生肖顺延更新。</p>
      </div>
      <div class="zodiac-years">
        <div
          class="zodiac-year-card"
          v-for="year in zodiacYears"
          :key="year.title"
        >
          <div class="zodiac-year-head">
            <div>
              <p class="zodiac-year-title">{{ year.title }}</p>
              <p class="zodiac-year-sub">{{ year.subtitle }}</p>
            </div>
            <span class="badge badge-soft">{{ year.badge }}</span>
          </div>
          <div class="zodiac-grid">
            <div
              class="zodiac-item"
              v-for="item in year.items"
              :key="item.name"
            >
              <span class="zodiac-emoji">{{ item.emoji }}</span>
              <p class="zodiac-name">{{ item.name }}</p>
              <p class="zodiac-numbers">{{ item.numbers.join(', ') }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="formula-card">
        <h3>计算提示</h3>
        <p>
          （年份 - 4） % 12 对应生肖顺序：鼠→牛→虎→兔→龙→蛇→马→羊→猴→鸡→狗→猪。
        </p>
        <p>示例：2025 年 = 蛇，2026 年 = 马，2027 年 = 羊。</p>
      </div>
    </section>

    <section id="draw-process" class="rule-card">
      <div class="section-head">
        <div>
          <p class="section-badge">开奖流程</p>
          <h2>客户可见的开奖与派奖节点</h2>
        </div>
        <p>透明化展示投注、封盘、开奖与派奖，全流程客户都能清楚掌握。</p>
      </div>
      <div class="timeline">
        <div class="timeline-item" v-for="step in processSteps" :key="step.title">
          <div class="timeline-marker">{{ step.order }}</div>
          <div>
            <p class="timeline-title">{{ step.title }}</p>
            <p class="timeline-desc">{{ step.desc }}</p>
          </div>
        </div>
      </div>
      <div class="result-card">
        <h3>结算说明</h3>
        <ul>
          <li v-for="note in payoutNotes" :key="note">
            <i class="fas fa-check"></i>
            <span>{{ note }}</span>
          </li>
        </ul>
      </div>
    </section>

    <section id="faq" class="rule-card">
      <div class="section-head">
        <div>
          <p class="section-badge">FAQ</p>
          <h2>常见问题</h2>
        </div>
        <p>聚焦客户最关心的规则说明，方便快速解答疑虑。</p>
      </div>
      <div class="faq-list">
        <div class="faq-item" v-for="faq in faqs" :key="faq.id">
          <button
            class="faq-question"
            type="button"
            @click="toggleFaq(faq.id)"
          >
            <span>{{ faq.question }}</span>
            <i
              :class="[
                'fas',
                activeFaq === faq.id ? 'fa-chevron-up' : 'fa-chevron-down'
              ]"
            ></i>
          </button>
          <div class="faq-answer" v-show="activeFaq === faq.id">
            <p>{{ faq.answer }}</p>
            <ul v-if="faq.points && faq.points.length">
              <li v-for="point in faq.points" :key="point">{{ point }}</li>
            </ul>
            <p v-if="faq.tip" class="faq-tip">
              <i class="fas fa-info-circle"></i>
              <span>{{ faq.tip }}</span>
            </p>
          </div>
        </div>
      </div>
    </section>

    <button
      v-show="showBackToTop"
      type="button"
      @click="scrollToTop"
      class="back-to-top"
      aria-label="返回顶部"
    >
      <i class="fas fa-arrow-up"></i>
    </button>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const sections = [
  { id: 'basic-info', title: '规则总览' },
  { id: 'play-rules', title: '玩法分类' },
  { id: 'zodiac-guide', title: '生肖对照' },
  { id: 'draw-process', title: '开奖与派奖' },
  { id: 'faq', title: '常见问题' }
]

const coreInfo = [
  { label: '号码范围', value: '01 - 49，共 49 个号码' },
  { label: '开奖球数', value: '6 个正码 + 1 个特码' },
  { label: '选号方式', value: '支持单号、生肖与生肖组合' },
  { label: '投注门槛', value: '单注 1 元起，具体限额依平台风控调整' }
]

const playerNotes = [
  '仅提供“中”类玩法，下单号码开出即视为中奖',
  '49 号作为特码时判定为和局，系统返还本金',
  '生肖号码按当年农历排序，每年更新一次',
  '封盘后不可修改订单，请提前确认投注'
]

const playTypes = [
  {
    title: '特码',
    badge: '高赔率',
    accent: 'accent-gold',
    summary: '预测当期第 7 个号码（特码）。',
    winText: '投注号码与特码完全一致即中奖。',
    bullets: ['49 号开出为和局，返还该注本金。'],
    examples: [
      '开奖号码：01、12、23、34、45、49、08（特码）',
      '投注 08 → 中奖；投注 01 → 不中奖（其为正码）'
    ]
  },
  {
    title: '平码',
    badge: '中赔率',
    accent: 'accent-green',
    summary: '预测前 6 个开奖区域（m1-m6）之中的任意号码。',
    winText: '所选号码出现在正码列表中即可中奖。',
    bullets: ['每期正码不重复，共 6 个号码。'],
    examples: [
      '开奖号码：01、12、23、34、45、49、08（特码）',
      '投注 01 或 23 → 中奖；投注 08 → 不中奖'
    ]
  },
  {
    title: '特肖',
    badge: '生肖玩法',
    accent: 'accent-purple',
    summary: '预测特码所属的生肖（生肖数量随当年顺序而定）。',
    winText: '选择的生肖与特码对应生肖一致。',
    bullets: ['生肖表以当年农历顺序为准。'],
    examples: ['特码 08 对应“狗”，投注“狗”即中奖。']
  },
  {
    title: '多生肖组合',
    badge: '组合玩法',
    accent: 'accent-blue',
    summary: '三肖、四肖、五肖按原组合规则；6肖中特选择6个生肖，只看特码。',
    winText:
      '6肖中特为特码生肖命中所选6个生肖中的任意一个即中奖，特码49打和退本金。',
    bullets: [
      '6肖中特赔率为1.95（含本金）。',
      '历史六肖/6肖投注名称仍按6肖中特规则兼容判奖。'
    ],
    examples: [
      '开奖特码：26（龙）',
      '投注“鼠、牛、虎、兔、龙、蛇”（6肖中特）→ 中奖'
    ]
  }
]

const zodiac2025 = [
  { name: '蛇', emoji: '🐍', numbers: [1, 13, 25, 37, 49] },
  { name: '马', emoji: '🐎', numbers: [12, 24, 36, 48] },
  { name: '羊', emoji: '🐑', numbers: [11, 23, 35, 47] },
  { name: '猴', emoji: '🐒', numbers: [10, 22, 34, 46] },
  { name: '鸡', emoji: '🐓', numbers: [9, 21, 33, 45] },
  { name: '狗', emoji: '🐕', numbers: [8, 20, 32, 44] },
  { name: '猪', emoji: '🐖', numbers: [7, 19, 31, 43] },
  { name: '鼠', emoji: '🐭', numbers: [6, 18, 30, 42] },
  { name: '牛', emoji: '🐮', numbers: [5, 17, 29, 41] },
  { name: '虎', emoji: '🐯', numbers: [4, 16, 28, 40] },
  { name: '兔', emoji: '🐰', numbers: [3, 15, 27, 39] },
  { name: '龙', emoji: '🐲', numbers: [2, 14, 26, 38] }
]

const zodiac2026 = [
  { name: '马', emoji: '🐎', numbers: [1, 13, 25, 37, 49] },
  { name: '羊', emoji: '🐑', numbers: [12, 24, 36, 48] },
  { name: '猴', emoji: '🐒', numbers: [11, 23, 35, 47] },
  { name: '鸡', emoji: '🐓', numbers: [10, 22, 34, 46] },
  { name: '狗', emoji: '🐕', numbers: [9, 21, 33, 45] },
  { name: '猪', emoji: '🐖', numbers: [8, 20, 32, 44] },
  { name: '鼠', emoji: '🐭', numbers: [7, 19, 31, 43] },
  { name: '牛', emoji: '🐮', numbers: [6, 18, 30, 42] },
  { name: '虎', emoji: '🐯', numbers: [5, 17, 29, 41] },
  { name: '兔', emoji: '🐰', numbers: [4, 16, 28, 40] },
  { name: '龙', emoji: '🐲', numbers: [3, 15, 27, 39] },
  { name: '蛇', emoji: '🐍', numbers: [2, 14, 26, 38] }
]

const zodiacYears = [
  {
    title: '2025 年（蛇年）',
    subtitle: '当前执行年份',
    badge: '生效中',
    items: zodiac2025
  },
  {
    title: '2026 年（马年）',
    subtitle: '下期预览',
    badge: '提前知悉',
    items: zodiac2026
  }
]

const processSteps = [
  {
    order: '01',
    title: '投注开放',
    desc: '期号状态显示“投注中”，客户可自由选择号码或生肖。'
  },
  {
    order: '02',
    title: '准时封盘',
    desc: '到达封盘时间后自动关闭，显示“封盘中”，无法继续下注。'
  },
  {
    order: '03',
    title: '系统开奖',
    desc: '系统摇出 6 个正码与 1 个特码（互不重复），并公布结果。'
  },
  {
    order: '04',
    title: '自动派奖',
    desc: '按玩法自动判奖，中奖金额实时入账，可在投注记录查看。'
  }
]

const payoutNotes = [
  '中奖金额自动发放至余额，并同步到“投注记录”与“资金明细”。',
  '49 号开作特码时，为保障公平将该期投注本金全额退还。',
  '所有中奖金额到账后可立即继续投注或申请提现。'
]

const faqs = [
  {
    id: 1,
    question: '生肖对照表如何确定？',
    answer: '生肖号码随农历生肖顺延，每年切换一次：',
    points: [
      '以 2025 年蛇年为当前基准',
      '新一年开始前会在页面发布提醒',
      '投注入口始终同步最新生肖表'
    ],
    tip: '如不确定生肖归属，可直接查看上方“生肖与号码”模块。'
  },
  {
    id: 2,
    question: '特码开出 49 为何是和局？',
    answer:
      '49 号是官方设定的特殊号码，当期作为特码开出时平台需保持公平：',
    points: [
      '该期视为和局，不判定输赢',
      '系统自动退回对应投注本金',
      '其他正码玩法不受此规则影响'
    ],
    tip: '退款会直接充入余额，无需手动申请。'
  },
  {
    id: 3,
    question: '多生肖玩法如何判奖？',
    answer: '按开奖生肖集合与投注组合比对：',
    points: [
      '收集 7 个开奖号码的生肖并去重',
      '若集合完全包含在所选生肖中则中奖',
      '缺少任意一个生肖则视为未中'
    ],
    tip: '开奖结果可能出现重复生肖，判奖只看唯一集合。'
  },
  {
    id: 4,
    question: '中奖后多久能到账？',
    answer: '开奖公布后系统立即执行判奖和派奖：',
    points: [
      '通常在数秒内可在余额看到变动',
      '记录会同步到“投注记录”和“资金明细”模块',
      '如遇网络波动，可在 1 分钟后刷新确认'
    ],
    tip: '前端仅展示结果与金额，具体后台逻辑无需客户关心。'
  }
]

const activeFaq = ref(null)
const showBackToTop = ref(false)
const dateFormatter = new Intl.DateTimeFormat('zh-CN', {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit'
})
const lastUpdated = dateFormatter.format(new Date())

const toggleFaq = (id) => {
  activeFaq.value = activeFaq.value === id ? null : id
}

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (!element) return
  const offset = 72
  const elementPosition = element.getBoundingClientRect().top
  const offsetPosition = elementPosition + window.pageYOffset - offset

  window.scrollTo({
    top: offsetPosition,
    behavior: 'smooth'
  })
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

const handleScroll = () => {
  showBackToTop.value = window.pageYOffset > 320
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.rules-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem 3rem;
  color: #1f2937;
}

.hero {
  background: linear-gradient(
    135deg,
    rgba(var(--primary-700, 180, 83, 9), 0.96),
    rgba(var(--primary-500, 245, 158, 11), 0.94)
  );
  border-radius: 1.5rem;
  padding: 2.5rem;
  color: #fff;
  box-shadow: 0 20px 60px rgba(191, 148, 63, 0.35);
}

.hero h1 {
  font-size: 2.25rem;
  margin-bottom: 0.75rem;
}

.hero-desc {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  line-height: 1.7;
  margin-bottom: 1.5rem;
}

.hero-tag {
  display: inline-flex;
  align-items: center;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: rgba(255, 255, 255, 0.85);
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.12);
  margin-bottom: 0.75rem;
}

.hero-meta {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.meta-item {
  background: rgba(255, 255, 255, 0.12);
  border-radius: 1rem;
  padding: 0.75rem 1rem;
  min-width: 180px;
}

.meta-label {
  display: block;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.8);
}

.meta-value {
  font-weight: 600;
  font-size: 1.1rem;
}

.quick-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin: 1.5rem 0 2rem;
}

.quick-nav-btn {
  border: 1px solid rgba(var(--primary-400, 251, 191, 36), 0.4);
  background: rgba(var(--primary-50, 253, 244, 214), 0.8);
  color: rgb(var(--primary-800, 146, 64, 14));
  padding: 0.6rem 1.2rem;
  border-radius: 999px;
  font-weight: 600;
  transition: transform 0.2s, box-shadow 0.2s;
}

.quick-nav-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(245, 158, 11, 0.25);
}

.rule-card {
  background: #fff;
  border-radius: 1.25rem;
  padding: 2rem;
  margin-bottom: 2rem;
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 35px rgba(15, 23, 42, 0.06);
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.section-head h2 {
  font-size: 1.75rem;
  margin: 0.35rem 0 0;
  color: rgb(var(--primary-900, 120, 53, 15));
}

.section-head p {
  color: #4b5563;
  max-width: 520px;
  line-height: 1.6;
}

.section-badge {
  font-size: 0.85rem;
  color: rgb(var(--primary-700, 180, 83, 9));
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.info-item {
  background: rgba(var(--primary-50, 253, 244, 214), 0.7);
  border: 1px solid rgba(var(--primary-200, 253, 230, 138), 0.7);
  border-radius: 1rem;
  padding: 1.25rem;
}

.info-label {
  font-size: 0.9rem;
  color: #6b7280;
}

.info-value {
  font-size: 1.1rem;
  font-weight: 600;
  margin-top: 0.35rem;
}

.note-card {
  margin-top: 1.75rem;
  background: #111827;
  color: rgba(255, 255, 255, 0.9);
  border-radius: 1.25rem;
  padding: 1.5rem;
}

.note-card h3 {
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.note-card ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 0.75rem;
}

.note-card li {
  display: flex;
  gap: 0.65rem;
  align-items: center;
}

.note-card i {
  color: #fbbf24;
}

.play-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 1.5rem;
}

.play-card {
  border-radius: 1.25rem;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  background: #fff;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
  min-height: 100%;
}

.play-card-head {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.play-card h3 {
  margin-bottom: 0.25rem;
  font-size: 1.3rem;
}

.play-summary {
  color: #4b5563;
  margin: 0;
}

.play-win strong {
  color: rgb(var(--primary-700, 180, 83, 9));
  margin-right: 0.35rem;
}

.play-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 0.4rem;
  color: #4b5563;
}

.play-list li {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.play-list i {
  color: rgba(var(--primary-600, 217, 119, 6), 0.85);
  font-size: 0.85rem;
}

.play-sub {
  color: #6b7280;
}

.example-box {
  background: #f9fafb;
  border-radius: 0.75rem;
  padding: 1rem;
  border: 1px solid #e5e7eb;
  font-size: 0.95rem;
  line-height: 1.6;
}

.example-title {
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 0.5rem;
  color: rgb(var(--primary-700, 180, 83, 9));
}

.badge {
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
  background: rgba(var(--primary-100, 254, 243, 199), 0.8);
  color: rgb(var(--primary-700, 180, 83, 9));
}

.badge-soft {
  background: rgba(15, 23, 42, 0.08);
  color: #0f172a;
}

.accent-gold {
  border-color: rgba(var(--primary-400, 251, 191, 36), 0.55);
  box-shadow: 0 15px 35px rgba(252, 211, 77, 0.25);
}

.accent-green {
  border-color: rgba(16, 185, 129, 0.35);
}

.accent-purple {
  border-color: rgba(168, 85, 247, 0.35);
}

.accent-blue {
  border-color: rgba(59, 130, 246, 0.35);
}

.zodiac-years {
  display: grid;
  gap: 1.5rem;
}

.zodiac-year-card {
  border: 1px solid #e5e7eb;
  border-radius: 1.25rem;
  padding: 1.5rem;
}

.zodiac-year-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.zodiac-year-title {
  font-size: 1.2rem;
  font-weight: 600;
}

.zodiac-year-sub {
  color: #6b7280;
}

.zodiac-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  margin-top: 1.25rem;
}

.zodiac-item {
  background: #fefefe;
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  padding: 1rem;
  text-align: center;
  box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.02);
}

.zodiac-emoji {
  font-size: 1.8rem;
}

.zodiac-name {
  font-weight: 600;
  margin: 0.25rem 0;
}

.zodiac-numbers {
  color: #6b7280;
  font-size: 0.9rem;
}

.formula-card {
  margin-top: 1.5rem;
  padding: 1.25rem;
  border-radius: 1rem;
  background: rgba(99, 102, 241, 0.08);
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.formula-card h3 {
  margin-bottom: 0.5rem;
}

.timeline {
  display: grid;
  gap: 1.25rem;
}

.timeline-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.timeline-marker {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 999px;
  background: rgb(var(--primary-600, 217, 119, 6));
  color: #fff;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 25px rgba(217, 119, 6, 0.4);
}

.timeline-title {
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.timeline-desc {
  color: #6b7280;
  line-height: 1.6;
}

.result-card {
  margin-top: 2rem;
  border-radius: 1.25rem;
  background: #0f172a;
  color: rgba(255, 255, 255, 0.92);
  padding: 1.5rem;
}

.result-card h3 {
  margin-bottom: 0.75rem;
}

.result-card ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 0.6rem;
}

.result-card li {
  display: flex;
  gap: 0.6rem;
  align-items: center;
}

.result-card i {
  color: #34d399;
}

.faq-list {
  display: grid;
  gap: 1rem;
}

.faq-item {
  border: 1px solid #e5e7eb;
  border-radius: 1rem;
  overflow: hidden;
  background: #fff;
}

.faq-question {
  width: 100%;
  border: none;
  background: none;
  padding: 1.1rem 1.3rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  color: #111827;
}

.faq-answer {
  padding: 0 1.3rem 1.2rem;
  color: #4b5563;
  line-height: 1.6;
}

.faq-answer ul {
  margin: 0.5rem 0;
  padding-left: 1.25rem;
  color: #4b5563;
}

.faq-tip {
  margin-top: 0.5rem;
  display: flex;
  gap: 0.5rem;
  align-items: center;
  color: rgb(var(--primary-700, 180, 83, 9));
}

.back-to-top {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  width: 3rem;
  height: 3rem;
  border-radius: 999px;
  border: none;
  background: rgb(var(--primary-600, 217, 119, 6));
  color: #fff;
  box-shadow: 0 20px 35px rgba(217, 119, 6, 0.35);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s;
  z-index: 20;
}

.back-to-top:hover {
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .hero {
    padding: 2rem;
  }

  .hero h1 {
    font-size: 1.8rem;
  }

  .rule-card {
    padding: 1.5rem;
  }

  .section-head {
    flex-direction: column;
    align-items: flex-start;
  }

  .zodiac-grid {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  }

  .quick-nav {
    gap: 0.5rem;
  }

  .quick-nav-btn {
    width: 100%;
  }

  .back-to-top {
    right: 1rem;
    bottom: 1.5rem;
  }
}
</style>
