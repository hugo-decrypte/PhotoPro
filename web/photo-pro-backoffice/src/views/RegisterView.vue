<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '../components/AuthLayout.vue'
import { normalizeApiError } from '../lib/apiError'
import { register } from '../services/authApi'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const firstName = ref('')
const lastName = ref('')
const pseudo = ref('')
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
  if (!firstName.value || !lastName.value || !pseudo.value || !email.value || !password.value) {
    errorMessage.value = 'Prénom, nom, pseudo, email et mot de passe sont obligatoires.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const data = await register({
      first_name: firstName.value.trim(),
      name: lastName.value.trim(),
      pseudo: pseudo.value.trim(),
      email: email.value,
      password: password.value,
    })
    const tokens = extractTokens(data)
    if (!tokens.accessToken) {
      throw new Error('Token invalide')
    }

    authStore.setTokens(tokens.accessToken, tokens.refreshToken)
    if (data?.user) {
      authStore.setUser({
        ...data.user,
        first_name: firstName.value.trim(),
        name: lastName.value.trim(),
        pseudo: pseudo.value.trim(),
      })
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
  <AuthLayout title="Inscription">
    <form class="auth-form" @submit.prevent="submit">
      <div class="auth-form__field">
        <label for="register-first-name">Prénom</label>
        <input
          id="register-first-name"
          v-model="firstName"
          class="auth-form__input"
          type="text"
          name="first_name"
          autocomplete="given-name"
          placeholder="Jean"
        />
      </div>
      <div class="auth-form__field">
        <label for="register-last-name">Nom</label>
        <input
          id="register-last-name"
          v-model="lastName"
          class="auth-form__input"
          type="text"
          name="name"
          autocomplete="family-name"
          placeholder="Dupont"
        />
      </div>
      <div class="auth-form__field">
        <label for="register-pseudo">Pseudo</label>
        <input
          id="register-pseudo"
          v-model="pseudo"
          class="auth-form__input"
          type="text"
          name="pseudo"
          autocomplete="nickname"
          placeholder="jeanphoto"
        />
      </div>
      <div class="auth-form__field">
        <label for="register-email">Email</label>
        <input
          id="register-email"
          v-model="email"
          class="auth-form__input"
          type="email"
          name="email"
          autocomplete="email"
          placeholder="vous@exemple.com"
        />
      </div>
      <div class="auth-form__field">
        <label for="register-password">Mot de passe</label>
        <input
          id="register-password"
          v-model="password"
          class="auth-form__input"
          type="password"
          name="password"
          autocomplete="new-password"
          placeholder="••••••••"
        />
      </div>
      <button class="auth-form__submit" type="submit" :disabled="loading">
        {{ loading ? 'Création…' : 'Créer un compte' }}
      </button>
    </form>

    <p v-if="errorMessage" class="auth-card__error" role="alert">{{ errorMessage }}</p>

    <p class="auth-card__switch">
      Déjà un compte ?
      <RouterLink :to="{ name: 'login' }">Se connecter</RouterLink>
    </p>
  </AuthLayout>
</template>
