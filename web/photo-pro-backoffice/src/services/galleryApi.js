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

/** Galeries du photographe connecté (JWT + X-Photographer-Id doivent correspondre à photographerId). */
export async function fetchGalleriesByPhotographer(photographerId) {
  try {
    const { data } = await http.get(`/galeries/photographer/${encodeURIComponent(photographerId)}`)
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

/**
 * @param {string} galleryId
 * @param {{ photo_id: string, order?: number }[]} photos
 */
export async function addPhotosToGallery(galleryId, photos) {
  try {
    const { data } = await http.post(`/galeries/${encodeURIComponent(galleryId)}/photos`, { photos })
    return data
  } catch (error) {
    throw normalizeApiError(error)
  }
}

export async function fetchGalleryPhotos(galleryId) {
  try {
    const { data } = await http.get(`/galeries/${encodeURIComponent(galleryId)}/photos`)
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

