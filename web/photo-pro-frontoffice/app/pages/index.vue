<template>
  <div class="galleries-page">

    <!-- Header -->
    <header class="page-header">
      <div class="header-inner">
        <div class="header-left">
          <p class="header-eyebrow">Photopro</p>
          <h1 class="header-title">Galeries publiques</h1>
        </div>
        <div class="header-right">
          <span class="gallery-count">{{ galleries.length }} galeries</span>
          <button class="private-btn" @click="showPrivateDialog = true">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="6" width="8" height="6" rx="1"/><path d="M5 6V4a2 2 0 0 1 4 0v2"/>
            </svg>
            Galerie privée
          </button>
        </div>
      </div>
    </header>

    <!-- Private gallery dialog -->
    <Teleport to="body">
      <div v-if="showPrivateDialog" class="dialog-overlay" @click.self="showPrivateDialog = false">
        <div class="dialog">
          <button class="dialog-close" @click="showPrivateDialog = false">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          </button>
          <div class="dialog-icon">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="4" y="10" width="14" height="10" rx="2"/><path d="M7 10V7a4 4 0 0 1 8 0v3"/>
            </svg>
          </div>
          <h2 class="dialog-title">Accès galerie privée</h2>
          <p class="dialog-desc">Saisissez les informations transmises par votre photographe.</p>

          <div class="field-group">
            <label class="field-label">Identifiant de la galerie</label>
            <input
                v-model="privateId"
                class="field-input"
                placeholder="Ex : b3000003-..."
                @keyup.enter="goToPrivate"
            />
          </div>
          <div class="field-group">
            <label class="field-label">Code secret</label>
            <input
                v-model="privateCode"
                class="field-input"
                placeholder="Ex : MARIAGE2024"
                @keyup.enter="goToPrivate"
            />
            <p v-if="dialogError" class="field-error">{{ dialogError }}</p>
          </div>

          <button
              class="dialog-submit"
              :disabled="!privateId.trim() || !privateCode.trim() || dialogLoading"
              @click="goToPrivate"
          >
            <span v-if="dialogLoading" class="btn-spinner" />
            <span v-else>Accéder à la galerie</span>
          </button>
        </div>
      </div>
    </Teleport>

    <!-- Gallery grid -->
    <main class="gallery-main">
      <div v-if="pending" class="loading-state">
        <div class="spinner" />
      </div>

      <div v-else-if="galleries.length === 0" class="empty-state">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1">
          <rect x="6" y="10" width="36" height="28" rx="3"/>
          <circle cx="18" cy="21" r="4"/><path d="M6 32l10-8 8 6 6-5 12 9"/>
        </svg>
        <p>Aucune galerie publiée pour le moment.</p>
      </div>

      <div v-else class="masonry">
        <NuxtLink
            v-for="gallery in galleries"
            :key="gallery.id"
            :to="`/galeries/${gallery.id}`"
            class="gallery-card"
        >
          <div class="card-media">
            <img
                v-if="gallery.cover_photo_url"
                :src="gallery.cover_photo_url"
                :alt="gallery.title"
                class="card-img"
                loading="lazy"
            />
            <div v-else class="card-placeholder">
              <svg width="32" height="32" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1">
                <rect x="3" y="6" width="26" height="20" rx="2"/><circle cx="11" cy="14" r="3"/>
                <path d="M3 22l8-6 6 5 4-4 8 7"/>
              </svg>
            </div>
          </div>
          <div class="card-overlay">
            <span class="card-title">{{ gallery.title }}</span>
            <span class="card-count">{{ gallery.photo_count }} photos</span>
          </div>
        </NuxtLink>
      </div>
    </main>

    <!-- Footer -->
    <footer class="page-footer">
      <span>© {{ new Date().getFullYear() }} Photopro — Tous droits réservés</span>
    </footer>
  </div>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const s3Endpoint = config.public.s3Endpoint || 'http://localhost:8888'

const showPrivateDialog = ref(false)
const privateId = ref('')
const privateCode = ref('')
const dialogLoading = ref(false)
const dialogError = ref('')

const { data, pending } = await useAsyncData('galleries', () => $fetch('/api/galleries'))
const galleries = computed(() => (data.value as any[]) ?? [])

async function goToPrivate() {
  if (!privateId.value.trim() || !privateCode.value.trim()) return
  dialogLoading.value = true
  dialogError.value = ''
  try {
    await $fetch(`/api/galleries/${privateId.value.trim()}`, {
      params: { code: privateCode.value.trim() }
    })
    navigateTo(`/galeries/${privateId.value.trim()}?code=${privateCode.value.trim()}`)
    showPrivateDialog.value = false
  } catch (err: any) {
    if (err.statusCode === 403 || err.statusCode === 404) {
      dialogError.value = 'Identifiant ou code secret invalide.'
    } else {
      dialogError.value = 'Une erreur est survenue.'
    }
  } finally {
    dialogLoading.value = false
  }

}
</script>

<style scoped>
/* ── Base ── */
.galleries-page {
  min-height: 100vh;
  background: #0a0a0a;
  color: #f0ece4;
  display: flex;
  flex-direction: column;
  font-family: 'Georgia', serif;
}

