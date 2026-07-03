<template>
  <div class="rules-container">
    <!-- 页面标题 -->
    <div class="page-header">
      <h1 class="text-3xl font-bold mb-2">六合彩游戏规则</h1>
      <p class="header-subtitle">新澳门六合彩完整规则说明</p>
    </div>

    <!-- 快速导航 -->
    <div class="quick-nav mb-8">
      <button
        v-for="section in sections"
        :key="section.id"
        @click="scrollToSection(section.id)"
        class="nav-btn"
      >
        {{ section.title }}
      </button>
    </div>

    <!-- 技术信息 -->
    <section id="technical-info" class="rule-section">
      <h2 class="section-title">
        <i class="fas fa-info-circle mr-2"></i>技术信息
      </h2>
      <div class="section-content">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">游戏ID：</span>
            <span class="info-value">200</span>
          </div>
          <div class="info-item">
            <span class="info-label">游戏名称：</span>
            <span class="info-value">新澳门六合彩</span>
          </div>
          <div class="info-item">
            <span class="info-label">号码范围：</span>
            <span class="info-value">01-49 共49个号码</span>
          </div>
          <div class="info-item">
            <span class="info-label">开奖数量：</span>
            <span class="info-value">7个号码（6个正码 + 1个特码）</span>
          </div>
          <div class="info-item">
            <span class="info-label">生肖数量：</span>
            <span class="info-value">12个生肖（鼠、牛、虎、兔、龙、蛇、马、羊、猴、鸡、狗、猪）</span>
          </div>
          <div class="info-item">
            <span class="info-label">最小投注：</span>
            <span class="info-value">1 元</span>
          </div>
          <div class="info-item">
            <span class="info-label">最大单笔投注：</span>
            <span class="info-value">无上限（受风控预警）</span>
          </div>
          <div class="info-item">
            <span class="info-label">单期总投注：</span>
            <span class="info-value">无限制（受风控预警）</span>
          </div>
          <div class="info-item">
            <span class="info-label">风控预警：</span>
            <span class="info-value">单笔 ≥ 10,000元 或 单期 ≥ 50,000元</span>
          </div>
        </div>

        <div class="notice-box notice-blue">
          <h4 class="notice-title">
            <i class="fas fa-lightbulb mr-2"></i>特别说明
          </h4>
          <ul class="notice-list">
            <li>开奖结果由7个不重复的号码组成：m1-m6为正码，m7为特码</li>
            <li>49号为特殊号码，作为特码时为和局，不计输赢，退还本金</li>
            <li>生肖对照表每年变化一次，以当年农历生肖为准</li>
            <li>用户仅可选择"中"类型投注（预测号码会出现）</li>
            <li>"不中"投注为平台盈利逻辑，非用户投注选项</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- 玩法规则 -->
    <section id="play-rules" class="rule-section">
      <h2 class="section-title">
        <i class="fas fa-gamepad mr-2"></i>玩法规则
      </h2>
      <div class="section-content">
        <!-- 特码玩法 -->
        <div class="play-type-card">
          <div class="play-header">
            <h3 class="play-title play-title-special">1. 特码（Special Code）</h3>
            <span class="badge badge-special">高赔率</span>
          </div>
          <div class="play-body">
            <p class="mb-3"><strong class="text-highlight">规则：</strong>预测开奖的第7个号码（特码 m7）</p>
            <p class="mb-3"><strong class="text-highlight">中奖条件：</strong>投注的号码与特码完全一致</p>
            <p class="mb-3"><strong class="text-highlight">示例：</strong></p>
            <div class="example-box">
              <div class="mb-2">开奖号码：01, 12, 23, 34, 45, 49, <strong class="text-red">08</strong>（特码）</div>
              <div class="text-success">✓ 投注 08 → 中奖</div>
              <div class="text-muted">✗ 投注 01 → 不中奖（01是正码不是特码）</div>
            </div>
            <p class="mt-3 text-warning">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              <strong>特殊情况：</strong>特码为49时，视为和局，退还本金
            </p>
          </div>
        </div>

        <!-- 平码玩法 -->
        <div class="play-type-card">
          <div class="play-header">
            <h3 class="play-title play-title-normal">2. 平码（Normal Code）</h3>
            <span class="badge badge-normal">中等赔率</span>
          </div>
          <div class="play-body">
            <p class="mb-3"><strong class="text-highlight">规则：</strong>预测开奖的前6个号码（m1-m6）中包含的号码</p>
            <p class="mb-3"><strong class="text-highlight">中奖条件：</strong>投注的号码在正码（m1-m6）中出现</p>
            <p class="mb-3"><strong class="text-highlight">示例：</strong></p>
            <div class="example-box">
              <div class="mb-2">开奖号码：<strong class="text-success">01, 12, 23, 34, 45, 49</strong>, 08（特码）</div>
              <div class="text-success">✓ 投注 01 → 中奖</div>
              <div class="text-success">✓ 投注 23 → 中奖</div>
              <div class="text-muted">✗ 投注 08 → 不中奖（08是特码不是正码）</div>
            </div>
          </div>
        </div>

        <!-- 特肖玩法 -->
        <div class="play-type-card">
          <div class="play-header">
            <h3 class="play-title play-title-zodiac">3. 特肖（Special Zodiac）</h3>
            <span class="badge badge-zodiac">生肖玩法</span>
          </div>
          <div class="play-body">
            <p class="mb-3"><strong class="text-highlight">规则：</strong>预测特码（m7）对应的生肖</p>
            <p class="mb-3"><strong class="text-highlight">中奖条件：</strong>投注的生肖与特码对应的生肖一致</p>
            <p class="mb-3"><strong class="text-highlight">示例（以2025年为例）：</strong></p>
            <div class="example-box">
              <div class="mb-2">开奖特码：<strong class="text-red">08</strong></div>
              <div class="mb-2">2025年生肖对照：08 → 狗</div>
              <div class="text-success">✓ 投注"狗" → 中奖</div>
              <div class="text-muted">✗ 投注"鼠" → 不中奖</div>
            </div>
          </div>
        </div>

        <!-- 多肖玩法 -->
        <div class="play-type-card">
          <div class="play-header">
            <h3 class="play-title play-title-multi">4. 多肖（Multi Zodiac）</h3>
            <span class="badge badge-multi">组合玩法</span>
          </div>
          <div class="play-body">
            <p class="mb-3"><strong class="text-highlight">规则：</strong>三肖、四肖、五肖按原组合规则投注；6肖中特选择6个生肖，只看特码</p>
            <p class="mb-3"><strong class="text-highlight">支持类型：</strong>三肖、四肖、五肖、6肖中特</p>
            <p class="mb-3"><strong class="text-highlight">6肖中特中奖条件：</strong>特码对应生肖命中所选6个生肖中的任意一个即中奖；特码开49打和退本金</p>
            <p class="mb-3"><strong class="text-highlight">示例（以2025年为例）：</strong></p>
            <div class="example-box">
              <div class="mb-2">开奖特码：<strong class="text-red">26(龙)</strong></div>
              <div class="mt-2">
                <div class="text-success mb-1">✓ 投注"鼠、牛、虎、兔、龙、蛇"（6肖中特） → 中奖</div>
                <div class="text-muted mb-1">✗ 投注"鼠、牛、虎、兔、蛇、马"（6肖中特） → 不中奖</div>
                <div class="text-warning">特码开49 → 和局退本金</div>
              </div>
            </div>
            <p class="mt-3 text-warning">
              <i class="fas fa-lightbulb mr-2"></i>
              <strong>提示：</strong>6肖中特赔率为1.95（含本金），只以第7个开奖号特码判奖
            </p>
          </div>
        </div>

        <!-- 五行玩法说明 -->
        <div class="notice-box notice-indigo">
          <h4 class="notice-title">
            <i class="fas fa-yin-yang mr-2"></i>五行对照表
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="five-element-card">
              <div class="element-name">金</div>
              <div class="element-numbers">05, 06, 19, 20, 27, 28, 35, 36, 49</div>
            </div>
            <div class="five-element-card">
              <div class="element-name">木</div>
              <div class="element-numbers">01, 02, 09, 10, 17, 18, 31, 32, 39, 40, 47, 48</div>
            </div>
            <div class="five-element-card">
              <div class="element-name">水</div>
              <div class="element-numbers">07, 08, 15, 16, 23, 24, 37, 38, 45, 46</div>
            </div>
            <div class="five-element-card">
              <div class="element-name">火</div>
              <div class="element-numbers">03, 04, 11, 12, 25, 26, 33, 34, 41, 42</div>
            </div>
            <div class="five-element-card">
              <div class="element-name">土</div>
              <div class="element-numbers">13, 14, 21, 22, 29, 30, 43, 44</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 生肖对照表 -->
    <section id="zodiac-table" class="rule-section">
      <h2 class="section-title">
        <i class="fas fa-dragon mr-2"></i>生肖对照表
      </h2>
      <div class="section-content">
        <div class="notice-box notice-yellow">
          <h4 class="notice-title">
            <i class="fas fa-calendar-alt mr-2"></i>生肖变化规律
          </h4>
          <ul class="notice-list">
            <li><strong>基准年份：</strong>2025年（蛇年）</li>
            <li><strong>号码分配：</strong>1-49号码固定不变，生肖每年向前移动一位</li>
            <li><strong>循环周期：</strong>12年为一个完整循环</li>
            <li><strong>当年生肖：</strong>始终对应起始号码1，包含5个号码（如2025年蛇对应1,13,25,37,49）</li>
            <li><strong>其他生肖：</strong>各对应4个号码</li>
          </ul>
        </div>

        <!-- 2025年生肖表 -->
        <div class="year-zodiac-card">
          <h3 class="zodiac-year-title">
            2025年（蛇年）生肖对照表
          </h3>
          <div class="zodiac-grid">
            <div class="zodiac-item" v-for="zodiac in zodiac2025" :key="zodiac.name">
              <div class="zodiac-icon">{{ zodiac.emoji }}</div>
              <div class="zodiac-name">{{ zodiac.name }}</div>
              <div class="zodiac-numbers">{{ zodiac.numbers.join(', ') }}</div>
              <div class="zodiac-count">共{{ zodiac.numbers.length }}个号码</div>
            </div>
          </div>
        </div>

        <!-- 2026年生肖表 -->
        <div class="year-zodiac-card mt-6">
          <h3 class="zodiac-year-title zodiac-year-title-green">
            2026年（马年）生肖对照表（预览）
          </h3>
          <div class="zodiac-grid">
            <div class="zodiac-item" v-for="zodiac in zodiac2026" :key="zodiac.name">
              <div class="zodiac-icon">{{ zodiac.emoji }}</div>
              <div class="zodiac-name">{{ zodiac.name }}</div>
              <div class="zodiac-numbers">{{ zodiac.numbers.join(', ') }}</div>
              <div class="zodiac-count">共{{ zodiac.numbers.length }}个号码</div>
            </div>
          </div>
        </div>

        <!-- 计算公式说明 -->
        <div class="notice-box notice-purple">
          <h4 class="notice-title">
            <i class="fas fa-calculator mr-2"></i>年份生肖计算公式
          </h4>
          <div class="formula-box">
            <p class="mb-2"><strong class="text-highlight">公式：</strong>(year - 4) % 12 → 对应生肖顺序索引</p>
            <p class="mb-2"><strong class="text-highlight">生肖顺序：</strong>鼠(0)、牛(1)、虎(2)、兔(3)、龙(4)、蛇(5)、马(6)、羊(7)、猴(8)、鸡(9)、狗(10)、猪(11)</p>
            <p class="mb-2"><strong class="text-highlight">示例：</strong></p>
            <div class="example-box">
              <div>2025年：(2025 - 4) % 12 = 2021 % 12 = 5 → 蛇 ✓</div>
              <div>2026年：(2026 - 4) % 12 = 2022 % 12 = 6 → 马 ✓</div>
              <div>2027年：(2027 - 4) % 12 = 2023 % 12 = 7 → 羊 ✓</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 开奖与结算 -->
    <section id="draw-settlement" class="rule-section">
      <h2 class="section-title">
        <i class="fas fa-trophy mr-2"></i>开奖与结算
      </h2>
      <div class="section-content">
        <!-- 开奖流程 -->
        <div class="settlement-card">
          <h3 class="settlement-title">开奖流程</h3>
          <div class="timeline">
            <div class="timeline-item">
              <div class="timeline-marker">1</div>
              <div class="timeline-content">
                <h4 class="timeline-title">投注阶段</h4>
                <p class="timeline-desc">期次状态为"投注中"（status=2），用户可正常投注</p>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-marker">2</div>
              <div class="timeline-content">
                <h4 class="timeline-title">封盘</h4>
                <p class="timeline-desc">到达截止时间（close_time），期次状态变为"已封盘"（status=3）</p>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-marker">3</div>
              <div class="timeline-content">
                <h4 class="timeline-title">开奖</h4>
                <p class="timeline-desc">系统生成7个不重复的号码（1-49范围内）</p>
              </div>
            </div>
            <div class="timeline-item">
              <div class="timeline-marker">4</div>
              <div class="timeline-content">
                <h4 class="timeline-title">结算</h4>
                <p class="timeline-desc">自动判奖，中奖用户账户自动到账</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 判奖规则 -->
        <div class="settlement-card mt-6">
          <h3 class="settlement-title">判奖规则</h3>
          <div class="judge-rules">
            <div class="judge-rule-item">
              <div class="rule-type">特码判奖</div>
              <div class="rule-desc">开奖号码的第7个（m7）是否匹配投注号码</div>
              <div class="rule-formula">中奖 = (m7 == 投注号码)</div>
            </div>
            <div class="judge-rule-item">
              <div class="rule-type">正码判奖</div>
              <div class="rule-desc">开奖号码的第1-6个（m1-m6）是否包含投注号码</div>
              <div class="rule-formula">中奖 = (投注号码 ∈ [m1, m2, m3, m4, m5, m6])</div>
            </div>
            <div class="judge-rule-item">
              <div class="rule-type">特肖判奖</div>
              <div class="rule-desc">特码（m7）对应的生肖是否匹配投注生肖</div>
              <div class="rule-formula">中奖 = (m7的生肖 == 投注生肖)</div>
            </div>
            <div class="judge-rule-item">
              <div class="rule-type">多肖判奖</div>
              <div class="rule-desc">全部7个号码的生肖集合是否都包含在投注生肖中</div>
              <div class="rule-formula">中奖 = (所有开奖号码的生肖集合 ⊆ 投注生肖集合)</div>
            </div>
          </div>
        </div>

        <!-- 奖金计算 -->
        <div class="settlement-card mt-6">
          <h3 class="settlement-title">奖金计算</h3>
          <div class="prize-calc">
            <div class="calc-formula">
              <div class="formula-title">基础奖金公式</div>
              <div class="formula-content">
                奖金 = 投注金额 × 赔率
              </div>
            </div>
            <div class="calc-formula">
              <div class="formula-title">净利润公式</div>
              <div class="formula-content">
                净利润 = 奖金 - 投注金额
              </div>
            </div>
            <div class="mt-4">
              <h4 class="example-subtitle">示例：</h4>
              <div class="example-box">
                <div class="mb-2">投注：特码 08，金额 100元，赔率 42.0</div>
                <div class="mb-2">开奖：01, 12, 23, 34, 45, 49, <strong class="text-red">08</strong></div>
                <div class="text-success">
                  ✓ 中奖奖金 = 100 × 42.0 = 4,200元<br>
                  ✓ 净利润 = 4,200 - 100 = 4,100元
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 特殊情况处理 -->
        <div class="notice-box notice-red">
          <h4 class="notice-title">
            <i class="fas fa-exclamation-circle mr-2"></i>特殊情况处理
          </h4>
          <div class="space-y-3">
            <div class="special-case">
              <div class="case-title">49号和局规则</div>
              <div class="case-content">
                当特码（m7）为49时，视为和局，不计输赢，退还本金
              </div>
            </div>
            <div class="special-case">
              <div class="case-title">生肖重复计数</div>
              <div class="case-content">
                多肖玩法中，7个开奖号码的生肖可能重复，判奖时只看不重复的生肖集合
              </div>
            </div>
            <div class="special-case">
              <div class="case-title">结算状态</div>
              <div class="case-content">
                中奖记录生成后，用户账户余额自动更新，中奖状态标记为"已支付"（is_paid=1）
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 常见问题 -->
    <section id="faq" class="rule-section">
      <h2 class="section-title">
        <i class="fas fa-question-circle mr-2"></i>常见问题
      </h2>
      <div class="section-content">
        <div class="faq-list">
          <!-- Q1-Q8 FAQ items remain the same structure -->
          <div class="faq-item" v-for="i in 8" :key="i">
            <div class="faq-question" @click="toggleFaq(i)">
              <span>Q{{ i }}: {{ getFaqQuestion(i) }}</span>
              <i :class="['fas', activeFaq === i ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </div>
            <div v-show="activeFaq === i" class="faq-answer" v-html="getFaqAnswer(i)"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 返回顶部按钮 -->
    <button
      v-show="showBackToTop"
      @click="scrollToTop"
      class="back-to-top"
      title="返回顶部"
    >
      <i class="fas fa-arrow-up"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// 页面章节
