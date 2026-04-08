import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/presentation/providers/preloaded_gallery_photos_provider.dart';
import 'package:photo_gallery/presentation/widgets/photo_grid.dart';

import 'package:photo_gallery/domain/entities/gallery_entity.dart';

class GalleryDetailPage extends ConsumerWidget {
  final String galleryId;
  final GalleryEntity? gallery;

  const GalleryDetailPage({
    Key? key,
    required this.galleryId,
    this.gallery,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final photosAsync = ref.watch(specificGalleryPhotosProvider(galleryId));

    return Scaffold(
      appBar: AppBar(
        title: Text(gallery?.title ?? 'Gallery'),
      ),
      body: photosAsync.when(
        data: (photos) {
          if (photos.isEmpty) {
            return const Center(
              child: Text('No photos in this gallery'),
            );
          }
          return PhotoGrid(
            photos: photos,
            galleryId: galleryId,
            isPrivate: gallery?.isPrivate ?? false,
          );
        },
        loading: () => const Center(
          child: CircularProgressIndicator(),
        ),
        error: (error, stack) {
          return Center(
            child: Text('Error: $error'),
          );
        },
      ),
    );
  }
}
