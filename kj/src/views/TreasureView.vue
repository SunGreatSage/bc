<script setup>
import { computed, ref } from 'vue'

const activeSection = ref('sites')
const activeMarket = ref('澳彩')
const keyword = ref('')

const websiteItems = [
  { name: '118图库', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/10/31/20231031133022-118.png' },
  { name: '六合宝典', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/11/26/20241126200925-1055287145.png' },
  { name: '天线宝宝', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/11/25/20241125201147-1055287145.png' },
  { name: '中特网', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/09/20250909214751-1055287145.png' },
  { name: '123开奖', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/06/09/20230609230522-123769.png' },
  { name: '第一炮', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/10/03/20251003102204-49501D566ABF548B592A1581EC5A491C.png' },
  { name: '小龙女', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/02/20/20240220122952-20230714130504--866120981.png' },
  { name: '118开奖', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/12/13/20231213103912-1036815031.png' },
  { name: '澳彩开奖', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2020/06/26/20200626182219-00853kj.png' },
  { name: '澳利澳', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/06/18/20230618105648-397065441.png' },
  { name: '49导航', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/01/13/20240113210048-1479556534.png' },
  { name: '澳彩网', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/06/12/20240612214353-128452119.jpg' },
  { name: '金牛版', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/08/25/20250825230724--1142931584.jpg' },
  { name: '六合风云', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/19/20250919003642-373934198.png' },
  { name: '王中王', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/20/20250320151425-1056976411.png' },
  { name: '澳门彩库', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2022/05/28/20220528132607--493306023.png' },
  { name: '雷锋网', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/06/12/20240612214420--875844936.jpg', muted: true },
  { name: '四不像', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/07/14/20230714125444--1780999522.png', muted: true },
  { name: '马会传真', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/06/12/20240612214450--2060572198.jpg', muted: true },
  { name: '澳门彩票', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2022/05/28/20220528132711--1176651365.png', muted: true },
  { name: '四九论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/21/20250921225316-587056290.png', muted: true },
  { name: '金多宝', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/20/20250320143136-49.jpg', muted: true },
  { name: '网址大全', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/04/25/20230425164709-267377083.png', muted: true },
  { name: '天天好彩', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/12/13/20231213192755--1299230753.png', muted: true },
  { name: '澳门跑狗', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2024/06/12/20240612214511--478385583.jpg', muted: true },
  { name: '神算子', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2020/06/26/20200626184146-171212.png', muted: true },
  { name: '财富论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/20/20250320144135-49.png', muted: true },
  { name: '大三巴', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/10/03/20251003114455-155E431EFC8642BF7CE6039C1E05E2DC.png', muted: true },
  { name: '澳彩图库', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2020/06/26/20200626182248-00853tk.png', muted: true },
  { name: '澳门玄机', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2023/12/13/20231213104227--1419882687.png', muted: true },
  { name: '蘭桂坊', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/19/20250919144237--407657712.jpg', muted: true },
  { name: '信息网', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/19/20250919144318-1354643992.jpg', muted: true },
  { name: '天上人间', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/09/30/20250930012530-82BCA37A3C46DB6B0CB66A5822107CDA.png', muted: true },
  { name: '港澳通行证', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/11/23/20251123121331--546897475.png', muted: true },
  { name: '港澳好运来', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/11/23/20251123121417-1012896584.png', muted: true },
  { name: '港澳天下彩', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/11/23/20251123121445--1588713004.png', muted: true },
  { name: '云顶论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/20/20250320144217-49.png', muted: true },
  { name: '如意论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328200609--1729945533.jpg', muted: true },
  { name: '澳门通天', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/11/05/20251105172046-777.jpg', muted: true },
  { name: '至尊论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328200840-1320312659.jpg', muted: true },
  { name: '财神论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/10/27/20251027204217-1315086979.png', muted: true },
  { name: '赌王论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328200957-943841836.jpg', muted: true },
  { name: '澳彩通', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/05/06/20250506133027--856398782.jpg', muted: true },
  { name: '七星论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328201244-1652680010.jpg', muted: true },
  { name: '九天论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328201319--1853789863.jpg', muted: true },
  { name: '金牛论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/10/27/20251027204358-1315086979.png', muted: true },
  { name: '红牛论坛', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328201445--716917700.jpg', muted: true },
  { name: '东方红', image: 'https://jmz.jlidesign.com:4949/unite49files/amyd/2025/03/28/20250328201711--1632387077.jpg', muted: true },
]

const sponsorItems = [
  { name: '49彩票', image: 'https://jmz.jlidesign.com:4949/unite49files/tmp/2026/03/18/20260318151409--1063654332.gif', url: 'https://tz.9049tz.com:4949' },
  { name: '澳门威尼斯人', image: 'https://jmz.jlidesign.com:4949/unite49files/tmp/2026/01/20/20260120201927-49.jpg', url: 'https://j.xms77.com:4949/' },
  { name: '金沙娱乐城', image: 'https://jmz.jlidesign.com:4949/unite49files/tmp/2025/10/06/20251006143139-1229966334.jpg', url: 'https://j.yunlingdui.com:7700/' },
  { name: '新葡京娱乐城', image: 'https://jmz.jlidesign.com:4949/unite49files/tmp/2025/04/12/20250412214849-800x190.jpg', url: 'https://j.tianditutangshan.net:8443/' },
  { name: '顺盈彩票', image: 'https://jmz.jlidesign.com:4949/unite49files/tmp/2024/11/16/20241116133419-20023056.jpg', url: 'https://jj.2468tz.com:2468/#0930' },
]

const filteredWebsiteItems = computed(() => {
  const text = keyword.value.trim()
  if (!text) return websiteItems
  return websiteItems.filter((item) => item.name.includes(text))
})
</script>

<template>
  <main class="treasure-page">
    <header class="treasure-hero">
      <nav class="top-switch">
        <button :class="{ active: activeSection === 'sites' }" type="button" @click="activeSection = 'sites'">
          网址大全
        </button>
        <button :class="{ active: activeSection === 'sponsor' }" type="button" @click="activeSection = 'sponsor'">
          担保平台
        </button>
      </nav>

      <div class="search-box">
        <span>⌕</span>
        <input v-model="keyword" type="search" placeholder="请输入您想要的内容">
        <button type="button">搜索</button>
      </div>
    </header>

    <nav v-if="activeSection === 'sites'" class="market-switch">
      <button :class="{ active: activeMarket === '澳彩' }" type="button" @click="activeMarket = '澳彩'">澳彩</button>
      <button :class="{ active: activeMarket === '港彩' }" type="button" @click="activeMarket = '港彩'">港彩</button>
    </nav>

    <section v-if="activeSection === 'sites'" class="website-grid">
      <a v-for="item in filteredWebsiteItems" :key="`${activeMarket}-${item.name}`" class="website-card" href="#">
        <img :src="item.image" :alt="item.name">
        <p :class="{ muted: item.muted }">{{ item.name }}</p>
      </a>
      <p v-if="filteredWebsiteItems.length === 0" class="empty-text">暂无匹配内容</p>
    </section>

    <section v-else class="sponsor-list">
      <a v-for="item in sponsorItems" :key="item.name" class="sponsor-card" :href="item.url" target="_blank">
        <img :src="item.image" :alt="item.name">
        <div>
          <strong>{{ item.name }}</strong>
          <span>点击访问 ›</span>
        </div>
      </a>
    </section>

    <nav class="treasure-bottom-nav">
      <RouterLink to="/home"><span>⌂</span><strong>49图库</strong></RouterLink>
      <RouterLink to="/find"><span>♣</span><strong>发现</strong></RouterLink>
      <RouterLink class="center" to="/gszj"><span>⌂</span><strong>高手之家</strong></RouterLink>
      <RouterLink class="active" to="/treasure"><span>▣</span><strong>寻宝</strong></RouterLink>
      <RouterLink to="/my"><span>♙</span><strong>我的</strong></RouterLink>
    </nav>
  </main>
</template>

<style scoped>
.treasure-page {
  min-height: 100vh;
  padding-bottom: 74px;
  background: #fff;
  color: #333;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

.treasure-page a {
  color: inherit;
  text-decoration: none;
}

.treasure-hero {
  padding: clamp(18px, 5vw, 34px) clamp(16px, 4vw, 22px) 16px;
  background: #08c765;
}

.top-switch {
  display: flex;
  justify-content: center;
  gap: clamp(44px, 15vw, 86px);
  margin-bottom: 22px;
}

.top-switch button {
  position: relative;
  border: 0;
  background: transparent;
  color: rgba(255, 255, 255, 0.85);
  font-size: clamp(19px, 5vw, 28px);
  font-weight: 800;
}

.top-switch button.active {
  color: #fff;
}

.top-switch button.active::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: -10px;
  width: 34px;
  height: 5px;
  border-radius: 999px;
  background: #fff;
  transform: translateX(-50%);
}

.search-box {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 9px;
}

.search-box span {
  display: grid;
  place-items: center;
  width: 34px;
  height: 44px;
  margin-right: -46px;
  z-index: 1;
  color: #9b9b9b;
  font-size: 24px;
}

.search-box input {
  width: 100%;
  height: 44px;
  padding: 0 16px 0 42px;
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #333;
  font-size: clamp(15px, 4vw, 20px);
  outline: none;
}

.search-box button {
  border: 0;
  background: transparent;
  color: #fff;
  font-size: clamp(18px, 5vw, 25px);
  font-weight: 900;
}

.market-switch {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  height: clamp(62px, 13vw, 76px);
  border-bottom: 1px solid #eee;
  background: #fff;
}

.market-switch button {
  position: relative;
  border: 0;
  background: transparent;
  color: #111;
  font-size: clamp(20px, 5vw, 26px);
  font-weight: 900;
}

.market-switch button.active {
  color: #08c765;
}

.market-switch button.active::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: 8px;
  width: 34px;
  height: 5px;
  border-radius: 999px;
  background: #08c765;
  transform: translateX(-50%);
}

.website-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: clamp(16px, 4vw, 28px) clamp(12px, 4vw, 24px);
  padding: clamp(22px, 5vw, 32px) clamp(18px, 4vw, 24px) 28px;
}

.website-card {
  display: grid;
  min-width: 0;
  justify-items: center;
  gap: 8px;
}

.website-card img {
  width: 100%;
  height: clamp(42px, 10.5vw, 64px);
  border-radius: 6px;
  background: #f4f4f4;
  object-fit: contain;
}

.website-card p {
  width: 100%;
  margin: 0;
  color: #333;
  font-size: clamp(14px, 3.6vw, 21px);
  line-height: 1.2;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.website-card p.muted {
  padding: 6px 4px;
  border-radius: 4px;
  background: #f0f0f0;
}

.empty-text {
  grid-column: 1 / -1;
  margin: 36px 0;
  color: #999;
  font-size: 16px;
  text-align: center;
}

.sponsor-list {
  display: grid;
  gap: 12px;
  padding: 18px 14px 28px;
  background: #f6f6f6;
}

.sponsor-card {
  display: grid;
  grid-template-columns: 45% 1fr;
  align-items: center;
  gap: 12px;
  padding: 10px;
  border-radius: 10px;
  background: #fff;
}

.sponsor-card img {
  width: 100%;
  height: 76px;
  border-radius: 8px;
  object-fit: cover;
}

.sponsor-card div {
  display: grid;
  gap: 12px;
}

.sponsor-card strong {
  font-size: 18px;
}

.sponsor-card span {
  color: #666;
  font-size: 14px;
}

.treasure-bottom-nav {
  position: fixed;
  left: 50%;
  bottom: 0;
  z-index: 30;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  width: min(100%, 600px);
  height: 64px;
  border-top: 1px solid #e9e9e9;
  background: #fff;
  transform: translateX(-50%);
}

.treasure-bottom-nav a {
  position: relative;
  display: grid;
  align-content: center;
  justify-items: center;
  gap: 2px;
  color: #555;
  font-size: 13px;
}

.treasure-bottom-nav span {
  color: #12c878;
  font-size: 23px;
  line-height: 1;
}

.treasure-bottom-nav strong {
  font-weight: 500;
}

.treasure-bottom-nav .active {
  color: #0ac66d;
}

.treasure-bottom-nav .center span {
  display: grid;
  place-items: center;
  width: 48px;
  height: 48px;
  margin-top: -28px;
  border: 5px solid #fff;
  border-radius: 999px;
  background: #08c765;
  color: #fff;
  box-shadow: 0 2px 10px rgb(0 0 0 / 12%);
}

@media (min-width: 601px) {
  .treasure-page {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 360px) {
  .website-grid {
    gap: 14px 10px;
    padding-inline: 14px;
  }
}
</style>
