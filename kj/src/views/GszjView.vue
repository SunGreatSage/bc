<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const quickRanks = [
  { title: '第123名', icon: '▣', color: '#ff37c8' },
  { title: '连中榜', icon: '✓', color: '#7b42f2' },
  { title: '连错榜', icon: '≡', color: '#21d76d' },
  { title: '高手榜', icon: '♛', color: '#ff4b3e' },
  { title: '新手榜', icon: '♟', color: '#2fc8ef' },
  { title: '关注榜', icon: '★', color: '#ffc11a' },
]

const animeAvatars = [
  'https://api.dicebear.com/9.x/adventurer/svg?seed=haru&backgroundColor=b6e3f4',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=akira&backgroundColor=ffdfbf',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=yuki&backgroundColor=c0aede',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=sora&backgroundColor=d1d4f9',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=mika&backgroundColor=ffd5dc',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=ren&backgroundColor=c8f7c5',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=nana&backgroundColor=ffe9a8',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=kai&backgroundColor=bde7ff',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=aoi&backgroundColor=f6c1d1',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=rio&backgroundColor=ccf0e1',
  'https://api.dicebear.com/9.x/adventurer/svg?seed=emi&backgroundColor=f2d3b1',
]

const rankSections = [
  {
    title: '总榜',
    items: [
      { name: '壮观的狗', avatar: animeAvatars[0], desc: '116期:澳门六合彩九肖中特', rate: '28800%', streak: '24连中' },
      { name: '有琴小蚂蚁', avatar: animeAvatars[1], desc: '116期:澳门六合彩九肖中特', rate: '31500%', streak: '21连中' },
      { name: '年爱豌豆笛', avatar: animeAvatars[2], desc: '116期:澳门六合彩九肖中特', rate: '29700%', streak: '20连中' },
    ],
  },
  {
    title: '平特一肖',
    items: [
      { name: '坚定的斑马', avatar: animeAvatars[3], desc: '116期:澳门六合彩平特一肖', rate: '9250%', streak: '1连中' },
      { name: '温柔的山风', avatar: animeAvatars[4], desc: '116期:澳门六合彩平特一肖', rate: '8800%', streak: '3连中' },
    ],
  },
  {
    title: '九肖中特',
    items: [
      { name: '负责的芹菜', avatar: animeAvatars[5], desc: '116期:澳门六合彩九肖中特', rate: '33900%', streak: '11连中' },
      { name: '美好的期待', avatar: animeAvatars[6], desc: '116期:澳门六合彩九肖中特', rate: '32700%', streak: '0连中' },
      { name: '成就的雨', avatar: animeAvatars[7], desc: '116期:澳门六合彩九肖中特', rate: '32700%', streak: '2连中' },
    ],
  },
  {
    title: '六肖连中',
    items: [
      { name: '幸运的海豚', avatar: animeAvatars[8], desc: '116期:香港六合彩六肖连中', rate: '21800%', streak: '8连中' },
      { name: '清醒的松树', avatar: animeAvatars[9], desc: '116期:香港六合彩六肖连中', rate: '19600%', streak: '6连中' },
      { name: '沉稳的月光', avatar: animeAvatars[10], desc: '116期:香港六合彩六肖连中', rate: '18400%', streak: '5连中' },
    ],
  },
]

onMounted(() => {
  document.title = '高手之家 - 金算盘论坛'
})
</script>

<template>
  <main class="master-page">
    <header class="master-header">
      <button class="back-button" type="button" @click="router.back()">‹</button>
      <nav class="top-nav">
        <a class="active" href="#"><span>♜</span>高手</a>
        <a href="#"><span>▱</span>论坛</a>
        <a href="#"><span>♙</span>我的</a>
      </nav>
    </header>

    <section class="market-tabs">
      <button class="active" type="button">澳门六合彩</button>
      <button type="button">香港六合彩</button>
    </section>

    <section class="quick-ranks" aria-label="榜单分类">
      <a v-for="item in quickRanks" :key="item.title" href="#">
        <span :style="{ background: item.color }">{{ item.icon }}</span>
        <strong>{{ item.title }}</strong>
      </a>
    </section>

    <section class="ad-banner">
      <div class="coin">金算盘</div>
      <strong>新葡京娱乐城 77777.com</strong>
      <span>全球综合娱乐领军平台</span>
      <em>服务第1　信誉第1　优惠第1</em>
    </section>

    <section v-for="section in rankSections" :key="section.title" class="rank-section">
      <h2>{{ section.title }}</h2>

      <article v-for="(item, index) in section.items" :key="`${section.title}-${item.name}`" class="rank-item">
        <div class="medal" :class="`medal--${index + 1}`">{{ index + 1 }}</div>
        <img class="avatar" :src="item.avatar" :alt="item.name" loading="lazy" />
        <div class="rank-copy">
          <div class="name-line">
            <strong>{{ item.name }}</strong>
            <span>已发帖</span>
          </div>
          <p>{{ item.desc }}</p>
        </div>
        <div class="rank-meta">
          <span>回报率{{ item.rate }}</span>
          <strong>{{ item.streak }}</strong>
        </div>
      </article>
    </section>

    <button class="float-home" type="button" @click="router.push('/home')">⌂</button>
  </main>
