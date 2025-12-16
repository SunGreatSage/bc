import axios from 'axios'

// Create axios instance with base configuration
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '',  // 使用空字符串以便 Vite 代理生效
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Request interceptor for adding authentication token if needed
apiClient.interceptors.request.use(
  (config) => {
    // Add logging in development
    if (import.meta.env.DEV && import.meta.env.VITE_ENABLE_AXIOS_LOGGING === 'true') {
      console.log(`🚀 API Request: ${config.method?.toUpperCase()} ${config.url}`)
      if (config.data) {
        console.log('Request data:', config.data)
      }
    }

    // Add auth token for lottery API calls
    const token = localStorage.getItem('userToken')
    if (token) {
      config.headers.token = token
    }
    return config
  },
  (error) => {
    if (import.meta.env.DEV && import.meta.env.VITE_ENABLE_AXIOS_LOGGING === 'true') {
      console.error('❌ Request error:', error)
    }
    return Promise.reject(error)
  }
)

// Response interceptor for handling common errors
apiClient.interceptors.response.use(
  (response) => {
    // Add logging in development
    if (import.meta.env.DEV && import.meta.env.VITE_ENABLE_AXIOS_LOGGING === 'true') {
      console.log(`✅ API Response: ${response.config.method?.toUpperCase()} ${response.config.url}`)
      console.log('Response data:', response.data)
    }

    // 检查返回消息是否包含"登录超时"
    if (response.data && response.data.msg && response.data.msg.includes('登录超时')) {
      console.warn('登录超时，跳转到登录页面')
      // 清除token
      localStorage.removeItem('userToken')
      // 跳转到登录页
      window.location.href = '/login'
      return Promise.reject(new Error('登录超时'))
    }

    return response.data
  },
  (error) => {
    // Add error logging in development
    if (import.meta.env.DEV && import.meta.env.VITE_ENABLE_AXIOS_LOGGING === 'true') {
      console.error(`❌ API Error: ${error.config?.method?.toUpperCase()} ${error.config?.url}`)
      console.error('Error details:', error)
    }

    // 检查错误响应中是否包含"登录超时"
    if (error.response && error.response.data && error.response.data.msg) {
      if (error.response.data.msg.includes('登录超时')) {
        console.warn('登录超时，跳转到登录页面')
        // 清除token
        localStorage.removeItem('userToken')
        // 跳转到登录页
        window.location.href = '/login'
        return Promise.reject(new Error('登录超时'))
      }
    }

    // Handle common HTTP errors
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // Handle unauthorized
          console.error('Unauthorized access')
          break
        case 403:
          // Handle forbidden
          console.error('Forbidden access')
          break
        case 404:
          // Handle not found
          console.error('Resource not found')
          break
        case 500:
          // Handle server error
          console.error('Server error')
          break
      }
    } else if (error.request) {
      // Handle network error
      console.error('Network error')
    } else {
      // Handle other errors
      console.error('Error:', error.message)
    }

    return Promise.reject(error)
  }
)

// User service methods
export const userService = {
  // Get all users
  getUsers: () => apiClient.get('/users'),

  // Get user by ID
  getUserById: (id) => apiClient.get(`/users/${id}`),

  // Create new user
  createUser: (userData) => apiClient.post('/users', userData),

  // Update user
  updateUser: (id, userData) => apiClient.put(`/users/${id}`, userData),

  // Delete user
  deleteUser: (id) => apiClient.delete(`/users/${id}`),

  // Lottery specific methods
  login: (username, password, terminal = 1) =>
    apiClient.post('/api/lottery_login/login', {
      username,
      password,
      terminal
    }),

  // Get user account information (credit limit, bet amount, balance, time info)
  getUserInfo: (gid = 200, plateCode = '') =>
    apiClient.get('/api/user_info/getUserInfo', {
      params: {
        gid,
        plate_code: plateCode
      }
    }),
}

// Lottery betting service
export const lotteryService = {
  // Place batch bets (批量投注)
  placeBet: (gid, qishu, orders, plateCode = 'A') => {
    // orders 数组格式：
    // [
    //   { pid: "bclass_24927", bet_content: "08", bet_amount: 1 },
    //   { pid: "bclass_24927", bet_content: "26", bet_amount: 2 }
    // ]
    return apiClient.post('/api/lottery_bet/placeBet', {
      gid: parseInt(gid),
      qishu: qishu,
      orders: orders,
      plate_code: plateCode  // 新增：盘口参数
    }, {
      headers: {
        'Content-Type': 'application/json'
      }
    })
  },

  // Get bet list
  getBetList: (params = {}) =>
    apiClient.get('/api/lottery_bet/getBetList', { params }),

  // Get draw results
  getDrawResult: (gid, qishu, plateCode = 'A') =>
    apiClient.get('/api/lottery_bet/getKjResult', {
      params: { gid, qishu, plate_code: plateCode }
    }),

  // Get current period
  getCurrentPeriod: (gid, plateCode = 'A') =>
    apiClient.get('/api/lottery_bet/getCurrentQishu', {
      params: { gid, plate_code: plateCode }
    }),

  // Get play list
  getPlayList: (gid, ftype_class) =>
    apiClient.get('/api/lottery_bet/getPlayList', {
      params: { gid, ftype_class }
    }),

  // Get bet numbers
  getBetNumbers: (play_name, gid, year, plate_code = '') => {
    const params = { play_name, gid, year }
    if (plate_code) {
      params.plate_code = plate_code
    }
    return apiClient.get('/api/lottery_bet/getBetNumbers', { params })
  },

  // Get lottery results (no auth required)
  getResultList: (params = {}) => {
    const { gid = 200, page = 1, limit = 20, plate_code } = params
    const requestParams = { gid, page, limit }
    if (plate_code) {
      requestParams.plate_code = plate_code
    }
    return apiClient.get('/api/lottery_result/getResultList', {
      params: requestParams
    })
  },

  // Get plate list (获取盘口列表)
  getPlateList: (gid = 200) =>
    apiClient.get('/adminapi/best_plan/getPlateList', {
      params: { gid }
    }),
}

// Generic API methods
export const api = {
  // GET request
  get: (url, config) => apiClient.get(url, config),

  // POST request
  post: (url, data, config) => apiClient.post(url, data, config),

  // PUT request
  put: (url, data, config) => apiClient.put(url, data, config),

  // DELETE request
  delete: (url, config) => apiClient.delete(url, config),

  // PATCH request
  patch: (url, data, config) => apiClient.patch(url, data, config),
}

export default apiClient