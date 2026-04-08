<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import PhotoGrid from '../components/PhotoGrid.vue'
import { addPhotosToGallery, createGallery } from '../services/galleryApi'
import { normalizeApiError } from '../lib/apiError'
import { filterItems } from '../lib/listClient'
import { usePhotoDisplay } from '../composables/usePhotoDisplay'

const GALLERY_PREFILL_STORAGE_KEY = 'photopro_gallery_prefill'
const PHOTOS_STORAGE_KEY = 'photopro_uploaded_photos'

const router = useRouter()

const { thumbSrc } = usePhotoDisplay()

/** 1 = infos galerie, 2 = choix des photos (optionnel) */
const step = ref(1)

const title = ref('')
const description = ref('')
const visibility = ref('public')
const clientEmail = ref('')
const clientName = ref('')
const clientPhone = ref('')
/** UUID d’une photo du pot commun (localStorage), optionnel */
const coverPhotoId = ref('')
const coverSearchQuery = ref('')

const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)
const selectedPhotoIds = ref([])
const allPhotos = ref([])

const isPrivate = computed(() => visibility.value === 'private')

const coverPickerPhotos = computed(() =>
  filterItems(allPhotos.value, coverSearchQuery.value, (p) => `${p.title || ''} ${p.originalName || ''}`),
)

const coverSelectionHiddenBySearch = computed(() => {
  const q = String(coverSearchQuery.value || '').trim()
  if (!q || !coverPhotoId.value) {
    return false
  }
  return !coverPickerPhotos.value.some((p) => String(p.id) === coverPhotoId.value)
})

watch(allPhotos, () => {
  if (!allPhotos.value.length) {
    coverSearchQuery.value = ''
  }
})

function loadPersistedPhotos() {
  try {
    const raw = localStorage.getItem(PHOTOS_STORAGE_KEY)
    if (!raw) {
      allPhotos.value = []
      return
    }
    const parsed = JSON.parse(raw)
    if (!Array.isArray(parsed)) {
      allPhotos.value = []
      return
    }
    allPhotos.value = parsed.filter((p) => p && p.id && (p.url || p.thumbnailUrl))
  } catch {
    allPhotos.value = []
  }
}

function togglePhotoSelection(id) {
  const idStr = String(id)
  const list = selectedPhotoIds.value
  const i = list.indexOf(idStr)
  if (i >= 0) {
    list.splice(i, 1)
  } else {
    list.push(idStr)
  }
}

function clearPhotoSelection() {
  selectedPhotoIds.value = []
}

function toggleCoverPhoto(id) {
  const s = String(id)
  coverPhotoId.value = coverPhotoId.value === s ? '' : s
}

function validateInfos() {
  if (!title.value.trim()) {
    errorMessage.value = 'Le titre est obligatoire.'
    return false
  }
  if (isPrivate.value && !clientEmail.value.trim()) {
    errorMessage.value = 'Email client obligatoire pour une galerie privée.'
    return false
  }
  return true
}

function goToPhotosStep() {
  errorMessage.value = ''
  successMessage.value = ''
  if (!validateInfos()) {
    return
  }
  step.value = 2
}

function goBackToInfos() {
  errorMessage.value = ''
  step.value = 1
}

