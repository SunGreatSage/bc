import { baseRequestClient, requestClient } from '#/api/request';

export namespace AuthApi {
  /** 登录接口参数 */
  export interface LoginParams {
    password?: string;
    username?: string;
  }

  /** 管理员信息 */
  export interface AdminInfo {
    id: number;
    adminid: number;
    adminname: string;
    is_super: boolean;
    logintimes: number;
    /** 管理员角色类型: 1=总管理, 2=代理 */
    root?: number;
    /** 管理员ID */
    admin_id?: number;
    /** 信用额度(代理) */
    credit_limit?: number;
  }

  /** 登录接口返回值 */
  export interface LoginResult {
    token: string;
    adminInfo: AdminInfo;
  }

  export interface RefreshTokenResult {
    data: string;
    status: number;
  }
}

/**
 * 管理员登录
 */
export async function loginApi(data: AuthApi.LoginParams) {
  // 将数据转换为 x-www-form-urlencoded 格式
  const formData = new URLSearchParams();
  if (data.username) formData.append('username', data.username);
  if (data.password) formData.append('password', data.password);

  return requestClient.post<AuthApi.LoginResult>('/lottery_login/adminLogin', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 刷新accessToken
 */
export async function refreshTokenApi() {
  return baseRequestClient.post<AuthApi.RefreshTokenResult>('/auth/refresh', {
    withCredentials: true,
  });
}

/**
 * 退出登录
 */
export async function logoutApi() {
  // 暂时只在前端清除token,不调用后端接口
  // 如果需要调用后端,可以使用: /adminapi/login/logout
  return Promise.resolve({ code: 1, msg: '退出成功' });
}

/**
 * 获取用户权限码
 * 注意：如果后端没有提供权限码接口，可以返回空数组或根据用户角色返回默认权限
 */
export async function getAccessCodesApi() {
  // 暂时返回空数组，待后端提供权限码接口后再对接
  return Promise.resolve([]);
  // return requestClient.get<string[]>('/auth/codes');
}
