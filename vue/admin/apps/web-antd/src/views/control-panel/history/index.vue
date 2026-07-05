<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';

import {
  Button,
  Card,
  Descriptions,
  Form,
  FormItem,
  Input,
  Modal,
  Select,
  Space,
  Table,
  Tag,
  message,
} from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import {
  deleteHistories,
  deleteIssueHistories,
  getDetail,
  getHistoryList,
  getIssueHistoryList,
  getPlateList,
  type BestPlanApi,
} from '#/api/best-plan';

defineOptions({ name: 'ControlPanelHistory' });

// 历史记录管理页面 - 已移除所有customRender，使用template slots

const loading = ref(false);
const historyList = ref<BestPlanApi.HistoryRecord[]>([]);
const detailVisible = ref(false);
const detailData = ref<BestPlanApi.DetailRecord | null>(null);
const detailLoading = ref(false);
const selectedRowKeys = ref<number[]>([]);
const issueHistoryList = ref<BestPlanApi.IssueHistoryRecord[]>([]);
const issueLoading = ref(false);
const selectedIssueRowKeys = ref<number[]>([]);
const plateOptions = ref<Array<{ label: string; value: string }>>([]);

const issueSearchParams = reactive({
  plate_code: '',
  issue: '',
  start_date: '',
  end_date: '',
});

const issuePagination = reactive({
  current: 1,
  pageSize: 20,
  total: 0,
});

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

const issueColumns: TableColumnsType = [
  {
    title: '期号',
    dataIndex: 'issue',
    key: 'issue',
    width: 130,
  },
  {
    title: '盘口',
    dataIndex: 'plate_code',
    key: 'plate_code',
    width: 80,
    align: 'center',
  },
  {
    title: '开奖号码',
    dataIndex: 'result',
    key: 'result',
    width: 180,
  },
  {
    title: '开奖时间',
    dataIndex: 'draw_time_text',
    key: 'draw_time_text',
    width: 180,
  },
  {
    title: '投注总额',
    dataIndex: 'total_bet_amount',
    key: 'total_bet_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '派奖总额',
    dataIndex: 'total_prize_amount',
    key: 'total_prize_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '平台利润',
    dataIndex: 'profit_amount',
    key: 'profit_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '结算状态',
    dataIndex: 'is_settled',
    key: 'is_settled',
    width: 100,
    align: 'center',
  },
  {
    title: '状态',
    dataIndex: 'status_text',
    key: 'status_text',
    width: 100,
    align: 'center',
  },
  {
    title: '操作',
    key: 'issue_action',
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
    selectedRowKeys.value = [];
  } catch (error: any) {
    message.error(error?.message || '获取历史列表失败');
  } finally {
    loading.value = false;
  }
}

async function fetchPlateOptions() {
  try {
    const plates = await getPlateList(200);
    plateOptions.value = plates.map((plate) => ({
      label: `${plate.code}盘`,
      value: plate.code,
    }));
  } catch (error) {
    console.error('获取盘口列表失败:', error);
  }
}

async function fetchIssueHistoryList() {
  issueLoading.value = true;
  try {
    const result = await getIssueHistoryList({
      gid: 200,
      page: issuePagination.current,
      limit: issuePagination.pageSize,
      plate_code: issueSearchParams.plate_code || undefined,
      issue: issueSearchParams.issue || undefined,
      start_date: issueSearchParams.start_date || undefined,
      end_date: issueSearchParams.end_date || undefined,
    });
    issueHistoryList.value = result.lists || [];
    selectedIssueRowKeys.value = [];
    issuePagination.total = result.count || 0;
  } catch (error: any) {
    message.error(error?.message || '获取期号历史失败');
  } finally {
    issueLoading.value = false;
  }
}

function handleSelectionChange(keys: (string | number)[]) {
  selectedRowKeys.value = keys.map((key) => Number(key));
}

function handleIssueSelectionChange(keys: (string | number)[]) {
  selectedIssueRowKeys.value = keys.map((key) => Number(key));
}

function handleIssueTableChange(pag: any) {
  issuePagination.current = pag.current;
  issuePagination.pageSize = pag.pageSize;
  fetchIssueHistoryList();
}

function handleIssueSearch() {
  issuePagination.current = 1;
  fetchIssueHistoryList();
}

function handleIssueReset() {
  issueSearchParams.plate_code = '';
  issueSearchParams.issue = '';
  issueSearchParams.start_date = '';
  issueSearchParams.end_date = '';
  issuePagination.current = 1;
  fetchIssueHistoryList();
}

function getHistoryDeleteLogFilters() {
  return {
    gid: 200,
  };
}

function getIssueDeleteLogFilters() {
  return {
    gid: 200,
    plate_code: issueSearchParams.plate_code || undefined,
    issue: issueSearchParams.issue || undefined,
    start_date: issueSearchParams.start_date || undefined,
    end_date: issueSearchParams.end_date || undefined,
  };
}

function confirmDeleteHistories(ids: number[]) {
  if (ids.length === 0) {
    message.warning('请先选择要删除的历史记录');
    return;
  }

  Modal.confirm({
    title: '确认删除历史记录？',
    content: '删除后仅后台历史记录列表不再显示，不会影响前台开奖和用户投注数据。',
    okText: '确认删除',
    okType: 'danger',
    cancelText: '取消',
    async onOk() {
      await deleteHistories(ids, getHistoryDeleteLogFilters());
      message.success('删除成功');
      fetchHistoryList();
    },
  });
}

