<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import PhotoSlideshow from '../components/PhotoSlideshow.vue'
import { fetchGalleriesByPhotographer, fetchGalleryPhotos } from '../services/galleryApi'
import { normalizeApiError } from '../lib/apiError'
import { photoAltText, photoFullSrc, photoThumbSrc } from '../composables/usePhotoDisplay'
import { useAuthStore } from '../stores/auth'

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const route = useRoute()
const authStore = useAuthStore()

const galleryId = computed(() => String(route.params.galleryId || ''))

const queryTitleFallback = computed(() =>
  typeof route.query.title === 'string' && route.query.title ? route.query.title : '',
)

const potPhotos = ref([])
const apiRows = ref([])
const galleryMeta = ref(null)
const loading = ref(true)
const errorMessage = ref('')

const slideshowOpen = ref(false)
const slideshowInitialIndex = ref(0)

const displayTitle = computed(() => {
  const t = galleryMeta.value?.title
  if (t != null && String(t).trim() !== '') {
    return String(t).trim()
  }
  if (queryTitleFallback.value) {
    return queryTitleFallback.value
  }
  return 'Galerie'
})

const displayDescription = computed(() => {
  const d = galleryMeta.value?.description
  if (d == null) {
    return ''
  }
  return String(d).trim()
})

const coverImageSrc = computed(() => {
  const cid = galleryMeta.value?.coverPhotoId ?? galleryMeta.value?.cover_photo_id
  if (cid == null || cid === '') {
    return ''
  }
  const p = potPhotos.value.find((x) => String(x.id) === String(cid))
  if (!p) {
    return ''
  }
  return photoFullSrc(p) || photoThumbSrc(p)
})

const resolvedPhotos = computed(() => {
  const map = new Map(potPhotos.value.map((p) => [String(p.id), p]))
  return apiRows.value
    .map((row) => {
      const id = String(row.photo_id ?? row.id ?? '')
      if (!id) {
        return null
      }
      const pot = map.get(id) || {}
      return {
        id,
        title: photoAltText(pot),
        url: photoFullSrc(pot),
        thumbnailUrl: photoThumbSrc(pot),
      }
    })
    .filter(Boolean)
})

function loadPotPhotos() {
  try {
    const raw = localStorage.getItem(PHOTOS_STORAGE_KEY)
    if (!raw) {
      potPhotos.value = []
      return
    }
    const parsed = JSON.parse(raw)
    if (!Array.isArray(parsed)) {
      potPhotos.value = []
      return
    }
    potPhotos.value = parsed.filter((p) => p && p.id && (p.url || p.thumbnailUrl))
  } catch {
    potPhotos.value = []
  }
}

async function loadGalleryMeta() {
  galleryMeta.value = null
  if (!galleryId.value) {
    return
  }
  authStore.loadFromStorage()
  const pid = authStore.userId
  if (!pid) {
    return
  }
  try {
    const payload = await fetchGalleriesByPhotographer(pid)
    const list = Array.isArray(payload?.data) ? payload.data : []
    galleryMeta.value = list.find((item) => String(item?.id) === galleryId.value) ?? null
  } catch {
    galleryMeta.value = null
  }
}

async function loadGalleryPhotos() {
  if (!galleryId.value) {
    apiRows.value = []
    return
  }
  errorMessage.value = ''
  try {
    const result = await fetchGalleryPhotos(galleryId.value)
    const list = Array.isArray(result?.data) ? result.data : []
    apiRows.value = list
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || err?.message || 'Chargement impossible.'
    apiRows.value = []
  }
}

async function refresh() {
  if (!galleryId.value) {
    loading.value = false
    return
  }
  loading.value = true
  errorMessage.value = ''
  loadPotPhotos()
  await Promise.all([loadGalleryMeta(), loadGalleryPhotos()])
  loading.value = false
}

function openSlideshow(index) {
  slideshowInitialIndex.value = index
  slideshowOpen.value = true
}

function closeSlideshow() {
  slideshowOpen.value = false
}

onMounted(refresh)

watch(galleryId, refresh)
</script>

