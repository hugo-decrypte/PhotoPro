<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

function submit() {
  if (!email.value || !password.value) {
    return
  }

  // Etape 1 simplifiee: on simule une connexion.
  authStore.login('demo-jwt-token', 'demo-refresh-token')
  router.push({ name: 'dashboard' })
}
</script>

<template>
  <main>
    <h1>Connexion</h1>
    <form @submit.prevent="submit">
      <input v-model="email" type="email" placeholder="Email" />
      <input v-model="password" type="password" placeholder="Mot de passe" />
      <button type="submit">Se connecter</button>
    </form>
    <p>
      Pas encore de compte ?
      <RouterLink :to="{ name: 'register' }">Inscription</RouterLink>
    </p>
  </main>
</template>
