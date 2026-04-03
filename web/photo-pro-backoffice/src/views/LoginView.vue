<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '../components/AuthLayout.vue'
import { normalizeApiError } from '../lib/apiError'
import { login } from '../services/authApi'
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
    const data = await login({
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
    errorMessage.value = normalizeApiError(err).message || 'Connexion impossible.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AuthLayout title="Connexion">
    <form class="auth-form" @submit.prevent="submit">
      <div class="auth-form__field">
        <label for="login-email">Email</label>
        <input
          id="login-email"
          v-model="email"
          class="auth-form__input"
          type="email"
          name="email"
          autocomplete="email"
          placeholder="vous@exemple.com"
        />
      </div>
      <div class="auth-form__field">
        <label for="login-password">Mot de passe</label>
        <input
          id="login-password"
          v-model="password"
          class="auth-form__input"
          type="password"
          name="password"
          autocomplete="current-password"
          placeholder="••••••••"
        />
      </div>
      <button class="auth-form__submit" type="submit" :disabled="loading">
        {{ loading ? 'Connexion…' : 'Se connecter' }}
      </button>
    </form>

    <p v-if="errorMessage" class="auth-card__error" role="alert">{{ errorMessage }}</p>

    <p class="auth-card__switch">
      Pas encore de compte ?
      <RouterLink :to="{ name: 'register' }">Créer un compte</RouterLink>
    </p>
  </AuthLayout>
</template>