async function submitGallery({ withPhotos }) {
  errorMessage.value = ''
  successMessage.value = ''

  if (!validateInfos()) {
    return
  }

  const payload = {
    title: title.value.trim(),
    description: description.value.trim(),
    type: isPrivate.value ? 'private' : 'public',
  }

  if (isPrivate.value) {
    payload.client_email = clientEmail.value.trim()
    if (clientName.value.trim()) {
      payload.client_name = clientName.value.trim()
    }
    if (clientPhone.value.trim()) {
      payload.client_phone = clientPhone.value.trim()
    }
  }

  const cover = coverPhotoId.value.trim()
  if (cover) {
    payload.cover_photo_id = cover
  }

  loading.value = true
  try {
    const result = await createGallery(payload)
    if (!result?.success) {
      throw new Error(result?.error || 'Création impossible.')
    }

    const created = result?.data
    const galleryId =
      created && typeof created === 'object' && created.id != null && created.id !== ''
        ? String(created.id)
        : ''

    if (galleryId && withPhotos && selectedPhotoIds.value.length) {
      const photosPayload = selectedPhotoIds.value.map((id, index) => ({
        photo_id: String(id),
        order: index + 1,
      }))
      const addResult = await addPhotosToGallery(galleryId, photosPayload)
      if (!addResult?.success) {
        throw new Error(
          addResult?.error ||
            'Galerie créée, mais l’ajout des photos a échoué. Tu peux les ajouter depuis la liste des galeries.',
        )
      }
    }

    successMessage.value = 'Galerie créée avec succès.'
    title.value = ''
    description.value = ''
    visibility.value = 'public'
    clientEmail.value = ''
    clientName.value = ''
    clientPhone.value = ''
    coverPhotoId.value = ''
    coverSearchQuery.value = ''
    selectedPhotoIds.value = []
    step.value = 1
    sessionStorage.removeItem(GALLERY_PREFILL_STORAGE_KEY)
    await router.push({ name: 'gallery' })
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || err?.message || 'Création impossible.'
  } finally {
    loading.value = false
  }
}

function loadPrefillFromSelection() {
  const raw = sessionStorage.getItem(GALLERY_PREFILL_STORAGE_KEY)
  if (!raw) {
    return
  }

  try {
    const parsed = JSON.parse(raw)
    const ids = Array.isArray(parsed?.photoIds) ? parsed.photoIds.map((id) => String(id)) : []
    const known = new Set(allPhotos.value.map((p) => String(p.id)))
    selectedPhotoIds.value = ids.filter((id) => known.has(id))
  } catch {
    selectedPhotoIds.value = []
  }
}

onMounted(() => {
  loadPersistedPhotos()
  loadPrefillFromSelection()
})
</script>

