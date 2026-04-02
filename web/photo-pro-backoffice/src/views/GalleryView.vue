<template>
  <main>
    <h1>Galeries</h1>
    <nav>
      <RouterLink :to="{ name: 'photos' }">Photos</RouterLink> |
      <RouterLink :to="{ name: 'gallery' }">Galeries</RouterLink> |
      <RouterLink :to="{ name: 'gallery-new' }">Nouvelle galerie</RouterLink>
    </nav>
    <p>Liste des galeries (a venir).</p>
  </main>
</template>