function confirmDeleteIssueHistories(ids: number[]) {
  if (ids.length === 0) {
    message.warning('请先选择要删除的期号历史');
    return;
  }

  Modal.confirm({
    title: '确认删除期号历史？',
    content: '删除后仅后台期号历史列表不再显示，不会影响前台开奖展示和用户投注数据。',
    okText: '确认删除',
    okType: 'danger',
    cancelText: '取消',
    async onOk() {
      await deleteIssueHistories(ids, getIssueDeleteLogFilters());
      message.success('删除成功');
      fetchIssueHistoryList();
    },
  });
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
  fetchPlateOptions();
  fetchHistoryList();
  fetchIssueHistoryList();
});
</script>

<template>
  <div class="p-4">
    <Card title="历史记录">
      <template #extra>
        <Space>
          <Button
            danger
            :disabled="selectedRowKeys.length === 0"
            @click="confirmDeleteHistories(selectedRowKeys)"
          >
            批量删除
          </Button>
          <Button type="primary" @click="fetchHistoryList" :loading="loading">
            刷新
          </Button>
        </Space>
      </template>

      <Table
        :columns="columns"
        :data-source="historyList"
        :loading="loading"
        :pagination="{ pageSize: 20 }"
        :scroll="{ x: 1200 }"
        row-key="id"
        :row-selection="{
          selectedRowKeys,
          onChange: handleSelectionChange,
        }"
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
            <Space>
              <Button type="link" @click="handleViewDetail(record.id)">
                查看详情
              </Button>
              <Button type="link" danger @click="confirmDeleteHistories([record.id])">
                删除
              </Button>
            </Space>
          </template>
        </template>
      </Table>
    </Card>

    <Card title="期号历史" class="mt-4">
      <Form :model="issueSearchParams" layout="inline" class="mb-4">
        <FormItem label="盘口">
          <Select
            v-model:value="issueSearchParams.plate_code"
            allow-clear
            :options="plateOptions"
            placeholder="全部"
            style="width: 120px"
          />
        </FormItem>
        <FormItem label="期号">
          <Input
            v-model:value="issueSearchParams.issue"
            allow-clear
            placeholder="期号模糊搜索"
            style="width: 180px"
            @press-enter="handleIssueSearch"
          />
        </FormItem>
        <FormItem label="开奖日期">
          <Space>
            <Input
              v-model:value="issueSearchParams.start_date"
              placeholder="开始日期"
              style="width: 130px"
              type="date"
            />
            <span class="text-gray-400">至</span>
            <Input
              v-model:value="issueSearchParams.end_date"
              placeholder="结束日期"
              style="width: 130px"
              type="date"
            />
          </Space>
        </FormItem>
        <FormItem>
          <Space>
            <Button type="primary" :loading="issueLoading" @click="handleIssueSearch">
              搜索
            </Button>
            <Button @click="handleIssueReset">重置</Button>
            <Button
              danger
              :disabled="selectedIssueRowKeys.length === 0"
              @click="confirmDeleteIssueHistories(selectedIssueRowKeys)"
            >
              批量删除
            </Button>
          </Space>
        </FormItem>
      </Form>

      <Table
        :columns="issueColumns"
        :data-source="issueHistoryList"
        :loading="issueLoading"
        :pagination="{
          current: issuePagination.current,
          pageSize: issuePagination.pageSize,
          total: issuePagination.total,
          showSizeChanger: true,
          showTotal: (total) => `共 ${total} 条记录`,
          pageSizeOptions: ['10', '20', '50', '100'],
        }"
        :scroll="{ x: 1120 }"
        row-key="id"
        :row-selection="{
          selectedRowKeys: selectedIssueRowKeys,
          onChange: handleIssueSelectionChange,
        }"
        @change="handleIssueTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'result'">
            <Tag v-if="record.result" color="success">{{ record.result }}</Tag>
            <span v-else class="text-gray-400">未开奖</span>
          </template>
          <template v-else-if="column.key === 'total_bet_amount'">
            ¥{{ Number(record.total_bet_amount).toFixed(2) }}
          </template>
          <template v-else-if="column.key === 'total_prize_amount'">
            ¥{{ Number(record.total_prize_amount).toFixed(2) }}
          </template>
          <template v-else-if="column.key === 'profit_amount'">
            <span :class="Number(record.profit_amount || 0) >= 0 ? 'text-green-600' : 'text-red-600'">
              ¥{{ Number(record.profit_amount || 0).toFixed(2) }}
            </span>
          </template>
          <template v-else-if="column.key === 'is_settled'">
            <Tag :color="record.is_settled === 1 ? 'success' : 'default'">
              {{ record.is_settled === 1 ? '已结算' : '未结算' }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'status_text'">
            <Tag :color="record.status >= 4 || record.is_settled === 1 ? 'success' : 'processing'">
              {{ record.status_text }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'issue_action'">
            <Button type="link" danger @click="confirmDeleteIssueHistories([record.id])">
              删除
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
