import type { RouteRecordRaw } from 'vue-router';

import { BasicLayout } from '#/layouts';

const routes: RouteRecordRaw[] = [
  {
    meta: {
      icon: 'lucide:bar-chart-4',
      order: 2,
      title: '控盘管理',
      // 代理不能访问控盘管理,只有总管理员(root=1)可以
      // hideInMenu 会在路由守卫中根据用户角色动态设置
      hideInMenu: false,
    },
    name: 'ControlPanel',
    path: '/control-panel',
    children: [
      {
        name: 'ControlPanelAnalysis',
        path: 'analysis',
        component: () => import('#/views/control-panel/analysis/index.vue'),
        meta: {
          icon: 'lucide:activity',
          title: '实时分析',
        },
      },
      {
        name: 'ControlPanelHistory',
        path: 'history',
        component: () => import('#/views/control-panel/history/index.vue'),
        meta: {
          icon: 'lucide:history',
          title: '历史记录',
        },
      },
    ],
  },
];

export default routes;
