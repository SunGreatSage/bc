<template>
  <div class="users">
    <h1>Users Page</h1>

    <div v-if="loading" class="loading">
      Loading users...
    </div>

    <div v-else-if="error" class="error">
      Error loading users: {{ error }}
      <button @click="fetchUsers" class="retry-btn">Retry</button>
    </div>

    <div v-else class="user-list">
      <div v-for="user in users" :key="user.id" class="user-card">
        <h3>{{ user.name }}</h3>
        <p>{{ user.email }}</p>
        <p>{{ user.phone }}</p>
      </div>
    </div>

    <div class="back-link">
      <router-link to="/">← Back to Home</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { userService } from '../services/api'

const users = ref([])
const loading = ref(true)
const error = ref('')

const fetchUsers = async () => {
  try {
    loading.value = true
    error.value = ''
    users.value = await userService.getUsers()
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.users {
  padding: 20px;
}

.loading {
  text-align: center;
  padding: 20px;
  font-style: italic;
}

.error {
  color: #ff4444;
  padding: 20px;
  text-align: center;
}

.retry-btn {
  margin-left: 10px;
  padding: 5px 10px;
  background-color: #ff4444;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.user-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin: 20px 0;
}

.user-card {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #f9f9f9;
}

.user-card h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.user-card p {
  margin: 5px 0;
  color: #666;
}

.back-link {
  margin-top: 30px;
}

.back-link a {
  color: #42b883;
  text-decoration: none;
}

.back-link a:hover {
  text-decoration: underline;
}
</style>