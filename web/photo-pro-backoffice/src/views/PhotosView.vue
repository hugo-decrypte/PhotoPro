<script setup>
import PhotoGrid from '../components/PhotoGrid.vue'

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
</script>

<template>
  <main>
    <h1>Photos</h1>
    <PhotoGrid :photos="photos" />
  </main>
</template>
