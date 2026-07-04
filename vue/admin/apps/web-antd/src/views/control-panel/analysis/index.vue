<script setup lang="ts">
import { computed, onMounted, ref, watch, onBeforeUnmount } from 'vue';

import { Card, Table, Tag, Button, Space, Statistic, Row, Col, InputNumber, Select, message, Modal } from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import {
  getCurrentQishu,
  getPlateList,
  calculateRealtime,
  findByTargetRate,
  executeDrawing,
  revokeDrawingPlan,
  previewCustomDrawing,
  customDrawing,
  createNewIssue,
  previewNewIssue,
  clearTodayData,
  type BestPlanApi,
} from '#/api/best-plan';

defineOptions({ name: 'ControlPanelAnalysis' });

const loading = ref(false);
const qishuInfo = ref<BestPlanApi.QishuInfo | null>(null);
const analyzeResult = ref<BestPlanApi.AnalyzeResult | null>(null);
const targetRate = ref<number>(10);
const tolerance = ref<number>(10);  // ✅ 默认误差范围改为10%
const autoRefresh = ref(false);
const selectedPlate = ref<string>('A'); // 默认选择A盘
const plateOptions = ref<Array<{ label: string; value: string }>>([]); // 动态盘口选项
const createIssueStrategy = ref<BestPlanApi.CreateIssueStrategy>('plate_config');
const createIssueStrategyOptions = [
  { label: '按盘口配置时间', value: 'plate_config' },
  { label: '立即开盘', value: 'immediate' },
  { label: '连续创建', value: 'continuous' },
];
const customDrawVisible = ref(false);
const customDrawSubmitting = ref(false);
const clearTodaySubmitting = ref(false);
const customDrawNumbers = ref<Array<number | undefined>>([undefined, undefined, undefined, undefined, undefined, undefined, undefined]);
let refreshTimer: NodeJS.Timeout | null = null;

// ⏱️ 倒计时相关
const countdown = ref<string>(''); // 倒计时文本
let countdownTimer: NodeJS.Timeout | null = null;

// 表格列定义
const columns: TableColumnsType = [
  {
    title: '7个号码组合',
    dataIndex: 'numbers',
    key: 'numbers',
    width: 220,
    align: 'center',
  },
  {
    title: '利润（元）',
    dataIndex: 'profit',
    key: 'profit',
    width: 120,
    align: 'right',
    customRender: ({ text }) => `¥${Number(text).toFixed(2)}`,
  },
  {
    title: '利润率（%）',
    dataIndex: 'profit_rate',
    key: 'profit_rate',
    width: 120,
    align: 'right',
    customRender: ({ text }) => `${Number(text).toFixed(2)}%`,
  },
  {
    title: '目标/实际',
    dataIndex: 'strategy',
    key: 'strategy',
    width: 240,
    align: 'center',
  },
  {
    title: '风险等级',
    dataIndex: 'risk_level',
    key: 'risk_level',
    width: 100,
    align: 'center',
  },
  {
    title: '操作',
    key: 'action',
    width: 150,
    align: 'center',
    fixed: 'right',
  },
];

// 号码详情表格列定义
const detailColumns: TableColumnsType = [
  {
    title: '号码',
    dataIndex: 'number',
    key: 'number',
    width: 80,
    align: 'center',
  },
  {
    title: '投注金额',
    dataIndex: 'bet_amount',
    key: 'bet_amount',
    width: 120,
    align: 'right',
    customRender: ({ text }) => `¥${Number(text).toFixed(2)}`,
  },
  {
    title: '投注笔数',
    dataIndex: 'bet_count',
    key: 'bet_count',
    width: 100,
    align: 'center',
  },
  {
    title: '权重',
    dataIndex: 'weight',
    key: 'weight',
    width: 120,
    align: 'right',
    customRender: ({ text }) => `${Number(text).toFixed(2)}`,
  },
];

function toFiniteNumber(value: unknown, fallback = 0) {
  const num = typeof value === 'number' ? value : Number(value);
  return Number.isFinite(num) ? num : fallback;
}

function formatFixed(value: unknown, digits = 2, fallback = 0) {
  return toFiniteNumber(value, fallback).toFixed(digits);
}

function formatRatePercent(value: unknown, digits = 2) {
  return `${formatFixed(value, digits)}%`;
}

function formatProfitSummary(value: unknown, rate: unknown, settled = false) {
  const profit = toFiniteNumber(value);
  const profitRate = toFiniteNumber(rate);
  if (profit < 0) {
    return `${settled ? '平台亏损' : '预计亏损'}：¥${formatFixed(Math.abs(profit))} (亏损率 ${formatFixed(Math.abs(profitRate))}%)`;
  }
  return `${settled ? '平台利润' : '预计利润'}：¥${formatFixed(profit)} (${formatFixed(profitRate)}%)`;
}

type WipeoutPlanType = '' | 'full' | 'near';

function confirmNegativePlan(totalProfit: number, profitRate: number, totalPrize: number, immediateMode: boolean) {
  if (totalProfit >= 0) {
    return true;
  }

  return window.confirm(
    `负盈利二次确认\n\n` +
    `预计亏损金额：¥${formatFixed(Math.abs(totalProfit))}\n` +
    `预计亏损率：${formatFixed(Math.abs(profitRate))}%\n` +
    `预计赔付：¥${formatFixed(totalPrize)}\n\n` +
    (immediateMode ? '确认后将立即开奖并结算，本次负盈利选择会写入操作日志。' : '确认后将锁定负盈利开奖计划，本次选择会写入操作日志。')
  );
}

function getWipeoutPlanType(profitRate: unknown, totalPayout: unknown, totalBetAmount: unknown): WipeoutPlanType {
  const betAmount = toFiniteNumber(totalBetAmount);
  if (betAmount <= 0) {
    return '';
  }

  const payout = toFiniteNumber(totalPayout, Number.NaN);
  const rate = toFiniteNumber(profitRate);
  if (Number.isFinite(payout) && payout <= 0.01) {
    return 'full';
  }
  if (rate >= 99) {
    return 'near';
  }
  return '';
}

