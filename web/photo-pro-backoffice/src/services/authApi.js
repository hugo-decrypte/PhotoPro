import axios from 'axios'
import { API_BASE_URL, API_TIMEOUT_MS } from '../config/api'
import { normalizeApiError } from '../lib/apiError'

const authHttp = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT_MS,
})

export async function register(payload) {
  try {
    const { data } = await authHttp.post('/register', payload)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function login(payload) {
  try {
    const { data } = await authHttp.post('/signin', payload)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function refreshToken(refresh_token) {
  try {
    const { data } = await authHttp.post('/refresh', { refresh_token })
    return data
  } catch {
    try {
      const { data } = await authHttp.post(
        '/refresh',
        {},
        { headers: { Authorization: `Bearer ${refresh_token}` } },
      )
      return data
    } catch (error) {
      throw normalizeApiError(error)
    }
  }
}
