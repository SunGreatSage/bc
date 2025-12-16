import type { RouteRecordRaw } from 'vue-router';

import { BasicLayout } from '#/layouts';

const routes: RouteRecordRaw[] = [
  {
    component: BasicLayout,
    meta: {
      icon: 'lucide:settings',
      order: 3,
      title: '盘口管理',
    },
    name: 'PlateManagement',
    path: '/plate',
    children: [
      {
        name: 'PlateSettings',
        path: '/plate/settings',
        component: () => import('#/views/plate/settings/index.vue'),
        meta: {
          icon: 'lucide:sliders',
          title: '盘口设置',
          // 代理不能访问盘口设置
          // hideInMenu 会在路由守卫中根据用户角色动态设置
          hideInMenu: false,
        },
      },
      {
        name: 'PlateUsers',
        path: '/plate/users',
        component: () => import('#/views/plate/users/index.vue'),
        meta: {
          icon: 'lucide:users',
          title: '用户管理',
          // 用户管理所有人都可以访问
        },
      },
    ],
  },
];

export default routes;
