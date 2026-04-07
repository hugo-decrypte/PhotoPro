import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/core/extensions/context_extension.dart';
import 'package:photo_gallery/core/widgets/error_widget.dart' as error_widget;
import 'package:photo_gallery/core/widgets/loading_widget.dart';
import 'package:photo_gallery/presentation/providers/private_gallery_provider.dart';
import 'package:photo_gallery/presentation/widgets/photo_grid.dart';
import 'package:photo_gallery/presentation/widgets/private_gallery_dialog.dart';

class PrivateGalleryPage extends ConsumerStatefulWidget {
  final String galleryId;

  const PrivateGalleryPage({
    Key? key,
    required this.galleryId,
  }) : super(key: key);

  @override
  ConsumerState<PrivateGalleryPage> createState() => _PrivateGalleryPageState();
}

class _PrivateGalleryPageState extends ConsumerState<PrivateGalleryPage> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _showAccessDialog();
    });
  }

  void _showAccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => PrivateGalleryAccessDialog(
        galleryId: widget.galleryId,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final galleryState = ref.watch(privateGalleryProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(galleryState.gallery?.name ?? 'Private Gallery'),
      ),
      body: _buildBody(context, galleryState),
    );
  }

  Widget _buildBody(BuildContext context, PrivateGalleryState state) {
    if (!state.isAccessGranted) {
      if (state.error != null) {
        return Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              error_widget.ErrorWidget(
                message: state.error!,
                onRetry: _showAccessDialog,
              ),
            ],
          ),
        );
      }
      return const LoadingWidget();
    }

    if (state.isLoading && state.photos.isEmpty) {
      return const LoadingWidget(message: 'Loading photos...');
    }

    return PhotoGrid(photos: state.photos);
  }
}
