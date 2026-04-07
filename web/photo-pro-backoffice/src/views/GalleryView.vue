<script setup>
import { computed, onMounted, ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'
import { fetchGalleriesByPhotographer, publishGallery, unpublishGallery } from '../services/galleryApi'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import '../css/gallery-view.css'

const authStore = useAuthStore()
const router = useRouter()

const galleries = ref([])
const loading = ref(false)
const listError = ref('')
const actionError = ref('')
const actionLoadingId = ref('')

const isEmpty = computed(() => !loading.value && !listError.value && galleries.value.length === 0)

function mapGallery(item) {
  return {
    id: String(item?.id || ''),
    title: String(item?.title || 'Sans titre'),
    visibility: item?.type === 'private' ? 'private' : 'public',
    isPublished: Boolean(item?.status),
    photosCount: Number.isFinite(item?.photosCount) ? item.photosCount : null,
  }
}

async function loadGalleries() {
  loading.value = true
  listError.value = ''
  authStore.loadFromStorage()
  const photographerId = authStore.userId
  if (!photographerId) {
    listError.value = 'Connecte-toi pour voir tes galeries.'
    galleries.value = []
    loading.value = false
    await router.push({ name: 'login' })
    return
  }
  try {
    const payload = await fetchGalleriesByPhotographer(photographerId)
    const list = Array.isArray(payload?.data) ? payload.data : []
    galleries.value = list.map(mapGallery).filter((g) => g.id)
  } catch (err) {
    listError.value = err.message || 'Chargement des galeries impossible.'
    galleries.value = []
  } finally {
    loading.value = false
  }
}

async function togglePublish(gallery) {
  actionError.value = ''
  actionLoadingId.value = gallery.id
  try {
    if (gallery.isPublished) {
      await unpublishGallery(gallery.id)
      gallery.isPublished = false
    } else {
      await publishGallery(gallery.id)
      gallery.isPublished = true
    }
  } catch (err) {
    actionError.value = err.message || 'Action impossible.'
  } finally {
    actionLoadingId.value = ''
  }
}

onMounted(() => {
  loadGalleries()
})
</script>

<template>
  <div class="app-shell">
    <AppHeader />

    <main class="app-shell__main">
      <div class="gallery-page-head">
        <h1 class="app-shell__title">Galeries</h1>
        <RouterLink class="gallery-page-head__new" :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
      </div>
      <p v-if="loading" class="gallery-state">Chargement des galeries...</p>
      <p v-else-if="listError" class="gallery-action-error" role="alert">{{ listError }}</p>
      <p v-if="actionError" class="gallery-action-error" role="alert">{{ actionError }}</p>
      <p v-if="isEmpty" class="gallery-state">Aucune galerie pour le moment.</p>

      <div v-if="!loading && !listError && galleries.length" class="gallery-grid">
        <article v-for="gallery in galleries" :key="gallery.id" class="gallery-card">
          <div class="gallery-card__cover" aria-hidden="true" />
          <div class="gallery-card__footer">
            <span
              class="gallery-card__badge"
              :class="
                gallery.visibility === 'private' ? 'gallery-card__badge--private' : 'gallery-card__badge--public'
              "
            >
              {{ gallery.visibility === 'private' ? 'Privé' : 'Public' }}
            </span>
            <h2 class="gallery-card__title">{{ gallery.title }}</h2>
            <p class="gallery-card__status">
              {{ gallery.isPublished ? 'Publiée' : 'Non publiée' }}
              <template v-if="gallery.photosCount !== null"> · {{ gallery.photosCount }} photo(s)</template>
            </p>
            <div class="gallery-card__actions">
              <RouterLink
                :to="{
                  name: 'gallery-photos',
                  params: { galleryId: gallery.id },
                  query: { title: gallery.title },
                }"
              >
                Ajouter des photos
              </RouterLink>
              <button
                type="button"
                :disabled="actionLoadingId === gallery.id"
                @click="togglePublish(gallery)"
              >
                {{
                  actionLoadingId === gallery.id
                    ? 'Patientez…'
                    : gallery.isPublished
                      ? 'Dépublier'
                      : 'Publier'
                }}
              </button>
              <small class="gallery-card__hint">Les erreurs backend sont affichées en haut de la page.</small>
            </div>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>

<style scoped>
.gallery-page-head {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 0.25rem;
}

.gallery-page-head .app-shell__title {
  margin: 0;
}

.gallery-page-head__new {
  padding: 0.4rem 0.85rem;
  border-radius: 6px;
  border: 1px solid #94a8f9;
  background: #94a8f9;
  color: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
}

.gallery-page-head__new:hover {
  filter: brightness(1.05);
}

.gallery-state {
  margin: 0 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.gallery-action-error {
  margin: 0 0 1rem;
  color: #c62828;
  font-size: 0.9rem;
}
</style>
