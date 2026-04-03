<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import '../css/app-shell.css'
import logoUrl from '../img/logo.png'

const router = useRouter()
const authStore = useAuthStore()

function logout() {
  authStore.clear()
  router.push({ name: 'login' })
}
</script>

<template>
  <header class="app-shell__header">
    <div class="app-shell__header-left">
      <RouterLink :to="{ name: 'dashboard' }" class="app-shell__brand" aria-label="Accueil backoffice">
        <img class="app-shell__logo" :src="logoUrl" width="44" height="44" alt="" />
      </RouterLink>
      <ul class="app-shell__nav">
        <li>
          <RouterLink :to="{ name: 'dashboard' }">Menu</RouterLink>
        </li>
        <li>
          <RouterLink :to="{ name: 'photos' }">Photos</RouterLink>
        </li>
        <li>
          <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink>
        </li>
        <li>
          <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
        </li>
      </ul>
    </div>
    <div class="app-shell__header-right">
      <button type="button" class="app-shell__logout" @click="logout">Se déconnecter</button>
      <div class="app-shell__avatar" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
          <path
            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
          />
        </svg>
      </div>
    </div>
  </header>
</template>
