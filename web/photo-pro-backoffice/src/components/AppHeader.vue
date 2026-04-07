<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import '../css/app-shell.css'
import logoUrl from '../img/logo.png'

const router = useRouter()
const authStore = useAuthStore()
const profileMenuOpen = ref(false)
const profileMenuRef = ref(null)

const userDisplayName = computed(() => authStore.displayName)

function toggleProfileMenu() {
  profileMenuOpen.value = !profileMenuOpen.value
}

function closeProfileMenu() {
  profileMenuOpen.value = false
}

function goToSettings() {
  closeProfileMenu()
  router.push({ name: 'settings' })
}

function logout() {
  closeProfileMenu()
  authStore.clear()
  router.push({ name: 'login' })
}

function handleClickOutside(event) {
  if (!profileMenuRef.value) {
    return
  }
  if (!profileMenuRef.value.contains(event.target)) {
    closeProfileMenu()
  }
}

onMounted(() => {
  authStore.loadFromStorage()
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <header class="app-shell__header">
    <div class="app-shell__header-left">
      <RouterLink :to="{ name: 'dashboard' }" class="app-shell__brand" aria-label="Accueil backoffice">
        <img class="app-shell__logo" :src="logoUrl" width="44" height="44" alt="" />
      </RouterLink>
      <ul class="app-shell__nav">
        <li>
          <RouterLink :to="{ name: 'dashboard' }">Accueil</RouterLink>
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
    <div class="app-shell__header-right" ref="profileMenuRef">
      <button
        type="button"
        class="app-shell__profile-trigger"
        :aria-expanded="profileMenuOpen ? 'true' : 'false'"
        aria-haspopup="menu"
        @click.stop="toggleProfileMenu"
      >
        <span class="app-shell__profile-name">{{ userDisplayName }}</span>
        <span class="app-shell__avatar" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
            />
          </svg>
        </span>
      </button>

      <div v-if="profileMenuOpen" class="app-shell__profile-menu" role="menu">
        <button type="button" class="app-shell__profile-menu-item" role="menuitem" @click="goToSettings">
          Paramètres
        </button>
        <button type="button" class="app-shell__profile-menu-item" role="menuitem" @click="logout">
          Se déconnecter
        </button>
      </div>
    </div>
  </header>
</template>
