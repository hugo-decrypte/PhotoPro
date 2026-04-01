import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT_MS } from '../config/api'
import { normalizeApiError } from './apiError'
import { useAuthStore } from '../stores/auth'
import { refreshToken as refreshTokenRequest } from '../services/authApi'

const http = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT_MS,
})

function extractTokens(data) {
  return {
    accessToken: data?.access_token || data?.token || '',
    refreshToken: data?.refresh_token || '',
  }
}

let interceptorsInitialized = false

export function setupHttpInterceptors() {
  if (interceptorsInitialized) {
    return
  }

  interceptorsInitialized = true

  http.interceptors.request.use((config) => {
    const authStore = useAuthStore()
    authStore.loadFromStorage()

    if (authStore.token) {
      config.headers = config.headers || {}
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    return config
  })

  http.interceptors.response.use(
    (response) => response,
    async (error) => {
      const authStore = useAuthStore()
      const originalRequest = error.config || {}
      const status = error?.response?.status
      const isRefreshCall = String(originalRequest.url || '').includes('/auth/refresh')

      if (status !== 401 || originalRequest._retry || isRefreshCall || !authStore.refreshToken) {
        return Promise.reject(normalizeApiError(error))
      }

      originalRequest._retry = true

      try {
        const data = await refreshTokenRequest(authStore.refreshToken)
        const tokens = extractTokens(data)
        if (!tokens.accessToken) {
          throw new Error('Missing access token')
        }

        authStore.setTokens(tokens.accessToken, tokens.refreshToken || authStore.refreshToken)
        originalRequest.headers = originalRequest.headers || {}
        originalRequest.headers.Authorization = `Bearer ${tokens.accessToken}`

        return http(originalRequest)
      } catch (refreshError) {
        authStore.clear()
        return Promise.reject(normalizeApiError(refreshError))
      }
    },
  )
}

export default http
