<script setup>
import { ref } from 'vue'
import PhotoGrid from '../components/PhotoGrid.vue'

const selectedIds = ref([])

const photos = [
  {
    id: 'p1',
    title: 'mokujin',
    originalName: 'mokujin.jpg',
    mimeType: 'image/jpeg',
    url: 'https://i.ytimg.com/vi/P-dcIT1MtFM/maxresdefault.jpg',
    thumbnailUrl: 'https://i.ytimg.com/vi/P-dcIT1MtFM/maxresdefault.jpg',
  },
  {
    id: 'p2',
    title: 'mokujin2',
    originalName: 'mokujin2.jpg',
    mimeType: 'image/jpeg',
    url: 'https://i.ytimg.com/vi/x9inDl2UPcA/maxresdefault.jpg',
    thumbnailUrl: 'https://i.ytimg.com/vi/x9inDl2UPcA/maxresdefault.jpg',
  },
  {
    id: 'p3',
    title: 'Jinton',
    originalName: 'jinton.png',
    mimeType: 'image/jpeg',
    url: 'https://static.wikia.nocookie.net/naruto/images/d/d9/Mu_using_Jinton.png/revision/latest/scale-to-width-down/985?cb=20130117144630',
    thumbnailUrl:
      'https://static.wikia.nocookie.net/naruto/images/d/d9/Mu_using_Jinton.png/revision/latest/scale-to-width-down/985?cb=20130117144630',
  },
]

function toggleSelection(id) {
  const list = selectedIds.value
  const i = list.indexOf(id)
  if (i >= 0) {
    list.splice(i, 1)
  } else {
    list.push(id)
  }
}

function clearSelection() {
  selectedIds.value = []
}
</script>

<template>
  <main>
    <h1>Photos</h1>
    <nav>
      <RouterLink :to="{ name: 'photos' }">Photos</RouterLink> |
      <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink> |
      <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
    </nav>
    <p v-if="selectedIds.length">
      {{ selectedIds.length }} photo(s) selectionnee(s).
      <button type="button" @click="clearSelection">Tout deselectionner</button>
    </p>
    <p v-else>Aucune sélection</p>
    <PhotoGrid :photos="photos" :selected-ids="selectedIds" @toggle="toggleSelection" />
  </main>
</template>