const sections = [
  { id: 'technical-info', title: '技术信息' },
  { id: 'play-rules', title: '玩法规则' },
  { id: 'zodiac-table', title: '生肖对照' },
  { id: 'draw-settlement', title: '开奖结算' },
  { id: 'faq', title: '常见问题' }
]

// 2025年生肖对照表（蛇年）
const zodiac2025 = [
  { name: '蛇', emoji: '🐍', numbers: [1, 13, 25, 37, 49] },
  { name: '马', emoji: '🐴', numbers: [12, 24, 36, 48] },
  { name: '羊', emoji: '🐑', numbers: [11, 23, 35, 47] },
  { name: '猴', emoji: '🐵', numbers: [10, 22, 34, 46] },
  { name: '鸡', emoji: '🐔', numbers: [9, 21, 33, 45] },
  { name: '狗', emoji: '🐕', numbers: [8, 20, 32, 44] },
  { name: '猪', emoji: '🐷', numbers: [7, 19, 31, 43] },
  { name: '鼠', emoji: '🐭', numbers: [6, 18, 30, 42] },
  { name: '牛', emoji: '🐮', numbers: [5, 17, 29, 41] },
  { name: '虎', emoji: '🐯', numbers: [4, 16, 28, 40] },
  { name: '兔', emoji: '🐰', numbers: [3, 15, 27, 39] },
  { name: '龙', emoji: '🐲', numbers: [2, 14, 26, 38] }
]

