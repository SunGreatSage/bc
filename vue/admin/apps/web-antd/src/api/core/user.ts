import type { UserInfo } from '@vben/types';

import { preferences } from '@vben/preferences';

import { requestClient } from '#/api/request';

/**
 * 管理员信息响应接口
 */
interface AdminInfoResponse {
  new_user_id: number;
  adminid: number;
  adminname: string;
  logintimes: number;
  lastloginip: string;
  lastlogintime: string;
  /** 管理员角色类型: 1=总管理, 2=代理 */
  root?: number;
  /** 管理员ID */
  admin_id?: number;
  /** 信用额度(代理) */
  credit_limit?: number;
}

/**
 * 获取管理员信息
 */
export async function getUserInfoApi(): Promise<UserInfo> {
  const adminInfo = await requestClient.get<AdminInfoResponse>(
    '/lottery_login/getAdminInfo',
  );

  // 根据角色类型设置默认首页
  const homePath = adminInfo.root === 2
    ? '/plate/users'  // 代理默认进入用户管理
    : '/control-panel/analysis';  // 总管理默认进入实时分析

  // 将管理员信息转换为 UserInfo 格式
  return {
    userId: String(adminInfo.adminid),
    username: adminInfo.adminname,
    realName: adminInfo.adminname,
    avatar: preferences.app.defaultAvatar,
    desc: adminInfo.root === 1 ? '超级管理员' : '管理员',
    homePath: homePath,
    roles: adminInfo.root === 1 ? ['super'] : ['admin'],
    token: '',
    // 添加角色信息
    root: adminInfo.root,
    admin_id: adminInfo.admin_id,
    credit_limit: adminInfo.credit_limit,
  };
}
