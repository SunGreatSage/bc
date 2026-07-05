<script setup>
defineProps({
  section: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <section :id="section.id" class="modd">
    <div class="title TitA">
      <em></em>
      <h3>{{ section.title }}</h3>
      <small v-if="section.articleCount !== undefined && section.articleCount !== null">
        {{ section.articleCount }} 篇
      </small>
    </div>

    <p v-if="section.description" class="block-tip">{{ section.description }}</p>

    <ul v-if="section.items.length" class="article-hot article-hot--section">
      <li v-for="article in section.items" :key="article.key">
        <span class="article-hot__dot">·</span>
        <time v-if="article.date && article.date !== '--'" class="article-hot__date">
          {{ article.date }}
        </time>
        <RouterLink
          class="article-hot__link"
          :to="{ name: 'article-detail', params: { articleId: article.routeId } }"
        >
          {{ article.title }}
        </RouterLink>
      </li>
    </ul>

    <div v-else class="block-empty">当前栏目暂无已发布文章。</div>
  </section>
</template>
