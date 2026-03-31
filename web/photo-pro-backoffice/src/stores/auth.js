import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

const STORAGE_KEY = 'photopro_auth'

export const useAuthStore = defineStore('auth', () => {
  const token = ref('')
  const refreshToken = ref('')

  const isAuthenticated = computed(() => Boolean(token.value))

  function loadFromStorage() {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (!raw) {
      return
    }

    try {
      const parsed = JSON.parse(raw)
      token.value = parsed.token || ''
      refreshToken.value = parsed.refreshToken || ''
    } catch {
      clear()
    }
  }

  function persist() {
    localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify({
        token: token.value,
        refreshToken: refreshToken.value,
      }),
    )
  }

  function login(newToken, newRefreshToken = '') {
    token.value = newToken
    refreshToken.value = newRefreshToken
    persist()
  }

  function clear() {
    token.value = ''
    refreshToken.value = ''
    localStorage.removeItem(STORAGE_KEY)
  }

  return {
    token,
    refreshToken,
    isAuthenticated,
    loadFromStorage,
    login,
    clear,
  }
})
