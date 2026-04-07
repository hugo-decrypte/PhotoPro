import 'package:photo_gallery/data/models/gallery_model.dart';
import 'package:photo_gallery/data/models/photo_model.dart';
import 'package:photo_gallery/data/models/comment_model.dart';

abstract class GalleryRemoteDataSource {
  Future<List<GalleryModel>> getAllGalleries({
    required int offset,
    required int limit,
  });

  Future<Map<String, dynamic>> getGalleryDetails(String galleryId);

  Future<Map<String, dynamic>> getPrivateGallery(String galleryId, String code);

  Future<void> addComment(
    String galleryId,
    String photoId,
    CommentModel comment,
  );
}
