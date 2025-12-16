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
} from 'ant-design-vue';
import { onMounted, reactive, ref } from 'vue';

import type { AccountLogItem, UserItem } from '#/api/plate-user';
import {
  addUser,
  adjustBalance,
  adjustAgentCredit,
  changeUserStatus,
  createAgent,
  deleteUser,
  editUser,
  getAccountLogs,
  getUserList,
} from '#/api/plate-user';

// 表格列定义
const columns = [
  {
    title: 'ID',
    dataIndex: 'id',
    key: 'id',
    width: 80,
  },
  {
    title: '用户名',
    dataIndex: 'username',
    key: 'username',
    width: 120,
  },
  {
    title: '昵称',
    dataIndex: 'nickname',
    key: 'nickname',
    width: 120,
  },
  {
    title: '手机号',
    dataIndex: 'mobile',
    key: 'mobile',
    width: 130,
  },
  {
    title: '余额',
    dataIndex: 'user_money',
    key: 'user_money',
    width: 120,
  },
  {
    title: '状态',
    key: 'status',
    width: 100,
  },
  {
    title: '创建时间',
    key: 'create_time',
    width: 170,
  },
  {
    title: '操作',
    key: 'action',
    width: 300,
    fixed: 'right' as 'right',
  },
];

// 流水表格列定义
const logColumns = [
  {
    title: '流水号',
    dataIndex: 'sn',
    key: 'sn',
    width: 180,
  },
  {
    title: '类型',
    dataIndex: 'change_type_text',
    key: 'change_type_text',
    width: 100,
  },
  {
    title: '变动金额',
    dataIndex: 'change_amount',
    key: 'change_amount',
    width: 120,
  },
  {
    title: '变动前',
    dataIndex: 'balance_before',
    key: 'balance_before',
    width: 120,
  },
  {
    title: '变动后',
    dataIndex: 'balance_after',
    key: 'balance_after',
    width: 120,
  },
  {
    title: '备注',
    dataIndex: 'remark',
    key: 'remark',
  },
  {
    title: '时间',
    dataIndex: 'created_time',
    key: 'created_time',
    width: 170,
  },
];

// 搜索参数
const searchParams = reactive({
  username: '',
  nickname: '',
  mobile: '',
  status: undefined as number | undefined,
  user_type: 'user' as string, // 默认显示普通用户,必须选择一种类型
});

// 分页参数
const pagination = reactive({
  page: 1,
  limit: 10,
});

// 表格数据
const tableData = ref<UserItem[]>([]);
const total = ref(0);
const loading = ref(false);

// 弹窗状态
const showDialog = ref(false);
const editingItem = ref<UserItem | null>(null);
const formRef = ref();

// 表单数据
const formData = reactive({
  username: '',
  password: '',
  nickname: '',
  mobile: '',
  status: 1,
  user_money: 0,
});

// 余额调整弹窗
const showBalanceDialog = ref(false);
const balanceFormRef = ref();
const balanceFormData = reactive({
  id: 0,
  change_amount: 0,
  change_type: 1, // 1=增加, 2=减少
  remark: '',
});

// 开设代理弹窗
const showAgentDialog = ref(false);
const agentFormRef = ref();
const agentFormData = reactive({
  username: '',
  password: '',
  nickname: '',
  mobile: '',
  status: 1,
  credit_limit: 0,
});

// 当前操作的用户类型
const currentUserType = ref<'user' | 'agent'>('user');

// 流水查看弹窗
const showLogsDialog = ref(false);
const logsData = ref<AccountLogItem[]>([]);
const logsTotal = ref(0);
const logsLoading = ref(false);
const logsPagination = reactive({
  page: 1,
  limit: 10,
});
const currentUserId = ref(0);
const currentViewUserType = ref<'user' | 'agent'>('user'); // 当前查看流水的用户类型

