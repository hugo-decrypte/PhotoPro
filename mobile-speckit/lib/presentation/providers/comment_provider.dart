import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/comment_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

class CommentState {
  final List<CommentEntity> comments;
  final bool isLoading;
  final String? error;
  final bool isSubmitting;

  const CommentState({
    this.comments = const [],
    this.isLoading = false,
    this.error,
    this.isSubmitting = false,
  });

  CommentState copyWith({
    List<CommentEntity>? comments,
    bool? isLoading,
    String? error,
    bool? isSubmitting,
  }) {
    return CommentState(
      comments: comments ?? this.comments,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      isSubmitting: isSubmitting ?? this.isSubmitting,
    );
  }
}

class CommentNotifier extends StateNotifier<CommentState> {
  final commentRepository;

  CommentNotifier(this.commentRepository) : super(const CommentState());

  Future<void> addComment(
    String galleryId,
    String photoId, {
    required String authorName,
    required String content,
  }) async {
    state = state.copyWith(isSubmitting: true, error: null);

    try {
      final comment = await commentRepository.addComment(
        galleryId,
        photoId,
        authorName: authorName,
        content: content,
        createdAt: DateTime.now(),
      );

      state = state.copyWith(
        isSubmitting: false,
        comments: [...state.comments, comment],
      );
    } catch (e) {
      state = state.copyWith(
        isSubmitting: false,
        error: _getErrorMessage(e),
      );
    }
  }

  void reset() {
    state = const CommentState();
  }

  String _getErrorMessage(dynamic error) {
    return error.toString().replaceFirst('Exception: ', '');
  }
}

final commentProvider =
    StateNotifierProvider<CommentNotifier, CommentState>((ref) {
  final repository = ref.watch(commentRepositoryProvider);
  return CommentNotifier(repository);
});