<template>
  <div class="app-shell">
    <main class="app-shell__main">
      <h1 class="app-shell__title">Créer une galerie</h1>
      <p class="gallery-edit-step-label">
        <template v-if="step === 1">Étape 1 — Informations</template>
        <template v-else>Étape 2 — Photos (optionnel)</template>
      </p>

      <div v-show="step === 1" class="gallery-edit-form gallery-edit-form--narrow">
        <form @submit.prevent="goToPhotosStep">
          <div>
            <label>Titre de la galerie</label>
            <input v-model="title" type="text" placeholder="Ex: Vacances 2026" />
          </div>

          <div>
            <label>Description</label>
            <textarea v-model="description" placeholder="Décrivez la galerie..." />
          </div>

          <div>
            <label>Visibilité</label>
            <select v-model="visibility">
              <option value="public">Publique</option>
              <option value="private">Privée</option>
            </select>
          </div>

          <div v-if="allPhotos.length" class="gallery-edit-cover">
            <span class="gallery-edit-cover__label">Photo d’entête (optionnel)</span>
            <p class="gallery-edit-form__hint">Choisis une image parmi tes photos importées (page Photos).</p>
            <label class="gallery-edit-cover__search-label" for="cover-photo-search">Rechercher une photo</label>
            <input
              id="cover-photo-search"
              v-model="coverSearchQuery"
              class="gallery-edit-cover__search"
              type="search"
              placeholder="Titre ou nom de fichier…"
              autocomplete="off"
            />
            <p v-if="coverSelectionHiddenBySearch" class="gallery-edit-form__hint gallery-edit-cover__search-note">
              La photo d’entête sélectionnée ne correspond pas au filtre — efface la recherche pour la voir ou la
              changer.
            </p>
            <p
              v-if="coverSearchQuery.trim() && !coverPickerPhotos.length"
              class="gallery-edit-form__hint"
              role="status"
            >
              Aucun résultat pour « {{ coverSearchQuery.trim() }} ».
            </p>
            <div
              v-else
              class="gallery-edit-cover__grid"
              role="group"
              aria-label="Choisir la photo d’entête"
            >
              <button
                v-for="p in coverPickerPhotos"
                :key="p.id"
                type="button"
                class="gallery-edit-cover__thumb"
                :class="{ 'gallery-edit-cover__thumb--on': coverPhotoId === String(p.id) }"
                :title="p.title || p.originalName || 'Photo'"
                @click="toggleCoverPhoto(p.id)"
              >
                <img :src="thumbSrc(p)" alt="" loading="lazy" decoding="async" />
              </button>
            </div>
          </div>
          <p v-else class="gallery-edit-form__hint">Importe des photos pour pouvoir définir une photo d’entête.</p>

          <div v-if="isPrivate">
            <label>Email client</label>
            <input v-model="clientEmail" type="email" placeholder="client@mail.com" />
          </div>

          <div v-if="isPrivate">
            <label>Nom client (optionnel)</label>
            <input v-model="clientName" type="text" placeholder="Nom du client" />
          </div>

          <div v-if="isPrivate">
            <label>Téléphone client (optionnel)</label>
            <input v-model="clientPhone" type="text" placeholder="+33..." />
          </div>

          <p v-if="isPrivate" class="gallery-edit-form__hint">
            Le code d'accès privé est généré automatiquement côté backend.
          </p>

          <div class="gallery-edit-form__actions">
            <button type="submit" class="gallery-edit-form__btn-primary" :disabled="loading">
              Continuer vers les photos
            </button>
            <button
              type="button"
              class="gallery-edit-form__btn-secondary"
              :disabled="loading"
              @click="submitGallery({ withPhotos: false })"
            >
              {{ loading ? 'Création…' : 'Créer la galerie sans photos' }}
            </button>
          </div>
        </form>
      </div>

      <div v-show="step === 2" class="gallery-edit-form">
        <section class="gallery-edit-form__photos" aria-labelledby="gallery-edit-photos-title">
          <h2 id="gallery-edit-photos-title" class="gallery-edit-form__photos-title">Photos</h2>
          <p v-if="!allPhotos.length" class="gallery-edit-form__hint">
            Aucune photo dans ce navigateur.
            <RouterLink :to="{ name: 'photos' }">Page Photos</RouterLink>
          </p>
          <template v-else>
            <p v-if="selectedPhotoIds.length" class="gallery-edit-form__selection-count">
              {{ selectedPhotoIds.length }} sélectionnée(s).
              <button type="button" class="gallery-edit-form__linkish" @click="clearPhotoSelection">
                Tout désélectionner
              </button>
            </p>
            <PhotoGrid
              class="gallery-edit-form__photo-grid"
              :photos="allPhotos"
              :selected-ids="selectedPhotoIds"
              @toggle="togglePhotoSelection"
            />
          </template>
        </section>

        <div class="gallery-edit-form__actions gallery-edit-form__actions--step2">
          <button type="button" class="gallery-edit-form__btn-secondary" :disabled="loading" @click="goBackToInfos">
            Retour aux informations
          </button>
          <button
            type="button"
            class="gallery-edit-form__btn-primary"
            :disabled="loading"
            @click="submitGallery({ withPhotos: true })"
          >
            {{ loading ? 'Création…' : 'Créer la galerie' }}
          </button>
        </div>
      </div>

      <p v-if="errorMessage" class="gallery-edit-form__msg gallery-edit-form__msg--error">{{ errorMessage }}</p>
      <p v-if="successMessage" class="gallery-edit-form__msg gallery-edit-form__msg--success">
        {{ successMessage }}
      </p>
    </main>
  </div>
</template>