function getWipeoutPlanLabel(type: WipeoutPlanType) {
  if (type === 'full') {
    return '通杀';
  }
  if (type === 'near') {
    return '近似通杀';
  }
  return '';
}

function confirmWipeoutPlan(
  wipeoutType: WipeoutPlanType,
  profitRate: number,
  totalPayout: number,
  totalBetAmount: number,
  immediateMode: boolean,
) {
  if (!wipeoutType) {
    return true;
  }

  const label = getWipeoutPlanLabel(wipeoutType);
  return window.confirm(
    `${label}方案二次确认\n\n` +
    `本期总投注：¥${formatFixed(totalBetAmount)}\n` +
    `预计赔付：¥${formatFixed(totalPayout)}\n` +
    `实际利润率：${formatFixed(profitRate)}%\n\n` +
    (immediateMode ? `确认后将立即开奖并结算，本次${label}选择会写入操作日志。` : `确认后将锁定${label}开奖计划，本次选择会写入操作日志。`)
  );
}

function normalizeDrawNumbers(values: Array<number | undefined>): number[] {
  return values
    .map((value) => Number(value))
    .filter((value) => Number.isInteger(value));
}

function validateDrawNumbers(numbers: number[]): string | null {
  if (numbers.length !== 7) {
    return '请填写完整的7个开奖号码';
  }
  const invalid = numbers.find((number) => number < 1 || number > 49);
  if (invalid !== undefined) {
    return '号码范围必须在1-49之间';
  }
  if (new Set(numbers).size !== numbers.length) {
    return '开奖号码不能重复';
  }
  return null;
}

function formatDrawNumber(number: number) {
  return String(number).padStart(2, '0');
}

function isBeforeCloseTime() {
  if (!qishuInfo.value?.closetime) {
    return false;
  }
  const closeTime = new Date(qishuInfo.value.closetime).getTime();
  return Number.isFinite(closeTime) && Date.now() < closeTime;
}

function isBeforeDrawTime() {
  if (!qishuInfo.value?.kjtime) {
    return false;
  }
  const drawTime = new Date(qishuInfo.value.kjtime).getTime();
  return Number.isFinite(drawTime) && Date.now() < drawTime;
}

function canSelectPlan() {
  return Boolean(qishuInfo.value?.qishu && !qishuInfo.value?.is_opened && !isBeforeCloseTime());
}

function isImmediateDrawingMode() {
  return Boolean(qishuInfo.value?.qishu && !qishuInfo.value?.is_opened && !isBeforeCloseTime() && !isBeforeDrawTime());
}

function canRevokePlan() {
  return Boolean(qishuInfo.value?.has_planned_result && !qishuInfo.value?.is_opened && isBeforeDrawTime());
}

function canCustomDrawNow() {
  return Boolean(qishuInfo.value?.qishu && !qishuInfo.value?.is_opened && !isBeforeCloseTime() && !isBeforeDrawTime());
}

// 获取盘口列表
async function fetchPlateList() {
  try {
    const plates = await getPlateList(200);
    console.log('📊 [盘口列表] 后端返回数据:', plates);
    plateOptions.value = plates.map(p => ({
      label: p.name,
      value: p.code,
    }));
    console.log('📊 [盘口列表] 转换后的选项:', plateOptions.value);
    // 默认选择第一个盘口
    const firstPlate = plates[0];
    if (firstPlate && !selectedPlate.value) {
      selectedPlate.value = firstPlate.code;
    }
    console.log('📊 [盘口列表] 当前选中:', selectedPlate.value);
  } catch (error) {
    console.error('❌ [盘口列表] 获取失败:', error);
    message.error('获取盘口列表失败');
  }
}

// 获取当前期号
async function fetchCurrentQishu() {
  try {
    const response = await getCurrentQishu(200, selectedPlate.value);
    console.log('🌐 [API原始响应]', response);
    console.log('🔑 [响应中的字段]', Object.keys(response));

    qishuInfo.value = response;
    console.log('📋 [期号信息]', qishuInfo.value);

    // 调试: 检查开奖号码
    if (qishuInfo.value?.is_opened) {
      console.log('🎰 [已开奖]', {
        期号: qishuInfo.value.qishu,
        status: qishuInfo.value.status,
        开奖号码: qishuInfo.value.draw_numbers,
        号码文本: qishuInfo.value.draw_numbers_text,
        是否有号码: qishuInfo.value.draw_numbers && qishuInfo.value.draw_numbers.length > 0,
        完整对象: JSON.stringify(qishuInfo.value)
      });
    }
  } catch (error) {
    console.error('❌ [获取期号失败]', error);
    message.error('获取当前期号失败');
  }
}

/**
 * ⏱️ 计算倒计时
 */
function updateCountdown() {
  if (!qishuInfo.value) {
    countdown.value = '';
    return;
  }

  const now = new Date().getTime();
  const openTime = new Date(qishuInfo.value.opentime).getTime();
  const closeTime = new Date(qishuInfo.value.closetime).getTime();
  const drawTime = new Date(qishuInfo.value.kjtime).getTime();

  let targetTime = 0;
  let prefix = '';

  // 已开奖
  if (qishuInfo.value.is_opened) {
    countdown.value = '已开奖';
    return;
  }

  // 未开盘:显示距离开盘时间
  if (now < openTime) {
    targetTime = openTime;
    prefix = '距离开盘：';
  }
  // 投注中:显示距离封盘时间
  else if (now >= openTime && now < closeTime) {
    targetTime = closeTime;
    prefix = '距离封盘：';
  }
  // 已封盘:显示距离开奖时间
  else if (now >= closeTime && now < drawTime) {
    targetTime = drawTime;
    prefix = '距离开奖：';
  }
  // 开奖时间已过但未开奖
  else {
    countdown.value = '等待开奖中...';
    return;
  }

  const diff = targetTime - now;
  if (diff <= 0) {
    countdown.value = prefix + '0秒';
    return;
  }

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  countdown.value = prefix + `${hours}时${minutes}分${seconds}秒`;
}

