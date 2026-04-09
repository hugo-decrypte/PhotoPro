<template>
  <div v-if="!pending && gallery" class="gallery-page">

    <!-- Hero Header -->
    <div class="hero">
      <div class="hero-bg" :style="gallery.cover_photo_url ? `background-image: url('${gallery.cover_photo_url}')` : ''">
        <div class="hero-overlay" />
      </div>
      <div class="hero-content">
        <NuxtLink to="/" class="back-btn">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Galeries
        </NuxtLink>
        <h1 class="hero-title">{{ gallery.title }}</h1>
        <p v-if="gallery.description" class="hero-desc">{{ gallery.description }}</p>
        <div class="hero-meta">
          <span class="meta-chip">{{ photos.length }} photos</span>
          <span v-if="gallery.published_at" class="meta-chip">
            {{ new Date(gallery.published_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Controls -->
    <div class="controls-bar">
      <div class="view-toggle">
        <button :class="['toggle-btn', { active: mode === 'grid' }]" @click="mode = 'grid'">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor">
            <rect x="0" y="0" width="6" height="6" rx="1"/><rect x="8" y="0" width="6" height="6" rx="1"/>
            <rect x="0" y="8" width="6" height="6" rx="1"/><rect x="8" y="8" width="6" height="6" rx="1"/>
          </svg>
          Grille
        </button>
        <button :class="['toggle-btn', { active: mode === 'slideshow' }]" @click="mode = 'slideshow'">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="1" y="3" width="12" height="8" rx="1"/><path d="M5.5 5.5l3 2-3 2V5.5z" fill="currentColor" stroke="none"/>
          </svg>
          Diaporama
        </button>
      </div>
    </div>

    <!-- GRID -->
    <div v-if="mode === 'grid'" class="photo-grid-wrapper">
      <div class="photo-masonry">
        <div
            v-for="photo in paginatedPhotos"
            :key="photo.id"
            class="photo-item"
            @click="openLightbox(photo.id)"
        >
          <img :src="photo.url" :alt="photo.title || 'Photo'" class="photo-img" loading="lazy" />
          <div class="photo-hover">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="white" stroke-width="1.5">
              <circle cx="9" cy="9" r="6"/><path d="M13.5 13.5L17 17"/><path d="M9 7v4M7 9h4"/>
            </svg>
          </div>
        </div>
      </div>

      <div v-if="photos.length > photosPerPage" class="pagination">
        <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
        <span class="page-info">{{ currentPage }} / {{ Math.ceil(photos.length / photosPerPage) }}</span>
        <button class="page-btn" :disabled="currentPage >= Math.ceil(photos.length / photosPerPage)" @click="currentPage++">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
      </div>
    </div>

    <!-- SLIDESHOW -->
    <div v-if="mode === 'slideshow'" class="slideshow">
      <div class="slide-main">
        <img :src="photos[currentIndex]?.url" class="slide-img" />
        <button class="slide-arrow left" @click="prev">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M13 4L7 10l6 6" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <button class="slide-arrow right" @click="next">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7 4l6 6-6 6" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="slide-counter">{{ currentIndex + 1 }} / {{ photos.length }}</div>
      </div>
      <div class="thumbs">
        <img
            v-for="(photo, i) in photos"
            :key="photo.id"
            :src="photo.url"
            :class="['thumb', { active: i === currentIndex }]"
            @click="currentIndex = i"
        />
      </div>
    </div>

    <!-- COMMENTS -->
    <div class="comments-section">
      <div class="comments-inner">
        <h2 class="section-title">Commentaires <span class="count-badge">{{ comments.length }}</span></h2>

        <div v-if="comments.length > 0" class="comments-list">
          <div v-for="comment in comments" :key="comment.id" class="comment-card">
            <div class="comment-avatar">{{ (comment.author_name || 'A')[0].toUpperCase() }}</div>
            <div class="comment-body">
              <div class="comment-header">
                <span class="comment-author">{{ comment.author_name || 'Anonyme' }}</span>
                <span class="comment-date">{{ new Date(comment.created_at).toLocaleDateString('fr-FR') }}</span>
              </div>
              <p class="comment-text">{{ comment.content }}</p>
            </div>
          </div>
        </div>
        <p v-else class="no-comments">Aucun commentaire.</p>
      </div>
    </div>


    <!-- LIGHTBOX -->
    <Teleport to="body">
      <div v-if="lightboxOpen" class="lightbox" @click.self="lightboxOpen = false">
        <button class="lb-close" @click="lightboxOpen = false">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4 4l12 12M16 4L4 16" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <button class="lb-arrow lb-prev" @click="lightboxPrev">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 5L9 12l6 7" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <img :src="photos[lightboxIndex]?.url" class="lb-img" />
        <button class="lb-arrow lb-next" @click="lightboxNext">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 5l6 7-6 7" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="lb-counter">{{ lightboxIndex + 1 }} / {{ photos.length }}</div>
      </div>
    </Teleport>
  </div>

  <div v-else-if="pending" class="loading-state">
    <div class="spinner" />
    <p>Chargement...</p>
  </div>

  <div v-else class="error-state">
    <p>Galerie introuvable.</p>
    <NuxtLink to="/">Retour aux galeries</NuxtLink>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()

const { data, pending } = await useAsyncData(
    `gallery-${route.params.id}-${route.query.code}`,
    () => $fetch(`/api/galleries/${route.params.id}`, {
      params: { code: route.query.code }
    })
)

watchEffect(() => {
  if (data.value && (data.value as any).private) {
    navigateTo(`/galeries/${route.params.id}/privee`)
  }
})

const gallery = computed(() => (data.value as any)?.gallery)
const photos = computed(() => (data.value as any)?.photos ?? [])

const photosPerPage = 12
const currentPage = ref(1)
const paginatedPhotos = computed(() => {
  const start = (currentPage.value - 1) * photosPerPage
  return photos.value.slice(start, start + photosPerPage)
})

const lightboxOpen = ref(false)
const lightboxIndex = ref(0)
function openLightbox(photoId: string) {
  lightboxIndex.value = photos.value.findIndex((p: any) => p.id === photoId)
  lightboxOpen.value = true
}
function lightboxPrev() {
  lightboxIndex.value = (lightboxIndex.value - 1 + photos.value.length) % photos.value.length
}
function lightboxNext() {
  lightboxIndex.value = (lightboxIndex.value + 1) % photos.value.length
}

const mode = ref<'grid' | 'slideshow'>('grid')
const currentIndex = ref(0)
function prev() {
  currentIndex.value = (currentIndex.value - 1 + photos.value.length) % photos.value.length
}
function next() {
  currentIndex.value = (currentIndex.value + 1) % photos.value.length
}

const { data: commentsData, refresh } = await useAsyncData(
    `comments-${route.params.id}`,
    () => $fetch(`/api/galleries/${route.params.id}/comments`)
)
const comments = computed(() => (commentsData.value as any)?.data ?? [])
const newComment = ref({
  author_name: '',
  content: '',
  photo_id: '00000000-0000-0000-0000-000000000000' // UUID vide ou fixe
});

async function submitComment() {
  if (!newComment.value.content.trim()) return;
  try {
    await $fetch(`/api/galleries/${route.params.id}/photos/${newComment.value.photo_id}/comments`, {
      method: 'POST',
      body: newComment.value,
    });
    newComment.value = { author_name: '', content: '', photo_id: '00000000-0000-0000-0000-000000000000' };
    await refresh();
  } catch {
    alert('Erreur lors de l\'envoi du commentaire');
  }
}


</script>

<style scoped>
/* ── Base ── */
.gallery-page {
  min-height: 100vh;
  background: #0a0a0a;
  color: #f0ece4;
  font-family: 'Georgia', serif;
}

/* ── Hero ── */
.hero {
  position: relative;
  height: 60vh;
  min-height: 400px;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
}
.hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  background-color: #1a1a1a;
  transform: scale(1.05);
  transition: transform 0.8s ease;
}
.hero:hover .hero-bg { transform: scale(1); }
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(10,10,10,0.95) 0%, rgba(10,10,10,0.3) 60%, transparent 100%);
}
.hero-content {
  position: relative;
  z-index: 1;
  padding: 2.5rem 3rem;
  width: 100%;
  max-width: 900px;
}
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: rgba(240,236,228,0.6);
  text-decoration: none;
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 1.5rem;
  transition: color 0.2s;
}
.back-btn:hover { color: #f0ece4; }
.hero-title {
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 400;
  line-height: 1.1;
  margin: 0 0 0.75rem;
  letter-spacing: -0.02em;
}
.hero-desc {
  font-size: 1rem;
  color: rgba(240,236,228,0.65);
  margin: 0 0 1.25rem;
  max-width: 560px;
  line-height: 1.6;
  font-style: italic;
}
.hero-meta {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.meta-chip {
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(240,236,228,0.5);
  border: 1px solid rgba(240,236,228,0.2);
  padding: 4px 12px;
  border-radius: 20px;
}

/* ── Controls ── */
.controls-bar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 1rem 3rem;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.view-toggle {
  display: flex;
  gap: 4px;
  background: rgba(255,255,255,0.05);
  border-radius: 8px;
  padding: 4px;
}
.toggle-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border: none;
  border-radius: 6px;
  background: transparent;
  color: rgba(240,236,228,0.5);
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}
.toggle-btn.active {
  background: rgba(255,255,255,0.12);
  color: #f0ece4;
}

/* ── Photo Grid ── */
.photo-grid-wrapper { padding: 2rem 3rem 3rem; }
.photo-masonry {
  columns: 3;
  column-gap: 12px;
}
.photo-item {
  break-inside: avoid;
  margin-bottom: 12px;
  border-radius: 6px;
  overflow: hidden;
  position: relative;
  cursor: pointer;
}
.photo-img {
  width: 100%;
  display: block;
  transition: transform 0.4s ease;
}
.photo-item:hover .photo-img { transform: scale(1.03); }
.photo-hover {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s;
}
.photo-item:hover .photo-hover {
  background: rgba(0,0,0,0.3);
  opacity: 1;
}

/* ── Pagination ── */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 2.5rem;
}
.page-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid rgba(255,255,255,0.15);
  background: transparent;
  color: #f0ece4;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.page-btn:hover:not(:disabled) { background: rgba(255,255,255,0.1); }
