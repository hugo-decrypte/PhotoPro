export function photoThumbSrc(photo) {
  if (!photo || typeof photo !== 'object') {
    return ''
  }
  return String(photo.thumbnailUrl || photo.url || '')
}

export function photoFullSrc(photo) {
  if (!photo || typeof photo !== 'object') {
    return ''
  }
  return String(photo.url || photo.thumbnailUrl || '')
}

export function photoAltText(photo) {
  if (!photo || typeof photo !== 'object') {
    return 'Photo'
  }
  return String(photo.title || photo.originalName || 'Photo')
}

export function photoHeading(photo) {
  if (!photo || typeof photo !== 'object') {
    return 'Sans titre'
  }
  return String(photo.title || photo.originalName || 'Sans titre')
}

export function usePhotoDisplay() {
  return {
    thumbSrc: photoThumbSrc,
    fullSrc: photoFullSrc,
    altText: photoAltText,
    heading: photoHeading,
  }
}
