import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

class GalleryListState {
  final List<GalleryEntity> galleries;
  final bool isLoading;
  final String? error;
  final int currentPage;
  final bool hasMorePages;

  const GalleryListState({
    this.galleries = const [],
    this.isLoading = false,
    this.error,
    this.currentPage = 0,
    this.hasMorePages = true,
  });

  GalleryListState copyWith({
    List<GalleryEntity>? galleries,
    bool? isLoading,
    String? error,
    int? currentPage,
    bool? hasMorePages,
  }) {
    return GalleryListState(
      galleries: galleries ?? this.galleries,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      currentPage: currentPage ?? this.currentPage,
      hasMorePages: hasMorePages ?? this.hasMorePages,
    );
  }
}

class GalleryListNotifier extends StateNotifier<GalleryListState> {
  final galleryRepository;

  GalleryListNotifier(this.galleryRepository)
      : super(const GalleryListState());

  Future<void> loadGalleries({bool isRefresh = false}) async {
    if (!isRefresh && !state.hasMorePages) return;

    final page = isRefresh ? 0 : state.currentPage;
    final offset = page * Config.pageSize;

    state = state.copyWith(isLoading: true, error: null);

    try {
      final newGalleries = await galleryRepository.getAllGalleries(
        offset: offset,
        limit: Config.pageSize,
      );

      state = state.copyWith(
        isLoading: false,
        galleries: isRefresh
            ? newGalleries
            : [...state.galleries, ...newGalleries],
        currentPage: page + 1,
        hasMorePages: newGalleries.length >= Config.pageSize,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: _getErrorMessage(e),
      );
    }
  }

  void reset() {
    state = const GalleryListState();
  }

  String _getErrorMessage(dynamic error) {
    return error.toString().replaceFirst('Exception: ', '');
  }
}

final galleryListProvider =
    StateNotifierProvider<GalleryListNotifier, GalleryListState>((ref) {
  final repository = ref.watch(galleryRepositoryProvider);
  return GalleryListNotifier(repository);
});
