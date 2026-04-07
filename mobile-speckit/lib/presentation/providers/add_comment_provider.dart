import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/data/models/comment_model.dart';
import 'package:photo_gallery/domain/entities/comment_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

final addCommentProvider = FutureProvider.family<CommentEntity,
    (String, String, CommentModel)>((ref, params) async {
  final (galleryId, photoId, comment) = params;
  final repository = ref.watch(commentRepositoryProvider);

  final createdAt = DateTime.parse(comment.createdAt.replaceAll(' ', 'T'));

  return repository.addComment(
    galleryId,
    photoId,
    authorName: comment.authorName,
    content: comment.content,
    createdAt: createdAt,
  );
});
