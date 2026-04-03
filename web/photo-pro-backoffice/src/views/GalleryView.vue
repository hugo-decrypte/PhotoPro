<script setup>
import { ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'
import { publishGallery, unpublishGallery } from '../services/galleryApi'
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
const actionError = ref('')
const actionLoadingId = ref('')

async function togglePublish(gallery) {
  actionError.value = ''
  actionLoadingId.value = gallery.id
  try {
    if (gallery.isPublished) {
      await unpublishGallery(gallery.id)
      gallery.isPublished = false
    } else {
      await publishGallery(gallery.id)
      gallery.isPublished = true
    }
  } catch (err) {
    actionError.value = err.message || 'Action impossible.'
  } finally {
    actionLoadingId.value = ''
  }
}
</script>

<template>
  <div class="app-shell">
    <AppHeader />

    <main class="app-shell__main">
      <h1 class="app-shell__title">Galeries</h1>
      <p v-if="actionError" class="gallery-action-error" role="alert">{{ actionError }}</p>

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
                :disabled="actionLoadingId === gallery.id"
                @click="togglePublish(gallery)"
              >
                {{
                  actionLoadingId === gallery.id
                    ? 'Patientez…'
                    : gallery.isPublished
                      ? 'Dépublier'
                      : 'Publier'
                }}
              </button>
              <small class="gallery-card__hint">Les erreurs backend sont affichées en haut de la page.</small>
            </div>
          </div>
        </article>
      </div>
    </main>
  </div>
</template>

<style scoped>
.gallery-action-error {
  margin: 0 0 1rem;
  color: #c62828;
  font-size: 0.9rem;
}
</style>