/* ── Header ── */
.page-header {
  padding: 2.5rem 3rem 2rem;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.header-inner {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
}
.header-eyebrow {
  font-family: 'system-ui', sans-serif;
  font-size: 11px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(240,236,228,0.35);
  margin: 0 0 0.4rem;
}
.header-title {
  font-size: clamp(1.8rem, 3vw, 2.8rem);
  font-weight: 400;
  margin: 0;
  letter-spacing: -0.02em;
  line-height: 1;
}
.header-right {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex-shrink: 0;
}
.gallery-count {
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  color: rgba(240,236,228,0.35);
  letter-spacing: 0.03em;
}
.private-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 18px;
  background: transparent;
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 6px;
  color: rgba(240,236,228,0.7);
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}
.private-btn:hover {
  background: rgba(255,255,255,0.06);
  color: #f0ece4;
  border-color: rgba(255,255,255,0.3);
}

/* ── Dialog ── */
.dialog-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(0,0,0,0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
.dialog {
  background: #161616;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 2.5rem;
  width: 100%;
  max-width: 420px;
  position: relative;
}
.dialog-close {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: rgba(255,255,255,0.07);
  color: rgba(240,236,228,0.6);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.dialog-close:hover { background: rgba(255,255,255,0.12); }
.dialog-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: rgba(255,255,255,0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(240,236,228,0.6);
  margin-bottom: 1.25rem;
}
.dialog-title {
  color : gainsboro;
  font-size: 1.2rem;
  font-weight: 400;
  margin: 0 0 0.5rem;
}
.dialog-desc {
  font-family: 'system-ui', sans-serif;
  font-size: 13px;
  color: gainsboro;
  margin: 0 0 1.75rem;
  line-height: 1.5;
}
.field-group { margin-bottom: 1rem; }
.field-label {
  display: block;
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  letter-spacing: 0.04em;
  color: gainsboro;
  margin-bottom: 6px;
  text-transform: uppercase;
}
.field-input {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 7px;
  padding: 10px 14px;
  color: #f0ece4;
  font-family: 'system-ui', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.field-input:focus { border-color: rgba(255,255,255,0.3); }
.field-input::placeholder { color: rgba(240,236,228,0.25); }
.field-error {
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  color: #e87070;
  margin: 6px 0 0;
}
.dialog-submit {
  width: 100%;
  margin-top: 1.5rem;
  padding: 11px;
  background: #f0ece4;
  color: #0a0a0a;
  border: none;
  border-radius: 7px;
  font-family: 'system-ui', sans-serif;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.2s;
}
.dialog-submit:disabled { opacity: 0.4; cursor: default; }
.dialog-submit:not(:disabled):hover { opacity: 0.88; }
.btn-spinner {
  color: gainsboro;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(10,10,10,0.2);
  border-top-color: #0a0a0a;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

/* ── Main ── */
.gallery-main { flex: 1; padding: 2.5rem 3rem; }

.loading-state {
  display: flex;
  justify-content: center;
  padding: 5rem 0;
}
.spinner {
  width: 32px;
  height: 32px;
  border: 2px solid rgba(255,255,255,0.08);
  border-top-color: rgba(255,255,255,0.4);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 5rem 0;
  color: rgba(240,236,228,0.25);
  font-family: 'system-ui', sans-serif;
  font-size: 14px;
}

/* ── Masonry ── */
.masonry {
  columns: 3;
  column-gap: 14px;
}
.gallery-card {
  break-inside: avoid;
  margin-bottom: 14px;
  display: block;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
  text-decoration: none;
  cursor: pointer;
  background: #1a1a1a;
}
.card-media { width: 100%; overflow: hidden; }
.card-img {
  width: 100%;
  display: block;
  transition: transform 0.5s ease;
}
.gallery-card:hover .card-img { transform: scale(1.04); }
.card-placeholder {
  width: 100%;
  height: 220px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(240,236,228,0.15);
  background: #1a1a1a;
}
.card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem 1rem 1rem;
  background: linear-gradient(transparent, rgba(5,5,5,0.85));
  display: flex;
  flex-direction: column;
  gap: 3px;
  border-radius: 0 0 8px 8px;
  opacity: 0;
  transform: translateY(4px);
  transition: all 0.3s ease;
}
.gallery-card:hover .card-overlay {
  opacity: 1;
  transform: translateY(0);
}
.card-title {
  color: #f0ece4;
  font-size: 15px;
  font-weight: 400;
  letter-spacing: -0.01em;
}
.card-count {
  font-family: 'system-ui', sans-serif;
  font-size: 11px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(240,236,228,0.45);
}

/* ── Footer ── */
.page-footer {
  padding: 1.5rem 3rem;
  border-top: 1px solid rgba(255,255,255,0.05);
  font-family: 'system-ui', sans-serif;
  font-size: 12px;
  color: rgba(240,236,228,0.2);
  letter-spacing: 0.03em;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  .page-header, .gallery-main { padding-left: 1.5rem; padding-right: 1.5rem; }
  .masonry { columns: 2; }
}
@media (max-width: 540px) {
  .header-inner { flex-direction: column; align-items: flex-start; }
  .masonry { columns: 1; }
}
</style>