.page-btn:disabled { opacity: 0.3; cursor: default; }
.page-info {
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  color: rgba(240,236,228,0.5);
}

/* ── Slideshow ── */
.slideshow { padding: 2rem 3rem 3rem; }
.slide-main {
  position: relative;
  background: #111;
  border-radius: 8px;
  overflow: hidden;
  aspect-ratio: 16/9;
  display: flex;
  align-items: center;
  justify-content: center;
}
.slide-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
.slide-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(0,0,0,0.5);
  border: 1px solid rgba(255,255,255,0.2);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.slide-arrow:hover { background: rgba(0,0,0,0.8); }
.slide-arrow.left { left: 1rem; }
.slide-arrow.right { right: 1rem; }
.slide-counter {
  position: absolute;
  bottom: 1rem;
  right: 1rem;
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  color: rgba(255,255,255,0.6);
  letter-spacing: 0.05em;
}
.thumbs {
  display: flex;
  gap: 6px;
  margin-top: 12px;
  overflow-x: auto;
  padding-bottom: 4px;
}
.thumb {
  width: 72px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
  cursor: pointer;
  opacity: 0.5;
  transition: opacity 0.2s;
  flex-shrink: 0;
  border: 2px solid transparent;
}
.thumb.active, .thumb:hover { opacity: 1; border-color: rgba(255,255,255,0.5); }

