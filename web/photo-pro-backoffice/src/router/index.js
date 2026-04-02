import { createRouter, createWebHistory } from 'vue-router'
import { REQUIRE_AUTH } from '../config/auth'
import { useAuthStore } from '../stores/auth'
import DashboardView from '../views/DashboardView.vue'
import GalleryView from '../views/GalleryView.vue'
import GalleryEditView from '../views/GalleryEditView.vue'
import LoginView from '../views/LoginView.vue'
import PhotosView from '../views/PhotosView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { public: true },
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { public: true },
    },
    {
      path: '/',
      name: 'dashboard',
      component: DashboardView,
    },
    {
      path: '/photos',
      name: 'photos',
      component: PhotosView,
    },
    {
      path: '/gallery',
      name: 'gallery',
      component: GalleryView,
    },
    {
      path: '/gallery/new',
      name: 'gallery-new',
      component: GalleryEditView,
    },
  ],
})

router.beforeEach((to) => {
  if (!REQUIRE_AUTH) {
    return true
  }

  const authStore = useAuthStore()
  authStore.loadFromStorage()

  if (to.meta.public) {
    return true
  }

  if (!authStore.isAuthenticated) {
    return { name: 'login' }
  }

  return true
})

export default router
