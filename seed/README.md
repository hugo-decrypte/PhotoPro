# Seed des données de test PhotoPro

Ce dossier contient les données de seed pour initialiser une base de données réaliste avec des photos.

## Structure

```
seed/
├── photos/
│   ├── eloi/
│   │   ├── winter1.jpg
│   │   ├── winter2.jpg
│   │   ├── winter3.jpg
│   │   └── winter4.jpg
│   ├── hugo/
│   │   ├── sea1.jpg
│   │   ├── sea2.jpg
│   │   └── sea3.jpg
│   ├── tuline/
│   │   ├── forest1.jpg
│   │   ├── forest2.jpg
│   │   ├── forest3.jpg
│   │   └── forest4.jpg
│   └── vivien/
│   │   ├── desert1.jpg
│   │   └── desert2.jpg
├── seed-photos.php
└── README.md
```

## Utilisation

### Étape 1 : Démarrer l'environnement

```bash
docker-compose up -d
```

### Étape 2 : Copier les photos vers S3 avec les s3_key fixes

```bash
docker-compose run --rm app-photo php /var/seed/seed-photos-to-s3.php
```

Ce script copie les photos directement sur SeaweedFS Filer avec les `s3_key` définis dans le fichier SQL, garantissant la cohérence avec les relations `gallery_photo`.

### Étape 3 : Vérifier que tout fonctionne

Vérifier que la base de données est rempli via adminer.

Et tester que les images sont présent dans le storage en passant par le lien :  
http://localhost:8888  

http://localhost:8888/uploads/bb3b51bd-9de0-4320-9168-1a9b1b8c352d.jpg

