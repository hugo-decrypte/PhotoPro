export function normalizeApiError(error) {
  const status = error?.response?.status || 0
  const data = error?.response?.data

  const message =
    data?.message ||
    data?.error ||
    error?.message ||
    'Erreur API inconnue.'

  return {
    status,
    message,
    data,
    raw: error,
  }
}
