<script setup lang="ts">
import {
  Button,
  Card,
  Col,
  Form,
  FormItem,
  Input,
  InputNumber,
  message,
  Modal,
  Popconfirm,
  Row,
  Select,
  SelectOption,
  Space,
  Switch,
  Table,
  Textarea,
  TimePicker,
} from 'ant-design-vue';
import { onMounted, reactive, ref } from 'vue';

import type { PlateItem } from '#/api/plate';
import {
  addPlate,
  changePlateStatus,
  deletePlate,
  editPlate,
  getPlateList,
} from '#/api/plate';

// 表格列定义
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    width: 80,
  },
  {
    title: '盘口代码',
    dataIndex: 'code',
    key: 'code',
    width: 100,
  },
  {
    title: '盘口名称',
    dataIndex: 'name',
    key: 'name',
    width: 150,
  },
  {
    title: '开盘时间',
    dataIndex: 'open_time',
    key: 'open_time',
    width: 100,
  },
  {
    title: '封盘时间',
    dataIndex: 'close_time',
    key: 'close_time',
    width: 100,
  },
  {
    title: '开奖时间',
    dataIndex: 'draw_time',
    key: 'draw_time',
    width: 100,
  },
  {
    title: '提前封盘(分钟)',
    dataIndex: 'close_advance',
    key: 'close_advance',
    width: 130,
  },
  {
    title: '状态',
    key: 'status',
    width: 100,
  },
  {
    title: '排序',
    dataIndex: 'sort',
    key: 'sort',
    width: 80,
  },
  {
    title: '操作',
    key: 'action',
    width: 150,
    fixed: 'right' as 'right',
  },
];

// 搜索参数
const searchParams = reactive({
  code: '',
  name: '',
});

// 分页参数
const pagination = reactive({
  page: 1,
  limit: 10,
});

// 表格数据
const tableData = ref<PlateItem[]>([]);
const total = ref(0);
const loading = ref(false);

// 弹窗状态
const showDialog = ref(false);
const editingItem = ref<PlateItem | null>(null);
const formRef = ref();

// 表单数据
const formData = reactive({
  code: '',
  name: '',
  game_id: 200,
  open_time: '06:00',
  close_time: '09:30',
  draw_time: '09:50',
  close_advance: 5,
  status: 1,
  sort: 0,
  remark: '',
});

// 加载列表数据
const loadData = async () => {
  try {
    loading.value = true;
    const res = await getPlateList({
      page: pagination.page,
      limit: pagination.limit,
      code: searchParams.code || undefined,
      name: searchParams.name || undefined,
    });

    tableData.value = res.lists || [];
    total.value = res.count || 0;
  } catch (error) {
    console.error('加载数据失败:', error);
    message.error('加载数据失败');
  } finally {
    loading.value = false;
  }
};

// 搜索
const handleSearch = () => {
  pagination.page = 1;
  loadData();
};

// 重置
const handleReset = () => {
  searchParams.code = '';
  searchParams.name = '';
  pagination.page = 1;
  loadData();
};

// 表格变化处理(分页、排序、筛选)
const handleTableChange = (pag: any) => {
  pagination.page = pag.current;
  pagination.limit = pag.pageSize;
  loadData();
};

// 新增
const handleAdd = () => {
  editingItem.value = null;
  Object.assign(formData, {
    code: '',
    name: '',
    game_id: 200,
    open_time: '06:00',
    close_time: '09:30',
    draw_time: '09:50',
    close_advance: 5,
    status: 1,
    sort: 0,
    remark: '',
  });
  showDialog.value = true;
};

// 编辑
const handleEdit = (item: PlateItem) => {
  editingItem.value = item;
  Object.assign(formData, {
    code: item.code,
    name: item.name,
    game_id: item.game_id,
    open_time: item.open_time,
    close_time: item.close_time,
    draw_time: item.draw_time,
    close_advance: item.close_advance,
    status: item.status,
    sort: item.sort,
    remark: item.remark || '',
  });
  showDialog.value = true;
};

// 提交
const handleSubmit = async () => {
  try {
    // 表单验证
    await formRef.value?.validate();

    if (editingItem.value) {
      // 编辑
      await editPlate({
        id: editingItem.value.id,
        ...formData,
      });
      message.success('编辑成功');
    } else {
      // 新增
      await addPlate(formData);
      message.success('新增成功');
    }

    showDialog.value = false;
    loadData();
  } catch (error: any) {
    if (error?.errorFields) {
      // 表单验证错误
      return;
    }
    message.error(error.message || '操作失败');
  }
};

// 删除
const handleDelete = async (item: PlateItem) => {
  try {
    await deletePlate(item.id);
    message.success('删除成功');
    loadData();
  } catch (error: any) {
    message.error(error.message || '删除失败');
  }
};

