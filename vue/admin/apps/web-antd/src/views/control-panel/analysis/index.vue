<script setup lang="ts">
import { computed, onMounted, ref, watch, onBeforeUnmount } from 'vue';

import { Card, Table, Tag, Button, Space, Statistic, Row, Col, InputNumber, Select, message, Modal } from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import {
  getCurrentQishu,
  getPlateList,
  calculateRealtime,
  analyzeAndSave,
  findByTargetRate,
  executeDrawing,
  createNewIssue,
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
    title: '策略',
    dataIndex: 'strategy',
    key: 'strategy',
    width: 100,
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

// 风险等级颜色
const getRiskColor = (level: number) => {
  return level === 0 ? 'green' : level === 1 ? 'orange' : 'red';
};

function toFiniteNumber(value: unknown, fallback = 0) {
  const num = typeof value === 'number' ? value : Number(value);
  return Number.isFinite(num) ? num : fallback;
}

function formatFixed(value: unknown, digits = 2, fallback = 0) {
  return toFiniteNumber(value, fallback).toFixed(digits);
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
    if (plates.length > 0 && !selectedPlate.value) {
      selectedPlate.value = plates[0].code;
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

  loading.value = true;
  try {
    analyzeResult.value = await calculateRealtime({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,  // 传递盘口代码
      year: new Date().getFullYear(),
      target_rate: targetRate.value,  // 传递目标利润率
      tolerance: tolerance.value,      // 传递误差范围
    });
    message.success('计算完成');
  } catch (error: any) {
    message.error(error?.message || '计算失败');
  } finally {
    loading.value = false;
  }
}

// 执行分析并保存
async function handleAnalyzeAndSave() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }

  loading.value = true;
  try {
    analyzeResult.value = await analyzeAndSave({
      gid: 200,
      qishu: qishuInfo.value.qishu,
      plate_code: selectedPlate.value,  // 添加盘口代码
      year: new Date().getFullYear(),
    });
    message.success('分析完成并已保存');
  } catch (error: any) {
    message.error(error?.message || '分析失败');
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
    let riskLevel = 0; // 0=安全, 1=注意, 2=危险
    if (solution.profit_rate < 0) {
      riskLevel = 2; // 负利润=危险
    } else if (solution.profit_rate < 50) {
      riskLevel = 1; // 低利润=注意
    }

    // 策略名称映射
    const strategyNames = {
      'optimal': '最优',
      'medium': '中等',
      'low_profit': '低利润',
      'mixed': '混合',
    };

    // ✅ 安全获取策略名称
    const strategyName = solution.strategy
      ? (strategyNames[solution.strategy] || solution.strategy)
      : '未知';

    return {
      key: index,
      numbers: numbers.join(', '),
      profit: solution.total_profit ?? 0,
      profit_rate: solution.profit_rate ?? 0,
      strategy: strategyName,
      risk_level: riskLevel,
      // 原始数据,用于开奖
      raw: solution,
    };
  });

  // 2. 按利润率降序排序 (从高到低)
  return formattedData.sort((a, b) => b.profit_rate - a.profit_rate);
});

// 用此方案开奖
async function handleExecuteDrawing() {
  if (!qishuInfo.value?.qishu) {
    message.warning('请先获取当前期号');
    return;
  }

  if (!analyzeResult.value?.summary?.best_numbers || analyzeResult.value.summary.best_numbers.length !== 7) {
    message.warning('请先计算出最佳方案');
    return;
  }

  // 二次确认
  const confirm = window.confirm(
    `确定要用此方案开奖吗？\n\n期号：${qishuInfo.value.qishu}\n正码(m1-m6)：${analyzeResult.value.summary.best_m1_m6.join(', ')}\n特码(m7)：${analyzeResult.value.summary.best_m7}\n\n预计利润：¥${formatFixed(analyzeResult.value.summary.best_profit)} (${formatFixed(analyzeResult.value.summary.best_profit_rate)}%)\n\n此操作不可撤销！`
  );

  if (!confirm) {
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
    });

    const totalOrders = toFiniteNumber((result as any)?.total_orders ?? (result as any)?.data?.total_orders);
    const winCount = toFiniteNumber((result as any)?.win_count ?? (result as any)?.data?.win_count);
    const loseCount = toFiniteNumber((result as any)?.lose_count ?? (result as any)?.data?.lose_count);
    const totalWinAmount = toFiniteNumber((result as any)?.total_win_amount ?? (result as any)?.data?.total_win_amount);
    const platformProfit = toFiniteNumber((result as any)?.platform_profit ?? (result as any)?.data?.platform_profit);

    message.success(
      `开奖成功！\n` +
      `总订单：${totalOrders}笔\n` +
      `中奖：${winCount}笔，派奖¥${formatFixed(totalWinAmount)}\n` +
      `未中奖：${loseCount}笔\n` +
      `平台利润：¥${formatFixed(platformProfit)}`
    , 10);

    // 开奖成功后，清空当前结果并刷新期号信息
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '开奖失败');
  } finally {
    loading.value = false;
  }
}