/* ── Comments ── */
.comments-section {
  background: #0f0f0f;
  border-top: 1px solid rgba(255,255,255,0.06);
  padding: 3rem;
}
.comments-inner { max-width: 720px; margin: 0 auto; }
.section-title {
  font-size: 1.3rem;
  font-weight: 400;
  margin: 0 0 2rem;
  display: flex;
  align-items: center;
  gap: 10px;
}
.count-badge {
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  background: rgba(255,255,255,0.08);
  color: rgba(240,236,228,0.6);
  padding: 2px 10px;
  border-radius: 20px;
}
.comments-list { display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 3rem; }
.comment-card {
  display: flex;
  gap: 1rem;
}
.comment-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: rgba(240,236,228,0.7);
  flex-shrink: 0;
}
.comment-body { flex: 1; }
.comment-header {
  display: flex;
  align-items: baseline;
  gap: 10px;
  margin-bottom: 6px;
}
.comment-author {
  font-family: 'system-ui', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: #f0ece4;
}
.comment-date {
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  color: rgba(240,236,228,0.35);
}
.comment-text {
  margin: 0;
  font-size: 15px;
  line-height: 1.65;
  color: rgba(240,236,228,0.75);
}
.no-comments {
  font-style: italic;
  color: rgba(240,236,228,0.35);
  margin: 0 0 3rem;
}

