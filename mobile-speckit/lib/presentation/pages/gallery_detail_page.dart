import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/core/widgets/error_widget.dart' as error_widget;
import 'package:photo_gallery/core/widgets/loading_widget.dart';
import 'package:photo_gallery/presentation/providers/gallery_detail_provider.dart';
import 'package:photo_gallery/presentation/widgets/photo_grid.dart';

class GalleryDetailPage extends ConsumerStatefulWidget {
  final String galleryId;

  const GalleryDetailPage({
    Key? key,
    required this.galleryId,
  }) : super(key: key);

  @override
  ConsumerState<GalleryDetailPage> createState() => _GalleryDetailPageState();
}

class _GalleryDetailPageState extends ConsumerState<GalleryDetailPage> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(galleryDetailProvider.notifier).loadGalleryDetails(widget.galleryId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final galleryState = ref.watch(galleryDetailProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(galleryState.gallery?.title ?? 'Gallery'),
      ),
      body: _buildBody(context, galleryState),
    );
  }

  Widget _buildBody(BuildContext context, GalleryDetailState state) {
    if (state.error != null) {
      return error_widget.ErrorWidget(
        message: state.error!,
        onRetry: () {
          ref
              .read(galleryDetailProvider.notifier)
              .loadGalleryDetails(widget.galleryId);
        },
      );
    }

    if (state.isLoading && state.photos.isEmpty) {
      return const LoadingWidget(message: 'Loading photos...');
    }

    return PhotoGrid(photos: state.photos);
  }
}
