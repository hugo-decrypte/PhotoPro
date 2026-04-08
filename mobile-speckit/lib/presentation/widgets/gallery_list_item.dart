import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/cover_image_provider.dart';

class GalleryListItem extends ConsumerWidget {
  final GalleryEntity gallery;
  final VoidCallback onTap;

  const GalleryListItem({
    Key? key,
    required this.gallery,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Card(
      child: ListTile(
        onTap: onTap,
        leading: _buildCoverImage(ref),
        title: Text(gallery.title),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (gallery.description != null && gallery.description!.isNotEmpty)
              Text(
                gallery.description!,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: Theme.of(context).textTheme.bodySmall,
              ),
          ],
        ),
        trailing: const Icon(Icons.arrow_forward),
      ),
    );
  }

  Widget _buildCoverImage(WidgetRef ref) {
    // Charger les photos de la galerie
    final galleryPhotos = ref.watch(galleryPhotosProvider(gallery.id));
    
    return galleryPhotos.when(
      data: (photos) {
        PhotoEntity? coverPhoto;
        
        // Si coverPhotoId existe, chercher cette photo
        if (gallery.coverPhotoId != null && gallery.coverPhotoId!.isNotEmpty) {
          try {
            coverPhoto = photos.firstWhere((p) => p.id == gallery.coverPhotoId);
          } catch (_) {
            coverPhoto = null;
          }
        }
        
        // Sinon utiliser la première photo
        coverPhoto ??= photos.isNotEmpty ? photos.first : null;
        
        if (coverPhoto == null) {
          return Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(4),
              color: Colors.grey[300],
            ),
            child: const Icon(Icons.image_not_supported, size: 28),
          );
        }
        
        return _buildImageContainer(ref, coverPhoto.id);
      },
      loading: () => Container(
        width: 56,
        height: 56,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(4),
          color: Colors.grey[300],
        ),
        child: const CircularProgressIndicator(strokeWidth: 2),
      ),
      error: (_, __) => Container(
        width: 56,
        height: 56,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(4),
          color: Colors.grey[300],
        ),
        child: const Icon(Icons.broken_image, size: 28),
      ),
    );
  }

  Widget _buildImageContainer(WidgetRef ref, String photoId) {
    final coverImageUrl = ref.watch(coverImageUrlProvider(photoId));

    return Container(
      width: 56,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(4),
        color: Colors.grey[300],
      ),
      child: coverImageUrl.when(
        data: (url) {
          if (url == null) {
            return const Icon(Icons.image_not_supported, size: 28);
          }
          return CachedNetworkImage(
            imageUrl: url,
            fit: BoxFit.cover,
            placeholder: (context, url) =>
                const CircularProgressIndicator(strokeWidth: 2),
            errorWidget: (context, url, error) =>
                const Icon(Icons.broken_image, size: 28),
          );
        },
        loading: () => const CircularProgressIndicator(strokeWidth: 2),
        error: (_, __) => const Icon(Icons.broken_image, size: 28),
      ),
    );
  }
}
