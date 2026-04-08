<template>
  <v-app>
    <v-app-bar flat color="white" border="b">
      <v-container class="d-flex align-center pa-0">
        <NuxtLink to="/" class="navbar-logo">PhotoPro</NuxtLink>
        <v-spacer />
        <v-text-field
            v-model="search"
            prepend-inner-icon="mdi-magnify"
            placeholder="Rechercher une galerie..."
            variant="outlined"
            density="compact"
            hide-details
            rounded
            style="max-width: 280px;"
            color="primary"
        />
      </v-container>
    </v-app-bar>

    <v-main>
      <slot />
    </v-main>

    <v-footer color="white" border="t" class="mt-8">
      <v-container class="text-center">
        <span style="color: #999; font-size: 13px;">© 2024 PhotoPro — Tous droits réservés</span>
      </v-container>
    </v-footer>
  </v-app>
</template>

<script setup lang="ts">
const search = ref('')
</script>

<style scoped>
.navbar-logo {
  color: #268D86;
  font-size: 20px;
  font-weight: 500;
  letter-spacing: 1px;
  text-decoration: none;
}
</style>