<template>
  <div class="app-shell">
    <main class="app-shell__main">
      <div class="preview__toolbar">
        <RouterLink class="preview__toolbar-link" :to="{ name: 'gallery' }">← Galeries</RouterLink>
        <RouterLink
          class="preview__toolbar-link"
          :to="{ name: 'gallery-photos', params: { galleryId }, query: { title: displayTitle } }"
        >
          Ajouter des photos
        </RouterLink>
      </div>

      <p v-if="!galleryId" class="preview__error" role="alert">Lien invalide.</p>

      <template v-else>
        <p v-if="loading" class="preview__muted">Chargement…</p>
        <p v-else-if="errorMessage" class="preview__error" role="alert">{{ errorMessage }}</p>

        <template v-else>
          <article class="preview-public" aria-label="Aperçu galerie public">
            <div class="preview-public__hero">
              <img
                v-if="coverImageSrc"
                class="preview-public__hero-img"
                :src="coverImageSrc"
                :alt="displayTitle"
              />
              <div v-else class="preview-public__hero-placeholder" aria-hidden="true" />
            </div>

            <div class="preview-public__intro">
              <h1 class="preview-public__title">{{ displayTitle }}</h1>
              <p v-if="displayDescription" class="preview-public__description">
                {{ displayDescription }}
              </p>
            </div>

            <p v-if="!resolvedPhotos.length" class="preview-public__empty">
              Aucune photo dans cette galerie pour l’instant, ou images introuvables dans ce navigateur.
            </p>

            <div v-else class="preview-public__masonry" role="list">
              <button
                v-for="(photo, i) in resolvedPhotos"
                :key="photo.id"
                type="button"
                class="preview-public__brick"
                role="listitem"
                @click="openSlideshow(i)"
              >
                <img
                  v-if="photoThumbSrc(photo)"
                  class="preview-public__brick-img"
                  :src="photoThumbSrc(photo)"
                  :alt="photo.title"
                  loading="lazy"
                  decoding="async"
                />
                <span v-else class="preview-public__brick-fallback">{{ photo.title }}</span>
              </button>
            </div>
          </article>

          <p v-if="resolvedPhotos.length" class="preview__hint">
            Clique sur une photo pour le diaporama. Flèches ou ← → , Échap pour fermer.
          </p>
        </template>
      </template>

      <PhotoSlideshow
        :show="slideshowOpen"
        :photos="resolvedPhotos"
        :initial-index="slideshowInitialIndex"
        @close="closeSlideshow"
      />
    </main>
  </div>
</template>

<style scoped>
.preview__toolbar {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 0.75rem;
  margin: 0 0 1.25rem;
}

.preview__toolbar-link {
  color: #4a5fc9;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
}

.preview__toolbar-link:hover {
  text-decoration: underline;
  text-underline-offset: 3px;
}

.preview__muted {
  margin: 0 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.preview__error {
  margin: 0 0 1rem;
  color: #c62828;
  font-size: 0.9rem;
}

.preview__hint {
  margin: 1rem 0 0;
  font-size: 0.82rem;
  color: #6b6b78;
  max-width: 40rem;
}

.preview-public {
  max-width: 960px;
  margin: 0 auto;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e8e8ef;
  box-shadow: 0 8px 28px rgba(30, 30, 40, 0.06);
  overflow: hidden;
}

.preview-public__hero {
  position: relative;
  width: 100%;
  max-height: min(42vh, 360px);
  background: #e8e8ef;
}

.preview-public__hero-img {
  display: block;
  width: 100%;
  height: min(42vh, 360px);
  object-fit: cover;
}

.preview-public__hero-placeholder {
  height: min(28vh, 220px);
  min-height: 140px;
  background: linear-gradient(135deg, #e4e8ff 0%, #f0f0f5 100%);
}

.preview-public__intro {
  padding: 1.35rem 1.5rem 1rem;
  text-align: center;
  border-bottom: 1px solid #f0f0f5;
}

.preview-public__title {
  margin: 0 0 0.5rem;
  font-size: clamp(1.35rem, 3vw, 1.85rem);
  font-weight: 700;
  color: #1a1a24;
  line-height: 1.25;
}

.preview-public__description {
  margin: 0;
  font-size: 1rem;
  line-height: 1.55;
  color: #4a4a58;
  white-space: pre-wrap;
}

.preview-public__empty {
  margin: 0;
  padding: 2rem 1.5rem;
  text-align: center;
  color: #6b6b78;
  font-size: 0.95rem;
}

.preview-public__masonry {
  padding: 1rem 1rem 1.25rem;
  column-count: 1;
  column-gap: 0.85rem;
}

@media (min-width: 520px) {
  .preview-public__masonry {
    column-count: 2;
  }
}

@media (min-width: 860px) {
  .preview-public__masonry {
    column-count: 3;
  }
}

.preview-public__brick {
  display: block;
  width: 100%;
  margin: 0 0 0.85rem;
  break-inside: avoid;
  padding: 0;
  border: none;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  background: #f0f0f4;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  transition:
    box-shadow 0.15s ease,
    transform 0.15s ease;
}

.preview-public__brick:hover {
  box-shadow: 0 6px 18px rgba(74, 95, 201, 0.18);
  transform: translateY(-1px);
}

.preview-public__brick-img {
  display: block;
  width: 100%;
  height: auto;
  vertical-align: bottom;
}

.preview-public__brick-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 120px;
  padding: 1rem;
  font-size: 0.8rem;
  color: #6b6b78;
  text-align: center;
}
</style>