<style scoped>
.gallery-edit-step-label {
  margin: -0.35rem 0 1rem;
  font-size: 0.9rem;
  color: #6b6b78;
}

.gallery-edit-form {
  max-width: min(52rem, 100%);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.gallery-edit-form--narrow {
  max-width: 32rem;
}

.gallery-edit-form__photos {
  margin-bottom: 0.25rem;
}

.gallery-edit-form__photos-title {
  margin: 0 0 0.35rem;
  font-size: 1.05rem;
  font-weight: 600;
  color: #1e1e28;
}

.gallery-edit-form__photo-grid {
  margin-top: 0.5rem;
}

.gallery-edit-form__selection-count {
  margin: 0 0 0.5rem;
  font-size: 0.875rem;
  color: #3a3a45;
}

.gallery-edit-form__linkish {
  margin-left: 0.5rem;
  padding: 0;
  border: none;
  background: none;
  color: #4a5fc9;
  font: inherit;
  font-size: 0.85rem;
  font-weight: 500;
  text-decoration: underline;
  cursor: pointer;
}

.gallery-edit-form__linkish:hover {
  color: #3d4fc4;
}

.gallery-edit-form a {
  color: #4a5fc9;
  font-weight: 500;
}

.gallery-edit-form label {
  display: block;
  margin-bottom: 0.35rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #3a3a45;
}

.gallery-edit-form input[type='text'],
.gallery-edit-form input[type='email'],
.gallery-edit-form input[type='search'],
.gallery-edit-form select,
.gallery-edit-form textarea {
  width: 100%;
  padding: 0.5rem 0.65rem;
  border: 1px solid #d8d8e4;
  border-radius: 6px;
  font: inherit;
  background: #fff;
}

.gallery-edit-form textarea {
  min-height: 5rem;
  resize: vertical;
}

.gallery-edit-cover__label {
  display: block;
  margin-bottom: 0.35rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #3a3a45;
}

.gallery-edit-cover__search-label {
  display: block;
  margin: 0.5rem 0 0.35rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #3a3a45;
}

.gallery-edit-cover__search {
  width: 100%;
  margin-bottom: 0.35rem;
}

.gallery-edit-cover__search-note {
  margin: 0 0 0.5rem;
}

.gallery-edit-cover__grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.35rem;
}

.gallery-edit-cover__thumb {
  padding: 0;
  border: 2px solid #e0e0ea;
  border-radius: 8px;
  overflow: hidden;
  width: 4.5rem;
  height: 4.5rem;
  cursor: pointer;
  background: #f3f3f6;
}

.gallery-edit-cover__thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.gallery-edit-cover__thumb--on {
  border-color: #4a5fc9;
  box-shadow: 0 0 0 2px rgba(148, 168, 249, 0.45);
}

.gallery-edit-form__hint {
  margin: 0;
  font-size: 0.8rem;
  color: #6b6b78;
}

.gallery-edit-form__actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.65rem;
  margin-top: 0.5rem;
}

.gallery-edit-form__actions--step2 {
  margin-top: 1rem;
}

.gallery-edit-form__btn-primary {
  padding: 0.5rem 1rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #94a8f9;
  color: #fff;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.gallery-edit-form__btn-primary:hover:not(:disabled) {
  filter: brightness(1.05);
}

.gallery-edit-form__btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.gallery-edit-form__btn-secondary {
  padding: 0.5rem 1rem;
  border: 1px solid #b8c4f0;
  border-radius: 6px;
  background: #fff;
  color: #4a5fc9;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.gallery-edit-form__btn-secondary:hover:not(:disabled) {
  background: #f3f5ff;
}

.gallery-edit-form__btn-secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.gallery-edit-form__msg {
  margin: 1rem 0 0;
  font-size: 0.875rem;
  max-width: 32rem;
}

.gallery-edit-form__msg--error {
  color: #c62828;
}

.gallery-edit-form__msg--success {
  color: #2e7d32;
}
</style>
