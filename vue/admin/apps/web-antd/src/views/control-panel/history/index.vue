<script setup lang="ts">
import { onMounted, ref } from 'vue';

import { Card, Table, Tag, Button, Modal, Descriptions, message } from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import {
  getHistoryList,
  getDetail,
  type BestPlanApi,
} from '#/api/best-plan';

defineOptions({ name: 'ControlPanelHistory' });

// 历史记录管理页面 - 已移除所有customRender，使用template slots

const loading = ref(false);
const historyList = ref<BestPlanApi.HistoryRecord[]>([]);
const detailVisible = ref(false);
const detailData = ref<BestPlanApi.DetailRecord | null>(null);
const detailLoading = ref(false);

// 历史列表表格列定义
const columns: TableColumnsType = [
  {
    title: '期号',
    dataIndex: 'qishu',
    key: 'qishu',
    width: 120,
  },
  {
    title: '分析时间',
    dataIndex: 'analyze_time',
    key: 'analyze_time',
    width: 180,
  },
  {
    title: '总投注额',
    dataIndex: 'total_bets',
    key: 'total_bets',
    width: 120,
    align: 'right',
  },
  {
    title: '投注笔数',
    dataIndex: 'total_orders',
    key: 'total_orders',
    width: 100,
    align: 'right',
  },
  {
    title: '推荐号码',
    dataIndex: 'best_numbers',
    key: 'best_numbers',
    width: 180,
    align: 'center',
  },
  {
    title: '推荐利润',
    dataIndex: 'best_profit',
    key: 'best_profit',
    width: 120,
    align: 'right',
  },
  {
    title: '实际开出',
    dataIndex: 'actual_number',
    key: 'actual_number',
    width: 100,
    align: 'center',
  },
  {
    title: '实际利润',
    dataIndex: 'actual_profit',
    key: 'actual_profit',
    width: 120,
    align: 'right',
  },
  {
    title: '状态',
    dataIndex: 'status',
    key: 'status',
    width: 100,
    align: 'center',
  },
  {
    title: '操作',
    key: 'action',
    width: 100,
    align: 'center',
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
    title: '利润（元）',
    dataIndex: 'profit',
    key: 'profit',
    width: 120,
    align: 'right',
  },
  {
    title: '利润率（%）',
    dataIndex: 'profit_rate',
    key: 'profit_rate',
    width: 120,
    align: 'right',
  },
  {
    title: '赔付金额（元）',
    dataIndex: 'prize_amount',
    key: 'prize_amount',
    width: 130,
    align: 'right',
  },
  {
    title: '中奖注数',
    dataIndex: 'bet_count',
    key: 'bet_count',
    width: 100,
    align: 'right',
  },
  {
    title: '风险等级',
    dataIndex: 'risk_level',
    key: 'risk_level',
    width: 100,
    align: 'center',
  },
];

// 获取历史列表
async function fetchHistoryList() {
  loading.value = true;
  try {
    historyList.value = await getHistoryList({
      gid: 200,
      limit: 50,
    });
  } catch (error: any) {
    message.error(error?.message || '获取历史列表失败');
  } finally {
    loading.value = false;
  }
}

// 查看详情
async function handleViewDetail(id: number) {
  detailVisible.value = true;
  detailLoading.value = true;
  try {
    detailData.value = await getDetail(id);
  } catch (error: any) {
    message.error(error?.message || '获取详情失败');
  } finally {
    detailLoading.value = false;
  }
}

// 关闭详情弹窗
function handleCloseDetail() {
  detailVisible.value = false;
  detailData.value = null;
}

onMounted(() => {
  fetchHistoryList();
});
</script>