/**
 * ⏱️ 启动倒计时定时器
 */
function startCountdownTimer() {
  // 清除旧定时器
  if (countdownTimer) {
    clearInterval(countdownTimer);
  }

  // 立即更新一次
  updateCountdown();

  // 每秒更新
  countdownTimer = setInterval(() => {
    updateCountdown();
  }, 1000);
}

/**
 * ⏱️ 停止倒计时定时器
 */
function stopCountdownTimer() {
  if (countdownTimer) {
    clearInterval(countdownTimer);
    countdownTimer = null;
  }
  countdown.value = '';
}

// 监听盘口切换，自动重新获取期号
watch(selectedPlate, (newValue, oldValue) => {
  console.log('🔄 [盘口切换]', { 旧盘口: oldValue, 新盘口: newValue });
  fetchCurrentQishu();
  // 清空之前的分析结果
  analyzeResult.value = null;
});

// 监听期号变化,启动倒计时
watch(qishuInfo, (newValue) => {
  if (newValue) {
    startCountdownTimer();
  } else {
    stopCountdownTimer();
  }
});

// 实时计算分析
async function handleCalculate() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }
  if (isBeforeCloseTime()) {
    message.warning('封盘后才会生成开奖方案');
    return;
  }

  loading.value = true;
  try {
    analyzeResult.value = await calculateRealtime({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,  // 传递盘口代码
      year: new Date().getFullYear(),
      target_rate: targetRate.value,  // 传递目标利润率
      tolerance: tolerance.value,      // 传递误差范围
      include_negative: true,
    });
    if (analyzeResult.value?.message) {
      message.info(analyzeResult.value.message);
    } else {
      message.success('计算完成');
    }
  } catch (error: any) {
    message.error(error?.message || '计算失败');
  } finally {
    loading.value = false;
  }
}

// 按目标利润率查找
async function handleFindByRate() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }
  if (isBeforeCloseTime()) {
    message.warning('封盘后才会生成开奖方案');
    return;
  }

  loading.value = true;
  try {
    const result = await findByTargetRate({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,  // 添加盘口代码
      target_rate: targetRate.value,
      tolerance: tolerance.value,
      year: new Date().getFullYear(),
    });

    if (result.matched_count === 0) {
      message.info('没有找到符合条件的号码');
      return;
    }

    // 显示匹配的号码
    analyzeResult.value = {
      summary: analyzeResult.value?.summary || {
        total_bets: 0,
        total_orders: 0,
        best_numbers: [],
        best_m7: 0,
        best_m1_m6: [],
        best_profit: 0,
        best_profit_rate: 0,
      },
      details: result.matched_numbers,
    };
    message.success(`找到 ${result.matched_count} 个符合条件的号码`);
  } catch (error: any) {
    message.error(error?.message || '查找失败');
  } finally {
    loading.value = false;
  }
}

// 切换自动刷新
function toggleAutoRefresh() {
  autoRefresh.value = !autoRefresh.value;
  if (autoRefresh.value) {
    refreshTimer = setInterval(() => {
      handleCalculate();
    }, 30000); // 每30秒刷新一次
    message.success('已开启自动刷新（30秒/次）');
  } else {
    if (refreshTimer) {
      clearInterval(refreshTimer);
      refreshTimer = null;
    }
    message.info('已关闭自动刷新');
  }
}

onMounted(() => {
  fetchPlateList();  // 先获取盘口列表
  fetchCurrentQishu();
});

// 📊 格式化表格数据 - 将 top_solutions 转换为表格行
// 💡 按利润率从高到低排序,让利润最高的方案显示在首位
const tableData = computed(() => {
  if (!analyzeResult.value?.top_solutions) {
    return [];
  }

  // 1. 先转换数据
  const formattedData = analyzeResult.value.top_solutions.map((solution, index) => {
    // 合并7个号码
    const numbers = [...solution.m1_m6, solution.m7].sort((a, b) => a - b);

    // 判断风险等级
    const totalPayout = toFiniteNumber(solution.total_prize ?? (solution as any).prize_amount);
    const totalBetAmount = toFiniteNumber(solution.bet_amount ?? analyzeResult.value?.summary?.total_bets);
    const wipeoutType = (solution.wipeout_type || getWipeoutPlanType(solution.profit_rate, totalPayout, totalBetAmount)) as WipeoutPlanType;
    let riskLevel = 0; // 0=安全, 1=注意, 2=危险
    if (solution.profit_rate < 0 || wipeoutType) {
      riskLevel = 2; // 负利润或通杀类方案=危险
    } else if (solution.profit_rate < 50) {
      riskLevel = 1; // 低利润=注意
    }

    // 策略名称映射
    const strategyNames: Record<string, string> = {
      'optimal': '最优',
      'medium': '中等',
      'low_profit': '低利润',
      'mixed': '混合',
    };

    // ✅ 安全获取策略名称
    let strategyName = solution.strategy
      ? (strategyNames[solution.strategy] || solution.strategy)
      : '推荐';
    if (
      (solution.rate_type === 'negative' || solution.rate_type === 'positive')
      && solution.target_rate !== undefined
    ) {
      if (solution.target_note) {
        strategyName = solution.target_note;
      } else if (solution.closest_available) {
        strategyName = `目标${formatRatePercent(solution.target_rate, 0)} / 最近${formatRatePercent(solution.profit_rate)}`;
      } else {
        strategyName = `目标${formatRatePercent(solution.target_rate, 0)} / 实际${formatRatePercent(solution.profit_rate)}`;
      }
    } else if (solution.source === 'special_outcome') {
      strategyName = `实际可选 ${formatRatePercent(solution.profit_rate)}`;
    }
    if (wipeoutType) {
      strategyName = `${getWipeoutPlanLabel(wipeoutType)} | ${strategyName}`;
    }

    return {
      key: index,
      numbers: numbers.join(', '),
      profit: solution.total_profit ?? 0,
      profit_rate: solution.profit_rate ?? 0,
      strategy: strategyName,
      risk_level: riskLevel,
      wipeout_type: wipeoutType,
      // 原始数据,用于开奖
      raw: solution,
    };
  });

  // 2. 按利润率降序排序 (从高到低)
  return formattedData.sort((a, b) => b.profit_rate - a.profit_rate);
});

