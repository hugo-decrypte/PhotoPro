<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '../components/AppHeader.vue'
import { createGallery } from '../services/galleryApi'
import { normalizeApiError } from '../lib/apiError'

const router = useRouter()

const title = ref('')
const description = ref('')
const visibility = ref('public')
const clientEmail = ref('')
const clientName = ref('')
const clientPhone = ref('')

const errorMessage = ref('')
const successMessage = ref('')
const loading = ref(false)

const isPrivate = computed(() => visibility.value === 'private')

async function submit() {
  errorMessage.value = ''
  successMessage.value = ''

  if (!title.value.trim()) {
    errorMessage.value = 'Le titre est obligatoire.'
    return
  }

  if (isPrivate.value && !clientEmail.value.trim()) {
    errorMessage.value = 'Email client obligatoire pour une galerie privée.'
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

  loading.value = true
  try {
    const result = await createGallery(payload)
    if (!result?.success) {
      throw new Error(result?.error || 'Création impossible.')
    }
    successMessage.value = 'Galerie créée avec succès.'
    title.value = ''
    description.value = ''
    visibility.value = 'public'
    clientEmail.value = ''
    clientName.value = ''
    clientPhone.value = ''
    await router.push({ name: 'gallery' })
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || err?.message || 'Création impossible.'
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
          <label>Visibilité</label>
          <select v-model="visibility">
            <option value="public">Publique</option>
            <option value="private">Privée</option>
          </select>
        </div>

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

        <button type="submit" :disabled="loading">
          {{ loading ? 'Création…' : 'Créer la galerie' }}
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

.gallery-edit-form__hint {
  margin: 0;
  font-size: 0.8rem;
  color: #6b6b78;
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
