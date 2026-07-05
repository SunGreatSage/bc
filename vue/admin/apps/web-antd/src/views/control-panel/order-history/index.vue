<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue';

import {
  Button,
  Card,
  Col,
  Form,
  FormItem,
  Input,
  Modal,
  Row,
  Select,
  SelectOption,
  Space,
  Statistic,
  Table,
  Tag,
  message,
} from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import {
  cancelBetBeforeClose,
  deleteBetRecords,
  getOrderHistory,
  getPlateList,
  type BestPlanApi,
} from '#/api/best-plan';

defineOptions({ name: 'ControlPanelOrderHistory' });

const loading = ref(false);
const tableData = ref<BestPlanApi.OrderHistoryRecord[]>([]);
const plateOptions = ref<Array<{ label: string; value: string }>>([]);
const selectedRowKeys = ref<number[]>([]);

const searchParams = reactive({
  username: '',
  user_type: '' as '' | 'user' | 'agent',
  plate_code: '',
  issue: '',
  status: '' as '' | '0' | '1' | '2' | '3' | '4',
  profit_type: '' as '' | 'profit' | 'loss' | 'flat',
  start_date: '',
  end_date: '',
});

const pagination = reactive({
  current: 1,
  pageSize: 20,
  total: 0,
});

const summary = reactive({
  order_count: 0,
  total_amount: '0.00',
  total_prize_amount: '0.00',
  total_profit_amount: '0.00',
});

const profitSummaryTitle = computed(() => (searchParams.issue ? '本期总利润' : '筛选总利润'));

const columns: TableColumnsType = [
  {
    title: '订单号',
    dataIndex: 'sn',
    key: 'sn',
    width: 210,
  },
  {
    title: '用户名',
    dataIndex: 'username',
    key: 'username',
    width: 130,
  },
  {
    title: '用户类型',
    dataIndex: 'user_type_text',
    key: 'user_type_text',
    width: 100,
    align: 'center',
  },
  {
    title: '盘口',
    dataIndex: 'plate_code',
    key: 'plate_code',
    width: 80,
    align: 'center',
  },
  {
    title: '下单期号',
    dataIndex: 'issue',
    key: 'issue',
    width: 130,
    customRender: ({ record }) => {
      const item = record as BestPlanApi.OrderHistoryRecord;
      return item.display_qishu ? `第${item.display_qishu}期` : item.issue;
    },
  },
  {
    title: '下单玩法',
    dataIndex: 'method_name',
    key: 'method_name',
    width: 120,
  },
  {
    title: '下单号码',
    dataIndex: 'bet_content',
    key: 'bet_content',
    width: 220,
  },
  {
    title: '下单金额',
    dataIndex: 'bet_amount',
    key: 'bet_amount',
    width: 110,
    align: 'right',
  },
  {
    title: '倍数',
    dataIndex: 'bet_multiple',
    key: 'bet_multiple',
    width: 80,
    align: 'right',
  },
  {
    title: '总计金额',
    dataIndex: 'total_amount',
    key: 'total_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '赔率',
    dataIndex: 'odds',
    key: 'odds',
    width: 90,
    align: 'right',
  },
  {
    title: '派奖金额',
    dataIndex: 'prize_amount',
    key: 'prize_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '盈利',
    dataIndex: 'profit_amount',
    key: 'profit_amount',
    width: 120,
    align: 'right',
  },
  {
    title: '中奖状态',
    dataIndex: 'status_text',
    key: 'status_text',
    width: 100,
    align: 'center',
  },
  {
    title: '下单时间',
    dataIndex: 'created_time',
    key: 'created_time',
    width: 180,
  },
  {
    title: '操作',
    key: 'action',
    width: 150,
    fixed: 'right',
    align: 'center',
  },
];