// 锁定最佳方案
async function handleExecuteDrawing() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }

  if (!analyzeResult.value?.summary?.best_numbers || analyzeResult.value.summary.best_numbers.length !== 7) {
    message.warning('请先计算出最佳方案');
    return;
  }
  if (!canSelectPlan()) {
    message.warning('封盘后才可以选择开奖计划');
    return;
  }

  const immediateMode = isImmediateDrawingMode();
  const bestSolution = (analyzeResult.value as any)?.best_solution;
  const totalProfit = toFiniteNumber(analyzeResult.value.summary.best_profit);
  const profitRate = toFiniteNumber(analyzeResult.value.summary.best_profit_rate);
  const totalPrize = toFiniteNumber(bestSolution?.total_prize ?? bestSolution?.prize_amount);
  const totalBetAmount = toFiniteNumber(bestSolution?.bet_amount ?? analyzeResult.value.summary.total_bets);
  const wipeoutType = (bestSolution?.wipeout_type || getWipeoutPlanType(profitRate, totalPrize, totalBetAmount)) as WipeoutPlanType;
  const isNegativePlan = totalProfit < 0;

  // 二次确认
  const confirm = window.confirm(
    `${immediateMode ? '确定要选择此最佳方案并立即开奖结算？' : '确定要锁定此开奖计划？'}\n\n` +
    `期号：${qishuInfo.value.qishu}\n` +
    `正码(m1-m6)：${analyzeResult.value.summary.best_m1_m6.join(', ')}\n` +
    `特码(m7)：${analyzeResult.value.summary.best_m7}\n\n` +
    `${formatProfitSummary(totalProfit, profitRate)}\n\n` +
    (immediateMode ? '当前已到开奖时间，确认后将立即公布开奖结果并结算。' : '开奖时间前可以改选或撤销，到开奖时间由系统自动结算。')
  );

  if (!confirm) {
    return;
  }
  if (!confirmNegativePlan(totalProfit, profitRate, totalPrize, immediateMode)) {
    return;
  }
  if (!confirmWipeoutPlan(wipeoutType, profitRate, totalPrize, totalBetAmount, immediateMode)) {
    return;
  }

  loading.value = true;
  try {
    const result = await executeDrawing({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,  // 添加盘口代码
      best_numbers: analyzeResult.value.summary.best_numbers,
      year: new Date().getFullYear(),
      negative_confirmed: isNegativePlan,
      wipeout_confirmed: Boolean(wipeoutType),
    });

    const totalOrders = toFiniteNumber((result as any)?.total_orders ?? (result as any)?.data?.total_orders);
    const winCount = toFiniteNumber((result as any)?.win_count ?? (result as any)?.data?.win_count);
    const loseCount = toFiniteNumber((result as any)?.lose_count ?? (result as any)?.data?.lose_count);
    const totalPayout = toFiniteNumber((result as any)?.expected_payout ?? (result as any)?.total_win_amount ?? (result as any)?.data?.expected_payout ?? (result as any)?.data?.total_win_amount);
    const platformProfit = toFiniteNumber((result as any)?.expected_profit ?? (result as any)?.platform_profit ?? (result as any)?.data?.expected_profit ?? (result as any)?.data?.platform_profit);
    const profitRate = toFiniteNumber((result as any)?.expected_profit_rate ?? (result as any)?.data?.expected_profit_rate);
    const settled = ((result as any)?.plan_status ?? (result as any)?.data?.plan_status) === 'settled';

    message.success(
      `${settled ? '开奖并结算完成！' : '开奖计划已锁定！'}\n` +
      `总订单：${totalOrders}笔\n` +
      `中奖：${winCount}笔，${settled ? '实际赔付' : '预计赔付'}¥${formatFixed(totalPayout)}\n` +
      `未中奖：${loseCount}笔\n` +
      formatProfitSummary(platformProfit, profitRate, settled)
    , 10);

    // 计划锁定后，清空当前结果并刷新期号信息
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '锁定计划失败');
  } finally {
    loading.value = false;
  }
}

