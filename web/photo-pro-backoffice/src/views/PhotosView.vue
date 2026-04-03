<script setup>
import { ref } from 'vue'
import AppHeader from '../components/AppHeader.vue'
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
  <div class="app-shell">
    <AppHeader />
    <main class="app-shell__main">
      <h1 class="app-shell__title">Photos</h1>
      <p v-if="selectedIds.length">
        {{ selectedIds.length }} photo(s) sélectionnée(s).
        <button type="button" class="photos-toolbar__btn" @click="clearSelection">Tout désélectionner</button>
      </p>
      <p v-else class="photos-toolbar__hint">Aucune sélection</p>
      <PhotoGrid :photos="photos" :selected-ids="selectedIds" @toggle="toggleSelection" />
    </main>
  </div>
</template>

<style scoped>
.photos-toolbar__hint {
  margin: 0 0 1rem;
  color: #6b6b78;
  font-size: 0.9rem;
}

.photos-toolbar__btn {
  margin-left: 0.5rem;
  padding: 0.35rem 0.75rem;
  border: 1px solid #94a8f9;
  border-radius: 6px;
  background: #f0f2ff;
  color: #4a5fc9;
  font-size: 0.85rem;
  font-weight: 500;
  cursor: pointer;
}

.photos-toolbar__btn:hover {
  background: #e4e8ff;
}
</style>
