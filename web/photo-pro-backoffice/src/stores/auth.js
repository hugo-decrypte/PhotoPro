import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

const STORAGE_KEY = 'photopro_auth'

export const useAuthStore = defineStore('auth', () => {
  const token = ref('')
  const refreshToken = ref('')
  const user = ref(null)

  const isAuthenticated = computed(() => Boolean(token.value))

  const userId = computed(() => (user.value?.id ? String(user.value.id) : ''))

  function loadFromStorage() {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (!raw) {
      return
    }

    try {
      const parsed = JSON.parse(raw)
      token.value = parsed.token || ''
      refreshToken.value = parsed.refreshToken || ''
      const u = parsed.user
      user.value =
        u && u.id != null && u.id !== ''
          ? { id: String(u.id), email: String(u.email || '') }
          : null
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
        user: user.value,
      }),
    )
  }

  function setTokens(newToken, newRefreshToken = '') {
    token.value = newToken
    refreshToken.value = newRefreshToken
    persist()
  }

  function setUser(profile) {
    if (!profile || profile.id == null || profile.id === '') {
      user.value = null
    } else {
      user.value = {
        id: String(profile.id),
        email: String(profile.email || ''),
      }
    }
    persist()
  }

  function setAccessToken(newToken) {
    token.value = newToken
    persist()
  }

  function clear() {
    token.value = ''
    refreshToken.value = ''
    user.value = null
    localStorage.removeItem(STORAGE_KEY)
  }

  return {
    token,
    refreshToken,
    user,
    userId,
    isAuthenticated,
    loadFromStorage,
    setTokens,
    setUser,
    setAccessToken,
    clear,
  }
})
