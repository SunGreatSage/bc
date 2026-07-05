<script setup>
const logoUrl = 'https://jmz.jlidesign.com:4949/unite49files/system/logo_large.png'

const sections = [
  {
    title: '必备工具',
    items: [
      { label: '活动中心', icon: '礼', color: '#ff5a68' },
      { label: '邀请好友', icon: '邀', color: '#14aeea' },
      { label: '卡包', icon: '卡', color: '#f253aa' },
      { label: '聊天室', icon: '聊', color: '#6b8dff' },
      { label: '月度报表', icon: '报', color: '#11d28d' },
      { label: '用户等级', icon: '级', color: '#ffa761' },
    ],
  },
  {
    title: '我的频道',
    items: [
      { label: '我的账户', icon: '¥', color: '#ff8b4a' },
      { label: '我的主页', icon: '主', color: '#0aa8e8' },
      { label: '我的勋章', icon: '勋', color: '#fb6069' },
      { label: '我的推广', icon: '推', color: '#8e65ff' },
      { label: '我的收藏', icon: '★', color: '#ffd15b' },
      { label: '我的评论', icon: '评', color: '#14c87b' },
      { label: '我的关注', icon: '♥', color: '#ff6170' },
      { label: '我的粉丝', icon: '粉', color: '#627bff' },
      { label: '我的点赞', icon: '赞', color: '#ff9a54' },
      { label: '小黑屋', icon: '黑', color: '#555' },
      { label: '黑名单', icon: '禁', color: '#333' },
    ],
  },
  {
    title: '排行榜',
    items: [
      { label: '粉丝排行榜', icon: '粉', color: '#ffad3d' },
      { label: '等级排行榜', icon: '级', color: '#11a7e8' },
      { label: '分享排行榜', icon: '享', color: '#16c97a' },
      { label: '礼物排行榜', icon: '礼', color: '#ff626b' },
    ],
  },
  {
    title: '平台信息',
    items: [
      { label: '站内信息', icon: '信', color: '#12c878' },
      { label: '联系客服', icon: '客', color: '#ff646d' },
      { label: '关于我们', icon: '关', color: '#ffb24d' },
      { label: '意见反馈', icon: '意', color: '#16a9e9' },
      { label: '清除缓存', icon: '清', color: '#16a9e9' },
      { label: '下载APP', icon: '下', color: '#333' },
      { label: '举报记录', icon: '举', color: '#ffad3d' },
      { label: '平台设置', icon: '设', color: '#ffad3d' },
    ],
  },
]
</script>

<template>
  <main class="my-page">
    <header class="my-hero">
      <img class="hero-logo" :src="logoUrl" alt="49图库">
      <button type="button">立即登录/注册</button>
    </header>

    <section class="member-card">
      <div class="member-watermark">49</div>
      <img :src="logoUrl" alt="49图库">
      <div>
        <strong>立即登录</strong>
        <span>暂时还不是会员</span>
      </div>
      <button type="button">立即开通</button>
    </section>

    <section v-for="section in sections" :key="section.title" class="my-section">
      <h2>{{ section.title }}</h2>
      <div class="menu-grid">
        <a v-for="item in section.items" :key="item.label" href="#" class="menu-item">
          <span class="menu-icon" :style="{ '--icon-color': item.color }">{{ item.icon }}</span>
          <strong>{{ item.label }}</strong>
        </a>
      </div>
    </section>

    <nav class="my-bottom-nav">
      <RouterLink to="/home"><span>⌂</span><strong>49图库</strong></RouterLink>
      <RouterLink to="/find"><span>♣</span><strong>发现</strong></RouterLink>
      <RouterLink class="center" to="/gszj"><span>⌂</span><strong>高手之家</strong></RouterLink>
      <RouterLink to="/treasure"><span>▣</span><strong>寻宝</strong></RouterLink>
      <RouterLink class="active" to="/my"><span>♙</span><strong>我的</strong></RouterLink>
    </nav>
  </main>
</template>

<style scoped>
.my-page {
  min-height: 100vh;
  padding-bottom: 78px;
  background: #fff;
  color: #1f2933;
  font-family: "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
}

.my-page a {
  color: inherit;
  text-decoration: none;
}

