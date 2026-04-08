<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppHeader from '../components/AppHeader.vue'
import PhotoGrid from '../components/PhotoGrid.vue'
import { addPhotosToGallery, fetchGalleryPhotos } from '../services/galleryApi'
import { normalizeApiError } from '../lib/apiError'
import { REQUIRE_AUTH } from '../config/auth'
import { useAuthStore } from '../stores/auth'

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const galleryTitle = computed(() =>
  typeof route.query.title === 'string' && route.query.title ? route.query.title : 'Galerie',
)

const galleryId = computed(() => String(route.params.galleryId || ''))

const photos = ref([])
const selectedIds = ref([])
const loading = ref(false)
const errorMessage = ref('')
const existingPhotoIds = ref([])

function loadPersistedPhotos() {
  try {
    const raw = localStorage.getItem(PHOTOS_STORAGE_KEY)
    if (!raw) {
      photos.value = []
      return
    }
    const parsed = JSON.parse(raw)
    if (!Array.isArray(parsed)) {
      photos.value = []
      return
    }
    photos.value = parsed
      .filter((p) => p && p.id && (p.thumbnailUrl || p.url))
      .map((p) => ({
        id: String(p.id),
        title: String(p.title || p.originalName || 'Sans titre'),
        originalName: String(p.originalName || ''),
        mimeType: String(p.mimeType || 'image/jpeg'),
        url: String(p.url || p.thumbnailUrl || ''),
        thumbnailUrl: String(p.thumbnailUrl || p.url || ''),
      }))
  } catch {
    photos.value = []
  }
}

function toggleSelection(id) {
  const sid = String(id)
  const i = selectedIds.value.indexOf(sid)
  if (i >= 0) {
    selectedIds.value.splice(i, 1)
  } else {
    selectedIds.value.push(sid)
  }
}

function clearSelection() {
  selectedIds.value = []
}

async function loadExistingGalleryPhotos() {
  if (!galleryId.value) {
    existingPhotoIds.value = []
    return
  }
  try {
    const result = await fetchGalleryPhotos(galleryId.value)
    const list = Array.isArray(result?.data) ? result.data : []
    existingPhotoIds.value = list
      .map((item) => {
        if (item?.photo_id != null && item.photo_id !== '') return String(item.photo_id)
        if (item?.id != null && item.id !== '') return String(item.id)
        return ''
      })
      .filter(Boolean)
  } catch {
    existingPhotoIds.value = []
  }
}

async function submitAddPhotos() {
  errorMessage.value = ''

  if (!galleryId.value) {
    errorMessage.value = 'Identifiant de galerie manquant.'
    return
  }

  if (!selectedIds.value.length) {
    errorMessage.value = 'Sélectionne au moins une photo.'
    return
  }

  authStore.loadFromStorage()
  if (!authStore.userId) {
    errorMessage.value = 'Connecte-toi pour ajouter des photos à la galerie.'
    if (REQUIRE_AUTH) {
      await router.push({ name: 'login' })
    }
    return
  }

  const photosPayload = selectedIds.value.map((id, index) => ({
    photo_id: String(id),
    order: index + 1,
  }))

  loading.value = true
  try {
    const result = await addPhotosToGallery(galleryId.value, photosPayload)
    if (!result?.success) {
      throw new Error(result?.error || 'Ajout impossible.')
    }
    clearSelection()
    await router.push({ name: 'gallery' })
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || err?.message || 'Ajout impossible.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  authStore.loadFromStorage()
  loadPersistedPhotos()
  loadExistingGalleryPhotos()
})
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Ajouter des photos</h1>
      <p class="gallery-add-photos__subtitle">{{ galleryTitle }}</p>

      <p v-if="!galleryId" class="gallery-add-photos__error" role="alert">Lien invalide.</p>

      <template v-else>
        <p v-if="photos.length === 0" class="gallery-add-photos__hint">
          Aucune photo dans ton navigateur. Va dans « Photos » pour en ajouter ou uploader.
        </p>

        <template v-else>
          <p v-if="selectedIds.length" class="gallery-add-photos__selection">
            {{ selectedIds.length }} photo(s) sélectionnée(s).
            <button type="button" class="gallery-add-photos__btn" @click="clearSelection">Tout désélectionner</button>
          </p>
          <p v-if="existingPhotoIds.length" class="gallery-add-photos__selection">
            Cette galerie contient déjà {{ existingPhotoIds.length }} photo(s).
          </p>

          <PhotoGrid :photos="photos" :selected-ids="selectedIds" @toggle="toggleSelection" />

          <div class="gallery-add-photos__actions">
            <button type="button" class="gallery-add-photos__submit" :disabled="loading" @click="submitAddPhotos">
              {{ loading ? 'Envoi…' : 'Ajouter à la galerie' }}
            </button>
            <RouterLink class="gallery-add-photos__link" :to="{ name: 'gallery' }">Retour aux galeries</RouterLink>
          </div>
        </template>

        <p v-if="errorMessage" class="gallery-add-photos__error" role="alert">{{ errorMessage }}</p>
      </template>
    </main>
  </div>
</template>

<style scoped>
.gallery-add-photos__subtitle {
  margin: -0.5rem 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.gallery-add-photos__hint {
  margin: 0 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.gallery-add-photos__selection {
  margin: 0 0 0.75rem;
  font-size: 0.9rem;
  color: #3a3a45;
}

.gallery-add-photos__btn {
  margin-left: 0.5rem;
  padding: 0.25rem 0.6rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #f0f2ff;
  color: #4a5fc9;
  font-size: 0.8rem;
  cursor: pointer;
}

.gallery-add-photos__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
  margin-top: 1rem;
}

.gallery-add-photos__submit {
  padding: 0.5rem 1rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #94a8f9;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}

.gallery-add-photos__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.gallery-add-photos__link {
  color: #4a5fc9;
  font-size: 0.9rem;
}

.gallery-add-photos__error {
  margin: 0.75rem 0 0;
  color: #c62828;
  font-size: 0.875rem;
}

</style>
