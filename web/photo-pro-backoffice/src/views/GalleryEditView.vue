<script setup>
import { computed, ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'

const title = ref('')
const description = ref('')
const visibility = ref('public')
const clientEmail = ref('')
const accessCode = ref('')
const isPublished = ref(false)
const coverPhotoId = ref('')
const selectedPhotoIds = ref([])

const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)

const photos = [
  { id: 'p1', label: 'mokujin.jpg' },
  { id: 'p2', label: 'mokujin2.jpg' },
  { id: 'p3', label: 'jinton.png' },
]

const isPrivate = computed(() => visibility.value === 'private')

function togglePhoto(photoId) {
  const i = selectedPhotoIds.value.indexOf(photoId)
  if (i >= 0) {
    selectedPhotoIds.value.splice(i, 1)
  } else {
    selectedPhotoIds.value.push(photoId)
  }
}

async function submit() {
  errorMessage.value = ''
  successMessage.value = ''

  if (!title.value.trim()) {
    errorMessage.value = 'Le titre est obligatoire.'
    return
  }

  if (isPrivate.value && !clientEmail.value.trim()) {
    errorMessage.value = "email client est obligatoire pour une galerie privée."
    return
  }

  if (isPrivate.value && !accessCode.value.trim()) {
    errorMessage.value = 'Le code d acces est obligatoire pour une galerie privée'
    return
  }

  if (isPublished.value && selectedPhotoIds.value.length === 0) {
    errorMessage.value = 'Impossible de publier une galerie sans photo.'
    return
  }

  loading.value = true
  try {
    successMessage.value = 'Galerie validée localement. En attente du branchement backend.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Créer une galerie</h1>

      <form class="gallery-edit-form" @submit.prevent="submit">
        <div>
          <label>Titre de la galerie</label>
          <input v-model="title" type="text" placeholder="Ex: Vacances 2026" />
        </div>

        <div>
          <label>Description</label>
          <textarea v-model="description" placeholder="Décrivez la galerie..." />
        </div>

        <div>
          <label>Visibilite</label>
          <select v-model="visibility">
            <option value="public">Publique</option>
            <option value="private">Privee</option>
          </select>
        </div>

        <div v-if="isPrivate">
          <label>Email client</label>
          <input v-model="clientEmail" type="email" placeholder="client@mail.com" />
        </div>

        <div v-if="isPrivate">
          <label>Code d acces</label>
          <input v-model="accessCode" type="text" placeholder="CODE123" />
        </div>

        <div>
          <label>Photo d entete</label>
          <select v-model="coverPhotoId">
            <option value="">Aucune</option>
            <option v-for="photo in photos" :key="photo.id" :value="photo.id">
              {{ photo.label }}
            </option>
          </select>
        </div>

        <div>
          <label>
            <input v-model="isPublished" type="checkbox" />
            Publier la galerie
          </label>
        </div>

        <div>
          <p>Photos de la galerie</p>
          <label v-for="photo in photos" :key="photo.id" style="display: block">
            <input
              type="checkbox"
              :checked="selectedPhotoIds.includes(photo.id)"
              @change="togglePhoto(photo.id)"
            />
            {{ photo.label }}
          </label>
        </div>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Création...' : 'Créer la galerie' }}
        </button>

        <p v-if="errorMessage" class="gallery-edit-form__msg gallery-edit-form__msg--error">{{ errorMessage }}</p>
        <p v-if="successMessage" class="gallery-edit-form__msg gallery-edit-form__msg--success">
          {{ successMessage }}
        </p>
      </form>
    </main>
  </div>
</template>

<style scoped>
.gallery-edit-form {
  max-width: 32rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
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

.gallery-edit-form button[type='submit'] {
  align-self: flex-start;
  margin-top: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #94a8f9;
  color: #fff;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.gallery-edit-form button[type='submit']:hover:not(:disabled) {
  filter: brightness(1.05);
}

.gallery-edit-form button[type='submit']:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.gallery-edit-form__msg {
  margin: 0.5rem 0 0;
  font-size: 0.875rem;
}

.gallery-edit-form__msg--error {
  color: #c62828;
}

.gallery-edit-form__msg--success {
  color: #2e7d32;
}
</style>