/* ── Form ── */
.comment-form { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 2rem; }
.form-title {
  font-size: 1rem;
  font-weight: 400;
  margin: 0 0 1.25rem;
  color: rgba(240,236,228,0.7);
  font-style: italic;
}
.form-input, .form-textarea {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 6px;
  padding: 10px 14px;
  color: #f0ece4;
  font-family: 'system-ui', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
  margin-bottom: 10px;
  resize: vertical;
}
.form-input:focus, .form-textarea:focus { border-color: rgba(255,255,255,0.3); }
.form-input::placeholder, .form-textarea::placeholder { color: rgba(240,236,228,0.3); }
.submit-btn {
  padding: 10px 28px;
  background: #f0ece4;
  color: #0a0a0a;
  border: none;
  border-radius: 6px;
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.04em;
  cursor: pointer;
  transition: opacity 0.2s;
}
.submit-btn:hover { opacity: 0.85; }

/* ── Lightbox ── */
.lightbox {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(0,0,0,0.95);
  display: flex;
  align-items: center;
  justify-content: center;
}
.lb-img {
  max-width: 90vw;
  max-height: 90vh;
  object-fit: contain;
  border-radius: 4px;
}
.lb-close {
  position: fixed;
  top: 1.5rem;
  right: 1.5rem;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255,255,255,0.1);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.lb-close:hover { background: rgba(255,255,255,0.2); }
.lb-arrow {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.lb-arrow:hover { background: rgba(255,255,255,0.15); }
.lb-prev { left: 1.5rem; }
.lb-next { right: 1.5rem; }
.lb-counter {
  position: fixed;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  color: rgba(255,255,255,0.4);
  letter-spacing: 0.08em;
}

/* ── States ── */
.loading-state, .error-state {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  background: #0a0a0a;
  color: rgba(240,236,228,0.5);
  font-family: 'system-ui', sans-serif;
}
.spinner {
  width: 32px;
  height: 32px;
  border: 2px solid rgba(255,255,255,0.1);
  border-top-color: rgba(255,255,255,0.5);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Responsive ── */
@media (max-width: 768px) {
  .hero-content, .controls-bar, .photo-grid-wrapper, .slideshow, .comments-section {
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }
  .photo-masonry { columns: 2; }
}
@media (max-width: 480px) {
  .photo-masonry { columns: 1; }
}
</style>