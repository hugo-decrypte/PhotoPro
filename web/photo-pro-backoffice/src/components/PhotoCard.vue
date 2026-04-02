<script setup>
const props = defineProps({
  photo: {
    type: Object,
    required: true,
  },
  selected: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['toggle'])

function onCheckboxChange() {
  emit('toggle', props.photo.id)
}
</script>

<template>
  <article class="photo-card" :class="{ 'photo-card--selected': selected }">
    <label class="photo-card__select">
      <input type="checkbox" :checked="selected" @change="onCheckboxChange" />
    </label>
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
  position: relative;
}

.photo-card--selected {
  border-color: #2563eb;
  box-shadow: 0 0 0 2px #2563eb33;
}

.photo-card__select {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 1;
  background: #fff;
  border-radius: 4px;
  padding: 2px;
  cursor: pointer;
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
