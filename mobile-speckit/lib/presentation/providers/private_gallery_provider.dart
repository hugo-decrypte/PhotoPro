import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

class PrivateGalleryState {
  final GalleryEntity? gallery;
  final List<PhotoEntity> photos;
  final bool isLoading;
  final String? error;
  final bool isAccessGranted;

  const PrivateGalleryState({
    this.gallery,
    this.photos = const [],
    this.isLoading = false,
    this.error,
    this.isAccessGranted = false,
  });

  PrivateGalleryState copyWith({
    GalleryEntity? gallery,
    List<PhotoEntity>? photos,
    bool? isLoading,
    String? error,
    bool? isAccessGranted,
  }) {
    return PrivateGalleryState(
      gallery: gallery ?? this.gallery,
      photos: photos ?? this.photos,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      isAccessGranted: isAccessGranted ?? this.isAccessGranted,
    );
  }
}

class PrivateGalleryNotifier extends StateNotifier<PrivateGalleryState> {
  final galleryRepository;

  PrivateGalleryNotifier(this.galleryRepository)
      : super(const PrivateGalleryState());

  Future<void> accessPrivateGallery(String galleryId, String code) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final (gallery, photos) =
          await galleryRepository.getPrivateGallery(galleryId, code);

      state = state.copyWith(
        isLoading: false,
        gallery: gallery,
        photos: photos,
        isAccessGranted: true,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: _getErrorMessage(e),
        isAccessGranted: false,
      );
    }
  }

  void reset() {
    state = const PrivateGalleryState();
  }

  String _getErrorMessage(dynamic error) {
    final message = error.toString();
    if (message.contains('Invalid access code')) {
      return 'Invalid access code';
    } else if (message.contains('Gallery not found')) {
      return 'Gallery not found';
    }
    return message.replaceFirst('Exception: ', '');
  }
}

final privateGalleryProvider =
    StateNotifierProvider<PrivateGalleryNotifier, PrivateGalleryState>((ref) {
  final repository = ref.watch(galleryRepositoryProvider);
  return PrivateGalleryNotifier(repository);
});
