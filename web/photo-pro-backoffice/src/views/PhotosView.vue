<script setup>
import { onMounted, ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'
import PhotoGrid from '../components/PhotoGrid.vue'
import { deletePhoto, uploadPhoto } from '../services/photoApi'
import { useAuthStore } from '../stores/auth'

const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const selectedIds = ref([])
const authStore = useAuthStore()
const fileInputRef = ref(null)
const uploadLoading = ref(false)
const deleteLoading = ref(false)
const uploadError = ref('')
const uploadSuccess = ref('')

const photos = ref([])

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

async function onFilesSelected(event) {
  const files = Array.from(event?.target?.files || [])
  if (!files.length) {
    return
  }

  uploadLoading.value = true
  uploadError.value = ''
  uploadSuccess.value = ''

  let uploadedCount = 0
  let firstUploadIssue = ''
  authStore.loadFromStorage()

  try {
    for (const file of files) {
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
    } else if (firstUploadIssue) {
      uploadError.value = firstUploadIssue
    } else {
      uploadError.value = 'Aucune photo n’a été ajoutée.'
    }
  } catch (err) {
    uploadError.value = err.message || 'Upload impossible.'
  } finally {
    uploadLoading.value = false
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

onMounted(() => {
  loadPersistedPhotos()
})
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Photos</h1>
      <div class="photos-toolbar">
        <button
          type="button"
          class="photos-toolbar__btn"
          :disabled="uploadLoading || deleteLoading"
          @click="openFilePicker"
        >
          {{ uploadLoading ? 'Upload…' : 'Ajouter des photos' }}
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
      <p v-if="selectedIds.length">
        {{ selectedIds.length }} photo(s) sélectionnée(s).
        <button type="button" class="photos-toolbar__btn" @click="clearSelection">Tout désélectionner</button>
        <button
          type="button"
          class="photos-toolbar__btn photos-toolbar__btn--danger"
          :disabled="deleteLoading || uploadLoading"
          @click="deleteSelectedPhotos"
        >
          {{ deleteLoading ? 'Suppression…' : 'Supprimer la sélection' }}
        </button>
      </p>
      <p v-else class="photos-toolbar__hint">Aucune sélection</p>
      <p v-if="photos.length === 0" class="photos-toolbar__hint">
        Aucune photo pour le moment. Ajoutez vos photos pour commencer.
      </p>
      <PhotoGrid v-else :photos="photos" :selected-ids="selectedIds" @toggle="toggleSelection" />
    </main>
  </div>
</template>

<style scoped>
.photos-toolbar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0 0 0.75rem;
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

.photos-toolbar__btn:hover {
  background: #e4e8ff;
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
</style>