// ✨ 选中某个方案锁定计划
async function handleSelectAndDraw(record: any) {
  console.log('🔵 handleSelectAndDraw 被调用', record);

  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    console.log('❌ 没有期号信息');
    return;
  }

  const solution = record.raw;

  // ✅ 安全检查: 确保必要字段存在
  if (!solution || !solution.m1_m6 || !solution.m7) {
    console.error('❌ 方案数据不完整:', solution);
    message.error('方案数据不完整,请重新计算');
    return;
  }

  const numbers = [...solution.m1_m6, solution.m7];
  if (!canSelectPlan()) {
    message.warning('封盘后才可以选择开奖计划');
    return;
  }

  console.log('📊 开奖号码:', numbers);
  console.log('📊 完整方案数据:', solution);

  // 安全获取字段值,提供默认值
  const totalProfit = toFiniteNumber(solution.total_profit);
  const profitRate = toFiniteNumber(solution.profit_rate ?? record.profit_rate);
  const totalPrize = toFiniteNumber(solution.total_prize ?? solution.prize_amount);
  const totalBetAmount = toFiniteNumber(solution.bet_amount ?? analyzeResult.value?.summary?.total_bets);
  const wipeoutType = (record.wipeout_type || solution.wipeout_type || getWipeoutPlanType(profitRate, totalPrize, totalBetAmount)) as WipeoutPlanType;
  const strategy = record.strategy || '未知';
  const immediateMode = isImmediateDrawingMode();
  const isNegativePlan = totalProfit < 0;

  // 二次确认
  const confirm = window.confirm(
    `${immediateMode ? '确定要选择此方案并立即开奖结算？' : '确定要锁定此开奖计划？'}\n\n` +
    `期号：${qishuInfo.value.qishu}\n` +
    `盘口：${selectedPlate.value}\n` +
    `正码(m1-m6)：${solution.m1_m6.join(', ')}\n` +
    `特码(m7)：${solution.m7}\n` +
    `策略：${strategy}\n\n` +
    `${formatProfitSummary(totalProfit, profitRate)}\n` +
    `预计赔付：¥${formatFixed(totalPrize)}\n\n` +
    (immediateMode ? '当前已到开奖时间，确认后将立即公布开奖结果并结算。' : '开奖时间前可以改选或撤销，到开奖时间由系统自动结算。')
  );

  if (!confirm) {
    return;
  }
  if (!confirmNegativePlan(totalProfit, profitRate, totalPrize, immediateMode)) {
    return;
  }
  if (!confirmWipeoutPlan(wipeoutType, profitRate, totalPrize, totalBetAmount, immediateMode)) {
    return;
  }

  loading.value = true;
  try {
    const result = await executeDrawing({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,
      best_numbers: numbers,
      year: new Date().getFullYear(),
      negative_confirmed: isNegativePlan,
      wipeout_confirmed: Boolean(wipeoutType),
    });

    const totalOrders = toFiniteNumber((result as any)?.total_orders ?? (result as any)?.data?.total_orders);
    const winCount = toFiniteNumber((result as any)?.win_count ?? (result as any)?.data?.win_count);
    const loseCount = toFiniteNumber((result as any)?.lose_count ?? (result as any)?.data?.lose_count);
    const totalPayout = toFiniteNumber((result as any)?.expected_payout ?? (result as any)?.total_win_amount ?? (result as any)?.data?.expected_payout ?? (result as any)?.data?.total_win_amount);
    const platformProfit = toFiniteNumber((result as any)?.expected_profit ?? (result as any)?.platform_profit ?? (result as any)?.data?.expected_profit ?? (result as any)?.data?.platform_profit);
    const profitRateResult = toFiniteNumber((result as any)?.expected_profit_rate ?? (result as any)?.data?.expected_profit_rate);
    const settled = ((result as any)?.plan_status ?? (result as any)?.data?.plan_status) === 'settled';

    message.success(
      `${settled ? '开奖并结算完成！' : '开奖计划已锁定！'}\n\n` +
      `计划号码：${numbers.join(', ')}\n` +
      `总订单：${totalOrders}笔\n` +
      `中奖：${winCount}笔，${settled ? '实际赔付' : '预计赔付'}¥${formatFixed(totalPayout)}\n` +
      `未中奖：${loseCount}笔\n` +
      formatProfitSummary(platformProfit, profitRateResult, settled)
    , 10);

    // 计划锁定后，刷新期号和清空结果
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '锁定计划失败');
  } finally {
    loading.value = false;
  }
}

async function handleRevokeDrawingPlan() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }
  if (!canRevokePlan()) {
    message.warning('只有开奖时间前可以撤销已锁定计划');
    return;
  }

  if (!window.confirm(`确认撤销当前期号的开奖计划？\n\n期号：${qishuInfo.value.qishu}\n盘口：${selectedPlate.value}\n\n撤销后到开奖时间前需要重新选择方案。`)) {
    return;
  }

  loading.value = true;
  try {
    await revokeDrawingPlan({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,
    });
    message.success('开奖计划已撤销');
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '撤销计划失败');
  } finally {
    loading.value = false;
  }
}

function openCustomDrawModal() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }
  if (qishuInfo.value.is_opened) {
    message.warning('当前期号已开奖');
    return;
  }
  if (isBeforeCloseTime()) {
    message.warning('当前期号尚未封盘，不能自定义开奖');
    return;
  }
  if (isBeforeDrawTime()) {
    message.warning('未到开奖时间，只能先选择并锁定开奖计划');
    return;
  }
  customDrawNumbers.value = [undefined, undefined, undefined, undefined, undefined, undefined, undefined];
  customDrawVisible.value = true;
}

