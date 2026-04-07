import 'package:photo_gallery/core/error/exceptions.dart';
import 'package:photo_gallery/data/datasources/comment_remote_datasource.dart';
import 'package:photo_gallery/data/models/comment_model.dart';
import 'package:photo_gallery/domain/entities/comment_entity.dart';
import 'package:photo_gallery/domain/repositories/comment_repository.dart';

class CommentRepositoryImpl implements CommentRepository {
  final CommentRemoteDataSource remoteDataSource;

  CommentRepositoryImpl({required this.remoteDataSource});

  @override
  Future<CommentEntity> addComment(
    String galleryId,
    String photoId, {
    required String authorName,
    required String content,
    required DateTime createdAt,
  }) async {
    try {
      final model = await remoteDataSource.addComment(
        galleryId,
        photoId,
        authorName: authorName,
        content: content,
        createdAt: createdAt,
      );
      return _modelToEntity(model);
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to add comment: $e');
    }
  }

  CommentEntity _modelToEntity(CommentModel model) => CommentEntity(
        id: model.id,
        authorName: model.authorName,
        content: model.content,
        createdAt: model.createdAt,
        photoId: model.photoId,
      );
}