// 加载列表数据
const loadData = async () => {
  try {
    loading.value = true;
    const res = await getUserList({
      page: pagination.page,
      limit: pagination.limit,
      username: searchParams.username || undefined,
      nickname: searchParams.nickname || undefined,
      mobile: searchParams.mobile || undefined,
      status: searchParams.status,
      user_type: searchParams.user_type || undefined,
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
  searchParams.username = '';
  searchParams.nickname = '';
  searchParams.mobile = '';
  searchParams.status = undefined;
  // 重置时保持当前用户类型筛选,不清空
  pagination.page = 1;
  loadData();
};

// 表格变化处理
const handleTableChange = (pag: any) => {
  pagination.page = pag.current;
  pagination.limit = pag.pageSize;
  loadData();
};

// 新增
const handleAdd = () => {
  editingItem.value = null;
  Object.assign(formData, {
    username: '',
    password: '',
    nickname: '',
    mobile: '',
    status: 1,
    user_money: 0,
  });
  showDialog.value = true;
};

// 编辑
const handleEdit = (item: UserItem) => {
  editingItem.value = item;
  Object.assign(formData, {
    username: item.username,
    password: '',
    nickname: item.nickname,
    mobile: item.mobile,
    status: item.status,
    user_money: item.user_money,
  });
  showDialog.value = true;
};

// 提交
const handleSubmit = async () => {
  try {
    await formRef.value?.validate();

    if (editingItem.value) {
      // 编辑
      const editData: any = {
        id: editingItem.value.id,
        nickname: formData.nickname,
        mobile: formData.mobile,
        status: formData.status,
      };
      if (formData.password) {
        editData.password = formData.password;
      }
      await editUser(editData);
      message.success('编辑成功');
    } else {
      // 新增
      await addUser(formData);
      message.success('新增成功');
    }

    showDialog.value = false;
    loadData();
  } catch (error: any) {
    if (error?.errorFields) {
      return;
    }
    message.error(error.message || '操作失败');
  }
};

// 删除
const handleDelete = async (item: UserItem) => {
  try {
    await deleteUser(item.id);
    message.success('删除成功');
    loadData();
  } catch (error: any) {
    message.error(error.message || '删除失败');
  }
};

// 状态切换
const handleStatusChange = async (checked: boolean, item: UserItem) => {
  const newStatus = checked ? 1 : 0;

  try {
    await changeUserStatus(item.id, newStatus);
    item.status = newStatus;
    message.success('状态切换成功');
  } catch (error: any) {
    message.error(error.message || '状态切换失败');
    item.status = item.status === 1 ? 0 : 1;
  }
};

// 调整余额
const handleAdjustBalance = (item: any) => {
  currentUserType.value = item.user_type || 'user';
  Object.assign(balanceFormData, {
    id: item.id,
    change_amount: 0,
    change_type: 1,
    remark: '',
  });
  showBalanceDialog.value = true;
};

// 提交余额调整
const handleBalanceSubmit = async () => {
  try {
    await balanceFormRef.value?.validate();

    if (currentUserType.value === 'agent') {
      // 调整代理信用额度
      await adjustAgentCredit(balanceFormData);
    } else {
      // 调整普通用户余额
      await adjustBalance(balanceFormData);
    }

    message.success('调整成功');

    showBalanceDialog.value = false;
    loadData();
  } catch (error: any) {
    if (error?.errorFields) {
      return;
    }
    message.error(error.message || '调整失败');
  }
};

// 查看流水
const handleViewLogs = async (item: any) => {
  currentUserId.value = item.id;
  currentViewUserType.value = item.user_type || 'user';
  logsPagination.page = 1;
  showLogsDialog.value = true;
  await loadLogs();
};

// 开设代理账户
const handleCreateAgent = () => {
  Object.assign(agentFormData, {
    username: '',
    password: '',
    nickname: '',
    mobile: '',
    status: 1,
    credit_limit: 0,
  });
  showAgentDialog.value = true;
};

// 提交开设代理
const handleAgentSubmit = async () => {
  try {
    await agentFormRef.value?.validate();

    await createAgent(agentFormData);
    message.success('开设成功');

    showAgentDialog.value = false;
    loadData();
  } catch (error: any) {
    if (error?.errorFields) {
      return;
    }
    message.error(error.message || '开设失败');
  }
};

// 加载流水数据
const loadLogs = async () => {
  try {
    logsLoading.value = true;

    // 根据用户类型传递不同参数
    const params: any = {
      page: logsPagination.page,
      limit: logsPagination.limit,
    };

    if (currentViewUserType.value === 'agent') {
      // 代理账户查询 admin_id
      params.admin_id = currentUserId.value;
    } else {
      // 普通用户查询 user_id
      params.user_id = currentUserId.value;
    }

    const res = await getAccountLogs(params);

    logsData.value = res.lists || [];
    logsTotal.value = res.count || 0;
  } catch (error) {
    console.error('加载流水失败:', error);
    message.error('加载流水失败');
  } finally {
    logsLoading.value = false;
  }
};

// 流水表格变化
const handleLogsTableChange = (pag: any) => {
  logsPagination.page = pag.current;
  logsPagination.limit = pag.pageSize;
  loadLogs();
};

// 格式化时间
const formatTime = (time: number | string | null) => {
  if (!time) return '-';

  // 如果是字符串格式的时间，直接返回
  if (typeof time === 'string') {
    return time;
  }

  // 如果是时间戳，转换为本地时间
  const date = new Date(time * 1000);
  return date.toLocaleString('zh-CN');
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
        <FormItem label="用户名">
          <Input
            v-model:value="searchParams.username"
            placeholder="请输入用户名"
            allow-clear
            style="width: 200px"
          />
        </FormItem>
        <FormItem label="昵称">
          <Input
            v-model:value="searchParams.nickname"
            placeholder="请输入昵称"
            allow-clear
            style="width: 200px"
          />
        </FormItem>
        <FormItem label="手机号">
          <Input
            v-model:value="searchParams.mobile"
            placeholder="请输入手机号"
            allow-clear
            style="width: 200px"
          />
        </FormItem>
        <FormItem label="状态">
          <Select
            v-model:value="searchParams.status"
            placeholder="全部"
            allow-clear
            style="width: 120px"
          >
            <SelectOption :value="1">正常</SelectOption>
            <SelectOption :value="0">禁用</SelectOption>
          </Select>
        </FormItem>
        <FormItem label="用户类型">
          <Select
            v-model:value="searchParams.user_type"
            placeholder="请选择用户类型"
            style="width: 200px"
            @change="handleSearch"
          >
            <SelectOption value="user">普通用户</SelectOption>
            <SelectOption value="agent">代理账户</SelectOption>
          </Select>
        </FormItem>
        <FormItem>
          <Space>
            <Button type="primary" @click="handleSearch"> 搜索 </Button>
            <Button @click="handleReset">重置</Button>
            <Button type="primary" @click="handleAdd"> 新增用户 </Button>
            <Button type="primary" @click="handleCreateAgent">
              开设代理账户
            </Button>
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
              checked-children="正常"
              un-checked-children="禁用"
              @change="(checked) => handleStatusChange(checked, record)"
            />
          </template>
          <template v-else-if="column.key === 'create_time'">
            {{ formatTime(record.create_time) }}
          </template>
          <template v-else-if="column.key === 'action'">
            <Space>
              <Button size="small" type="link" @click="handleEdit(record)">
                编辑
              </Button>
              <Button
                size="small"
                type="link"
                @click="handleAdjustBalance(record)"
              >
                调整余额
              </Button>
              <Button size="small" type="link" @click="handleViewLogs(record)">
                查看流水
              </Button>
              <Popconfirm
                title="确定要删除该用户吗?"
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
      :title="editingItem ? '编辑用户' : '新增用户'"
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
              label="用户名"
              name="username"
              :rules="[{ required: true, message: '请输入用户名' }]"
            >
              <Input
                v-model:value="formData.username"
                placeholder="请输入用户名"
                :disabled="!!editingItem"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem
              label="密码"
              name="password"
              :rules="
                editingItem
                  ? []
                  : [{ required: true, message: '请输入密码' }]
              "
            >
              <Input
                v-model:value="formData.password"
                type="password"
                :placeholder="editingItem ? '留空则不修改' : '请输入密码'"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="昵称">
              <Input v-model:value="formData.nickname" placeholder="请输入昵称" />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="手机号">
              <Input v-model:value="formData.mobile" placeholder="请输入手机号" />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="状态">
              <Select v-model:value="formData.status" style="width: 100%">
                <SelectOption :value="1">正常</SelectOption>
                <SelectOption :value="0">禁用</SelectOption>
              </Select>
            </FormItem>
          </Col>
          <Col v-if="!editingItem" :span="12">
            <FormItem label="初始余额">
              <InputNumber
                v-model:value="formData.user_money"
                :min="0"
                :precision="2"
                addon-after="元"
                style="width: 100%"
              />
            </FormItem>
          </Col>
        </Row>
      </Form>
    </Modal>

    <!-- 调整余额弹窗 -->
    <Modal
      v-model:open="showBalanceDialog"
      title="调整余额"
      :width="500"
      @ok="handleBalanceSubmit"
    >
      <Form
        ref="balanceFormRef"
        :model="balanceFormData"
        :label-col="{ span: 6 }"
        :wrapper-col="{ span: 16 }"
      >
        <FormItem label="调整类型">
          <Select
            v-model:value="balanceFormData.change_type"
            style="width: 100%"
          >
            <SelectOption :value="1">增加</SelectOption>
            <SelectOption :value="2">减少</SelectOption>
          </Select>
        </FormItem>
        <FormItem
          label="调整金额"
          name="change_amount"
          :rules="[
            { required: true, message: '请输入调整金额' },
            { type: 'number', min: 0.01, message: '金额必须大于0' },
          ]"
        >
          <InputNumber
            v-model:value="balanceFormData.change_amount"
            :min="0.01"
            :precision="2"
            addon-after="元"
            style="width: 100%"
          />
        </FormItem>
        <FormItem label="备注">
          <Textarea
            v-model:value="balanceFormData.remark"
            :rows="3"
            placeholder="请输入备注"
          />
        </FormItem>
      </Form>
    </Modal>

    <!-- 查看流水弹窗 -->
    <Modal
      v-model:open="showLogsDialog"
      title="账户流水"
      :width="1200"
      :footer="null"
    >
      <Table
        :columns="logColumns"
        :data-source="logsData"
        :loading="logsLoading"
        :pagination="{
          current: logsPagination.page,
          pageSize: logsPagination.limit,
          total: logsTotal,
          showSizeChanger: true,
          showQuickJumper: true,
          showTotal: (total) => `共 ${total} 条记录`,
        }"
        row-key="id"
        @change="handleLogsTableChange"
      />
    </Modal>

    <!-- 开设代理账户弹窗 -->
    <Modal
      v-model:open="showAgentDialog"
      title="开设代理账户"
      :width="800"
      @ok="handleAgentSubmit"
    >
      <Form
        ref="agentFormRef"
        :model="agentFormData"
        :label-col="{ span: 6 }"
        :wrapper-col="{ span: 16 }"
      >
        <Row :gutter="16">
          <Col :span="12">
            <FormItem
              label="账号"
              name="username"
              :rules="[{ required: true, message: '请输入账号' }]"
            >
              <Input
                v-model:value="agentFormData.username"
                placeholder="请输入账号"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem
              label="密码"
              name="password"
              :rules="[{ required: true, message: '请输入密码' }]"
            >
              <Input
                v-model:value="agentFormData.password"
                type="password"
                placeholder="请输入密码"
              />
            </FormItem>
          </Col>
        </Row>
        <Row :gutter="16">
          <Col :span="12">
            <FormItem label="昵称" name="nickname">
              <Input
                v-model:value="agentFormData.nickname"
                placeholder="请输入昵称"
              />
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem label="手机号" name="mobile">
              <Input
                v-model:value="agentFormData.mobile"
                placeholder="请输入手机号"
              />
            </FormItem>
          </Col>
        </Row>
        <Row :gutter="16">
          <Col :span="12">
            <FormItem label="状态" name="status">
              <Select v-model:value="agentFormData.status">
                <SelectOption :value="1">正常</SelectOption>
                <SelectOption :value="0">禁用</SelectOption>
              </Select>
            </FormItem>
          </Col>
          <Col :span="12">
            <FormItem
              label="信用额度"
              name="credit_limit"
              :rules="[{ required: true, message: '请输入信用额度' }]"
            >
              <InputNumber
                v-model:value="agentFormData.credit_limit"
                :min="0"
                :precision="2"
                placeholder="请输入信用额度"
                style="width: 100%"
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
