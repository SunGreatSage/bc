/**
 * 盘口用户管理API
 */
import { requestClient } from '#/api/request';

/**
 * 用户列表(分页)
 */
export async function getUserList(params?: {
  page?: number;
  limit?: number;
  username?: string;
  nickname?: string;
  mobile?: string;
  status?: number;
  user_type?: string; // 'user' | 'agent'
}) {
  return requestClient.get('/plate.user/lists', { params });
}

/**
 * 用户详情
 */
export async function getUserDetail(id: number) {
  return requestClient.get('/plate.user/detail', { params: { id } });
}

/**
 * 新增用户
 */
export async function addUser(data: {
  username: string;
  password: string;
  nickname?: string;
  mobile?: string;
  status?: number;
  user_money?: number;
}) {
  return requestClient.post('/plate.user/add', data);
}

/**
 * 编辑用户
 */
export async function editUser(data: {
  id: number;
  username?: string;
  password?: string;
  nickname?: string;
  mobile?: string;
  status?: number;
}) {
  return requestClient.post('/plate.user/edit', data);
}

/**
 * 删除用户
 */
export async function deleteUser(id: number) {
  return requestClient.post('/plate.user/delete', { id });
}

/**
 * 切换用户状态
 */
export async function changeUserStatus(id: number, status: number) {
  return requestClient.post('/plate.user/status', { id, status });
}

/**
 * 调整用户余额
 */
export async function adjustBalance(data: {
  id: number;
  change_amount: number;
  change_type: number; // 1=增加, 2=减少
  remark?: string;
}) {
  return requestClient.post('/plate.user/adjustBalance', data);
}

/**
 * 用户账户流水列表
 */
export async function getAccountLogs(params?: {
  page?: number;
  limit?: number;
  user_id?: number;
  admin_id?: number; // 代理ID
  change_type?: number;
}) {
  return requestClient.get('/plate.user/accountLogs', { params });
}

/**
 * 用户数据类型
 */
export interface UserItem {
  id: number;
  username: string;
  nickname: string;
  mobile: string;
  status: number;
  user_money: number;
  create_time: number;
  update_time: number;
}

/**
 * 账户流水数据类型
 */
export interface AccountLogItem {
  id: number;
  sn: string;
  user_id: number;
  change_type: number;
  change_type_text: string;
  change_amount: number;
  balance_before: number;
  balance_after: number;
  frozen_before: number;
  frozen_after: number;
  related_sn: string;
  related_type: number;
  remark: string;
  operator_id: number;
  ip: string;
  created_at: number;
  created_time: string;
}

/**
 * 开设代理账户
 */
export async function createAgent(data: {
  username: string;
  password: string;
  nickname?: string;
  mobile?: string;
  status?: number;
  credit_limit?: number;
}) {
  return requestClient.post('/plate.user/createAgent', data);
}

/**
 * 调整代理信用额度
 */
export async function adjustAgentCredit(data: {
  id: number;
  change_amount: number;
  change_type: number; // 1=增加, 2=减少
  remark?: string;
}) {
  return requestClient.post('/plate.user/adjustAgentCredit', data);
}
