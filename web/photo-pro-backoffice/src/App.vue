<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'

const route = useRoute()
const showAppHeader = computed(() => route.meta.public !== true)
</script>

<template>
  <AppHeader v-if="showAppHeader" />
  <router-view v-slot="{ Component }">
    <Transition name="app-route" mode="out-in">
      <component :is="Component" />
    </Transition>
  </router-view>
</template>

<style>
.app-route-enter-active,
.app-route-leave-active {
  transition: opacity 0.2s ease;
}

.app-route-enter-from,
.app-route-leave-to {
  opacity: 0;
}
</style>
