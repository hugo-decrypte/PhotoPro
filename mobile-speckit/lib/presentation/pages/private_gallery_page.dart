import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/widgets/photo_grid.dart';

class PrivateGalleryPage extends ConsumerWidget {
  final String galleryId;
  final GalleryEntity? gallery;
  final List<PhotoEntity>? photos;

  const PrivateGalleryPage({
    Key? key,
    required this.galleryId,
    this.gallery,
    this.photos,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(
        title: Text(gallery?.title ?? 'Private Gallery'),
      ),
      body: (photos != null && photos!.isNotEmpty)
          ? PhotoGrid(
              photos: photos!,
              galleryId: galleryId,
              isPrivate: gallery?.isPrivate ?? true,
            )
          : const Center(
              child: Text('No photos in this gallery'),
            ),
    );
  }
}