</template>

<style scoped>
.master-page {
  min-height: 100vh;
  padding-bottom: 28px;
  background: #f5f6f7;
  color: #111;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

a {
  color: inherit;
  text-decoration: none;
}

.master-header {
  position: sticky;
  top: 0;
  z-index: 30;
  display: grid;
  grid-template-columns: clamp(44px, 9vw, 58px) 1fr;
  align-items: end;
  height: clamp(58px, 12vw, 74px);
  background: #08c76b;
}

.back-button {
  height: clamp(58px, 12vw, 74px);
  border: 0;
  background: transparent;
  color: #fff;
  font-size: clamp(30px, 7vw, 40px);
}

.top-nav {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  align-items: end;
  height: clamp(58px, 12vw, 74px);
}

.top-nav a {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: clamp(4px, 1.2vw, 7px);
  height: clamp(46px, 9.6vw, 58px);
  color: #fff;
  font-size: clamp(16px, 4.2vw, 22px);
  font-weight: 900;
}

.top-nav a.active {
  border-radius: 15px 15px 0 0;
  background: #fff;
  color: #08c76b;
}

.top-nav span {
  font-size: clamp(20px, 5vw, 27px);
  line-height: 1;
}

.market-tabs {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  height: clamp(52px, 11.5vw, 70px);
  border-bottom: 1px solid #ddd;
  background: #fff;
}

.market-tabs button {
  position: relative;
  border: 0;
  background: transparent;
  color: #111;
  font-size: clamp(16px, 4.2vw, 21px);
  font-weight: 900;
}

.market-tabs button.active {
  color: #08c76b;
}

.market-tabs button.active::after {
  content: "";
  position: absolute;
  left: 50%;
  bottom: 0;
  width: clamp(48px, 10vw, 64px);
  height: 4px;
  border-radius: 999px;
  background: #08c76b;
  transform: translateX(-50%);
}

.quick-ranks {
  display: flex;
  gap: clamp(14px, 4.3vw, 24px);
  overflow-x: auto;
  padding: clamp(16px, 4vw, 22px) clamp(12px, 3vw, 18px) 14px;
  background: #fff;
  scrollbar-width: none;
}

.quick-ranks::-webkit-scrollbar {
  display: none;
}

.quick-ranks a {
  display: grid;
  justify-items: center;
  gap: 7px;
  flex: 0 0 clamp(58px, 14vw, 74px);
}

.quick-ranks span {
  display: grid;
  place-items: center;
  width: clamp(46px, 12vw, 58px);
  height: clamp(46px, 12vw, 58px);
  border-radius: clamp(12px, 3vw, 16px);
  color: #fff;
  font-size: clamp(24px, 6vw, 31px);
  font-weight: 900;
  box-shadow: inset 0 -8px 14px rgb(0 0 0 / 12%);
}

.quick-ranks strong {
  color: #050505;
  font-size: clamp(13px, 3.7vw, 17px);
  font-weight: 900;
  white-space: nowrap;
}

.quick-ranks a:first-child strong {
  color: #e91cd0;
}

.ad-banner {
  display: grid;
  grid-template-columns: clamp(50px, 12vw, 64px) 1fr auto;
  grid-template-rows: repeat(2, auto);
  gap: 2px 8px;
  align-items: center;
  margin: 0 0 14px;
  padding: 7px 9px;
  border: 1px solid #dfb538;
  background:
    linear-gradient(90deg, rgb(255 255 255 / 92%), rgb(255 246 203 / 92%)),
    repeating-linear-gradient(135deg, #f5c44c 0 8px, #fff0af 8px 16px);
  box-shadow: 0 1px 6px rgb(0 0 0 / 12%);
}

.coin {
  grid-row: 1 / 3;
  display: grid;
  place-items: center;
  height: clamp(34px, 8vw, 42px);
  border-radius: 8px;
  background: linear-gradient(180deg, #fff3bd, #d99a18);
  color: #a96e00;
  font-size: clamp(10px, 2.8vw, 13px);
  font-weight: 900;
}

.ad-banner strong {
  color: #ef1a1a;
  font-size: clamp(13px, 3.7vw, 17px);
  line-height: 1.1;
}

.ad-banner span {
  color: #cc9300;
  font-size: clamp(12px, 3.5vw, 15px);
  font-weight: 900;
}

.ad-banner em {
  grid-row: 1 / 3;
  grid-column: 3;
  color: #0bbd68;
  font-size: clamp(10px, 2.8vw, 13px);
  font-style: normal;
  font-weight: 900;
}

.rank-section {
  margin-top: 14px;
  background: #fff;
}

.rank-section h2 {
  display: inline-grid;
  place-items: center;
  min-width: clamp(72px, 18vw, 90px);
  height: clamp(38px, 9vw, 46px);
  margin: 0 0 10px;
  padding: 0 18px;
  border-radius: 0 999px 999px 0;
  background: #08c76b;
  color: #fff;
  font-size: clamp(17px, 4.5vw, 22px);
  font-weight: 900;
}

.rank-item {
  display: grid;
  grid-template-columns: clamp(26px, 7vw, 34px) clamp(44px, 12vw, 56px) 1fr auto;
  gap: clamp(5px, 1.8vw, 8px);
  align-items: center;
  min-height: clamp(68px, 18vw, 82px);
  padding: 0 clamp(10px, 3vw, 16px) 0 8px;
  border-bottom: 1px solid #e5e5e5;
}

.medal {
  display: grid;
  place-items: center;
  width: clamp(21px, 5.5vw, 26px);
  height: clamp(21px, 5.5vw, 26px);
  border-radius: 50%;
  background: linear-gradient(180deg, #ffcf4d, #ff9a11);
  color: #fff;
  font-size: clamp(10px, 2.8vw, 13px);
  font-weight: 900;
}

.medal--2 {
  background: linear-gradient(180deg, #6fe8ff, #34a8ff);
}

.medal--3 {
  background: linear-gradient(180deg, #ffd27d, #df8b29);
}

.avatar {
  display: block;
  width: clamp(44px, 12vw, 56px);
  height: clamp(44px, 12vw, 56px);
  border-radius: 50%;
  background: #eef3f8;
  object-fit: cover;
  box-shadow: inset 0 0 0 1px rgb(255 255 255 / 70%);
}

.rank-copy {
  min-width: 0;
}

.name-line {
  display: flex;
  align-items: center;
  gap: clamp(5px, 1.5vw, 8px);
  min-width: 0;
}

.name-line strong {
  overflow: hidden;
  color: #60646f;
  font-size: clamp(15px, 4vw, 18px);
  font-weight: 900;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.name-line span {
  flex: none;
  padding: 1px 5px;
  border: 1px solid #ff1717;
  border-radius: 6px;
  color: #ff1717;
  font-size: clamp(11px, 3vw, 13px);
}

.rank-copy p {
  margin: 7px 0 0;
  overflow: hidden;
  color: #555;
  font-size: clamp(12px, 3.4vw, 14px);
  text-overflow: ellipsis;
  white-space: nowrap;
}

.rank-meta {
  display: grid;
  justify-items: end;
  gap: clamp(8px, 2.8vw, 12px);
  white-space: nowrap;
}

.rank-meta span {
  color: #222;
  font-size: clamp(12px, 3.2vw, 14px);
}

.rank-meta strong {
  color: #ff1717;
  font-size: clamp(14px, 3.8vw, 17px);
  font-weight: 500;
}

.float-home {
  position: fixed;
  left: 6px;
  bottom: 98px;
  z-index: 25;
  display: grid;
  place-items: center;
  width: 52px;
  height: 52px;
  border: 0;
  border-radius: 999px;
  background: rgb(130 130 140 / 78%);
  color: #fff;
  font-size: 31px;
}

@media (min-width: 601px) {
  .master-page {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 430px) {
  .master-header {
    grid-template-columns: 46px 1fr;
  }

  .top-nav a {
    gap: 4px;
    font-size: 17px;
  }

  .top-nav span {
    font-size: 21px;
  }

  .market-tabs button {
    font-size: 17px;
  }

  .quick-ranks {
    gap: 16px;
    padding-inline: 12px;
  }

  .quick-ranks a {
    flex-basis: 62px;
  }

  .quick-ranks span {
    width: 50px;
    height: 50px;
    font-size: 26px;
  }

  .quick-ranks strong {
    font-size: 14px;
  }

  .ad-banner strong {
    font-size: 14px;
  }

  .ad-banner span {
    font-size: 12px;
  }

  .ad-banner em {
    font-size: 10px;
  }

  .rank-section h2 {
    height: 40px;
    font-size: 18px;
  }

  .rank-item {
    grid-template-columns: 28px 48px 1fr auto;
    gap: 6px;
    min-height: 72px;
    padding-right: 10px;
  }

  .avatar {
    width: 48px;
    height: 48px;
  }

  .name-line strong {
    font-size: 16px;
  }

  .rank-copy p {
    font-size: 12px;
  }

  .rank-meta span {
    font-size: 12px;
  }

  .rank-meta strong {
    font-size: 14px;
  }
}
</style>
