<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

function logout() {
  authStore.clear()
  router.push({ name: 'login' })
}
</script>

<template>
  <main>
    <h1>Backoffice PhotoPro</h1>
    <p>Connecte avec succes.</p>
    <button type="button" @click="logout">Se deconnecter</button>
  </main>
</template>
