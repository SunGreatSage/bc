import { requestClient } from '#/api/request';

export namespace RiskLogApi {
  export type RiskType = 'all' | 'negative' | 'wipeout' | '';

  export interface RiskOption {
    label: string;
    value: RiskType;
  }

  export interface RiskDetail {
    issue?: string;
    plate_code?: string;
    numbers?: string;
    plan_status?: string;
    selection_source?: string;
    negative_confirmed?: string;
    wipeout_type?: string;
    wipeout_label?: string;
    wipeout_confirmed?: string;
    expected_loss?: string;
    expected_profit?: string;
    expected_profit_rate?: string;
    expected_payout?: string;
    total_bet_amount?: string;
    total_orders?: number;
  }

  export interface RiskLogRecord {
    id: number;
    action: string;
    admin_name: string;
    admin_id: number;
    account?: string;
    url: string;
    type: string;
    params: string;
    parsed_params?: Record<string, any>;
    ip: string;
    create_time: string | number;
    risk_type: RiskType;
    risk_tag: string;
    risk_level: 'danger' | 'normal';
    risk_summary: string;
    risk_detail: RiskDetail;
    risk_detail_text: string;
  }

  export interface RiskLogResult {
    lists: RiskLogRecord[];
    count: number;
    page_no: number;
    page_size: number;
  }
}

export async function getRiskLogOptions() {
  return requestClient.get<RiskLogApi.RiskOption[]>('/setting.system.log/riskOptions');
}

export async function getRiskLogList(params: {
  page_no?: number;
  page_size?: number;
  risk_type?: RiskLogApi.RiskType;
  issue?: string;
  plate_code?: string;
  admin_name?: string;
}) {
  return requestClient.get<RiskLogApi.RiskLogResult>('/setting.system.log/lists', {
    params,
  });
}
