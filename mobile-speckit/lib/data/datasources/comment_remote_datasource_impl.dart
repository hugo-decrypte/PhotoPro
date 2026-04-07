import 'package:photo_gallery/core/http/dio_client.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/data/datasources/comment_remote_datasource.dart';
import 'package:photo_gallery/data/models/comment_model.dart';

class CommentRemoteDataSourceImpl implements CommentRemoteDataSource {
  final DioClient dioClient;

  CommentRemoteDataSourceImpl({required this.dioClient});

  @override
  Future<CommentModel> addComment(
    String galleryId,
    String photoId, {
    required String authorName,
    required String content,
    required DateTime createdAt,
  }) async {
    try {
      final response = await dioClient.post(
        '${Config.commentsEndpoint}/$galleryId/photos/$photoId/comments',
        data: {
          'authorName': authorName,
          'content': content,
          'createdAt': createdAt.toIso8601String(),
        },
      );

      return CommentModel.fromJson(response.data);
    } catch (e) {
      rethrow;
    }
  }
}