async function handleCustomDrawOk() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }
  if (isBeforeCloseTime()) {
    message.warning('当前期号尚未封盘，不能自定义开奖');
    return;
  }
  if (isBeforeDrawTime()) {
    message.warning('未到开奖时间，只能先选择并锁定开奖计划');
    return;
  }

  const numbers = normalizeDrawNumbers(customDrawNumbers.value);
  const errorText = validateDrawNumbers(numbers);
  if (errorText) {
    message.warning(errorText);
    return;
  }

  const normalNumbers = numbers.slice(0, 6);
  const specialNumber = numbers[6] as number;

  customDrawSubmitting.value = true;
  loading.value = true;
  try {
    const preview = await previewCustomDrawing({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,
      draw_numbers: numbers,
      year: new Date().getFullYear(),
    });
    const previewData = (preview as any)?.data ?? preview;
    const previewPayout = toFiniteNumber(previewData?.expected_payout ?? previewData?.total_payout ?? previewData?.total_win_amount);
    const previewProfit = toFiniteNumber(previewData?.expected_profit ?? previewData?.platform_profit);
    const previewProfitRate = toFiniteNumber(previewData?.expected_profit_rate);
    const previewTotalBet = toFiniteNumber(previewData?.total_bet_amount);
    const previewWipeoutType = (previewData?.wipeout_type || getWipeoutPlanType(previewProfitRate, previewPayout, previewTotalBet)) as WipeoutPlanType;
    const isNegativePlan = previewProfit < 0;

    const confirmText =
      `确认自定义开奖？\n\n` +
      `期号：${qishuInfo.value.qishu}\n` +
      `盘口：${selectedPlate.value}\n` +
      `正码(m1-m6)：${normalNumbers.map(formatDrawNumber).join(', ')}\n` +
      `特码(m7)：${formatDrawNumber(specialNumber)}\n\n` +
      `预计赔付：¥${formatFixed(previewPayout)}\n` +
      (previewWipeoutType ? `风险标记：${getWipeoutPlanLabel(previewWipeoutType)}\n` : '') +
      `${formatProfitSummary(previewProfit, previewProfitRate)}\n\n` +
      `此操作不可撤销！`;

    if (!window.confirm(confirmText)) {
      return;
    }
    if (!confirmNegativePlan(previewProfit, previewProfitRate, previewPayout, true)) {
      return;
    }
    if (!confirmWipeoutPlan(previewWipeoutType, previewProfitRate, previewPayout, previewTotalBet, true)) {
      return;
    }

    const result = await customDrawing({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,
      draw_numbers: numbers,
      year: new Date().getFullYear(),
      negative_confirmed: isNegativePlan,
      wipeout_confirmed: Boolean(previewWipeoutType),
    });

    const totalOrders = toFiniteNumber((result as any)?.total_orders ?? (result as any)?.data?.total_orders);
    const winCount = toFiniteNumber((result as any)?.win_count ?? (result as any)?.data?.win_count);
    const loseCount = toFiniteNumber((result as any)?.lose_count ?? (result as any)?.data?.lose_count);
    const drawCount = toFiniteNumber((result as any)?.draw_count ?? (result as any)?.data?.draw_count);
    const totalPayout = toFiniteNumber(
      (result as any)?.total_payout ??
      (result as any)?.total_win_amount ??
      (result as any)?.data?.total_payout ??
      (result as any)?.data?.total_win_amount
    );
    const platformProfit = toFiniteNumber((result as any)?.platform_profit ?? (result as any)?.data?.platform_profit);
    const profitRate = toFiniteNumber((result as any)?.expected_profit_rate ?? (result as any)?.data?.expected_profit_rate);

    message.success(
      `自定义开奖并结算成功！\n` +
      `开奖号码：${numbers.map(formatDrawNumber).join(', ')}\n` +
      `总订单：${totalOrders}笔\n` +
      `中奖：${winCount}笔，和局：${drawCount}笔，派奖¥${formatFixed(totalPayout)}\n` +
      `未中奖：${loseCount}笔\n` +
      formatProfitSummary(platformProfit, profitRate, true),
      10
    );

    customDrawVisible.value = false;
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '自定义开奖失败');
  } finally {
    customDrawSubmitting.value = false;
    loading.value = false;
  }
}

/**
 * 手动创建新期号
 */
async function handleCreateNewIssue() {
  let preview: BestPlanApi.NewIssueResult;

  loading.value = true;
  try {
    preview = await previewNewIssue({
      gid: 200,
      plate_code: selectedPlate.value,
      strategy: createIssueStrategy.value,
    });
  } catch (error: any) {
    message.error(error?.message || '预览新期号失败');
    loading.value = false;
    return;
  } finally {
    loading.value = false;
  }

  const currentIssue = preview.current_issue;
  const confirmMessage = `确认创建新期号\n\n` +
    `选择盘口：${selectedPlate.value}\n` +
    `创建方式：${preview.strategy_text}\n` +
    `规则说明：${preview.source_text || '按当前选择的创建方式生成'}\n\n` +
    `当前期号：${currentIssue?.issue || '暂无'}${currentIssue ? `（${currentIssue.status_text}）` : ''}\n` +
    `新期号：${preview.issue}\n\n` +
    `新开盘时间：${preview.open_time}\n` +
    `新封盘时间：${preview.close_time}\n` +
    `新开奖时间：${preview.draw_time}\n` +
    `新状态：${preview.status_text}\n\n` +
    `确认后将按以上时间写入数据库。`;

  Modal.confirm({
    title: '创建新期号',
    content: confirmMessage,
    okText: '确定',
    cancelText: '取消',
    onOk: async () => {
      loading.value = true;
      try {
        const result = await createNewIssue({
          gid: 200,
          plate_code: selectedPlate.value,
          strategy: createIssueStrategy.value,
        });

        message.success(
          `✅ 新期号创建成功！\n\n` +
          `期号：${result.issue}\n` +
          `开盘时间：${result.open_time}\n` +
          `封盘时间：${result.close_time}\n` +
          `开奖时间：${result.draw_time}\n` +
          `状态：${result.status_text}\n` +
          `创建方式：${result.strategy_text}`,
          5
        );

        // 刷新期号信息
        await fetchCurrentQishu();
      } catch (error: any) {
        message.error(error?.message || '创建新期号失败');
      } finally {
        loading.value = false;
      }
    },
  });
}

async function handleClearTodayData() {
  const plate = selectedPlate.value || 'A';
  const confirmMessage =
    `确认清空今日期数？\n\n` +
    `盘口：${plate}\n` +
    `日期：${new Date().toLocaleDateString('zh-CN')}\n\n` +
    `此操作会删除当前盘口今日的期号、开奖/待开奖信息、今日下单记录、中奖记录、账户流水和分析历史。\n` +
    `系统会先按投注状态回滚用户余额、冻结金额、累计投注和累计派奖。\n\n` +
    `此操作不可撤销，确认继续？`;

  Modal.confirm({
    title: '清空今日期数',
    content: confirmMessage,
    okText: '确认清空',
    okButtonProps: { danger: true },
    cancelText: '取消',
    onOk: async () => {
      clearTodaySubmitting.value = true;
      loading.value = true;
      try {
        const result = await clearTodayData({
          gid: 200,
          plate_code: plate,
        });

        message.success(
          `清空完成！\n` +
          `日期：${result.date}\n` +
          `盘口：${result.plate_code}\n` +
          `删除期号：${result.issue_count}个\n` +
          `删除订单：${result.betting_count}笔\n` +
          `影响用户：${result.affected_users}个`,
          8
        );

        analyzeResult.value = null;
        await fetchCurrentQishu();
      } catch (error: any) {
        message.error(error?.message || '清空今日期数失败');
      } finally {
        clearTodaySubmitting.value = false;
        loading.value = false;
      }
    },
  });
}

// 组件卸载时清理定时器
onBeforeUnmount(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer);
  }
  if (countdownTimer) {
    clearInterval(countdownTimer);
  }
});
</script>