<template>
  <div class="p-4">
    <Card title="历史记录">
      <template #extra>
        <Button type="primary" @click="fetchHistoryList" :loading="loading">
          刷新
        </Button>
      </template>

      <Table
        :columns="columns"
        :data-source="historyList"
        :loading="loading"
        :pagination="{ pageSize: 20 }"
        :scroll="{ x: 1200 }"
        row-key="id"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'total_bets'">
            ¥{{ Number(record.total_bets).toFixed(2) }}
          </template>
          <template v-else-if="column.key === 'best_numbers'">
            <Tag color="success">{{ record.best_numbers }}</Tag>
          </template>
          <template v-else-if="column.key === 'best_profit'">
            ¥{{ Number(record.best_profit).toFixed(2) }} ({{ Number(record.best_profit_rate).toFixed(2) }}%)
          </template>
          <template v-else-if="column.key === 'actual_number'">
            <span v-if="record.actual_number === null">未开奖</span>
            <Tag v-else :color="record.best_numbers?.includes(String(record.actual_number)) ? 'success' : 'default'">
              {{ record.actual_number }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'actual_profit'">
            <span v-if="record.actual_profit === null">-</span>
            <span v-else :class="record.actual_profit >= 0 ? 'text-green-600' : 'text-red-600'">
              ¥{{ Number(record.actual_profit).toFixed(2) }}
            </span>
          </template>
          <template v-else-if="column.key === 'status'">
            <Tag :color="record.status === 0 ? 'processing' : record.status === 1 ? 'success' : 'default'">
              {{ record.status === 0 ? '未开奖' : record.status === 1 ? '已开奖' : '已验证' }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'action'">
            <Button type="link" @click="handleViewDetail(record.id)">
              查看详情
            </Button>
          </template>
        </template>
      </Table>
    </Card>

    <!-- 详情弹窗 -->
    <Modal
      v-model:open="detailVisible"
      title="分析详情"
      width="90%"
      :footer="null"
      @cancel="handleCloseDetail"
    >
      <div v-if="detailData && !detailLoading">
        <!-- 基本信息 -->
        <Card title="基本信息" class="mb-4">
          <Descriptions bordered :column="2">
            <Descriptions.Item label="期号">{{ detailData.qishu }}</Descriptions.Item>
            <Descriptions.Item label="分析时间">{{ detailData.analyze_time }}</Descriptions.Item>
            <Descriptions.Item label="总投注额">¥{{ Number(detailData.total_bets).toFixed(2) }}</Descriptions.Item>
            <Descriptions.Item label="总投注笔数">{{ detailData.total_orders }}</Descriptions.Item>
            <Descriptions.Item label="推荐号码">
              <Tag color="success">{{ detailData.best_numbers }}</Tag>
            </Descriptions.Item>
            <Descriptions.Item label="推荐利润">
              ¥{{ Number(detailData.best_profit).toFixed(2) }} ({{ Number(detailData.best_profit_rate).toFixed(2) }}%)
            </Descriptions.Item>
            <Descriptions.Item label="最差号码">
              <Tag color="error">{{ detailData.worst_number }}</Tag>
            </Descriptions.Item>
            <Descriptions.Item label="最差利润">
              ¥{{ Number(detailData.worst_profit).toFixed(2) }} ({{ Number(detailData.worst_profit_rate).toFixed(2) }}%)
            </Descriptions.Item>
            <Descriptions.Item label="平均利润">¥{{ Number(detailData.avg_profit).toFixed(2) }}</Descriptions.Item>
            <Descriptions.Item label="状态">
              <Tag :color="detailData.status === 0 ? 'processing' : 'success'">
                {{ detailData.status === 0 ? '未开奖' : detailData.status === 1 ? '已开奖' : '已验证' }}
              </Tag>
            </Descriptions.Item>
            <Descriptions.Item label="实际开出号码" v-if="detailData.actual_number !== null">
              <Tag :color="detailData.best_numbers?.includes(String(detailData.actual_number)) ? 'success' : 'default'">
                {{ detailData.actual_number }}
              </Tag>
            </Descriptions.Item>
            <Descriptions.Item label="实际利润" v-if="detailData.actual_profit !== null">
              <span :class="detailData.actual_profit >= 0 ? 'text-green-600' : 'text-red-600'">
                ¥{{ Number(detailData.actual_profit).toFixed(2) }}
              </span>
            </Descriptions.Item>
          </Descriptions>
        </Card>

        <!-- 号码详情 -->
        <Card title="号码详情（49个号码）">
          <Table
            :columns="detailColumns"
            :data-source="detailData.number_details"
            :pagination="{ pageSize: 20 }"
            :scroll="{ x: 800 }"
            row-key="number"
          >
            <template #bodyCell="{ column, record }">
              <template v-if="column.key === 'profit'">
                <span :class="record.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                  ¥{{ Number(record.profit).toFixed(2) }}
                </span>
              </template>
              <template v-else-if="column.key === 'profit_rate'">
                {{ Number(record.profit_rate).toFixed(2) }}%
              </template>
              <template v-else-if="column.key === 'prize_amount'">
                ¥{{ Number(record.prize_amount).toFixed(2) }}
              </template>
              <template v-else-if="column.key === 'risk_level'">
                <Tag :color="record.risk_level === 0 ? 'success' : record.risk_level === 1 ? 'warning' : 'error'">
                  {{ record.risk_level_text || (record.risk_level === 0 ? '安全' : record.risk_level === 1 ? '注意' : '危险') }}
                </Tag>
              </template>
            </template>
          </Table>
        </Card>
      </div>

      <div v-else class="text-center py-8">
        <span v-if="detailLoading">加载中...</span>
        <span v-else>暂无数据</span>
      </div>
    </Modal>
  </div>
</template>