// 状态切换
const handleStatusChange = async (checked: boolean, item: PlateItem) => {
  const newStatus = checked ? 1 : 0;

  try {
    await changePlateStatus(item.id, newStatus);
    item.status = newStatus;
    message.success('状态切换成功');
  } catch (error: any) {
    message.error(error.message || '状态切换失败');
    // 失败时恢复原状态
    item.status = item.status === 1 ? 0 : 1;
  }
};

// 初始化
onMounted(() => {
  loadData();
});
</script>

<template>
  <div class="p-4">
    <Card :bordered="false" class="search-card">
      <!-- 搜索表单 -->
      <Form :model="searchParams" layout="inline" class="search-form">
        <FormItem label="盘口代码">
          <Input
            v-model:value="searchParams.code"
            placeholder="请输入盘口代码"
            allow-clear
            style="width: 200px"
          />
        </FormItem>
        <FormItem label="盘口名称">
          <Input
            v-model:value="searchParams.name"
            placeholder="请输入盘口名称"
            allow-clear
            style="width: 200px"
          />
        </FormItem>
        <FormItem>
          <Space>
            <Button type="primary" @click="handleSearch"> 搜索 </Button>
            <Button @click="handleReset">重置</Button>
            <Button type="primary" @click="handleAdd"> 新增盘口 </Button>
          </Space>
        </FormItem>
      </Form>

      <!-- 数据表格 -->
      <Table
        :columns="columns"
        :data-source="tableData"
        :loading="loading"
        :pagination="{
          current: pagination.page,
          pageSize: pagination.limit,
          total: total,
          showSizeChanger: true,
          showQuickJumper: true,
          showTotal: (total) => `共 ${total} 条记录`,
          pageSizeOptions: ['10', '20', '50', '100'],
        }"
        row-key="id"
        @change="handleTableChange"
      >
        <template #bodyCell="{ column, record }">
          <template v-if="column.key === 'status'">
            <Switch
              :checked="record.status === 1"
              checked-children="启用"
              un-checked-children="禁用"
              @change="(checked) => handleStatusChange(checked, record)"
            />
          </template>
          <template v-else-if="column.key === 'action'">
            <Space>
              <Button size="small" type="link" @click="handleEdit(record)">
                编辑
              </Button>
              <Popconfirm
                title="确定要删除该盘口吗?"
                ok-text="确定"
                cancel-text="取消"
                @confirm="handleDelete(record)"
              >
                <Button danger size="small" type="link">删除</Button>
              </Popconfirm>
            </Space>
          </template>
        </template>
      </Table>
    </Card>

    <!-- 新增/编辑弹窗 -->
    <Modal
      v-model:open="showDialog"
      :title="editingItem ? '编辑盘口' : '新增盘口'"
      :width="800"
      @ok="handleSubmit"
    >
      <Form
        ref="formRef"
        :model="formData"
        :label-col="{ span: 6 }"
        :wrapper-col="{ span: 16 }"
      >
        <Row :gutter="16">
          <Col :span="12">
            <FormItem
              label="盘口代码"
              name="code"
              :rules="[{ required: true, message: '请输入盘口代码' }]"
            >
              <Input v-model:value="formData.code" placeholder="如: A, B, C" />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem
              label="盘口名称"
              name="name"
              :rules="[{ required: true, message: '请输入盘口名称' }]"
            >
              <Input
                v-model:value="formData.name"
                placeholder="如: A盘, B盘"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="开盘时间">
              <TimePicker
                v-model:value="formData.open_time"
                format="HH:mm"
                value-format="HH:mm"
                style="width: 100%"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="封盘时间">
              <TimePicker
                v-model:value="formData.close_time"
                format="HH:mm"
                value-format="HH:mm"
                style="width: 100%"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="开奖时间">
              <TimePicker
                v-model:value="formData.draw_time"
                format="HH:mm"
                value-format="HH:mm"
                style="width: 100%"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="提前封盘">
              <InputNumber
                v-model:value="formData.close_advance"
                :min="0"
                addon-after="分钟"
                style="width: 100%"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="状态">
              <Select v-model:value="formData.status" style="width: 100%">
                <SelectOption :value="1">启用</SelectOption>
                <SelectOption :value="0">禁用</SelectOption>
              </Select>
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="排序">
              <InputNumber
                v-model:value="formData.sort"
                :min="0"
                placeholder="数字越大越靠前"
                style="width: 100%"
              />
            </FormItem>
          </Col>
          <Col :span="24">
            <FormItem
              label="备注"
              :label-col="{ span: 3 }"
              :wrapper-col="{ span: 20 }"
            >
              <Textarea
                v-model:value="formData.remark"
                :rows="3"
                placeholder="选填"
              />
            </FormItem>
          </Col>
        </Row>
      </Form>
    </Modal>
  </div>
</template>

<style scoped lang="scss">
.search-card {
  margin-bottom: 16px;
}

.search-form {
  :deep(.ant-form-item) {
    margin-bottom: 16px;
  }
}

:deep(.ant-table) {
  .ant-table-thead > tr > th {
    font-weight: 600;
  }
}

:deep(.ant-card-body) {
  padding: 24px;
}
</style>
