import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT_MS } from '../config/api'
import { normalizeApiError } from './apiError'
import { useAuthStore } from '../stores/auth'
import { refreshToken as refreshTokenRequest } from '../services/authApi'

const http = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT_MS,
})

/**
 * app-gallery exige X-Photographer-Id sur création / publish / unpublish.
 * Chemins relatifs à API_BASE_URL (/api).
 */
function requestNeedsXPhotographerId(config) {
  const method = (config.method || 'get').toLowerCase()
  const path = String(config.url || '')
    .split('?')[0]
    .replace(/^\/+/, '')
    .replace(/\/+$/, '')

  if (method === 'post' && path === 'galeries') {
    return true
  }
  if (method === 'post' && /^galeries\/[^/]+\/photos$/.test(path)) {
    return true
  }
  if (method === 'patch' && /^galeries\/[^/]+\/(publish|unpublish)$/.test(path)) {
    return true
  }
  if (method === 'get' && /^galeries\/photographer\/[^/]+$/.test(path)) {
    return true
  }
  return false
}

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

    config.headers = config.headers || {}

    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    if (requestNeedsXPhotographerId(config) && authStore.userId) {
      config.headers['X-Photographer-Id'] = authStore.userId
    }

    return config
  })

  http.interceptors.response.use(
    (response) => response,
    async (error) => {
      const authStore = useAuthStore()
      const originalRequest = error.config || {}
      const status = error?.response?.status
      const reqUrl = String(originalRequest.url || '')
      const isRefreshCall = reqUrl.includes('/refresh')

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

        if (requestNeedsXPhotographerId(originalRequest) && authStore.userId) {
          originalRequest.headers['X-Photographer-Id'] = authStore.userId
        }

        return http(originalRequest)
      } catch (refreshError) {
        authStore.clear()
        return Promise.reject(normalizeApiError(refreshError))
      }
    },
  )
}

export default http
