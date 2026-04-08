<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from '../components/AppHeader.vue'
import PhotoSlideshow from '../components/PhotoSlideshow.vue'
import { fetchGalleryPhotos } from '../services/galleryApi'
import { normalizeApiError } from '../lib/apiError'

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const route = useRoute()

const galleryTitle = computed(() =>
  typeof route.query.title === 'string' && route.query.title ? route.query.title : 'Galerie',
)

const galleryId = computed(() => String(route.params.galleryId || ''))

const potPhotos = ref([])
const apiRows = ref([])
const loading = ref(true)
const errorMessage = ref('')

const slideshowOpen = ref(false)
const slideshowInitialIndex = ref(0)

const resolvedPhotos = computed(() => {
  const map = new Map(potPhotos.value.map((p) => [String(p.id), p]))
  return apiRows.value
    .map((row) => {
      const id = String(row.photo_id ?? row.id ?? '')
      if (!id) {
        return null
      }
      const pot = map.get(id)
      const url = String(pot?.url || pot?.thumbnailUrl || '')
      const thumbnailUrl = String(pot?.thumbnailUrl || pot?.url || url)
      return {
        id,
        title: String(pot?.title || pot?.originalName || 'Photo'),
        url: url || thumbnailUrl,
        thumbnailUrl: thumbnailUrl || url,
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

async function loadGalleryPhotos() {
  if (!galleryId.value) {
    apiRows.value = []
    loading.value = false
    return
  }
  loading.value = true
  errorMessage.value = ''
  try {
    const result = await fetchGalleryPhotos(galleryId.value)
    const list = Array.isArray(result?.data) ? result.data : []
    apiRows.value = list
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || err?.message || 'Chargement impossible.'
    apiRows.value = []
  } finally {
    loading.value = false
  }
}

function openSlideshow(index) {
  slideshowInitialIndex.value = index
  slideshowOpen.value = true
}

function closeSlideshow() {
  slideshowOpen.value = false
}

onMounted(() => {
  loadPotPhotos()
  loadGalleryPhotos()
})

watch(galleryId, () => {
  loadPotPhotos()
  loadGalleryPhotos()
})
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Aperçu & diaporama</h1>
      <p class="preview__subtitle">{{ galleryTitle }}</p>

      <p v-if="!galleryId" class="preview__error" role="alert">Lien invalide.</p>

      <template v-else>
        <p v-if="loading" class="preview__muted">Chargement des photos…</p>
        <p v-else-if="errorMessage" class="preview__error" role="alert">{{ errorMessage }}</p>
        <p v-else-if="!resolvedPhotos.length" class="preview__muted">
          Cette galerie ne contient encore aucune photo, ou aucune image n’est disponible dans ce navigateur.
        </p>

        <template v-else>
          <p class="preview__hint">
            Clique sur une vignette pour l’agrandir. Utilise les flèches ou ← → du clavier, Échap pour fermer.
          </p>
          <ul class="preview__grid">
            <li v-for="(photo, i) in resolvedPhotos" :key="photo.id" class="preview__cell">
              <button type="button" class="preview__thumb" @click="openSlideshow(i)">
                <img v-if="photo.thumbnailUrl" :src="photo.thumbnailUrl" :alt="photo.title" />
                <span v-else class="preview__thumb-fallback">{{ photo.title }}</span>
              </button>
            </li>
          </ul>
        </template>

        <nav class="preview__nav">
          <RouterLink class="preview__back" :to="{ name: 'gallery' }">← Galeries</RouterLink>
          <RouterLink
            class="preview__back"
            :to="{ name: 'gallery-photos', params: { galleryId }, query: { title: galleryTitle } }"
          >
            Ajouter des photos
          </RouterLink>
        </nav>
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
.preview__subtitle {
  margin: -0.5rem 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
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
  margin: 0 0 0.75rem;
  font-size: 0.82rem;
  color: #6b6b78;
  max-width: 40rem;
}

.preview__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 0.65rem;
  margin: 0 0 1.25rem;
  padding: 0;
  list-style: none;
}

.preview__thumb {
  display: block;
  width: 100%;
  aspect-ratio: 1;
  padding: 0;
  border: 2px solid #e4e4ee;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  background: #f3f3f6;
  transition:
    border-color 0.15s ease,
    box-shadow 0.15s ease;
}

.preview__thumb:hover {
  border-color: #4a5fc9;
  box-shadow: 0 4px 14px rgba(74, 95, 201, 0.2);
}

.preview__thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.preview__thumb-fallback {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 0.5rem;
  font-size: 0.75rem;
  color: #6b6b78;
  text-align: center;
}

.preview__nav {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.preview__back {
  color: #4a5fc9;
  font-size: 0.9rem;
  font-weight: 500;
  text-decoration: none;
}

.preview__back:hover {
  text-decoration: underline;
  text-underline-offset: 3px;
}
</style>
