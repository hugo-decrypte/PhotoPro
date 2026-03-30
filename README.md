# PhotoPro

### PhotoPro.net

Plateforme de galeries photos professionnelles à destination des photographes.

## Description
PhotoPro.net permet à des photographes inscrits de stocker leurs photos et de créer des galeries publiques ou privées à destination de leurs clients. Les clients peuvent accéder aux galeries privées via un code d'accès ou une URL directe, et y laisser des commentaires.

## Fonctionnalités
### Backoffice (Vue.js)

Inscription et authentification
Upload de photos vers l'espace personnel
Création et gestion de galeries publiques et privées
Publication / dépublication de galeries
Notifications automatiques aux clients par email

### Site public (Nuxt.js)

Navigation dans les galeries publiques
Accès aux galeries privées via URL directe ou code d'accès
Affichage en lightbox et plein écran

### Application mobile (Flutter)

Visualisation des galeries publiques et privées
Commentaires sur les photos d'une galerie privée
Upload de photos (mode authentifié)

## Structure initiale

- `api/` : point d'entree API (gateway/backoffice/navigation a separer ensuite)
- `services/` : microservices metier
- `infra/` : composants techniques (stockage S3, broker, mail)
- `web/` : frontend web (Vue/Nuxt a preciser ensuite)
- `mobile/` : application Flutter
- `docker-compose.yml` : stack locale minimale

## Équipe

####  Bena Hugo (dévelppeur Nuxt.js)
#### Herrman Vivien (développeur Nuxt.js)
#### Lévèque Tuline (développeuse Flutter)
#### Cazottes Alexandre (développeur Flutter)
#### Hayrapetyan Arman (développeur Flutter)
#### Reigner Eloi (développeur Flutter)
#### Hmem Wiem (développeuse Flutter)
#### Tout le monde a participé à la partie backoffice en Vue.js

### Installation et lancement

////////////////////////////////////

A compléter

///////////////////////////////////


## Contexte
SAE Atelier-Projet de Développement Web 2 — BUT 3 Informatique DWM
IUT Nancy-Charlemagne — Université de Lorraine
30 mars – 10 avril 2026