async function loadPlateOptions() {
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

async function loadData() {
  loading.value = true;
  try {
    const result = await getOrderHistory({
      gid: 200,
      page: pagination.current,
      limit: pagination.pageSize,
      username: searchParams.username || undefined,
      user_type: searchParams.user_type || undefined,
      plate_code: searchParams.plate_code || undefined,
      issue: searchParams.issue || undefined,
      status: searchParams.status || undefined,
      profit_type: searchParams.profit_type || undefined,
      start_date: searchParams.start_date || undefined,
      end_date: searchParams.end_date || undefined,
    });

    tableData.value = result.lists || [];
    selectedRowKeys.value = [];
    pagination.total = result.count || 0;
    summary.order_count = result.summary?.order_count || 0;
    summary.total_amount = result.summary?.total_amount || '0.00';
    summary.total_prize_amount = result.summary?.total_prize_amount || '0.00';
    summary.total_profit_amount = result.summary?.total_profit_amount || '0.00';
  } catch (error: any) {
    message.error(error?.message || '获取历史下单失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  pagination.current = 1;
  loadData();
}

function handleReset() {
  searchParams.username = '';
  searchParams.user_type = '';
  searchParams.plate_code = '';
  searchParams.issue = '';
  searchParams.status = '';
  searchParams.profit_type = '';
  searchParams.start_date = '';
  searchParams.end_date = '';
  pagination.current = 1;
  loadData();
}

function handleTableChange(pag: any) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  loadData();
}

function handleSelectionChange(keys: (string | number)[]) {
  selectedRowKeys.value = keys.map((key) => Number(key));
}

function getStatusColor(status: number) {
  if (status === 1) return 'success';
  if (status === 2) return 'default';
  if (status === 3) return 'red';
  return 'processing';
}

function getProfitClass(value: string | number) {
  const amount = Number(value);
  if (amount > 0) return 'profit-positive';
  if (amount < 0) return 'profit-negative';
  return 'muted-text';
}

function getDeleteLogFilters() {
  return {
    gid: 200,
    username: searchParams.username || undefined,
    user_type: searchParams.user_type || undefined,
    plate_code: searchParams.plate_code || undefined,
    issue: searchParams.issue || undefined,
    status: searchParams.status || undefined,
    profit_type: searchParams.profit_type || undefined,
    start_date: searchParams.start_date || undefined,
    end_date: searchParams.end_date || undefined,
  };
}

function confirmDeleteRecords(ids: number[]) {
  if (ids.length === 0) {
    message.warning('请先选择要删除的历史下单数据');
    return;
  }

  Modal.confirm({
    title: '确认删除历史下单数据？',
    content: '删除后仅后台历史列表不再显示，不会撤单、不会退款、不会影响前台用户记录。',
    okText: '确认删除',
    okType: 'danger',
    cancelText: '取消',
    async onOk() {
      await deleteBetRecords(ids, getDeleteLogFilters());
      message.success('删除成功');
      loadData();
    },
  });
}

function confirmCancelBet(record: Record<string, any>) {
  Modal.confirm({
    title: '确认撤单？',
    content: `确认撤销注单 ${record.sn}？撤单后下注金额将退回用户余额。`,
    okText: '确认撤单',
    okType: 'danger',
    cancelText: '取消',
    async onOk() {
      await cancelBetBeforeClose(record.id);
      message.success('撤单成功');
      loadData();
    },
  });
}

onMounted(() => {
  loadPlateOptions();
  loadData();
});
</script>

<template>
  <div class="p-4">
    <Card title="历史下单">
      <Form :model="searchParams" layout="inline" class="search-form">
        <FormItem label="用户名">
          <Input
            v-model:value="searchParams.username"
            allow-clear
            placeholder="用户名/昵称模糊搜索"
            style="width: 220px"
            @press-enter="handleSearch"
          />
        </FormItem>
        <FormItem label="用户类型">
          <Select
            v-model:value="searchParams.user_type"
            allow-clear
            placeholder="全部"
            style="width: 150px"
          >
            <SelectOption value="user">普通用户</SelectOption>
            <SelectOption value="agent">代理用户</SelectOption>
          </Select>
        </FormItem>
        <FormItem label="盘口">
          <Select
            v-model:value="searchParams.plate_code"
            allow-clear
            :options="plateOptions"
            placeholder="全部"
            style="width: 120px"
          />
        </FormItem>
        <FormItem label="期号">
          <Input
            v-model:value="searchParams.issue"
            allow-clear
            placeholder="期号模糊搜索"
            style="width: 180px"
            @press-enter="handleSearch"
          />
        </FormItem>
        <FormItem label="下单日期">
          <Space>
            <Input
              v-model:value="searchParams.start_date"
              placeholder="开始日期"
              style="width: 130px"
              type="date"
            />
            <span class="muted-text">至</span>
            <Input
              v-model:value="searchParams.end_date"
              placeholder="结束日期"
              style="width: 130px"
              type="date"
            />
          </Space>
        </FormItem>
        <FormItem label="中奖状态">
          <Select
            v-model:value="searchParams.status"
            allow-clear
            placeholder="全部"
            style="width: 140px"
          >
            <SelectOption value="0">待开奖</SelectOption>
            <SelectOption value="1">已中奖</SelectOption>
            <SelectOption value="2">未中奖</SelectOption>
            <SelectOption value="3">已撤单</SelectOption>
            <SelectOption value="4">和局</SelectOption>
          </Select>
        </FormItem>
        <FormItem label="利润类型">
          <Select
            v-model:value="searchParams.profit_type"
            allow-clear
            placeholder="全部"
            style="width: 140px"
          >
            <SelectOption value="profit">平台盈利</SelectOption>
            <SelectOption value="loss">平台负盈利</SelectOption>
            <SelectOption value="flat">持平</SelectOption>
          </Select>
        </FormItem>
        <FormItem>
          <Space>
            <Button type="primary" :loading="loading" @click="handleSearch">
              搜索
            </Button>
            <Button @click="handleReset">重置</Button>
            <Button
              danger
              :disabled="selectedRowKeys.length === 0"
              @click="confirmDeleteRecords(selectedRowKeys)"
            >
              批量删除
            </Button>
          </Space>
        </FormItem>
      </Form>

      <Row :gutter="24" class="summary-row">
        <Col :span="6">
          <Statistic title="订单数" :value="summary.order_count" suffix="笔" />
        </Col>
        <Col :span="6">
          <Statistic title="投注总额" :value="Number(summary.total_amount)" prefix="¥" :precision="2" />
        </Col>
        <Col :span="6">
          <Statistic title="派奖总额" :value="Number(summary.total_prize_amount)" prefix="¥" :precision="2" />
        </Col>
        <Col :span="6">
          <Statistic
            :title="profitSummaryTitle"
            :value="Number(summary.total_profit_amount)"
            prefix="¥"
            :suffix="Number(summary.total_profit_amount) < 0 ? '负盈利' : ''"
            :precision="2"
            :value-style="{ color: Number(summary.total_profit_amount) >= 0 ? '#16a34a' : '#dc2626' }"
          />
        </Col>
      </Row>

      <Table
        :columns="columns"
        :data-source="tableData"
        :loading="loading"
        :pagination="{
          current: pagination.current,
          pageSize: pagination.pageSize,
          total: pagination.total,
          showSizeChanger: true,
          showTotal: (total) => `共 ${total} 条记录`,
          pageSizeOptions: ['10', '20', '50', '100'],
        }"
        :scroll="{ x: 1900 }"
        row-key="id"
        :row-selection="{
          selectedRowKeys,
          onChange: handleSelectionChange,
        }"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'username'">
            <div>{{ record.username || '-' }}</div>
            <div v-if="record.nickname" class="muted-text">{{ record.nickname }}</div>
          </template>
          <template v-else-if="column.key === 'user_type_text'">
            <Tag :color="record.is_agent === 1 ? 'blue' : 'green'">
              {{ record.user_type_text }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'bet_content'">
            <span class="bet-content">{{ record.bet_content || '-' }}</span>
          </template>
          <template v-else-if="column.key === 'bet_amount'">
            ¥{{ Number(record.bet_amount).toFixed(2) }}
          </template>
          <template v-else-if="column.key === 'total_amount'">
            <strong>¥{{ Number(record.total_amount).toFixed(2) }}</strong>
          </template>
          <template v-else-if="column.key === 'prize_amount'">
            ¥{{ Number(record.prize_amount).toFixed(2) }}
          </template>
          <template v-else-if="column.key === 'profit_amount'">
            <span :class="getProfitClass(record.profit_amount)">
              ¥{{ Number(record.profit_amount).toFixed(2) }}
            </span>
          </template>
          <template v-else-if="column.key === 'status_text'">
            <Tag :color="getStatusColor(record.status)">
              {{ record.status_text }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'action'">
            <Space>
              <Button
                v-if="record.can_cancel"
                type="link"
                danger
                size="small"
                @click="confirmCancelBet(record)"
              >
                撤单
              </Button>
              <Button
                type="link"
                danger
                size="small"
                @click="confirmDeleteRecords([record.id])"
              >
                删除
              </Button>
            </Space>
          </template>
        </template>
      </Table>
    </Card>
  </div>
</template>

<style scoped>
.search-form {
  margin-bottom: 18px;
}

.summary-row {
  margin-bottom: 18px;
}

.muted-text {
  color: #8c8c8c;
  font-size: 12px;
}

.bet-content {
  white-space: normal;
  word-break: break-all;
}

.profit-positive {
  color: #16a34a;
  font-weight: 600;
}

.profit-negative {
  color: #dc2626;
  font-weight: 600;
}
</style>
