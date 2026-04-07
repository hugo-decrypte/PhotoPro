Je souhaite développer une application destinée aux photopgrahes professionnels pour partager leurs galeries avec leurs clients. L'application doit être en flutter. La partie backend est gérée par notre API qu'il faudra connecter à l'application. Il y a deux niveaux d'accès : anonyme et authentifié (un photographe). Pour le moment, nous n'allons pas gérer les personnes authentifiées, mais prends en compte le fait que cette fonctionnalité pourra être mise en place. Une personne anonyme peut naviguer parmi les galeries publiques + accéder à une galerie privée en saisissant le code d'accès (route GET /galerie/{id}/privee?code=CODE) + ajouter un commentaire sur une photo d'une galerie PRIVEE (route POST /galeries/{id}/photos/{photoId}/comments). Si tu as besoin de spécifications, demande moi. Modifie le fichier @.specify/memory/constitution.md en ajoutant ce que tu considères le plus pertinent pour mon projet.


IA :  **Structure des galeries**: Une galerie est-elle composée de dossiers/albums imbriqués, ou juste une liste de photos plates?
Réponse : On a une table gallery, une table photo, et une table gallery_photo qui permet de lier une photo à une galerie

IA : **Commentaires**: Quand un utilisateur anonyme ajoute un commentaire, doit-il saisir un nom/email ou être entièrement anonyme? Y a-t-il une modération?
Réponse : Quand l'utilisateur veut ajouter un commentaire, il doit renseigner les informations suivantes (formulaire) : Son nom (authorName), le contenu de son message (content) et la date de création du commentaire (createdAt). Ce dernier champ peut être rempli automatiquement en prenant la date du jour. Les noms de variables entre parenthèses sont celles à renseigner dans le body de la requête POST /galeries/{id}/photos/{photoId}/comments.

IA : **Galeries publiques vs privées**: Une galerie a-t-elle un flag `public` ou `is_private`? Les galeries publiques requièrent-elles aussi un code d'accès, ou c'est simplement une liste visible?
Réponse : La différence entre une galerie publique et privée est son champ "type". Une galerie privée est à "PUBLIC" tandis qu'une galerie privée est à "PRIVATE". Une galerie publique ne requiert pas de code d'accès. Pour accéder à la liste des galeries publiques, il suffit d'accéder à la route GET /galeries, tandis que pour accéder à une galerie publique, il faut accéder à la route GET /galerie/{id}/privee?code=CODE. Je te conseille donc de faire une interface spécifique pour accéder à une galerie privée où l'utilisateur devra renseigner le CODE et également l'id de la galerie.


IA :  **Affichage des photos**: Comment les photos doivent-elles être affichées? (grille, carrousel, liste) Y a-t-il des métadonnées importantes (description, date, photographe)?
Réponse : Quand on est sur la page qui liste toute les galeries, tu mets une seule photo (que tu peux récupérer grâce à "cover_photo_id". Mais quand on souhaite voir le détail de la galerie, tu affiches une liste de toutes les photos. On utilise un stockage compatible AWS S3. Les données renvoyées par une photo sont : id (uuid), photographer_id (uuid), title (string), mime_type (string), size_bytes (int), original_filename (string), s3_key (string), uploaded_at (date).


IA : **Architecture du code**: Souhaites-tu une structure avec providers (Riverpod/Provider), clean architecture, ou une approche plus simple pour débuter?
Réponse : Je souhaite une clean architecture, de cette façon si je modifie par exemple mon chemin api, je veux que ça puisse se faire simplement à un seul endroit.


IA : **Navigation**: Préfères-tu utiliser go_router pour la navigation, ou l'approche standard de Flutter avec Navigator?
Réponse : go_router


Problème : accès api
Solution : remplacer localhost par 198.xx.xx.xx

Problème : image non affichées
Solution : Pour obtenir l'imagine d'une photo il faut déjà que tu aies l'id de l'image. Une fois que tu l'as, tu récupères les infos plus détaillées de l'aimges avec la route /photos/{id}, ce qui va te retourner un attribut "s3Key" sous la forme "uploads/40d8354a-3e32-4550-90e0-0e046be0ef74.jpg". Enfin, tu vas aller à l'url http://localhost:8888/{s3Key} et tu as l'images. Par exemple: http://localhost:8888/uploads/40d8354a-3e32-4550-90e0-0e046be0ef74.jpg. Je précise que tu dois donc remplacer le localhost car c'est le localhost du pc et pas du téléphone sur lequel l'app tourne.


Problème : temps longs pour afficher les images
Solutions : toutes les charger en amont