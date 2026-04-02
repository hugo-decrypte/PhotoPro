<script setup>
import PhotoCard from './PhotoCard.vue'

const props = defineProps({
  photos: {
    type: Array,
    default: () => [],
  },
  selectedIds: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['toggle'])

function isSelected(id) {
  return props.selectedIds.includes(id)
}

function onToggle(id) {
  emit('toggle', id)
}
</script>

<template>
  <section class="photo-grid">
    <PhotoCard
      v-for="photo in photos"
      :key="photo.id"
      :photo="photo"
      :selected="isSelected(photo.id)"
      @toggle="onToggle"
    />
  </section>
</template>

<style scoped>
.photo-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
}
</style>
