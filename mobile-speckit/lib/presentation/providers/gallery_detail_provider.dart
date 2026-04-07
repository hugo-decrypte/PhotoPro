import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

class GalleryDetailState {
  final GalleryEntity? gallery;
  final List<PhotoEntity> photos;
  final bool isLoading;
  final String? error;

  const GalleryDetailState({
    this.gallery,
    this.photos = const [],
    this.isLoading = false,
    this.error,
  });

  GalleryDetailState copyWith({
    GalleryEntity? gallery,
    List<PhotoEntity>? photos,
    bool? isLoading,
    String? error,
  }) {
    return GalleryDetailState(
      gallery: gallery ?? this.gallery,
      photos: photos ?? this.photos,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class GalleryDetailNotifier extends StateNotifier<GalleryDetailState> {
  final galleryRepository;

  GalleryDetailNotifier(this.galleryRepository)
      : super(const GalleryDetailState());

  Future<void> loadGalleryDetails(String galleryId) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final (gallery, photos) =
          await galleryRepository.getGalleryDetails(galleryId);

      state = state.copyWith(
        isLoading: false,
        gallery: gallery,
        photos: photos,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: _getErrorMessage(e),
      );
    }
  }

  void reset() {
    state = const GalleryDetailState();
  }

  String _getErrorMessage(dynamic error) {
    return error.toString().replaceFirst('Exception: ', '');
  }
}

final galleryDetailProvider =
    StateNotifierProvider<GalleryDetailNotifier, GalleryDetailState>((ref) {
  final repository = ref.watch(galleryRepositoryProvider);
  return GalleryDetailNotifier(repository);
});
