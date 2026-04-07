import 'package:photo_gallery/domain/entities/photo_entity.dart';

abstract class PhotoRepository {
  Future<List<PhotoEntity>> getGalleryPhotos(
    String galleryId, {
    required int offset,
    required int limit,
  });
}
