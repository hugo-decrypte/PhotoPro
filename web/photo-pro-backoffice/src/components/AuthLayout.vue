<script setup>
import '../css/app-shell.css'
import '../css/auth-view.css'
import logoUrl from '../img/logo.png'

defineProps({
  title: {
    type: String,
    required: true,
  },
})
</script>

<template>
  <div class="auth-page">
    <header class="auth-page__header">
      <RouterLink :to="{ name: 'login' }" class="auth-page__brand" aria-label="PhotoPro — connexion">
        <img class="auth-page__logo" :src="logoUrl" width="44" height="44" alt="" />
        <span>PhotoPro</span>
      </RouterLink>
    </header>

    <div class="auth-page__body">
      <div class="auth-card">
        <h1 class="auth-card__title">{{ title }}</h1>
        <slot />
      </div>
    </div>
  </div>
</template>
