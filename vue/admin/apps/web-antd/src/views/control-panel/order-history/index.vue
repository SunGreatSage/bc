<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';

import {
  Button,
  Card,
  Col,
  Form,
  FormItem,
  Input,
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
  getOrderHistory,
  getPlateList,
  type BestPlanApi,
} from '#/api/best-plan';

defineOptions({ name: 'ControlPanelOrderHistory' });

const loading = ref(false);
const tableData = ref<BestPlanApi.OrderHistoryRecord[]>([]);
const plateOptions = ref<Array<{ label: string; value: string }>>([]);

const searchParams = reactive({
  username: '',
  user_type: '' as '' | 'user' | 'agent',
  plate_code: '',
  issue: '',
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
});

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
    title: '状态',
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
    });

    tableData.value = result.lists || [];
    pagination.total = result.count || 0;
    summary.order_count = result.summary?.order_count || 0;
    summary.total_amount = result.summary?.total_amount || '0.00';
    summary.total_prize_amount = result.summary?.total_prize_amount || '0.00';
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
  pagination.current = 1;
  loadData();
}

function handleTableChange(pag: any) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  loadData();
}

function getStatusColor(status: number) {
  if (status === 1) return 'success';
  if (status === 2) return 'default';
  if (status === 3) return 'red';
  return 'processing';
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
        <FormItem>
          <Space>
            <Button type="primary" :loading="loading" @click="handleSearch">
              搜索
            </Button>
            <Button @click="handleReset">重置</Button>
          </Space>
        </FormItem>
      </Form>

      <Row :gutter="24" class="summary-row">
        <Col :span="8">
          <Statistic title="订单数" :value="summary.order_count" suffix="笔" />
        </Col>
        <Col :span="8">
          <Statistic title="投注总额" :value="Number(summary.total_amount)" prefix="¥" :precision="2" />
        </Col>
        <Col :span="8">
          <Statistic title="派奖总额" :value="Number(summary.total_prize_amount)" prefix="¥" :precision="2" />
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
        :scroll="{ x: 1780 }"
        row-key="id"
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
          <template v-else-if="column.key === 'status_text'">
            <Tag :color="getStatusColor(record.status)">
              {{ record.status_text }}
            </Tag>
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
</style>
