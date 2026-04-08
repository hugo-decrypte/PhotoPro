import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

final specificGalleryPhotosProvider =
    FutureProvider.family<List<PhotoEntity>, String>((ref, galleryId) async {
  final repository = ref.watch(galleryRepositoryProvider);
  return repository.getGalleryPhotos(galleryId);
});