<template>
  <div class="p-4">
    <!-- 期号信息 -->
    <Card title="期号信息" class="mb-4">
      <Row :gutter="16" v-if="qishuInfo">
        <Col :span="4">
          <div class="text-gray-500">当前期号</div>
          <div class="text-2xl font-bold">{{ qishuInfo.qishu }}</div>
        </Col>
        <Col :span="4">
          <div class="text-gray-500">开盘时间</div>
          <div class="text-lg">{{ qishuInfo.opentime }}</div>
        </Col>
        <Col :span="4">
          <div class="text-gray-500">封盘时间</div>
          <div class="text-lg">{{ qishuInfo.closetime }}</div>
        </Col>
        <Col :span="4">
          <div class="text-gray-500">开奖时间</div>
          <div class="text-lg">{{ qishuInfo.kjtime }}</div>
        </Col>
        <Col :span="3">
          <div class="text-gray-500">当前状态</div>
          <div class="text-lg">
            <Tag v-if="qishuInfo.is_opened" color="red">已开奖</Tag>
            <Tag v-else-if="qishuInfo.has_planned_result" color="purple">已锁定计划</Tag>
            <Tag v-else-if="new Date() > new Date(qishuInfo.closetime)" color="orange">已封盘</Tag>
            <Tag v-else-if="new Date() > new Date(qishuInfo.opentime)" color="green">投注中</Tag>
            <Tag v-else color="blue">待开盘</Tag>
          </div>
        </Col>
        <Col :span="5" v-if="countdown">
          <div class="text-gray-500">倒计时</div>
          <div class="text-lg font-bold" :class="{
            'text-blue-600': countdown.includes('开盘'),
            'text-orange-600': countdown.includes('封盘'),
            'text-red-600': countdown.includes('开奖'),
            'text-gray-500': countdown === '已开奖' || countdown.includes('等待')
          }">
            {{ countdown }}
          </div>
        </Col>
        <Col :span="5" v-if="qishuInfo.is_opened && qishuInfo.draw_numbers && qishuInfo.draw_numbers.length > 0">
          <div class="text-gray-500">开奖号码</div>
          <div class="text-lg font-bold">
            <Space :size="4">
              <Tag v-for="(num, index) in qishuInfo.draw_numbers" :key="index"
                   :color="index === 6 ? 'volcano' : 'blue'"
                   style="font-size: 14px; font-weight: bold; padding: 2px 8px;">
                {{ num }}
              </Tag>
            </Space>
          </div>
        </Col>
      </Row>
      <div v-else class="text-gray-400">暂无期号信息</div>
    </Card>

    <!-- 操作按钮 -->
    <Card title="操作" class="mb-4">
      <div class="mb-4">
        <Space>
          <span>选择盘口：</span>
          <Select v-model:value="selectedPlate" style="width: 120px" :options="plateOptions" />
          <span>创建方式：</span>
          <Select
            v-model:value="createIssueStrategy"
            style="width: 170px"
            :options="createIssueStrategyOptions"
          />
          <Button type="dashed" :loading="loading" @click="handleCreateNewIssue">
            🆕 创建新期号
          </Button>
        </Space>
      </div>

      <Space>
        <Button type="primary" :loading="loading" :disabled="isBeforeCloseTime()" @click="handleCalculate">
          实时计算
        </Button>
        <Button :type="autoRefresh ? 'default' : 'primary'" @click="toggleAutoRefresh">
          {{ autoRefresh ? '关闭' : '开启' }}自动刷新
        </Button>
        <Button
          type="primary"
          danger
          size="large"
          :loading="loading"
          :disabled="(analyzeResult?.summary?.best_numbers?.length ?? 0) !== 7 || !canSelectPlan()"
          @click="handleExecuteDrawing"
          style="margin-left: 20px;"
        >
          {{ isImmediateDrawingMode() ? '选择最佳并开奖' : '锁定最佳计划' }}
        </Button>
        <Button
          size="large"
          :loading="loading"
          :disabled="!canRevokePlan()"
          @click="handleRevokeDrawingPlan"
        >
          撤销计划
        </Button>
        <Button
          danger
          size="large"
          :loading="customDrawSubmitting"
          :disabled="!canCustomDrawNow()"
          @click="openCustomDrawModal"
        >
          自定义开奖
        </Button>
        <Button
          danger
          size="large"
          :loading="clearTodaySubmitting"
          @click="handleClearTodayData"
        >
          清空今日期数
        </Button>
      </Space>

      <div class="mt-4">
        <Space>
          <span>目标利润率：</span>
          <InputNumber v-model:value="targetRate" :min="0" :max="100" :step="1" />
          <span>%</span>
          <span>误差范围：±</span>
          <InputNumber v-model:value="tolerance" :min="0" :max="10" :step="0.1" />
          <span>%</span>
          <Button :loading="loading" :disabled="isBeforeCloseTime()" @click="handleFindByRate">
            查找号码
          </Button>
        </Space>
      </div>
    </Card>

    <!-- 汇总信息 -->
    <Card title="汇总信息" class="mb-4" v-if="analyzeResult">
      <Row :gutter="16">
        <Col :span="6">
          <Statistic title="总投注额" :value="analyzeResult.summary.total_bets" prefix="¥" :precision="2" />
        </Col>
        <Col :span="6">
          <Statistic title="总投注笔数" :value="analyzeResult.summary.total_orders" />
        </Col>
        <Col :span="12">
          <div>
            <div class="text-gray-500 mb-1">最佳7个号码组合</div>
            <div class="flex items-center gap-2 mb-2">
              <span class="text-sm text-gray-500">正码(m1-m6):</span>
              <div class="flex gap-1">
                <span
                  v-for="num in analyzeResult.summary.best_m1_m6"
                  :key="num"
                  class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm"
                >
                  {{ num }}
                </span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-500">特码(m7):</span>
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-700 font-bold text-lg">
                {{ analyzeResult.summary.best_m7 }}
              </span>
              <div class="ml-4 text-sm text-gray-500">
                利润 ¥{{ formatFixed(analyzeResult.summary.best_profit) }}
                ({{ formatFixed(analyzeResult.summary.best_profit_rate) }}%)
              </div>
            </div>
          </div>
        </Col>
      </Row>

      <!-- 亏损分析与建议（仅在利润为负时显示） -->
      <div v-if="toFiniteNumber(analyzeResult.summary.best_profit) < 0" class="mt-4">
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <h3 class="text-lg font-medium text-red-800">⚠️ 亏损警告</h3>
              <div class="mt-2 text-sm text-red-700">
                <p class="font-semibold">亏损原因分析：</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                  <li>投注过于集中在少数高赔率玩法（正码赔率7倍、特码赔率47倍）</li>
                  <li>任选7个号码都会触发多个正码中奖，导致赔付超过收入</li>
                  <li>当前投注分布下，算法无法找到盈利方案</li>
                  <li>预计赔付 ≥ ¥{{ formatFixed(Math.abs(toFiniteNumber(analyzeResult.summary.best_profit))) }}，收入 ¥{{ formatFixed(analyzeResult.summary.total_bets) }}</li>
                </ul>
              </div>
              <div class="mt-3 text-sm text-red-700">
                <p class="font-semibold">💡 建议措施：</p>
                <ol class="list-decimal list-inside mt-2 space-y-1">
                  <li><strong>降低赔率</strong>：将正码赔率从7倍降低至5-6倍，特码从47倍降至40倍</li>
                  <li><strong>延迟开奖</strong>：等待更多投注进入，稀释当前集中投注的风险</li>
                  <li><strong>调整投注限额</strong>：限制单个号码的最大投注金额，避免过度集中</li>
                  <li><strong>接受亏损</strong>：选择当前方案（亏损最小化），作为营销成本吸引玩家</li>
                  <li><strong>人工干预</strong>：联系大额投注用户，协商退款或转移投注</li>
                </ol>
              </div>
              <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800">
                  <strong>风控提示：</strong>当前为极端情况，建议立即采取措施。系统已选择"亏损最小"方案（{{ formatFixed(analyzeResult.summary.best_profit_rate) }}%），
                  如强制开奖将损失 ¥{{ formatFixed(Math.abs(toFiniteNumber(analyzeResult.summary.best_profit))) }} 元。
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Card>

    <!-- 所有方案列表表格 -->
    <Card title="所有方案列表（封盘后可选择方案）" v-if="analyzeResult">
      <Table
        :columns="columns"
        :data-source="tableData"
        :loading="loading"
        :pagination="{ pageSize: 20, showSizeChanger: true, showTotal: (total) => `共 ${total} 个方案` }"
        :scroll="{ x: 1200 }"
        row-key="key"
      >
        <template #bodyCell="{ column, record }">
          <!-- 风险等级列 - 使用Tag组件显示 -->
          <template v-if="column.key === 'risk_level'">
            <Tag :color="record.wipeout_type ? 'error' : record.risk_level === 0 ? 'success' : record.risk_level === 1 ? 'warning' : 'error'">
              {{ record.wipeout_type ? getWipeoutPlanLabel(record.wipeout_type) : record.risk_level === 0 ? '✅ 安全' : record.risk_level === 1 ? '⚠️ 注意' : '🔴 危险' }}
            </Tag>
          </template>

          <!-- 操作列 - 显示"选中此方案"按钮 -->
          <template v-if="column.key === 'action'">
            <Button
              type="primary"
              size="small"
              @click="() => handleSelectAndDraw(record)"
              :disabled="!canSelectPlan()"
              :title="canSelectPlan() ? (isImmediateDrawingMode() ? '点击选择此方案并立即开奖结算' : '点击选择此方案锁定计划') : '封盘后可选择方案'"
            >
              {{ isImmediateDrawingMode() ? '选中并开奖' : '选中此方案' }}
            </Button>
          </template>
        </template>
      </Table>
    </Card>

    <!-- 详细数据表格（原有的号码详情，保持不变） -->
    <Card title="号码投注详情" v-if="analyzeResult?.details && analyzeResult.details.length > 0">
      <Table
        :columns="detailColumns"
        :data-source="analyzeResult.details"
        :loading="loading"
        :pagination="{ pageSize: 20 }"
        :scroll="{ x: 800 }"
        row-key="number"
      />
    </Card>

    <Modal
      v-model:open="customDrawVisible"
      title="自定义开奖"
      ok-text="确定开奖"
      cancel-text="取消"
      :confirm-loading="customDrawSubmitting"
      @ok="handleCustomDrawOk"
    >
      <div v-if="qishuInfo" class="space-y-4">
        <div class="text-sm text-gray-600">
          <div>期号：<span class="font-medium text-gray-900">{{ qishuInfo.qishu }}</span></div>
          <div>盘口：<span class="font-medium text-gray-900">{{ selectedPlate }}</span></div>
        </div>

        <div>
          <div class="mb-2 text-sm font-medium text-gray-700">正码 m1-m6</div>
          <div class="grid grid-cols-3 gap-3">
            <div v-for="index in 6" :key="`normal-${index}`">
              <div class="mb-1 text-xs text-gray-500">m{{ index }}</div>
              <InputNumber
                v-model:value="customDrawNumbers[index - 1]"
                :min="1"
                :max="49"
                :precision="0"
                style="width: 100%;"
                placeholder="1-49"
              />
            </div>
          </div>
        </div>

        <div>
          <div class="mb-2 text-sm font-medium text-gray-700">特码 m7</div>
          <InputNumber
            v-model:value="customDrawNumbers[6]"
            :min="1"
            :max="49"
            :precision="0"
            style="width: 100%;"
            placeholder="1-49"
          />
        </div>

        <div class="rounded border border-orange-200 bg-orange-50 p-3 text-sm text-orange-700">
          前 6 个号码按正码计算，第 7 个号码按特码计算；号码必须为 1-49 且不能重复。
        </div>
      </div>
    </Modal>
  </div>
</template>
