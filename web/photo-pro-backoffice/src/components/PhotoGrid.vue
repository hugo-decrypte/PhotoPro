<script setup>
import PhotoCard from './PhotoCard.vue'

defineProps({
  photos: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <section class="photo-grid">
    <PhotoCard v-for="photo in photos" :key="photo.id" :photo="photo" />
  </section>
</template>

<style scoped>
.photo-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
}
</style>
