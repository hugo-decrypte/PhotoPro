<template>
  <v-container class="py-12 d-flex justify-center">
    <v-card max-width="500" width="100%" class="pa-6" elevation="3">
      <div class="text-center mb-6">
        <v-icon icon="mdi-lock-outline" size="64" color="primary" class="mb-4"></v-icon>
        <h2 class="text-h5 font-weight-bold mb-2">Galerie Privée</h2>
        <p class="text-body-1 text-medium-emphasis">
          Cette galerie est protégée. Veuillez entrer le code d'accès que vous avez reçu par email pour la visionner.
        </p>
      </div>

      <v-form @submit.prevent="submitCode">
        <v-text-field
            v-model="accessCode"
            label="Code d'accès"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-key"
            :error-messages="errorMessage"
            @input="errorMessage = ''"
            :loading="loading"
            required
        ></v-text-field>

        <v-btn
            type="submit"
            color="primary"
            block
            size="large"
            class="mt-4"
            :loading="loading"
            :disabled="!accessCode.trim() || loading"
        >
          Accéder à la galerie
        </v-btn>
        
        <v-btn
            variant="text"
            block
            class="mt-2"
            @click="navigateTo('/')"
        >
          Retour à l'accueil
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">

const route = useRoute()
const accessCode = ref('')
const loading = ref(false)
const errorMessage = ref('')

async function submitCode() {
  loading.value = true
  errorMessage.value = ''
  
  try {
    // Vérification du code en appelant l'API Nuxt locale
    await $fetch(`/api/galleries/${route.params.id}`, {
      params: { code: accessCode.value.trim() } // on teste l'endpoint local
    })
    
    // Si la requête réussit sans code 403, le mot de passe est bon
    await navigateTo(`/galeries/${route.params.id}?code=${accessCode.value.trim()}`)
  } catch (err: any) {
    if (err.data?.statusCode === 403) {
      errorMessage.value = "Code d'accès invalide. Veuillez réessayer."
    } else {
      errorMessage.value = "Une erreur serveur est survenue."
    }
  } finally {
    loading.value = false
  }
}
</script>
