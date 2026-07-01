/**
 * 盘口管理API
 */
import { requestClient } from '#/api/request';

/**
 * 盘口列表(分页)
 */
export async function getPlateList(params?: {
  page?: number;
  limit?: number;
  code?: string;
  name?: string;
}) {
  return requestClient.get('/plate.plate/lists', { params });
}

/**
 * 盘口详情
 */
export async function getPlateDetail(id: number) {
  return requestClient.get('/plate.plate/detail', { params: { id } });
}

/**
 * 新增盘口
 */
export async function addPlate(data: {
  code: string;
  name: string;
  game_id?: number;
  open_time?: string;
  close_time?: string;
  draw_time?: string;
  close_advance?: number;
  status?: number;
  sort?: number;
  remark?: string;
}) {
  return requestClient.post('/plate.plate/add', data);
}

/**
 * 编辑盘口
 */
export async function editPlate(data: {
  id: number;
  code?: string;
  name?: string;
  game_id?: number;
  open_time?: string;
  close_time?: string;
  draw_time?: string;
  close_advance?: number;
  status?: number;
  sort?: number;
  remark?: string;
  sync_pending_issues?: boolean | number;
}) {
  return requestClient.post<{
    sync_pending_issues: boolean;
    updated_issue_count: number;
  }>('/plate.plate/edit', data);
}

/**
 * 删除盘口
 */
export async function deletePlate(id: number) {
  return requestClient.post('/plate.plate/delete', { id });
}

/**
 * 切换盘口状态
 */
export async function changePlateStatus(id: number, status: number) {
  return requestClient.post('/plate.plate/status', { id, status });
}

/**
 * 获取所有盘口(无分页)
 */
export async function getAllPlates() {
  return requestClient.get('/plate.plate/all');
}

/**
 * 盘口数据类型
 */
export interface PlateItem {
  id: number;
  code: string;
  name: string;
  game_id: number;
  open_time: string;
  close_time: string;
  draw_time: string;
  close_advance: number;
  status: number;
  sort: number;
  remark?: string;
  created_at: number;
  updated_at: number;
}
