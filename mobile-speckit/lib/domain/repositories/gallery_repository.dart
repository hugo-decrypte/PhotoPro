import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';

abstract class GalleryRepository {
  Future<List<GalleryEntity>> getAllGalleries({
    required int offset,
    required int limit,
  });

  Future<List<PhotoEntity>> getGalleryPhotos(String galleryId);

  Future<(GalleryEntity, List<PhotoEntity>)> getGalleryDetails(String galleryId);

  Future<(GalleryEntity, List<PhotoEntity>)> getPrivateGallery(
    String galleryId,
    String code,
  );
}
