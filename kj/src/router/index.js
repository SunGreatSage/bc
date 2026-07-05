import { createRouter, createWebHashHistory } from 'vue-router'
import ArticleDetailView from '../views/ArticleDetailView.vue'
import FindDetailView from '../views/FindDetailView.vue'
import FindView from '../views/FindView.vue'
import GszjView from '../views/GszjView.vue'
import HomeView from '../views/HomeView.vue'
import LotteryHistoryView from '../views/LotteryHistoryView.vue'
import LotteryHomeView from '../views/LotteryHomeView.vue'
import MyView from '../views/MyView.vue'
import TreasureView from '../views/TreasureView.vue'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/home',
      name: 'lottery-home',
      component: LotteryHomeView,
    },
    {
      path: '/find',
      name: 'find',
      component: FindView,
    },
    {
      path: '/gszj',
      name: 'gszj',
      component: GszjView,
    },
    {
      path: '/treasure',
      name: 'treasure',
      component: TreasureView,
    },
    {
      path: '/my',
      name: 'my',
      component: MyView,
    },
    {
      path: '/find/:articleId',
      name: 'find-detail',
      component: FindDetailView,
      props: true,
    },
    {
      path: '/lottery-history',
      name: 'lottery-history',
      component: LotteryHistoryView,
    },
    {
      path: '/article/:articleId',
      name: 'article-detail',
      component: ArticleDetailView,
      props: true,
    },
  ],
  scrollBehavior() {
    return { top: 0 }
  },
})

export default router
