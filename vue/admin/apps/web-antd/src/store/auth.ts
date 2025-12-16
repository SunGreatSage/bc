import type { Recordable, UserInfo } from '@vben/types';

import { ref } from 'vue';
import { useRouter } from 'vue-router';

import { LOGIN_PATH } from '@vben/constants';
import { preferences } from '@vben/preferences';
import { resetAllStores, useAccessStore, useUserStore } from '@vben/stores';

import { notification } from 'ant-design-vue';
import { defineStore } from 'pinia';

import { getAccessCodesApi, getUserInfoApi, loginApi, logoutApi } from '#/api';
import { $t } from '#/locales';

export const useAuthStore = defineStore('auth', () => {
  const accessStore = useAccessStore();
  const userStore = useUserStore();
  const router = useRouter();

  const loginLoading = ref(false);

  /**
   * 异步处理登录操作
   * Asynchronously handle the login process
   * @param params 登录表单数据
   */
  async function authLogin(
    params: Recordable<any>,
    onSuccess?: () => Promise<void> | void,
  ) {
    // 异步处理用户登录操作并获取 accessToken
    let userInfo: null | UserInfo = null;
    try {
      loginLoading.value = true;
      const { token, adminInfo } = await loginApi(params);

      // 如果成功获取到 token
      if (token) {
        accessStore.setAccessToken(token);

        // 如果登录接口已返回管理员信息，直接使用
        if (adminInfo) {
          // 调试输出
          console.log('登录返回的 adminInfo:', adminInfo);
          console.log('adminInfo.root 值:', adminInfo.root);
          console.log('adminInfo.root === 2:', adminInfo.root === 2);

          // 根据角色类型设置默认首页
          // root=1(总管理): 实时分析, root=2(代理): 用户管理
          const homePath = adminInfo.root === 2
            ? '/plate/users'  // 代理默认进入用户管理
            : '/control-panel/analysis';  // 总管理默认进入实时分析

          console.log('计算出的 homePath:', homePath);

          // 将管理员信息转换为 UserInfo 格式
          userInfo = {
            userId: String(adminInfo.adminid),
            username: adminInfo.adminname,
            realName: adminInfo.adminname,
            avatar: '',
            desc: adminInfo.is_super ? '超级管理员' : '管理员',
            homePath: homePath,
            roles: adminInfo.is_super ? ['super'] : ['admin'],
            token: token,
            // 添加角色类型、管理员ID、信用额度信息(用于菜单权限判断)
            root: adminInfo.root,
            admin_id: adminInfo.admin_id,
            credit_limit: adminInfo.credit_limit,
          };
          userStore.setUserInfo(userInfo);

          // 获取权限码
          const accessCodes = await getAccessCodesApi();
          accessStore.setAccessCodes(accessCodes);
        } else {
          // 如果登录接口未返回管理员信息，则获取用户信息
          const [fetchUserInfoResult, accessCodes] = await Promise.all([
            fetchUserInfo(),
            getAccessCodesApi(),
          ]);

          userInfo = fetchUserInfoResult;
          userStore.setUserInfo(userInfo);
          accessStore.setAccessCodes(accessCodes);
        }

        if (accessStore.loginExpired) {
          accessStore.setLoginExpired(false);
        } else {
          onSuccess
            ? await onSuccess?.()
            : await router.push(
                userInfo.homePath || preferences.app.defaultHomePath,
              );
        }

        if (userInfo?.realName) {
          notification.success({
            description: `${$t('authentication.loginSuccessDesc')}:${userInfo?.realName}`,
            duration: 3,
            message: $t('authentication.loginSuccess'),
          });
        }
      }
    } finally {
      loginLoading.value = false;
    }

    return {
      userInfo,
    };
  }

  async function logout(redirect: boolean = true) {
    try {
      await logoutApi();
    } catch {
      // 不做任何处理
    }
    resetAllStores();
    accessStore.setLoginExpired(false);

    // 回登录页带上当前路由地址
    await router.replace({
      path: LOGIN_PATH,
      query: redirect
        ? {
            redirect: encodeURIComponent(router.currentRoute.value.fullPath),
          }
        : {},
    });
  }

  async function fetchUserInfo() {
    let userInfo: null | UserInfo = null;
    userInfo = await getUserInfoApi();
    userStore.setUserInfo(userInfo);
    return userInfo;
  }

  function $reset() {
    loginLoading.value = false;
  }

  return {
    $reset,
    authLogin,
    fetchUserInfo,
    loginLoading,
    logout,
  };
});
