import { createRouter, createWebHistory } from 'vue-router'

// Layout components
import LoginLayout from '../layouts/LoginLayout.vue'
import MainLayout from '../layouts/MainLayout.vue'

const routes = [
  {
    path: '/login',
    component: LoginLayout,
    children: [
      {
        path: '',
        name: 'Login',
        component: () => import('../views/Login.vue')
      }
    ]
  },
  {
    path: '/kj',
    name: 'Kj',
    component: () => import('../views/Kj.vue')
  },
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Home',
        redirect: '/betting'
      },
      {
        path: 'about',
        name: 'About',
        component: () => import('../views/About.vue')
      },
      {
        path: 'users',
        name: 'Users',
        component: () => import('../views/Users.vue')
      },
      {
        path: 'betting',
        name: 'Betting',
        component: () => import('../views/Betting.vue')
      },
      {
        path: 'bet-status',
        name: 'BetStatus',
        component: () => import('../views/BetStatus.vue')
      },
      {
        path: 'results',
        name: 'Results',
        component: () => import('../views/Results.vue')
      },
      {
        path: 'rules',
        name: 'Rules',
        component: () => import('../views/Rules.vue')
      },
      {
        path: 'change-password',
        name: 'ChangePassword',
        component: () => import('../views/ChangePassword.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// 路由守卫：检查登录状态
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('userToken')
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

  if (requiresAuth && !token) {
    // 需要登录但没有token，跳转到登录页
    next('/login')
  } else if (to.path === '/login' && token) {
    // 已登录用户访问登录页，跳转到主页
    next('/')
  } else {
    // 正常通行
    next()
  }
})

export default router
