<script setup>
import { computed, nextTick, onUnmounted, ref, watch } from 'vue'
import { usePhotoDisplay } from '../composables/usePhotoDisplay'

const { fullSrc, altText } = usePhotoDisplay()

const props = defineProps({
  show: { type: Boolean, default: false },
  /** { id, url, thumbnailUrl?, title? } */
  photos: { type: Array, default: () => [] },
  initialIndex: { type: Number, default: 0 },
})

const emit = defineEmits(['close'])

const index = ref(0)
const rootRef = ref(null)

watch(
  () => props.show,
  async (visible) => {
    if (visible) {
      const len = props.photos.length
      index.value = len ? Math.min(Math.max(0, props.initialIndex), len - 1) : 0
      document.body.style.overflow = 'hidden'
      await nextTick()
      rootRef.value?.focus({ preventScroll: true })
    } else {
      document.body.style.overflow = ''
    }
  },
)

onUnmounted(() => {
  document.body.style.overflow = ''
})

const current = computed(() => props.photos[index.value] ?? null)
const hasPrev = computed(() => index.value > 0)
const hasNext = computed(() => index.value < props.photos.length - 1)
const positionLabel = computed(() =>
  props.photos.length ? `${index.value + 1} / ${props.photos.length}` : '',
)

function close() {
  emit('close')
}

function prev() {
  if (hasPrev.value) {
    index.value -= 1
  }
}

function next() {
  if (hasNext.value) {
    index.value += 1
  }
}

function onKeydown(e) {
  if (e.key === 'Escape') {
    e.preventDefault()
    close()
    return
  }
  if (e.key === 'ArrowLeft') {
    e.preventDefault()
    prev()
    return
  }
  if (e.key === 'ArrowRight') {
    e.preventDefault()
    next()
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show && photos.length"
      ref="rootRef"
      class="slideshow"
      role="dialog"
      aria-modal="true"
      aria-label="Diaporama"
      tabindex="-1"
      @keydown="onKeydown"
    >
      <button type="button" class="slideshow__backdrop" aria-label="Fermer" @click="close" />
      <div class="slideshow__panel">
        <header class="slideshow__top">
          <p v-if="current" class="slideshow__title">{{ current.title || altText(current) }}</p>
          <p class="slideshow__counter">{{ positionLabel }}</p>
          <button type="button" class="slideshow__close" aria-label="Fermer" @click="close">×</button>
        </header>

        <div class="slideshow__stage">
          <button
            type="button"
            class="slideshow__nav slideshow__nav--prev"
            :disabled="!hasPrev"
            aria-label="Photo précédente"
            @click="prev"
          >
            ‹
          </button>

          <div class="slideshow__frame">
            <img
              v-if="current && fullSrc(current)"
              class="slideshow__img"
              :src="fullSrc(current)"
              :alt="altText(current)"
              loading="eager"
              decoding="async"
            />
            <div v-else class="slideshow__placeholder">
              <p>Image indisponible</p>
              <p class="slideshow__placeholder-hint">La vignette n’est pas trouvée dans ce navigateur (pot commun).</p>
            </div>
          </div>

          <button
            type="button"
            class="slideshow__nav slideshow__nav--next"
            :disabled="!hasNext"
            aria-label="Photo suivante"
            @click="next"
          >
            ›
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.slideshow {
  position: fixed;
  inset: 0;
  z-index: 4000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  outline: none;
}

.slideshow__backdrop {
  position: absolute;
  inset: 0;
  border: none;
  margin: 0;
  padding: 0;
  background: rgba(15, 15, 22, 0.88);
  cursor: pointer;
}

.slideshow__panel {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  max-width: min(96vw, 1100px);
  max-height: min(92vh, 900px);
  width: 100%;
  border-radius: 12px;
  overflow: hidden;
  background: #1a1a24;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
}

.slideshow__top {
  display: grid;
  grid-template-columns: 1fr auto auto;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.85rem;
  background: #252530;
  color: #f0f0f5;
}

.slideshow__title {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 600;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.slideshow__counter {
  margin: 0;
  font-size: 0.8rem;
  color: #b8b8c8;
  font-variant-numeric: tabular-nums;
}

.slideshow__close {
  width: 2rem;
  height: 2rem;
  border: none;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-size: 1.5rem;
  line-height: 1;
  cursor: pointer;
}

.slideshow__close:hover {
  background: rgba(255, 255, 255, 0.18);
}

.slideshow__stage {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.5rem;
  min-height: 0;
  flex: 1;
}

.slideshow__nav {
  flex-shrink: 0;
  width: 2.75rem;
  height: 2.75rem;
  border: none;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  font-size: 1.75rem;
  line-height: 1;
  cursor: pointer;
}

.slideshow__nav:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.22);
}

.slideshow__nav:disabled {
  opacity: 0.25;
  cursor: not-allowed;
}

.slideshow__frame {
  flex: 1;
  min-height: 0;
  min-width: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0e0e14;
  border-radius: 8px;
  max-height: min(78vh, 760px);
}

.slideshow__img {
  max-width: 100%;
  max-height: min(78vh, 760px);
  width: auto;
  height: auto;
  object-fit: contain;
  display: block;
}

.slideshow__placeholder {
  padding: 2rem 1.5rem;
  text-align: center;
  color: #9a9aaa;
  font-size: 0.95rem;
}

.slideshow__placeholder p {
  margin: 0 0 0.5rem;
}

.slideshow__placeholder-hint {
  font-size: 0.8rem;
  color: #6b6b78;
  max-width: 22rem;
  margin-left: auto;
  margin-right: auto;
}

@media (max-width: 560px) {
  .slideshow__stage {
    flex-wrap: wrap;
    justify-content: center;
  }

  .slideshow__nav--prev {
    order: 2;
  }

  .slideshow__frame {
    order: 1;
    width: 100%;
    max-height: 55vh;
  }

  .slideshow__nav--next {
    order: 3;
  }
}
</style>
