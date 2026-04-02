<script setup>
import { computed, ref } from 'vue'

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
  <main>
    <h1>Créer une galerie</h1>
    <nav>
      <RouterLink :to="{ name: 'photos' }">Photos</RouterLink> |
      <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink> |
      <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
    </nav>

    <form @submit.prevent="submit">
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
    </form>

    <p v-if="errorMessage">{{ errorMessage }}</p>
    <p v-if="successMessage">{{ successMessage }}</p>
  </main>
</template>
