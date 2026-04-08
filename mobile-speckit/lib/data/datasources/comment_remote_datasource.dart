import 'package:photo_gallery/data/models/comment_model.dart';

abstract class CommentRemoteDataSource {
  Future<CommentModel> addComment(
    String galleryId,
    String photoId, {
    required String authorName,
    required String content,
    required DateTime createdAt,
  });
}
