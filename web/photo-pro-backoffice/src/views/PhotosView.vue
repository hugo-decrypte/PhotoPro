<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import PhotoGrid from '../components/PhotoGrid.vue'
import '../css/list-controls.css'
import { filterItems, pageCount, slicePage } from '../lib/listClient'
import { REQUIRE_AUTH } from '../config/auth'
import { normalizeApiError } from '../lib/apiError'
import { addPhotosToGallery, fetchGalleriesByPhotographer } from '../services/galleryApi'
import { deletePhoto, uploadPhoto } from '../services/photoApi'
import { useAuthStore } from '../stores/auth'

const PHOTOS_PAGE_SIZE = 12

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'
const GALLERY_PREFILL_STORAGE_KEY = 'photopro_gallery_prefill'

const router = useRouter()
const selectedIds = ref([])
const authStore = useAuthStore()
const fileInputRef = ref(null)
const dropzoneDepth = ref(0)
const uploadLoading = ref(false)
const deleteLoading = ref(false)
const uploadError = ref('')
const uploadSuccess = ref('')

const galleriesForPicker = ref([])
const galleriesLoading = ref(false)
const galleriesLoadError = ref('')
const selectedGalleryId = ref('')
const addToGalleryLoading = ref(false)

const photos = ref([])

const photoSearch = ref('')
const photoPage = ref(1)

const filteredPhotos = computed(() =>
  filterItems(photos.value, photoSearch.value, (p) => `${p.title || ''} ${p.originalName || ''}`),
)

const photoPageCount = computed(() => pageCount(filteredPhotos.value.length, PHOTOS_PAGE_SIZE))

const visiblePhotos = computed(() => slicePage(filteredPhotos.value, photoPage.value, PHOTOS_PAGE_SIZE))

const photoDisplayPage = computed(() => Math.min(Math.max(1, photoPage.value), photoPageCount.value))

const isPhotosFilteredEmpty = computed(() => photos.value.length > 0 && filteredPhotos.value.length === 0)

const isDropzoneActive = computed(() => dropzoneDepth.value > 0)

watch(photoSearch, () => {
  photoPage.value = 1
})

watch(
  () => filteredPhotos.value.length,
  () => {
    const pc = pageCount(filteredPhotos.value.length, PHOTOS_PAGE_SIZE)
    if (photoPage.value > pc) {
      photoPage.value = pc
    }
  },
)

function photoPrev() {
  photoPage.value = Math.max(1, photoPage.value - 1)
}

function photoNext() {
  photoPage.value = Math.min(photoPageCount.value, photoPage.value + 1)
}

function serializePhotos(list) {
  return list.map((p) => ({
    id: p.id,
    title: p.title || '',
    originalName: p.originalName || '',
    mimeType: p.mimeType || 'image/jpeg',
    url: p.url || '',
    thumbnailUrl: p.thumbnailUrl || p.url || '',
  }))
}

function persistPhotos() {
  try {
    localStorage.setItem(PHOTOS_STORAGE_KEY, JSON.stringify(serializePhotos(photos.value)))
  } catch {
    // localStorage full/blocked: non-bloquant pour l'UI
  }
}

function loadPersistedPhotos() {
  try {
    const raw = localStorage.getItem(PHOTOS_STORAGE_KEY)
    if (!raw) {
      return
    }
    const parsed = JSON.parse(raw)
    if (!Array.isArray(parsed)) {
      return
    }
    photos.value = parsed.filter((p) => p && p.id && p.url)
  } catch {
    photos.value = []
  }
}

function toggleSelection(id) {
  const list = selectedIds.value
  const i = list.indexOf(id)
  if (i >= 0) {
    list.splice(i, 1)
  } else {
    list.push(id)
  }
}

function clearSelection() {
  selectedIds.value = []
  selectedGalleryId.value = ''
}

