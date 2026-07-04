<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';

import {
  Button,
  Card,
  Descriptions,
  DescriptionsItem,
  Drawer,
  Form,
  FormItem,
  Input,
  Select,
  SelectOption,
  Space,
  Table,
  Tag,
  message,
} from 'ant-design-vue';
import type { TableColumnsType } from 'ant-design-vue';

import { getPlateList } from '#/api/best-plan';
import {
  getRiskLogList,
  getRiskLogOptions,
  type RiskLogApi,
} from '#/api/risk-log';

defineOptions({ name: 'ControlPanelRiskLog' });

const loading = ref(false);
const detailVisible = ref(false);
const currentRecord = ref<RiskLogApi.RiskLogRecord | null>(null);
const tableData = ref<RiskLogApi.RiskLogRecord[]>([]);
const riskOptions = ref<RiskLogApi.RiskOption[]>([
  { label: '全部风控日志', value: 'all' },
  { label: '负盈利方案', value: 'negative' },
  { label: '通杀/近似通杀方案', value: 'wipeout' },
]);
const plateOptions = ref<Array<{ label: string; value: string }>>([]);

const searchParams = reactive({
  risk_type: 'all' as RiskLogApi.RiskType,
  issue: '',
  plate_code: '',
  admin_name: '',
});

const pagination = reactive({
  current: 1,
  pageSize: 20,
  total: 0,
});

const columns: TableColumnsType = [
  {
    title: '风控标记',
    key: 'risk_tag',
    dataIndex: 'risk_tag',
    width: 130,
    align: 'center',
  },
  {
    title: '风控摘要',
    key: 'risk_summary',
    dataIndex: 'risk_summary',
    width: 260,
  },
  {
    title: '期号',
    key: 'issue',
    width: 150,
  },
  {
    title: '盘口',
    key: 'plate_code',
    width: 80,
    align: 'center',
  },
  {
    title: '开奖号码',
    key: 'numbers',
    width: 170,
  },
  {
    title: '管理员',
    key: 'admin_name',
    dataIndex: 'admin_name',
    width: 130,
  },
  {
    title: '来源/状态',
    key: 'source_status',
    width: 150,
  },
  {
    title: '操作时间',
    key: 'create_time',
    dataIndex: 'create_time',
    width: 170,
  },
  {
    title: '操作',
    key: 'action',
    width: 100,
    align: 'center',
    fixed: 'right',
  },
];

function getRiskColor(record: any) {
  return record.risk_level === 'danger' ? 'error' : 'default';
}

function formatSource(value?: string) {
  if (value === 'custom_drawing') {
    return '自定义开奖';
  }
  if (value === 'plan_selection') {
    return '方案选择';
  }
  return value || '-';
}

function formatPlanStatus(value?: string) {
  if (value === 'locked') {
    return '已锁定';
  }
  if (value === 'settled') {
    return '已结算';
  }
  if (value === 'preview') {
    return '预估';
  }
  return value || '-';
}

async function loadRiskOptions() {
  try {
    const options = await getRiskLogOptions();
    if (Array.isArray(options) && options.length > 0) {
      riskOptions.value = options;
    }
  } catch (error) {
    console.error('获取风控日志筛选项失败:', error);
  }
}

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
    const result = await getRiskLogList({
      page_no: pagination.current,
      page_size: pagination.pageSize,
      risk_type: searchParams.risk_type || 'all',
      issue: searchParams.issue || undefined,
      plate_code: searchParams.plate_code || undefined,
      admin_name: searchParams.admin_name || undefined,
    });

    tableData.value = result.lists || [];
    pagination.total = result.count || 0;
  } catch (error: any) {
    message.error(error?.message || '获取风控日志失败');
  } finally {
    loading.value = false;
  }
}

function handleSearch() {
  pagination.current = 1;
  loadData();
}

function handleReset() {
  searchParams.risk_type = 'all';
  searchParams.issue = '';
  searchParams.plate_code = '';
  searchParams.admin_name = '';
  pagination.current = 1;
  loadData();
}

function handleTableChange(pag: any) {
  pagination.current = pag.current;
  pagination.pageSize = pag.pageSize;
  loadData();
}

function openDetail(record: any) {
  currentRecord.value = record;
  detailVisible.value = true;
}

onMounted(() => {
  loadRiskOptions();
  loadPlateOptions();
  loadData();
});
</script>

