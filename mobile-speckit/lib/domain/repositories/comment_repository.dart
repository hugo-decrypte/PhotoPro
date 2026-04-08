import 'package:photo_gallery/domain/entities/comment_entity.dart';

abstract class CommentRepository {
  Future<CommentEntity> addComment(
    String galleryId,
    String photoId, {
    required String authorName,
    required String content,
    required DateTime createdAt,
  });
}
