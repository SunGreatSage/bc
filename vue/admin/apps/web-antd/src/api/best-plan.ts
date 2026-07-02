import { requestClient } from '#/api/request';

export namespace BestPlanApi {
  /** 盘口信息 */
  export interface PlateInfo {
    id: number;
    code: string;
    name: string;
  }

  /** 期号信息 */
  export interface QishuInfo {
    qishu: string;
    plate_code: string;  // 盘口代码
    opentime: string;
    closetime: string;
    kjtime: string;
    is_opened: boolean;
    status?: number;  // 状态: 0=未开盘, 1=待开盘, 2=投注中, 3=已开奖
    draw_numbers?: string[];  // 开奖号码数组 ["01", "13", "25", "37", "42", "49", "07"]
    draw_numbers_text?: string;  // 开奖号码文本 "01,13,25,37,42,49,07"
  }

  /** 号码详情 */
  export interface NumberDetail {
    number: number;
    profit: number;
    profit_rate: number;
    prize_amount: number;
    bet_count: number;
    risk_level: number;
    risk_level_text?: string;
  }

  /** 汇总信息 */
  export interface Summary {
    total_bets: number;
    total_orders: number;
    best_numbers: number[];  // 最佳7个号码组合
    best_m7: number;  // 最佳特码(第7个号码)
    best_m1_m6: number[];  // 最佳正码(前6个号码)
    best_profit: number;
    best_profit_rate: number;
    // 兼容旧字段
    best_number?: number;
    worst_number?: number;
    worst_profit?: number;
    worst_profit_rate?: number;
    avg_profit?: number;
  }

  /** 分析结果 */
  export interface AnalyzeResult {
    summary: Summary;
    details: NumberDetail[];
  }

  /** 历史记录 */
  export interface HistoryRecord {
    id: number;
    gid: number;
    qishu: string;
    plate_code: string;  // 新增：盘口代码
    analyze_time: string;
    total_bets: number;
    total_orders: number;
    best_numbers: string;  // 修改：改为字符串类型（逗号分隔的7个号码）
    best_profit: number;
    best_profit_rate: number;
    worst_number: number;
    worst_profit: number;
    worst_profit_rate: number;
    avg_profit: number;
    status: number;
    actual_number: number | null;
    actual_profit: number | null;
  }

  /** 分析详情 */
  export interface DetailRecord extends HistoryRecord {
    number_details: NumberDetail[];
  }

  /** 投注汇总 */
  export interface BetSummary {
    play_name: string;
    bet_count: number;
    total_amount: number;
  }

  /** 号码分布 */
  export interface NumberDistribution {
    number: string;
    bet_count: number;
    total_amount: number;
  }

  /** 目标利润率查找结果 */
  export interface TargetRateResult {
    target_rate: number;
    tolerance: number;
    matched_count: number;
    matched_numbers: NumberDetail[];
  }

  /** 历史下单记录 */
  export interface OrderHistoryRecord {
    id: number;
    sn: string;
    user_id: number;
    username: string;
    nickname: string;
    mobile: string;
    is_agent: number;
    user_type: 'user' | 'agent';
    user_type_text: string;
    game_id: number;
    plate_code: string;
    issue: string;
    method_id: number;
    method_name: string;
    bet_type: string;
    bet_content: string;
    bet_amount: string;
    bet_multiple: number;
    total_amount: string;
    odds: string;
    status: number;
    status_text: string;
    prize_amount: string;
    is_settled: number;
    created_at: number;
    created_time: string;
  }

  /** 历史下单列表 */
  export interface OrderHistoryResult {
    lists: OrderHistoryRecord[];
    count: number;
    page_no: number;
    page_size: number;
    summary: {
      order_count: number;
      total_amount: string;
      total_prize_amount: string;
    };
  }

  /** 新期号创建策略 */
  export type CreateIssueStrategy = 'plate_config' | 'immediate' | 'continuous';

  /** 新期号预览/创建结果 */
  export interface NewIssueResult {
    issue: string;
    open_time: string;
    close_time: string;
    draw_time: string;
    status: number;
    status_text: string;
    strategy: CreateIssueStrategy;
    strategy_text: string;
    source_text?: string;
    current_issue?: {
      issue: string;
      open_time: string;
      close_time: string;
      draw_time: string;
      status: number;
      status_text: string;
    };
  }

  /** 清空今日期号和订单结果 */
  export interface ClearTodayDataResult {
    date: string;
    plate_code: string;
    issue_count: number;
    betting_count: number;
    winning_count: number;
    account_log_count: number;
    commission_count: number;
    history_count: number;
    affected_users: number;
  }
}

/**
 * 获取盘口列表
 */
export async function getPlateList(gid: number = 200) {
  return requestClient.get<BestPlanApi.PlateInfo[]>('/best_plan/getPlateList', {
    params: { gid },
  });
}

/**
 * 获取当前期号
 */
export async function getCurrentQishu(gid: number = 200, plateCode?: string) {
  return requestClient.get<BestPlanApi.QishuInfo>('/best_plan/getCurrentQishu', {
    params: { gid, plate_code: plateCode },
  });
}

/**
 * 实时计算分析
 */