// ✨ 选中某个方案进行开奖
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

  console.log('📊 开奖号码:', numbers);
  console.log('📊 完整方案数据:', solution);

  // 安全获取字段值,提供默认值
  const totalProfit = toFiniteNumber(solution.total_profit);
  const profitRate = toFiniteNumber(solution.profit_rate ?? record.profit_rate);
  const totalPrize = toFiniteNumber(solution.total_prize);
  const strategy = record.strategy || '未知';

  // 二次确认
  const confirm = window.confirm(
    `确定要用此方案开奖吗？\n\n` +
    `期号：${qishuInfo.value.qishu}\n` +
    `盘口：${selectedPlate.value}\n` +
    `正码(m1-m6)：${solution.m1_m6.join(', ')}\n` +
    `特码(m7)：${solution.m7}\n` +
    `策略：${strategy}\n\n` +
    `预计利润：¥${formatFixed(totalProfit)} (${formatFixed(profitRate)}%)\n` +
    `预计赔付：¥${formatFixed(totalPrize)}\n\n` +
    `此操作不可撤销！`
  );

  if (!confirm) {
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
    });

    const totalOrders = toFiniteNumber((result as any)?.total_orders ?? (result as any)?.data?.total_orders);
    const winCount = toFiniteNumber((result as any)?.win_count ?? (result as any)?.data?.win_count);
    const loseCount = toFiniteNumber((result as any)?.lose_count ?? (result as any)?.data?.lose_count);
    const totalWinAmount = toFiniteNumber((result as any)?.total_win_amount ?? (result as any)?.data?.total_win_amount);
    const platformProfit = toFiniteNumber((result as any)?.platform_profit ?? (result as any)?.data?.platform_profit);

    message.success(
      `🎉 开奖成功！\n\n` +
      `开奖号码：${numbers.sort((a, b) => a - b).join(', ')}\n` +
      `总订单：${totalOrders}笔\n` +
      `中奖：${winCount}笔，派奖¥${formatFixed(totalWinAmount)}\n` +
      `未中奖：${loseCount}笔\n` +
      `平台利润：¥${formatFixed(platformProfit)}`
    , 10);

    // 开奖成功后，刷新期号和清空结果
    analyzeResult.value = null;
    await fetchCurrentQishu();
  } catch (error: any) {
    message.error(error?.message || '开奖失败');
  } finally {
    loading.value = false;
  }
}

/**
 * 手动创建新期号
 */
async function handleCreateNewIssue() {
  let confirmMessage = '';

  // 如果没有期号信息，说明是第一次创建
  if (!qishuInfo.value) {
    confirmMessage = `🆕 首次创建期号\n\n` +
      `选择盘口：${selectedPlate.value}\n\n` +
      `系统将使用盘口配置的默认时间创建第一个期号\n` +
      `开盘时间：06:00\n` +
      `封盘时间：09:30\n` +
      `开奖时间：09:50\n\n` +
      `是否确认创建？`;
  } else {
    const currentTime = new Date();
    const currentHour = currentTime.getHours();
    const currentMinute = currentTime.getMinutes();

    // 检查今天是否已经开过奖(开奖时间是09:50)
    const hasDrawnToday = qishuInfo.value.is_opened && currentHour >= 9 && currentMinute >= 50;

    if (hasDrawnToday) {
      confirmMessage = `⚠️ 今日已开过奖！\n\n` +
        `当前期号：${qishuInfo.value.qishu}\n\n` +
        `系统将在上一期开奖时间 + 30分钟作为新期开盘时间\n` +
        `封盘和开奖时间也将 + 30分钟\n\n` +
        `是否确认创建新期号？`;
    } else {
      confirmMessage = `📋 确认开设新期号\n\n` +
        `当前期号：${qishuInfo.value.qishu}\n` +
        `选择盘口：${selectedPlate.value}\n\n` +
        `开盘时间：${qishuInfo.value.opentime}\n` +
        `封盘时间：${qishuInfo.value.closetime}\n` +
        `开奖时间：${qishuInfo.value.kjtime}\n\n` +
        `确认信息是否正确？`;
    }
  }

  Modal.confirm({
    title: '开设新盘口',
    content: confirmMessage,
    okText: '确定',
    cancelText: '取消',
    onOk: async () => {
      loading.value = true;
      try {
        const result = await createNewIssue({
          gid: 200,
          plate_code: selectedPlate.value,
        });

        message.success(
          `✅ 新期号创建成功！\n\n` +
          `期号：${result.issue}\n` +
          `开盘时间：${result.open_time}\n` +
          `封盘时间：${result.close_time}\n` +
          `开奖时间：${result.draw_time}\n` +
          `状态：${result.status_text}`,
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
          <Button type="dashed" :loading="loading" @click="handleCreateNewIssue">
            🆕 开设新盘口
          </Button>
        </Space>
      </div>

      <Space>
        <Button type="primary" :loading="loading" @click="handleCalculate">
          实时计算
        </Button>
        <Button type="primary" :loading="loading" @click="handleAnalyzeAndSave">
          分析并保存
        </Button>
        <Button :type="autoRefresh ? 'default' : 'primary'" @click="toggleAutoRefresh">
          {{ autoRefresh ? '关闭' : '开启' }}自动刷新
        </Button>
        <Button
          type="primary"
          danger
          size="large"
          :loading="loading"
          :disabled="!analyzeResult?.summary?.best_numbers"
          @click="handleExecuteDrawing"
          style="margin-left: 20px;"
        >
          🎯 用此方案开奖
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
          <Button :loading="loading" @click="handleFindByRate">
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
    <Card title="所有方案列表（可选择任意方案开奖）" v-if="analyzeResult">
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
            <Tag :color="record.risk_level === 0 ? 'success' : record.risk_level === 1 ? 'warning' : 'error'">
              {{ record.risk_level === 0 ? '✅ 安全' : record.risk_level === 1 ? '⚠️ 注意' : '🔴 危险' }}
            </Tag>
          </template>

          <!-- 操作列 - 显示"选中此号码开奖"按钮 -->
          <template v-if="column.key === 'action'">
            <Button
              type="primary"
              size="small"
              @click="() => handleSelectAndDraw(record)"
              :disabled="!qishuInfo?.qishu"
              :title="!qishuInfo?.qishu ? '请先获取当前期号' : '点击选择此方案开奖'"
            >
              选中此号码开奖
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
  </div>
</template>