.my-hero {
  display: flex;
  align-items: center;
  gap: clamp(20px, 6vw, 28px);
  min-height: clamp(150px, 32vw, 194px);
  padding: clamp(30px, 8vw, 46px) clamp(28px, 6vw, 36px);
  background: #08c765;
}

.hero-logo {
  width: clamp(86px, 17vw, 106px);
  height: clamp(86px, 17vw, 106px);
  border: 3px solid #fff;
  border-radius: 999px;
  object-fit: cover;
}

.my-hero button {
  height: clamp(52px, 11vw, 66px);
  padding: 0 clamp(24px, 7vw, 34px);
  border: 0;
  border-radius: 999px;
  background: #fff;
  color: #08c765;
  font-size: clamp(20px, 5vw, 28px);
  font-weight: 900;
  white-space: nowrap;
}

.member-card {
  position: relative;
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 12px;
  min-height: 118px;
  margin: 16px clamp(16px, 4vw, 22px) 28px;
  padding: 20px clamp(18px, 4vw, 24px);
  overflow: hidden;
  border-radius: 10px;
  background: linear-gradient(105deg, #f8ca6d, #de9e2c);
  color: #fff;
}

.member-watermark {
  position: absolute;
  left: 72px;
  bottom: -38px;
  color: rgba(255, 255, 255, 0.16);
  font-size: 132px;
  font-weight: 900;
  line-height: 1;
}

.member-card img {
  position: relative;
  width: clamp(62px, 14vw, 78px);
  height: clamp(62px, 14vw, 78px);
  border-radius: 999px;
  object-fit: cover;
}

.member-card div:not(.member-watermark) {
  position: relative;
  display: grid;
  gap: 5px;
}

.member-card strong {
  font-size: clamp(22px, 5vw, 28px);
  line-height: 1;
}

.member-card span {
  font-size: clamp(14px, 3.5vw, 18px);
}

.member-card button {
  position: relative;
  height: 54px;
  padding: 0 clamp(16px, 5vw, 28px);
  border: 0;
  border-radius: 999px;
  background: #fff4d5;
  color: #a76800;
  font-size: clamp(18px, 4.5vw, 24px);
  font-weight: 900;
  white-space: nowrap;
}

.my-section {
  padding: 0 clamp(18px, 4vw, 22px) 18px;
}

.my-section h2 {
  margin: 0 0 22px;
  color: #1f2933;
  font-size: clamp(24px, 5.5vw, 30px);
  font-weight: 900;
}

.menu-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  row-gap: clamp(24px, 6vw, 34px);
}

.menu-item {
  display: grid;
  justify-items: center;
  gap: 10px;
  min-width: 0;
}

.menu-icon {
  display: grid;
  place-items: center;
  width: clamp(42px, 10vw, 58px);
  height: clamp(42px, 10vw, 58px);
  border-radius: 999px;
  background:
    radial-gradient(circle at 35% 30%, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.28) 42%, transparent 43%),
    color-mix(in srgb, var(--icon-color) 16%, #fff);
  color: var(--icon-color);
  font-size: clamp(18px, 4vw, 24px);
  font-weight: 900;
}

.menu-item strong {
  width: 100%;
  color: #2c343c;
  font-size: clamp(14px, 3.6vw, 20px);
  font-weight: 500;
  line-height: 1.2;
  text-align: center;
  white-space: nowrap;
}

.my-bottom-nav {
  position: fixed;
  left: 50%;
  bottom: 0;
  z-index: 40;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  width: min(100%, 600px);
  height: 64px;
  border-top: 1px solid #e9e9e9;
  background: #fff;
  transform: translateX(-50%);
}

.my-bottom-nav a {
  position: relative;
  display: grid;
  align-content: center;
  justify-items: center;
  gap: 2px;
  color: #555;
  font-size: 13px;
}

.my-bottom-nav span {
  color: #12c878;
  font-size: 23px;
  line-height: 1;
}

.my-bottom-nav strong {
  font-weight: 500;
}

.my-bottom-nav .active {
  color: #0ac66d;
}

.my-bottom-nav .center span {
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
  .my-page {
    max-width: 600px;
    margin: 0 auto;
  }
}

@media (max-width: 360px) {
  .member-card {
    grid-template-columns: auto 1fr;
  }

  .member-card button {
    grid-column: 1 / -1;
    justify-self: end;
  }
}
</style>