// 2026年生肖对照表（马年）
const zodiac2026 = [
  { name: '马', emoji: '🐴', numbers: [1, 13, 25, 37, 49] },
  { name: '羊', emoji: '🐑', numbers: [12, 24, 36, 48] },
  { name: '猴', emoji: '🐵', numbers: [11, 23, 35, 47] },
  { name: '鸡', emoji: '🐔', numbers: [10, 22, 34, 46] },
  { name: '狗', emoji: '🐕', numbers: [9, 21, 33, 45] },
  { name: '猪', emoji: '🐷', numbers: [8, 20, 32, 44] },
  { name: '鼠', emoji: '🐭', numbers: [7, 19, 31, 43] },
  { name: '牛', emoji: '🐮', numbers: [6, 18, 30, 42] },
  { name: '虎', emoji: '🐯', numbers: [5, 17, 29, 41] },
  { name: '兔', emoji: '🐰', numbers: [4, 16, 28, 40] },
  { name: '龙', emoji: '🐲', numbers: [3, 15, 27, 39] },
  { name: '蛇', emoji: '🐍', numbers: [2, 14, 26, 38] }
]

// FAQ数据
const faqData = {
  1: {
    question: '什么是"中"和"不中"投注类型？',
    answer: `
      <p><strong>"中"投注：</strong>预测号码会出现，是用户可选择的投注类型。</p>
      <p><strong>"不中"投注：</strong>预测号码不会出现，这是平台盈利逻辑，用户无法选择此类型投注。</p>
      <p class="text-warning mt-2">
        <i class="fas fa-info-circle mr-2"></i>
        前端投注时，后端会强制设置为"win"（中）类型，忽略前端传值。
      </p>
    `
  },
  2: {
    question: '生肖对照表为什么每年变化？',
    answer: `
      <p>生肖对照表遵循中国农历生肖纪年规律：</p>
      <ul class="notice-list mt-2">
        <li>号码1-49固定不变</li>
        <li>每年生肖向前移动一位（12年循环）</li>
        <li>当年生肖始终对应起始号1，包含5个号码</li>
        <li>其他生肖各对应4个号码</li>
      </ul>
      <p class="text-warning mt-2">
        <i class="fas fa-lightbulb mr-2"></i>
        例如：2025年（蛇年）蛇对应1,13,25,37,49；2026年（马年）马对应1,13,25,37,49
      </p>
    `
  },
  3: {
    question: '特码为49时为什么是和局？',
    answer: `
      <p>49号在六合彩中是特殊号码：</p>
      <ul class="notice-list mt-2">
        <li>当特码（m7）为49时，视为和局</li>
        <li>不计输赢，系统自动退还本金</li>
        <li>49作为正码（m1-m6）时，正常参与判奖</li>
      </ul>
      <p class="text-info mt-2">
        <i class="fas fa-shield-alt mr-2"></i>
        此规则是为了平衡游戏风险，保护平台和玩家双方利益。
      </p>
    `
  },
  4: {
    question: '多肖玩法如何判奖？',
    answer: `
      <p>多肖玩法的判奖逻辑：</p>
      <ol class="notice-list mt-2">
        <li>三肖、四肖、五肖按原组合规则判断</li>
        <li>6肖中特只取第7个开奖号作为特码</li>
        <li>特码生肖命中所选6个生肖中的任意一个，即为中奖</li>
        <li>特码开49时打和，退还本金</li>
      </ol>
      <div class="example-box mt-3">
        <p class="font-semibold mb-2">示例（2025年）：</p>
        <p class="mb-1">开奖特码：26(龙)</p>
        <p class="text-success mt-2">✓ 投注{鼠,牛,虎,兔,龙,蛇}（6肖中特）→ 中奖</p>
        <p class="text-muted">✗ 投注{鼠,牛,虎,兔,蛇,马}（6肖中特）→ 不中奖</p>
      </div>
    `
  },
  5: {
    question: '投注限额是多少？',
    answer: `
      <p>投注限额规则：</p>
      <ul class="notice-list mt-2">
        <li><strong>最小投注：</strong>1元</li>
        <li><strong>最大单笔投注：</strong>无上限（受风控预警）</li>
        <li><strong>单期总投注：</strong>无限制（受风控预警）</li>
      </ul>
      <div class="notice-box notice-orange mt-3">
        <p class="text-warning font-semibold">风控预警机制：</p>
        <ul class="notice-list mt-2">
          <li>单笔投注 ≥ 10,000元时触发预警</li>
          <li>单期总投注 ≥ 50,000元时触发预警</li>
          <li>预警后可能需要人工审核</li>
        </ul>
      </div>
    `
  },
  6: {
    question: '奖金何时到账？',
    answer: `
      <p>奖金到账流程：</p>
      <ol class="notice-list mt-2">
        <li>开奖结果公布后，系统自动进行判奖</li>
        <li>生成中奖记录（la_winning_record表）</li>
        <li>用户账户余额自动更新（la_user_account表）</li>
        <li>中奖状态标记为"已支付"（is_paid=1）</li>
      </ol>
      <p class="text-success mt-3">
        <i class="fas fa-check-circle mr-2"></i>
        整个过程自动完成，无需手动操作，通常在开奖后几秒内到账。
      </p>
    `
  },
  7: {
    question: '如何查看历史开奖结果？',
    answer: `
      <p>查看历史开奖的方法：</p>
      <ul class="notice-list mt-2">
        <li>访问<strong>"开奖结果"</strong>页面</li>
        <li>查看最近的开奖记录和统计信息</li>
        <li>每个开奖结果显示：期号、日期、7个号码、生肖、五行、奇偶等属性</li>
        <li>支持按日期筛选和分页浏览</li>
      </ul>
    `
  },
  8: {
    question: '五行在游戏中有什么作用？',
    answer: `
      <p>五行（金、木、水、火、土）是号码的附加属性：</p>
      <ul class="notice-list mt-2">
        <li>每个号码对应一个固定的五行属性</li>
        <li>五行主要用于统计分析和走势参考</li>
        <li>当前版本暂不支持五行玩法投注</li>
        <li>开奖结果页面会显示每个号码的五行属性</li>
      </ul>
      <p class="text-muted mt-2 text-sm">
        <i class="fas fa-road mr-2"></i>
        未来可能会增加五行相关的投注玩法
      </p>
    `
  }
}

const getFaqQuestion = (index) => faqData[index]?.question || ''
const getFaqAnswer = (index) => faqData[index]?.answer || ''

// FAQ 激活状态
const activeFaq = ref(null)

// 返回顶部按钮显示状态
const showBackToTop = ref(false)

// 切换FAQ展开/收起
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index
}

// 滚动到指定章节
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    const offset = 80
    const elementPosition = element.getBoundingClientRect().top
    const offsetPosition = elementPosition + window.pageYOffset - offset

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    })
  }
}

// 返回顶部
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

// 监听滚动事件
const handleScroll = () => {
  showBackToTop.value = window.pageYOffset > 300
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
/* ================================
   基础容器
   ================================ */
.rules-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  min-height: calc(100vh - 120px);
}

/* ================================
   页面标题
   ================================ */
.page-header {
  text-align: center;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 3px solid rgba(253, 224, 71, 0.4);
}

.page-header h1 {
  color: #fef3c7;
</script>
