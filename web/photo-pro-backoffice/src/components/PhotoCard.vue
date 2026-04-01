<script setup>
defineProps({
  photo: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <article class="photo-card">
    <img
      class="photo-card__image"
      :src="photo.thumbnailUrl || photo.url"
      :alt="photo.title || photo.originalName || 'Photo'"
    />
    <div class="photo-card__meta">
      <strong>{{ photo.title || photo.originalName || 'Sans titre' }}</strong>
      <small>{{ photo.mimeType || 'image/jpeg' }}</small>
    </div>
  </article>
</template>

<style scoped>
.photo-card {
  border: 1px solid #ddd;
  border-radius: 10px;
  overflow: hidden;
}

.photo-card__image {
  background: #f3f3f3;
  display: block;
  height: 170px;
  object-fit: cover;
  width: 100%;
}

.photo-card__meta {
  display: grid;
  gap: 0.25rem;
  padding: 0.6rem;
}
</style>
