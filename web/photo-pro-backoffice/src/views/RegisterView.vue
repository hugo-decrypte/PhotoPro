<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { normalizeApiError } from '../lib/apiError'
import { register } from '../services/authApi'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

function extractTokens(data) {
  return {
    accessToken: data?.access_token || data?.token || '',
    refreshToken: data?.refresh_token || '',
  }
}

async function submit() {
  if (!email.value || !password.value) {
    errorMessage.value = 'Email et mot de passe obligatoires.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const data = await register({
      email: email.value,
      password: password.value,
    })
    const tokens = extractTokens(data)
    if (!tokens.accessToken) {
      throw new Error('Token invalide')
    }

    authStore.setTokens(tokens.accessToken, tokens.refreshToken)
    if (data?.user) {
      authStore.setUser(data.user)
    }
    router.push({ name: 'dashboard' })
  } catch (err) {
    errorMessage.value = normalizeApiError(err).message || 'Inscription impossible.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main>
    <h1>Inscription</h1>
    <form @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Mot de passe" />
      <button type="submit" :disabled="loading">Creer un compte</button>
    </form>
    <p v-if="errorMessage">{{ errorMessage }}</p>
    <p>
      Deja un compte ?
      <RouterLink :to="{ name: 'login' }">Connexion</RouterLink>
    </p>
  </main>
</template>
