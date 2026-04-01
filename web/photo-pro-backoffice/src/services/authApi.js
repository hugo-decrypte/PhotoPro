import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT_MS } from '../config/api'
import { normalizeApiError } from '../lib/apiError'

const apiBaseURL = API_BASE_URL
const authHttp = axios.create({
  baseURL: apiBaseURL,
  timeout: API_TIMEOUT_MS,
})

async function postFirstAvailable(candidates, payload, config) {
  let lastError

  for (const url of candidates) {
    try {
      const { data } = await authHttp.post(url, payload, config)
      return data
    } catch (error) {
      lastError = error
      const status = error?.response?.status
      if (status && status >= 400 && status < 500 && status !== 404) {
        break
      }
    }
  }

  throw normalizeApiError(lastError)
}

export async function register(payload) {
  return postFirstAvailable(['/auth/register', '/register'], payload)
}

export async function login(payload) {
  return postFirstAvailable(['/auth/login', '/signin', '/login'], payload)
}

export async function refreshToken(refresh_token) {
  try {
    return await postFirstAvailable(['/auth/refresh', '/refresh'], { refresh_token })
  } catch {
    return postFirstAvailable(
      ['/auth/refresh', '/refresh'],
      {},
      { headers: { Authorization: `Bearer ${refresh_token}` } },
    )
  }
}
