<template>
  <div class="about">
    <h1>About Page</h1>
    <p>This is a Vue 3 application with:</p>
    <ul>
      <li>Vue Router for navigation</li>
      <li>Axios for HTTP requests</li>
      <li>Modern Vue 3 Composition API with script setup syntax</li>
    </ul>

    <div class="back-link">
      <router-link to="/">← Back to Home</router-link>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.about {
  padding: 20px;
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