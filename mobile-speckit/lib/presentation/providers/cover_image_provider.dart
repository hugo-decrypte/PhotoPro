import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

final galleryPhotosProvider =
    FutureProvider.family<List<PhotoEntity>, String>((ref, galleryId) async {
  final repository = ref.read(galleryRepositoryProvider);
  return repository.getGalleryPhotos(galleryId);
});

final photoS3KeyProvider =
    FutureProvider.family<String, String>((ref, photoId) async {
  final photoRepository = ref.read(photoRepositoryProvider);
  return photoRepository.getPhotoS3Key(photoId);
});

final coverImageUrlProvider =
    FutureProvider.family<String?, String>((ref, photoId) async {
  final s3Key = await ref.watch(photoS3KeyProvider(photoId).future);
  if (s3Key.isEmpty) return null;
  return '${Config.s3ServerUrl}/$s3Key';
});