function createGalleryFromSelection() {
  if (!selectedIds.value.length) {
    return
  }

  const selectedPhotos = photos.value
    .filter((p) => selectedIds.value.includes(p.id))
    .map((p) => ({
      id: p.id,
      title: p.title || p.originalName || 'Sans titre',
      originalName: p.originalName || '',
      thumbnailUrl: p.thumbnailUrl || p.url || '',
    }))

  sessionStorage.setItem(
    GALLERY_PREFILL_STORAGE_KEY,
    JSON.stringify({
      photoIds: [...selectedIds.value],
      photos: selectedPhotos,
      createdAt: Date.now(),
    }),
  )

  router.push({ name: 'gallery-new' })
}

async function loadGalleriesForPicker() {
  authStore.loadFromStorage()
  const pid = authStore.userId
  if (!pid) {
    galleriesForPicker.value = []
    return
  }
  galleriesLoading.value = true
  galleriesLoadError.value = ''
  try {
    const payload = await fetchGalleriesByPhotographer(pid)
    const list = Array.isArray(payload?.data) ? payload.data : []
    galleriesForPicker.value = list
      .filter((g) => g?.id != null && g.id !== '')
      .map((g) => ({
        id: String(g.id),
        title: String(g.title || 'Sans titre'),
      }))
  } catch (err) {
    galleriesLoadError.value = normalizeApiError(err).message || err?.message || 'Chargement des galeries impossible.'
    galleriesForPicker.value = []
  } finally {
    galleriesLoading.value = false
  }
}

async function addSelectionToExistingGallery() {
  if (!selectedIds.value.length) {
    return
  }
  if (!selectedGalleryId.value) {
    uploadError.value = 'Choisis une galerie dans la liste.'
    uploadSuccess.value = ''
    return
  }

  authStore.loadFromStorage()
  if (!authStore.userId) {
    uploadError.value = 'Connecte-toi pour ajouter des photos à une galerie.'
    uploadSuccess.value = ''
    if (REQUIRE_AUTH) {
      await router.push({ name: 'login' })
    }
    return
  }

  addToGalleryLoading.value = true
  uploadError.value = ''
  uploadSuccess.value = ''

  const photosPayload = selectedIds.value.map((id, index) => ({
    photo_id: String(id),
    order: index + 1,
  }))

  try {
    const result = await addPhotosToGallery(selectedGalleryId.value, photosPayload)
    if (!result?.success) {
      throw new Error(result?.error || 'Ajout impossible.')
    }
    selectedIds.value = []
    selectedGalleryId.value = ''
    uploadSuccess.value = 'Photos ajoutées à la galerie.'
  } catch (err) {
    uploadError.value = normalizeApiError(err).message || err?.message || 'Ajout impossible.'
  } finally {
    addToGalleryLoading.value = false
  }
}

async function deleteSelectedPhotos() {
  if (!selectedIds.value.length) {
    return
  }

  deleteLoading.value = true
  uploadError.value = ''
  uploadSuccess.value = ''

  const idsToDelete = [...selectedIds.value]
  let deletedCount = 0

  try {
    for (const id of idsToDelete) {
      await deletePhoto(id)
      photos.value = photos.value.filter((p) => p.id !== id)
      deletedCount += 1
    }
    selectedIds.value = []
    persistPhotos()
    uploadSuccess.value = `${deletedCount} photo(s) supprimée(s).`
  } catch (err) {
    uploadError.value = err.message || 'Suppression impossible.'
  } finally {
    deleteLoading.value = false
  }
}

function openFilePicker() {
  uploadError.value = ''
  uploadSuccess.value = ''
  fileInputRef.value?.click()
}

function fileNameWithoutExtension(name) {
  return String(name || '').replace(/\.[^.]+$/, '')
}

function fileToDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(String(reader.result || ''))
    reader.onerror = () => reject(new Error('Lecture du fichier impossible.'))
    reader.readAsDataURL(file)
  })
}

function isImageFile(file) {
  if (!file || typeof file.type !== 'string') {
    return false
  }
  if (file.type.startsWith('image/')) {
    return true
  }
  if (!file.type && file.name) {
    return /\.(jpe?g|png|gif|webp|avif|bmp|heic)$/i.test(file.name)
  }
  return false
}

