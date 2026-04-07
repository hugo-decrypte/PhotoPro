import http from '../lib/http'
import { normalizeApiError } from '../lib/apiError'

export async function uploadPhoto({ file, title = '', photographerId = '' }) {
  const formData = new FormData()
  formData.append('photo', file)
  if (title) {
    formData.append('title', title)
  }
  if (photographerId) {
    formData.append('photographer_id', photographerId)
  }

  try {
    // Laisser Axios/browser gérer automatiquement le boundary multipart.
    const { data } = await http.post('/photos', formData)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function deletePhoto(photoId) {
  try {
    const { data } = await http.delete(`/photos/${encodeURIComponent(photoId)}`)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

