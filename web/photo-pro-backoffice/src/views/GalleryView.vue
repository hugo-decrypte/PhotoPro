<script setup>
import { ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'
import '../css/gallery-view.css'

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
</script>

<template>
  <div class="app-shell">
    <AppHeader />

    <main class="app-shell__main">
      <h1 class="app-shell__title">Galeries</h1>

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