function extractUploadedPhotoInfo(result, fallbackName, fallbackMime, fallbackUrl) {
  const payload = result?.data && typeof result.data === 'object' ? result.data : result

  const id =
    payload?.id ||
    payload?.photo_id ||
    payload?.id_photo ||
    payload?.uuid ||
    payload?.s3_key ||
    ''

  if (!id) {
    return null
  }

  return {
    id: String(id),
    title: String(payload?.title || fileNameWithoutExtension(fallbackName)),
    originalName: String(payload?.original_filename || payload?.originalName || fallbackName),
    mimeType: String(payload?.mime_type || payload?.mimeType || fallbackMime || 'image/jpeg'),
    url: String(payload?.url || payload?.thumbnailUrl || fallbackUrl),
    thumbnailUrl: String(payload?.thumbnailUrl || payload?.url || fallbackUrl),
  }
}

/**
 * @param {File[]} files
 */
async function uploadImageFiles(files) {
  const raw = Array.from(files || [])
  if (!raw.length) {
    return
  }

  const images = raw.filter(isImageFile)
  if (!images.length) {
    uploadError.value = 'Aucun fichier image (JPEG, PNG, WebP, etc.).'
    uploadSuccess.value = ''
    return
  }

  uploadLoading.value = true
  uploadError.value = ''
  uploadSuccess.value = ''

  let uploadedCount = 0
  let firstUploadIssue = ''
  authStore.loadFromStorage()

  try {
    for (const file of images) {
      const localPreviewUrl = await fileToDataUrl(file)
      const result = await uploadPhoto({
        file,
        title: fileNameWithoutExtension(file.name),
        photographerId: authStore.userId || '',
      })

      const photoInfo = extractUploadedPhotoInfo(result, file.name, file.type, localPreviewUrl)
      if (photoInfo) {
        uploadedCount += 1
        // fallback local (persisté) en attendant une route GET /photos de listing
        if (!photoInfo.url) {
          photoInfo.url = localPreviewUrl
          photoInfo.thumbnailUrl = localPreviewUrl
        }
        photos.value.unshift(photoInfo)
        persistPhotos()
      } else if (result?.message || result?.error) {
        if (!firstUploadIssue) {
          firstUploadIssue = String(result.message || result.error)
        }
      } else if (!firstUploadIssue) {
        const availableKeys = Object.keys(result || {})
        firstUploadIssue = availableKeys.length
          ? `Réponse upload inattendue (clés: ${availableKeys.join(', ')})`
          : 'Réponse upload vide du serveur.'
      }
    }

    if (uploadedCount > 0) {
      uploadSuccess.value = `${uploadedCount} photo(s) uploadée(s) avec succès.`
      loadGalleriesForPicker()
    } else if (firstUploadIssue) {
      uploadError.value = firstUploadIssue
    } else {
      uploadError.value = 'Aucune photo n’a été ajoutée.'
    }
  } catch (err) {
    uploadError.value = err.message || 'Upload impossible.'
  } finally {
    uploadLoading.value = false
  }
}

