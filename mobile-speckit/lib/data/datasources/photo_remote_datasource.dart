import 'package:photo_gallery/data/models/photo_model.dart';

abstract class PhotoRemoteDataSource {
  Future<List<PhotoModel>> getGalleryPhotos(
    String galleryId, {
    required int offset,
    required int limit,
  });

  Future<String> getPhotoS3Key(String photoId);
}
