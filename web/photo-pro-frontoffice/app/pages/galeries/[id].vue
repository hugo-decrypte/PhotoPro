<template>
  <v-container v-if="!pending && gallery && !data?.private" class="py-8">

    <!-- Header -->
    <div class="gallery-header mb-6">
      <div class="cover-wrapper">
        <img
            v-if="gallery.cover_photo_id"
            :src="`${s3Endpoint}/photopro-photos/${gallery.cover_photo_id}`"
            :alt="gallery.title"
            class="cover-img"
        />
        <div v-else class="cover-placeholder">
          <v-icon size="32" color="primary">mdi-image-outline</v-icon>
        </div>
      </div>

      <div class="gallery-info">
        <h1 class="gallery-title">{{ gallery.title }}</h1>

        <p v-if="gallery.description" class="gallery-desc">
          {{ gallery.description }}
        </p>

        <div class="d-flex gap-2 mt-2">
          <v-chip size="small" color="primary" variant="tonal">
            {{ photos.length }} photos
          </v-chip>

          <v-chip
              v-if="gallery.published_at"
              size="small"
              color="grey"
              variant="tonal"
          >
            {{ new Date(gallery.published_at).toLocaleDateString('fr-FR') }}
          </v-chip>
        </div>
      </div>
    </div>

    <!-- Switch -->
    <div class="d-flex align-center gap-3 mb-6">
      <v-btn
          :variant="mode === 'grid' ? 'flat' : 'outlined'"
          color="primary"
          size="small"
          @click="mode = 'grid'"
      >
        Grille
      </v-btn>

      <v-btn
          :variant="mode === 'slideshow' ? 'flat' : 'outlined'"
          color="primary"
          size="small"
          @click="mode = 'slideshow'"
      >
        Slideshow
      </v-btn>
    </div>

    <!-- GRID -->
    <div v-if="mode === 'grid'" class="masonry-grid">
      <div
          v-for="photo in photos"
          :key="photo.photo_id"
          class="photo-card"
          @click="openSlideshow(photo.photo_id)"
      >
        <img
            :src="`${s3Endpoint}/photopro-photos/${photo.photo_id}`"
            class="photo-img"
        />
      </div>
    </div>

    <!-- SLIDESHOW -->
    <div v-if="mode === 'slideshow'" class="slideshow-wrapper">
      <div class="slideshow-main">
        <img
            v-if="photos.length > 0 && photos[currentIndex]"
            :src="`${s3Endpoint}/photopro-photos/${photos[currentIndex]?.photo_id}`"
            class="slideshow-img"
        />

        <button class="slide-btn slide-left" @click="prev">‹</button>
        <button class="slide-btn slide-right" @click="next">›</button>

        <div class="slide-counter">
          {{ currentIndex + 1 }} / {{ photos.length }}
        </div>
      </div>
    </div>

  </v-container>

  <!-- LOADING -->
  <div v-else-if="pending" class="text-center py-16">
    Chargement...
  </div>

  <!-- ERROR -->
  <div v-else class="text-center py-16">
    Galerie introuvable
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const config = useRuntimeConfig()
const s3Endpoint = config.public.s3Endpoint || 'http://localhost:8333'

const { data, pending, error } = await useAsyncData(
    `gallery-${route.params.id}-${route.query.code}`,
    () => $fetch(`/api/galleries/${route.params.id}`, {
      params: { code: route.query.code }
    })
)

// 🔥 Redirection immédiate si privée
watchEffect(() => {
  if (data.value?.private) {
    navigateTo(`/galeries/${route.params.id}/privee`)
  }
})

const gallery = computed(() => data.value?.gallery)
const photos = computed(() => data.value?.photos ?? [])

const mode = ref<'grid' | 'slideshow'>('grid')
const currentIndex = ref(0)

function openSlideshow(photoId: string) {
  const idx = photos.value.findIndex(p => p.photo_id === photoId)
  currentIndex.value = idx
  mode.value = 'slideshow'
}

function prev() {
  currentIndex.value =
      (currentIndex.value - 1 + photos.value.length) % photos.value.length
}

function next() {
  currentIndex.value =
      (currentIndex.value + 1) % photos.value.length
}
</script>

<style scoped>
.gallery-header { display: flex; gap: 1.5rem; align-items: flex-start; }
.cover-wrapper { flex-shrink: 0; }
.cover-img { width: 120px; height: 90px; object-fit: cover; border-radius: 8px; }
.cover-placeholder { width: 120px; height: 90px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.gallery-title { font-size: 22px; font-weight: 500; color: #051630; }
.gallery-desc { font-size: 14px; color: #555; margin-top: 4px; }

.masonry-grid { columns: 3; gap: 12px; }
.photo-card { break-inside: avoid; margin-bottom: 12px; border-radius: 8px; overflow: hidden; cursor: pointer; }
.photo-card:hover { opacity: 0.9; }
.photo-img { width: 100%; display: block; border-radius: 8px; }

.slideshow-wrapper { background: #111; border-radius: 12px; overflow: hidden; }
.slideshow-main { position: relative; }
.slideshow-img { width: 100%; max-height: 600px; object-fit: contain; display: block; }
.slide-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.slide-left { left: 12px; }
.slide-right { right: 12px; }
.slide-counter { position: absolute; bottom: 12px; right: 12px; background: rgba(0,0,0,0.6); color: #4FD180; font-size: 12px; padding: 3px 10px; border-radius: 10px; }
.thumbs-bar { display: flex; gap: 6px; padding: 10px; background: #222; overflow-x: auto; }
.thumb { width: 60px; height: 45px; object-fit: cover; border-radius: 4px; cursor: pointer; opacity: 0.6; }
.thumb.active { opacity: 1; outline: 2px solid #4FD180; }

@media (max-width: 960px) { .masonry-grid { columns: 2; } }
@media (max-width: 600px) { .masonry-grid { columns: 1; } }
</style>