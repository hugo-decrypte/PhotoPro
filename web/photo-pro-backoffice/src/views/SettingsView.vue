<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import AppHeader from '../components/AppHeader.vue'
import { changePassword } from '../services/authApi'
import { useAuthStore } from '../stores/auth'
import '../css/settings-view.css'

const authStore = useAuthStore()

onMounted(() => {
  authStore.loadFromStorage()
})

const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const loading = ref(false)
const error = ref('')
const success = ref('')

function resetMessages() {
  error.value = ''
  success.value = ''
}

async function onSubmit() {
  resetMessages()

  if (!authStore.isAuthenticated) {
    error.value = 'Connecte-toi pour modifier ton mot de passe.'
    return
  }

  if (newPassword.value.length < 8) {
    error.value = 'Le nouveau mot de passe doit contenir au moins 8 caractères.'
    return
  }

  if (newPassword.value !== confirmPassword.value) {
    error.value = 'La confirmation ne correspond pas au nouveau mot de passe.'
    return
  }

  loading.value = true
  try {
    await changePassword({
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
    })
    success.value = 'Mot de passe mis à jour.'
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  } catch (err) {
    error.value = err.message || 'Impossible de mettre à jour le mot de passe.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Paramètres</h1>

      <section class="settings-section" aria-labelledby="settings-password-heading">
        <h2 id="settings-password-heading">Mot de passe</h2>

        <template v-if="!authStore.isAuthenticated">
          <p class="settings-login-hint">
            Tu dois être connecté pour changer ton mot de passe.
            <RouterLink :to="{ name: 'login' }">Se connecter</RouterLink>
          </p>
        </template>

        <form v-else class="settings-form" @submit.prevent="onSubmit">
          <div class="settings-field">
            <label for="settings-current-password">Mot de passe actuel</label>
            <input
              id="settings-current-password"
              v-model="currentPassword"
              type="password"
              autocomplete="current-password"
              required
            />
          </div>
          <div class="settings-field">
            <label for="settings-new-password">Nouveau mot de passe</label>
            <input
              id="settings-new-password"
              v-model="newPassword"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
            />
            <p class="settings-hint">Au moins 8 caractères.</p>
          </div>
          <div class="settings-field">
            <label for="settings-confirm-password">Confirmer le nouveau mot de passe</label>
            <input
              id="settings-confirm-password"
              v-model="confirmPassword"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
            />
          </div>

          <p v-if="error" class="settings-msg settings-msg--error" role="alert">{{ error }}</p>
          <p v-if="success" class="settings-msg settings-msg--success">{{ success }}</p>

          <button type="submit" class="settings-submit" :disabled="loading">
            {{ loading ? 'Enregistrement…' : 'Enregistrer' }}
          </button>
        </form>
      </section>
    </main>
  </div>
</template>
