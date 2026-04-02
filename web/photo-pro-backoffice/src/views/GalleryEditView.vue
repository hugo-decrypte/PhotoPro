<script setup>
import { ref } from 'vue'

const title = ref('')
const description = ref('')

const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)

async function submit() {
  errorMessage.value = ''
  successMessage.value = ''

  const cleanedTitle = title.value.trim()
  if (!cleanedTitle) {
    errorMessage.value = 'Le titre est obligatoire.'
    return
  }

  // Pour l'instant, pas d'appel API: on prépare juste la UI.
  loading.value = true
  try {
    successMessage.value = 'Il faut faire la route dans le back'
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

      <button type="submit" :disabled="loading">
        {{ loading ? 'Création...' : 'Créer la galerie' }}
      </button>
    </form>

    <p v-if="errorMessage">{{ errorMessage }}</p>
    <p v-if="successMessage">{{ successMessage }}</p>
  </main>
</template>
