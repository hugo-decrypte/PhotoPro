<template>
  <v-container class="py-8">
    <div class="d-flex align-center justify-space-between mb-6">
      <h1 style="font-size: 22px; font-weight: 500; color: #051630;">
        Galeries publiques
      </h1>
      <div class="d-flex align-center ga-3">
        <v-btn
          variant="tonal"
          color="primary"
          prepend-icon="mdi-lock-open-outline"
          size="small"
          @click="showPrivateDialog = true"
        >
          Accéder à une galerie privée
        </v-btn>
        <span style="font-size: 13px; color: #999;">
          {{ galleries.length }} galeries
        </span>
      </div>
    </div>

    <!-- Dialogue pour galerie privée -->
    <v-dialog v-model="showPrivateDialog" max-width="450">
      <v-card class="pa-4">
        <v-card-title class="text-h6 pb-2">Accès galerie privée</v-card-title>
        <v-card-text>
          <p class="text-body-2 text-medium-emphasis mb-4">
            Veuillez saisir les informations de votre accès privé.
          </p>
          
          <v-text-field
            v-model="privateId"
            label="ID de la galerie"
            placeholder="Ex: b3000003-..."
            variant="outlined"
            density="comfortable"
            class="mb-3"
            hide-details
            :loading="dialogLoading"
            @keyup.enter="goToPrivate"
          ></v-text-field>

          <v-text-field
            v-model="privateCode"
            label="Code secret / Mot de passe"
            placeholder="Ex: MARIAGE2024"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-key"
            :error-messages="dialogError"
            @input="dialogError = ''"
            :loading="dialogLoading"
            @keyup.enter="goToPrivate"
          ></v-text-field>
        </v-card-text>
        <v-card-actions class="pt-0 px-6 pb-6">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showPrivateDialog = false">Annuler</v-btn>
          <v-btn 
            color="primary" 
            variant="flat" 
            :loading="dialogLoading"
            :disabled="!privateId.trim() || !privateCode.trim() || dialogLoading" 
            @click="goToPrivate"
          >
            Accéder à la galerie
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div class="masonry-grid">
      <NuxtLink
          v-for="gallery in galleries"
          :key="gallery.id"
          :to="`/galeries/${gallery.id}`"
          class="gallery-card"
      >
        <div class="card-img-wrapper">
          <img
              v-if="gallery.cover_photo_id"
              :src="`${s3Endpoint}/photopro-photos/${gallery.cover_photo_id}`"
              :alt="gallery.title"
              class="card-img"
          />
          <div v-else class="card-img-placeholder">
            <v-icon size="40" color="primary">mdi-image-outline</v-icon>
          </div>
        </div>

        <div class="card-overlay">
          <div class="card-title">{{ gallery.title }}</div>
          <div class="card-meta">{{ gallery.photo_count }} photos</div>
        </div>
      </NuxtLink>
    </div>

    <div v-if="!pending && galleries.length === 0" class="text-center py-16">
      <v-icon size="64" color="grey-lighten-2">mdi-image-off-outline</v-icon>
      <p style="color: #999; margin-top: 1rem;">
        Aucune galerie publiée pour le moment
      </p>
    </div>
  </v-container>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const s3Endpoint = config.public.s3Endpoint || 'http://localhost:8333'

const showPrivateDialog = ref(false)
const privateId = ref('')
const privateCode = ref('')
const dialogLoading = ref(false)
const dialogError = ref('')

const { data, pending, error } = await useAsyncData('galleries', () =>
    $fetch('/api/galleries')
)

const galleries = computed(() => data.value ?? [])

async function goToPrivate() {
  if (!privateId.value.trim() || !privateCode.value.trim()) return

  dialogLoading.value = true
  dialogError.value = ''

  try {
    // On vérifie le couple ID/Code via l'API Nuxt locale
    await $fetch(`/api/galleries/${privateId.value.trim()}`, {
      params: { code: privateCode.value.trim() }
    })

    // Si ça passe (pas de 403/404), on navigue
    navigateTo(`/galeries/${privateId.value.trim()}?code=${privateCode.value.trim()}`)
    showPrivateDialog.value = false
  } catch (err: any) {
    if (err.statusCode === 403 || err.statusCode === 404) {
      dialogError.value = "Identifiant ou code secret invalide."
    } else {
      dialogError.value = "Une erreur est survenue lors de la vérification."
    }
  } finally {
    dialogLoading.value = false
  }
}
</script>

<style scoped>
.masonry-grid {
  columns: 3;
  gap: 16px;
}

.gallery-card {
  break-inside: avoid;
  margin-bottom: 16px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  display: block;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.2s;
}

.gallery-card:hover {
  transform: scale(1.01);
}

.card-img-wrapper {
  width: 100%;
}

.card-img {
  width: 100%;
  display: block;
  border-radius: 12px;
}

.card-img-placeholder {
  width: 100%;
  height: 200px;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
}

.card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(5, 22, 48, 0.75));
  padding: 12px;
  border-radius: 0 0 12px 12px;
}

.card-title {
  color: #fff;
  font-size: 14px;
  font-weight: 500;
}

.card-meta {
  color: #4FD180;
  font-size: 12px;
  margin-top: 2px;
}

@media (max-width: 960px) {
  .masonry-grid { columns: 2; }
}

@media (max-width: 600px) {
  .masonry-grid { columns: 1; }
}
</style>