export async function calculateRealtime(data: {
  gid?: number;
  qishu: string;
  plate_code?: string;
  year?: number;
  target_rate?: number;
  tolerance?: number;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  formData.append('qishu', data.qishu);
  if (data.plate_code) formData.append('plate_code', data.plate_code);
  if (data.year) formData.append('year', String(data.year));
  if (data.target_rate !== undefined && data.target_rate !== null) {
    formData.append('target_rate', String(data.target_rate));
  }
  if (data.tolerance !== undefined && data.tolerance !== null) {
    formData.append('tolerance', String(data.tolerance));
  }

  return requestClient.post<BestPlanApi.AnalyzeResult>('/best_plan/calculateRealtime', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 执行分析并保存
 */
export async function analyzeAndSave(data: { gid?: number; qishu: string; plate_code?: string; year?: number }) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  formData.append('qishu', data.qishu);
  if (data.plate_code) formData.append('plate_code', data.plate_code);
  if (data.year) formData.append('year', String(data.year));

  return requestClient.post<BestPlanApi.AnalyzeResult>('/best_plan/analyze', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 按目标利润率查找
 */
export async function findByTargetRate(data: {
  gid?: number;
  qishu: string;
  plate_code?: string;
  target_rate?: number;
  tolerance?: number;
  year?: number;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  formData.append('qishu', data.qishu);
  if (data.plate_code) formData.append('plate_code', data.plate_code);
  if (data.target_rate) formData.append('target_rate', String(data.target_rate));
  if (data.tolerance) formData.append('tolerance', String(data.tolerance));
  if (data.year) formData.append('year', String(data.year));

  return requestClient.post<BestPlanApi.TargetRateResult>('/best_plan/findByTargetRate', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 获取历史列表
 */
export async function getHistoryList(params: { gid?: number; limit?: number }) {
  return requestClient.get<BestPlanApi.HistoryRecord[]>('/best_plan/getHistoryList', {
    params,
  });
}

/**
 * 获取分析详情
 */
export async function getDetail(id: number) {
  return requestClient.get<BestPlanApi.DetailRecord>('/best_plan/getDetail', {
    params: { id },
  });
}

/**
 * 获取历史下单列表
 */
export async function getOrderHistory(params: {
  gid?: number;
  page?: number;
  limit?: number;
  username?: string;
  user_type?: 'user' | 'agent' | '';
  plate_code?: string;
  issue?: string;
}) {
  return requestClient.get<BestPlanApi.OrderHistoryResult>('/best_plan/getOrderHistory', {
    params,
  });
}

/**
 * 获取投注汇总
 */
export async function getBetSummary(params: { gid?: number; qishu: string }) {
  return requestClient.get<BestPlanApi.BetSummary[]>('/best_plan/getBetSummary', {
    params,
  });
}

/**
 * 获取号码分布
 */
export async function getNumberDistribution(params: { gid?: number; qishu: string }) {
  return requestClient.get<BestPlanApi.NumberDistribution[]>('/best_plan/getNumberDistribution', {
    params,
  });
}

/**
 * 执行开奖（使用最佳方案）
 */
export async function executeDrawing(data: {
  gid?: number;
  qishu: string;
  plate_code?: string;
  best_numbers: number[] | string;
  year?: number;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  formData.append('qishu', data.qishu);
  if (data.plate_code) formData.append('plate_code', data.plate_code);

  // 处理号码：可能是数组或字符串
  const numbers = Array.isArray(data.best_numbers)
    ? data.best_numbers.join(',')
    : data.best_numbers;
  formData.append('best_numbers', numbers);

  if (data.year) formData.append('year', String(data.year));

  return requestClient.post<{
    qishu: string;
    draw_numbers: number[];
    total_orders: number;
    win_count: number;
    lose_count: number;
    total_win_amount: number;
    platform_profit: number;
  }>('/best_plan/executeDrawing', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 自定义开奖号码并立即开奖结算
 */
export async function customDrawing(data: {
  gid?: number;
  qishu: string;
  plate_code?: string;
  draw_numbers: number[] | string;
  year?: number;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  formData.append('qishu', data.qishu);
  if (data.plate_code) formData.append('plate_code', data.plate_code);

  const numbers = Array.isArray(data.draw_numbers)
    ? data.draw_numbers.join(',')
    : data.draw_numbers;
  formData.append('draw_numbers', numbers);

  if (data.year) formData.append('year', String(data.year));

  return requestClient.post<{
    issue: string;
    plate_code: string;
    numbers: number[];
    draw_numbers: number[];
    total_orders: number;
    win_count: number;
    lose_count: number;
    draw_count: number;
    total_bet_amount: number;
    total_payout: number;
    total_win_amount: number;
    platform_profit: number;
    settled_at: number;
  }>('/best_plan/customDrawing', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 预览手动创建的新期号
 */
export async function previewNewIssue(data: {
  gid?: number;
  plate_code?: string;
  strategy?: BestPlanApi.CreateIssueStrategy;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  if (data.plate_code) formData.append('plate_code', data.plate_code);
  if (data.strategy) formData.append('strategy', data.strategy);

  return requestClient.post<BestPlanApi.NewIssueResult>('/best_plan/previewNewIssue', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 手动创建新期号
 */
export async function createNewIssue(data: {
  gid?: number;
  plate_code?: string;
  strategy?: BestPlanApi.CreateIssueStrategy;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  if (data.plate_code) formData.append('plate_code', data.plate_code);
  if (data.strategy) formData.append('strategy', data.strategy);

  return requestClient.post<BestPlanApi.NewIssueResult>('/best_plan/createNewIssue', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}

/**
 * 清空当前盘口今日测试期号和今日下单数据
 */
export async function clearTodayData(data: {
  gid?: number;
  plate_code?: string;
}) {
  const formData = new URLSearchParams();
  if (data.gid) formData.append('gid', String(data.gid));
  if (data.plate_code) formData.append('plate_code', data.plate_code);

  return requestClient.post<BestPlanApi.ClearTodayDataResult>('/best_plan/clearTodayData', formData, {
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
  });
}
