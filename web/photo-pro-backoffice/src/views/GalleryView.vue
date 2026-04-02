<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import '../css/gallery-view.css'
import logoUrl from '../img/logo.png'

const router = useRouter()
const authStore = useAuthStore()

const galleries = ref([
  {
    id: 'g1',
    title: 'Mariage - Juin',
    visibility: 'public',
    isPublished: true,
    photosCount: 12,
  },
  {
    id: 'g2',
    title: 'Portrait prive client A',
    visibility: 'private',
    isPublished: false,
    photosCount: 4,
  },
  {
    id: 'g3',
    title: 'Evenement entreprise',
    visibility: 'public',
    isPublished: false,
    photosCount: 0,
  },
])

function togglePublish(gallery) {
  if (!gallery.isPublished && gallery.photosCount === 0) {
    return
  }
  gallery.isPublished = !gallery.isPublished
}

function logout() {
  authStore.clear()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="gallery-page">
    <header class="gallery-header">
      <div class="gallery-header__left">
        <img class="gallery-header__logo" :src="logoUrl" width="44" height="44" alt="PhotoPro" />
        <ul class="gallery-header__nav">
          <li>
            <RouterLink :to="{ name: 'dashboard' }">Menu</RouterLink>
          </li>
          <li>
            <RouterLink :to="{ name: 'photos' }">Photos</RouterLink>
          </li>
          <li>
            <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink>
          </li>
        </ul>
      </div>
      <div class="gallery-header__right">
        <button type="button" class="gallery-header__logout" @click="logout">Se déconnecter</button>
        <div class="gallery-header__avatar" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
          </svg>
        </div>
      </div>
    </header>

    <main class="gallery-main">
      <h1 class="gallery-main__title">Galeries</h1>

      <div class="gallery-grid">
        <article v-for="gallery in galleries" :key="gallery.id" class="gallery-card">
          <div class="gallery-card__cover" aria-hidden="true" />
          <div class="gallery-card__footer">
            <span
              class="gallery-card__badge"
              :class="
                gallery.visibility === 'private' ? 'gallery-card__badge--private' : 'gallery-card__badge--public'
              "
            >
              {{ gallery.visibility === 'private' ? 'Privé' : 'Public' }}
            </span>
            <h2 class="gallery-card__title">{{ gallery.title }}</h2>
            <p class="gallery-card__status">
              {{ gallery.isPublished ? 'Publiée' : 'Non publiée' }} · {{ gallery.photosCount }} photo(s)
            </p>
            <div class="gallery-card__actions">
              <RouterLink :to="{ name: 'gallery-new' }">Éditer</RouterLink>
              <button
                type="button"
                :disabled="!gallery.isPublished && gallery.photosCount === 0"
                @click="togglePublish(gallery)"
              >
                {{ gallery.isPublished ? 'Dépublier' : 'Publier' }}
              </button>
              <small v-if="!gallery.isPublished && gallery.photosCount === 0" class="gallery-card__hint">
                Ajoutez au moins une photo pour publier.
              </small>
            </div>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>