<template>
  <div class="p-4">
    <Card title="风控操作日志">
      <Form :model="searchParams" layout="inline" class="search-form">
        <FormItem label="风控类型">
          <Select v-model:value="searchParams.risk_type" style="width: 180px;">
            <SelectOption
              v-for="option in riskOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </SelectOption>
          </Select>
        </FormItem>
        <FormItem label="期号">
          <Input
            v-model:value="searchParams.issue"
            allow-clear
            placeholder="输入期号"
            style="width: 170px;"
          />
        </FormItem>
        <FormItem label="盘口">
          <Select
            v-model:value="searchParams.plate_code"
            allow-clear
            placeholder="全部"
            style="width: 120px;"
          >
            <SelectOption
              v-for="plate in plateOptions"
              :key="plate.value"
              :value="plate.value"
            >
              {{ plate.label }}
            </SelectOption>
          </Select>
        </FormItem>
        <FormItem label="管理员">
          <Input
            v-model:value="searchParams.admin_name"
            allow-clear
            placeholder="管理员名称"
            style="width: 150px;"
          />
        </FormItem>
        <FormItem>
          <Space>
            <Button type="primary" :loading="loading" @click="handleSearch">查询</Button>
            <Button @click="handleReset">重置</Button>
          </Space>
        </FormItem>
      </Form>

      <Table
        :columns="columns"
        :data-source="tableData"
        :loading="loading"
        :pagination="{
          current: pagination.current,
          pageSize: pagination.pageSize,
          total: pagination.total,
          showSizeChanger: true,
          showTotal: (total: number) => `共 ${total} 条`,
        }"
        :scroll="{ x: 1380 }"
        row-key="id"
        class="mt-4"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'risk_tag'">
            <Tag :color="getRiskColor(record)">
              {{ record.risk_tag || record.action }}
            </Tag>
          </template>
          <template v-else-if="column.key === 'issue'">
            {{ record.risk_detail?.issue || '-' }}
          </template>
          <template v-else-if="column.key === 'plate_code'">
            {{ record.risk_detail?.plate_code || '-' }}
          </template>
          <template v-else-if="column.key === 'numbers'">
            {{ record.risk_detail?.numbers || '-' }}
          </template>
          <template v-else-if="column.key === 'source_status'">
            <div>{{ formatSource(record.risk_detail?.selection_source) }}</div>
            <div class="text-xs text-gray-500">{{ formatPlanStatus(record.risk_detail?.plan_status) }}</div>
          </template>
          <template v-else-if="column.key === 'action'">
            <Button size="small" @click="() => openDetail(record)">详情</Button>
          </template>
        </template>
      </Table>
    </Card>

    <Drawer
      v-model:open="detailVisible"
      title="风控日志详情"
      width="560"
    >
      <Descriptions v-if="currentRecord" bordered :column="1" size="small">
        <DescriptionsItem label="风控标记">
          <Tag :color="getRiskColor(currentRecord)">
            {{ currentRecord.risk_tag }}
          </Tag>
        </DescriptionsItem>
        <DescriptionsItem label="摘要">
          {{ currentRecord.risk_summary }}
        </DescriptionsItem>
        <DescriptionsItem label="管理员">
          {{ currentRecord.admin_name || currentRecord.account || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="请求IP">
          {{ currentRecord.ip }}
        </DescriptionsItem>
        <DescriptionsItem label="操作时间">
          {{ currentRecord.create_time }}
        </DescriptionsItem>
        <DescriptionsItem label="期号">
          {{ currentRecord.risk_detail?.issue || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="盘口">
          {{ currentRecord.risk_detail?.plate_code || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="开奖号码">
          {{ currentRecord.risk_detail?.numbers || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="选择来源">
          {{ formatSource(currentRecord.risk_detail?.selection_source) }}
        </DescriptionsItem>
        <DescriptionsItem label="计划状态">
          {{ formatPlanStatus(currentRecord.risk_detail?.plan_status) }}
        </DescriptionsItem>
        <DescriptionsItem label="预计利润">
          {{ currentRecord.risk_detail?.expected_profit || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="实际利润率">
          {{ currentRecord.risk_detail?.expected_profit_rate || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="预计赔付">
          {{ currentRecord.risk_detail?.expected_payout || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="本期总投注">
          {{ currentRecord.risk_detail?.total_bet_amount || '-' }}
        </DescriptionsItem>
        <DescriptionsItem label="本期订单数">
          {{ currentRecord.risk_detail?.total_orders ?? '-' }}
        </DescriptionsItem>
      </Descriptions>
    </Drawer>
  </div>
</template>

<style scoped>
.search-form {
  row-gap: 12px;
}
</style>
