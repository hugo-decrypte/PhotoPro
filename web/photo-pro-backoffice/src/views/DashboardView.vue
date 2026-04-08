<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '../components/AppHeader.vue'
import { REQUIRE_AUTH } from '../config/auth'
import { fetchGalleriesByPhotographer } from '../services/galleryApi'
import { useAuthStore } from '../stores/auth'

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const authStore = useAuthStore()
const router = useRouter()

const loading = ref(false)
const error = ref('')
const galleries = ref([])
const photoCountLocal = ref(0)

function countPhotosFromStorage() {
  try {
    const raw = localStorage.getItem(PHOTOS_STORAGE_KEY)
    if (!raw) {
      return 0
    }
    const parsed = JSON.parse(raw)
    return Array.isArray(parsed) ? parsed.filter((p) => p && p.id).length : 0
  } catch {
    return 0
  }
}

function mapGallery(item) {
  return {
    id: String(item?.id || ''),
    title: String(item?.title || 'Sans titre'),
    isPublished: Boolean(item?.status),
    photosCount: Number.isFinite(item?.photosCount) ? item.photosCount : null,
  }
}

const recentGalleries = computed(() => galleries.value.slice(0, 5))
const galleryCount = computed(() => galleries.value.length)

async function load() {
  authStore.loadFromStorage()
  photoCountLocal.value = countPhotosFromStorage()

  const photographerId = authStore.userId
  if (!photographerId) {
    if (REQUIRE_AUTH) {
      await router.push({ name: 'login' })
      return
    }
    galleries.value = []
    loading.value = false
    return
  }

  loading.value = true
  error.value = ''
  try {
    const payload = await fetchGalleriesByPhotographer(photographerId)
    const list = Array.isArray(payload?.data) ? payload.data : []
    galleries.value = list.map(mapGallery).filter((g) => g.id)
  } catch (err) {
    error.value = err.message || 'Impossible de charger les galeries.'
    galleries.value = []
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Tableau de bord</h1>
      <p class="dash-welcome">Bonjour, {{ authStore.displayName }}.</p>

      <div class="dash-stats" aria-label="Indicateurs">
        <div class="dash-stat">
          <span class="dash-stat__val">{{ loading ? '…' : galleryCount }}</span>
          <span class="dash-stat__label">Galeries</span>
        </div>
        <div class="dash-stat">
          <span class="dash-stat__val">{{ photoCountLocal }}</span>
          <span class="dash-stat__label">Photos (cet appareil)</span>
        </div>
      </div>

      <p v-if="error" class="dash-error" role="alert">{{ error }}</p>

      <h2 class="dash-h2">Raccourcis</h2>
      <ul class="dash-shortcuts">
        <li>
          <RouterLink :to="{ name: 'photos' }">Photos</RouterLink>
        </li>
        <li>
          <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink>
        </li>
        <li>
          <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
        </li>
      </ul>

      <h2 class="dash-h2">Galeries (aperçu)</h2>
      <p v-if="loading" class="dash-muted">Chargement des galeries…</p>
      <p v-else-if="!recentGalleries.length" class="dash-muted">Aucune galerie pour le moment.</p>
      <ul v-else class="dash-recent">
        <li v-for="g in recentGalleries" :key="g.id" class="dash-recent__item">
          <RouterLink
            class="dash-recent__link"
            :to="{
              name: 'gallery-photos',
              params: { galleryId: g.id },
              query: { title: g.title },
            }"
          >
            {{ g.title }}
          </RouterLink>
          <span class="dash-recent__meta">
            {{ g.isPublished ? 'Publiée' : 'Non publiée' }}
            <template v-if="g.photosCount !== null"> · {{ g.photosCount }} photo(s)</template>
          </span>
        </li>
      </ul>
    </main>
  </div>
</template>

<style scoped>
.dash-welcome {
  margin: 0 0 1.25rem;
  color: #4a4a5c;
  font-size: 1rem;
}

.dash-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.75rem;
}

.dash-stat {
  min-width: 140px;
  padding: 1rem 1.25rem;
  border-radius: 10px;
  background: #fff;
  border: 1px solid #e4e8ff;
  box-shadow: 0 1px 3px rgba(30, 30, 40, 0.06);
}

.dash-stat__val {
  display: block;
  font-size: 1.75rem;
  font-weight: 700;
  color: #4a5fc9;
  line-height: 1.2;
}

.dash-stat__label {
  font-size: 0.85rem;
  color: #6b6b78;
}

.dash-error {
  margin: 0 0 1rem;
  color: #c62828;
  font-size: 0.9rem;
}

.dash-h2 {
  margin: 1.5rem 0 0.5rem;
  font-size: 1.05rem;
  font-weight: 600;
  color: #1e1e28;
}

.dash-h2:first-of-type {
  margin-top: 0;
}

.dash-muted {
  margin: 0 0 0.5rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.dash-shortcuts {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem 1.25rem;
}

.dash-shortcuts a {
  color: #4a5fc9;
  font-weight: 500;
  text-decoration: none;
}

.dash-shortcuts a:hover {
  text-decoration: underline;
  text-underline-offset: 3px;
}

.dash-recent {
  margin: 0;
  padding: 0;
  list-style: none;
}

.dash-recent__item {
  padding: 0.65rem 0;
  border-bottom: 1px solid #e8e8ef;
}

.dash-recent__item:last-child {
  border-bottom: none;
}

.dash-recent__link {
  color: #1e1e28;
  font-weight: 500;
  text-decoration: none;
}

.dash-recent__link:hover {
  color: #4a5fc9;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.dash-recent__meta {
  display: block;
  margin-top: 0.2rem;
  font-size: 0.8rem;
  color: #6b6b78;
}
</style>
