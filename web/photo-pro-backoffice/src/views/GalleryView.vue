<script setup>
import { ref } from 'vue'

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
  <main>
    <h1>Galeries</h1>
    <nav>
      <RouterLink :to="{ name: 'photos' }">Photos</RouterLink> |
      <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink> |
      <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
    </nav>

    <ul>
      <li v-for="gallery in galleries" :key="gallery.id">
        <strong>{{ gallery.title }}</strong>
        -
        <span>{{ gallery.visibility === 'private' ? 'Privee' : 'Publique' }}</span>
        -
        <span>{{ gallery.isPublished ? 'Publiee' : 'Non publiee' }}</span>
        -
        <span>{{ gallery.photosCount }} photo(s)</span>
        <div>
          <RouterLink :to="{ name: 'gallery-new' }">Editer</RouterLink>
          <button type="button" @click="togglePublish(gallery)">
            {{ gallery.isPublished ? 'Depublier' : 'Publier' }}
          </button>
          <small v-if="!gallery.isPublished && gallery.photosCount === 0">
            Ajoutez au moins une photo pour publier.
          </small>
        </div>
      </li>
    </ul>
  </main>
</template>
