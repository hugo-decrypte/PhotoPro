<script setup>
import { computed } from 'vue'
import { usePhotoDisplay } from '../composables/usePhotoDisplay'

const { thumbSrc, altText, heading } = usePhotoDisplay()

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

const label = computed(() => {
  const name = altText(props.photo)
  return props.selected ? `Désélectionner ${name}` : `Sélectionner ${name}`
})

function toggle() {
  emit('toggle', props.photo.id)
}
</script>

<template>
  <article
    class="photo-card"
    :class="{ 'photo-card--selected': selected }"
    role="button"
    tabindex="0"
    :aria-pressed="selected"
    :aria-label="label"
    @click="toggle"
    @keydown.enter.prevent="toggle"
    @keydown.space.prevent="toggle"
  >
    <img
      class="photo-card__image"
      :src="thumbSrc(photo)"
      :alt="altText(photo)"
      loading="lazy"
      decoding="async"
    />
    <div class="photo-card__meta">
      <strong>{{ heading(photo) }}</strong>
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
  cursor: pointer;
  outline: none;
}

.photo-card:focus-visible {
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #94a8f9;
}

.photo-card--selected {
  border-color: #4a5fc9;
  box-shadow: 0 0 0 2px rgba(148, 168, 249, 0.55);
}

.photo-card__image {
  background: #f3f3f3;
  display: block;
  height: 170px;
  object-fit: cover;
  width: 100%;
  pointer-events: none;
  user-select: none;
}

.photo-card__meta {
  display: grid;
  gap: 0.25rem;
  padding: 0.6rem;
  user-select: none;
}

.photo-card__meta strong,
.photo-card__meta small {
  pointer-events: none;
}
</style>
