export function filterItems(items, query, getSearchableText) {
  const q = String(query || '').trim().toLowerCase()
  if (!q) {
    return items
  }
  return items.filter((item) => String(getSearchableText(item)).toLowerCase().includes(q))
}

/** Nombre de pages (minimum 1) */
export function pageCount(itemsLength, pageSize) {
  const n = Math.max(0, Number(itemsLength) || 0)
  const size = Math.max(1, Number(pageSize) || 1)
  return Math.max(1, Math.ceil(n / size))
}

export function slicePage(items, page, pageSize) {
  const pc = pageCount(items.length, pageSize)
  const p = Math.min(Math.max(1, Number(page) || 1), pc)
  const size = Math.max(1, Number(pageSize) || 1)
  const start = (p - 1) * size
  return items.slice(start, start + size)
}
