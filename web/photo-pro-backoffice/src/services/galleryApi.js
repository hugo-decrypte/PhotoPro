import http from '../lib/http'
import { normalizeApiError } from '../lib/apiError'

export async function fetchGalleries() {
  try {
    const { data } = await http.get('/galeries')
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function createGallery(payload) {
  try {
    const { data } = await http.post('/galeries', payload)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function publishGallery(galleryId) {
  try {
    const { data } = await http.patch(`/galeries/${encodeURIComponent(galleryId)}/publish`)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function unpublishGallery(galleryId) {
  try {
    const { data } = await http.patch(`/galeries/${encodeURIComponent(galleryId)}/unpublish`)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

