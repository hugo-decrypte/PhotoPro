import axios from 'axios'

const apiBaseURL = 'http://localhost:6080'
const authHttp = axios.create({
  baseURL: apiBaseURL,
  timeout: 10000,
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
      if (status && status !== 404) {
        break
      }
    }
  }

  throw lastError
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
