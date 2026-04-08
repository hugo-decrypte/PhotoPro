import { computed, ref } from 'vue'
import { defineStore } from 'pinia'

const STORAGE_KEY = 'photopro_auth'

function pseudoFromEmail(email) {
  const normalized = String(email || '').trim()
  if (!normalized) {
    return ''
  }
  const [localPart] = normalized.split('@')
  return String(localPart || '').trim()
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref('')
  const refreshToken = ref('')
  const user = ref(null)

  const isAuthenticated = computed(() => Boolean(token.value))

  const userId = computed(() => (user.value?.id ? String(user.value.id) : ''))
  const displayName = computed(() => {
    const pseudo = String(user.value?.pseudo || '').trim()
    if (pseudo) {
      return pseudo
    }
    const emailPseudo = pseudoFromEmail(user.value?.email)
    if (emailPseudo) {
      return emailPseudo
    }
    const firstName = String(user.value?.firstName || '').trim()
    const lastName = String(user.value?.lastName || '').trim()
    const fullName = `${firstName} ${lastName}`.trim()
    if (fullName) {
      return fullName
    }
    return 'Utilisateur'
  })

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
          ? {
              id: String(u.id),
              email: String(u.email || ''),
              pseudo: String(u.pseudo || pseudoFromEmail(u.email)),
              firstName: String(u.firstName || ''),
              lastName: String(u.lastName || ''),
            }
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
      const incomingId = String(profile.id)
      const currentPseudo = user.value?.id === incomingId ? String(user.value?.pseudo || '') : ''
      user.value = {
        id: incomingId,
        email: String(profile.email || ''),
        pseudo: String(
          profile.pseudo || profile.user_name || currentPseudo || pseudoFromEmail(profile.email),
        ),
        firstName: String(profile.first_name || profile.firstName || ''),
        lastName: String(profile.name || profile.lastName || ''),
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
    displayName,
    isAuthenticated,
    loadFromStorage,
    setTokens,
    setUser,
    setAccessToken,
    clear,
  }
})