async function onFilesSelected(event) {
  try {
    const files = Array.from(event?.target?.files || [])
    await uploadImageFiles(files)
  } finally {
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

function onDropzoneEnter() {
  dropzoneDepth.value += 1
}

function onDropzoneLeave() {
  dropzoneDepth.value = Math.max(0, dropzoneDepth.value - 1)
}

function onDropzoneOver(event) {
  event.preventDefault()
  if (event.dataTransfer) {
    event.dataTransfer.dropEffect = 'copy'
  }
}

async function onDropzoneDrop(event) {
  event.preventDefault()
  dropzoneDepth.value = 0
  const dt = event.dataTransfer
  if (!dt?.files?.length) {
    return
  }
  await uploadImageFiles(Array.from(dt.files))
}

onMounted(() => {
  loadPersistedPhotos()
  loadGalleriesForPicker()
})
</script>

<template>
  <div class="app-shell">
    <main class="app-shell__main">
      <h1 class="app-shell__title">Photos</h1>
      <div
        class="photos-dropzone"
        :class="{ 'photos-dropzone--active': isDropzoneActive }"
        role="region"
        aria-label="Zone d’import de photos"
        @dragenter.prevent="onDropzoneEnter"
        @dragleave.prevent="onDropzoneLeave"
        @dragover.prevent="onDropzoneOver"
        @drop.prevent="onDropzoneDrop"
      >
        <p class="photos-dropzone__title">Glisse-dépose plusieurs images ici</p>
        <p class="photos-dropzone__hint">Fichiers image uniquement · sélection multiple</p>
        <p class="photos-dropzone__or">ou</p>
        <button
          type="button"
          class="photos-toolbar__btn photos-toolbar__btn--standalone"
          :disabled="uploadLoading || deleteLoading"
          @click="openFilePicker"
        >
          {{ uploadLoading ? 'Upload…' : 'Parcourir les fichiers' }}
        </button>
        <input
          ref="fileInputRef"
          class="photos-toolbar__input"
          type="file"
          accept="image/*"
          multiple
          @change="onFilesSelected"
        />
      </div>
      <p v-if="uploadError" class="photos-toolbar__msg photos-toolbar__msg--error" role="alert">{{ uploadError }}</p>
      <p v-if="uploadSuccess" class="photos-toolbar__msg photos-toolbar__msg--success">{{ uploadSuccess }}</p>
      <div v-if="selectedIds.length" class="photos-toolbar-selection">
        <p class="photos-toolbar-selection__summary">{{ selectedIds.length }} photo(s) sélectionnée(s).</p>
        <div class="photos-toolbar-selection__actions">
          <button type="button" class="photos-toolbar__btn" @click="clearSelection">Tout désélectionner</button>
          <button
            type="button"
            class="photos-toolbar__btn photos-toolbar__btn--primary"
            @click="createGalleryFromSelection"
          >
            Créer une galerie avec la sélection
          </button>
          <button
            type="button"
            class="photos-toolbar__btn photos-toolbar__btn--danger"
            :disabled="deleteLoading || uploadLoading || addToGalleryLoading"
            @click="deleteSelectedPhotos"
          >
            {{ deleteLoading ? 'Suppression…' : 'Supprimer la sélection' }}
          </button>
        </div>
        <div class="photos-toolbar-selection__existing">
          <span class="photos-toolbar-selection__existing-label">Ou ajouter à une galerie existante :</span>
          <select
            v-model="selectedGalleryId"
            class="photos-toolbar-selection__select"
            :disabled="galleriesLoading || addToGalleryLoading || uploadLoading"
          >
            <option value="">{{ galleriesLoading ? 'Chargement des galeries…' : '— Choisir une galerie —' }}</option>
            <option v-for="g in galleriesForPicker" :key="g.id" :value="g.id">{{ g.title }}</option>
          </select>
          <button
            type="button"
            class="photos-toolbar__btn photos-toolbar__btn--primary"
            :disabled="!selectedGalleryId || addToGalleryLoading || uploadLoading || deleteLoading"
            @click="addSelectionToExistingGallery"
          >
            {{ addToGalleryLoading ? 'Ajout…' : 'Ajouter à la galerie' }}
          </button>
        </div>
        <p v-if="galleriesLoadError" class="photos-toolbar__msg photos-toolbar__msg--error" role="alert">
          {{ galleriesLoadError }}
        </p>
        <p
          v-else-if="!galleriesLoading && !galleriesForPicker.length && authStore.isAuthenticated"
          class="photos-toolbar-selection__hint"
        >
          Tu n’as pas encore de galerie. Crée-en une depuis Galeries, puis reviens ici.
        </p>
        <p v-else-if="!authStore.isAuthenticated" class="photos-toolbar-selection__hint">
          Connecte-toi pour lister tes galeries et y ajouter des photos.
        </p>
      </div>
      <p v-else class="photos-toolbar__hint">Aucune sélection</p>
      <p v-if="photos.length === 0" class="photos-toolbar__hint">
        Aucune photo pour le moment. Ajoutez vos photos pour commencer.
      </p>
      <template v-else>
        <div class="list-controls">
          <label class="list-controls__search">
            <span class="list-controls__search-label">Rechercher</span>
            <input v-model="photoSearch" type="search" placeholder="Titre ou nom de fichier…" autocomplete="off" />
          </label>
          <div class="list-controls__bar">
            <p class="list-controls__meta">
              {{ filteredPhotos.length }} photo(s) · page {{ photoDisplayPage }} / {{ photoPageCount }}
            </p>
            <div class="list-controls__pager">
              <button type="button" :disabled="photoDisplayPage <= 1" @click="photoPrev">Précédent</button>
              <button type="button" :disabled="photoDisplayPage >= photoPageCount" @click="photoNext">
                Suivant
              </button>
            </div>
          </div>
        </div>
        <p v-if="isPhotosFilteredEmpty" class="list-filter-empty">Aucun résultat pour cette recherche.</p>
        <PhotoGrid
          v-else
          :photos="visiblePhotos"
          :selected-ids="selectedIds"
          @toggle="toggleSelection"
        />
      </template>
    </main>
  </div>
</template>

<style scoped>
.photos-dropzone {
  margin: 0 0 1rem;
  padding: 1.35rem 1rem;
  border: 2px dashed #c5cae8;
  border-radius: 12px;
  background: #f9faff;
  text-align: center;
  transition:
    border-color 0.15s ease,
    background 0.15s ease;
}

.photos-dropzone--active {
  border-color: #4a5fc9;
  background: #eef1ff;
}

.photos-dropzone__title {
  margin: 0 0 0.35rem;
  font-size: 1rem;
  font-weight: 600;
  color: #2e2e3a;
}

.photos-dropzone__hint {
  margin: 0 0 0.5rem;
  font-size: 0.8rem;
  color: #6b6b78;
}

.photos-dropzone__or {
  margin: 0 0 0.5rem;
  font-size: 0.75rem;
  color: #9a9aaa;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.photos-toolbar__input {
  display: none;
}

.photos-toolbar__hint {
  margin: 0 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.photos-toolbar__btn {
  margin-left: 0.5rem;
  padding: 0.35rem 0.75rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #f0f2ff;
  color: #4a5fc9;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
}

.photos-toolbar__btn--standalone {
  margin-left: 0;
}

.photos-toolbar__btn:hover {
  background: #e4e8ff;
}

.photos-toolbar__btn--primary {
  border-color: #94a8f9;
  background: #94a8f9;
  color: #fff;
}

.photos-toolbar__btn--primary:hover {
  background: #7f96f6;
}

.photos-toolbar__btn--danger {
  border-color: #ef9a9a;
  background: #ffebee;
  color: #c62828;
}

.photos-toolbar__btn--danger:hover {
  background: #ffcdd2;
}

.photos-toolbar__msg {
  margin: 0 0 0.75rem;
  font-size: 0.9rem;
}

.photos-toolbar__msg--error {
  color: #c62828;
}

.photos-toolbar__msg--success {
  color: #2e7d32;
}

.photos-toolbar-selection {
  margin: 0 0 1rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #e8e8ef;
}

.photos-toolbar-selection__summary {
  margin: 0 0 0.5rem;
  font-size: 0.9rem;
  font-weight: 500;
  color: #3a3a45;
}

.photos-toolbar-selection__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.photos-toolbar-selection__existing {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.photos-toolbar-selection__existing-label {
  font-size: 0.85rem;
  color: #6b6b78;
  width: 100%;
}

@media (min-width: 640px) {
  .photos-toolbar-selection__existing-label {
    width: auto;
  }
}

.photos-toolbar-selection__select {
  min-width: min(100%, 220px);
  padding: 0.4rem 0.55rem;
  border: 1px solid #d8d8e4;
  border-radius: 6px;
  font: inherit;
  font-size: 0.85rem;
  background: #fff;
  color: #1e1e28;
}

.photos-toolbar-selection__hint {
  margin: 0.5rem 0 0;
  font-size: 0.8rem;
  color: #6b6b78;
}
</style>
