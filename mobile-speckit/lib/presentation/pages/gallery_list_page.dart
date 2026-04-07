import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:photo_gallery/core/extensions/context_extension.dart';
import 'package:photo_gallery/core/widgets/error_widget.dart' as error_widget;
import 'package:photo_gallery/core/widgets/loading_widget.dart';
import 'package:photo_gallery/presentation/providers/gallery_list_provider.dart';
import 'package:photo_gallery/presentation/widgets/gallery_list_item.dart';
import 'package:photo_gallery/router/app_router.dart';

class GalleryListPage extends ConsumerStatefulWidget {
  const GalleryListPage({Key? key}) : super(key: key);

  @override
  ConsumerState<GalleryListPage> createState() => _GalleryListPageState();
}

class _GalleryListPageState extends ConsumerState<GalleryListPage> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(galleryListProvider.notifier).loadGalleries();
    });
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >
        _scrollController.position.maxScrollExtent - 500) {
      final state = ref.read(galleryListProvider);
      if (!state.isLoading && state.hasMorePages) {
        ref.read(galleryListProvider.notifier).loadGalleries();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final galleryState = ref.watch(galleryListProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Photo Galleries'),
        centerTitle: true,
      ),
      body: _buildBody(context, galleryState),
    );
  }

  Widget _buildBody(BuildContext context, GalleryListState state) {
    if (state.error != null && state.galleries.isEmpty) {
      return error_widget.ErrorWidget(
        message: state.error!,
        onRetry: () {
          ref.read(galleryListProvider.notifier).reset();
          ref.read(galleryListProvider.notifier).loadGalleries();
        },
      );
    }

    if (state.galleries.isEmpty && state.isLoading) {
      return const LoadingWidget(message: 'Loading galleries...');
    }

    return ListView.builder(
      controller: _scrollController,
      itemCount: state.galleries.length + (state.isLoading ? 1 : 0),
      itemBuilder: (context, index) {
        if (index == state.galleries.length) {
          return const Padding(
            padding: EdgeInsets.all(16.0),
            child: CircularProgressIndicator(),
          );
        }

        final gallery = state.galleries[index];
        return Padding(
          padding: const EdgeInsets.all(8.0),
          child: GalleryListItem(
            gallery: gallery,
            onTap: () {
              if (gallery.isPublic) {
                context.pushNamed(
                  AppRoute.galleryDetail.name,
                  pathParameters: {'galleryId': gallery.id},
                );
              } else {
                context.pushNamed(
                  AppRoute.privateGallery.name,
                  pathParameters: {'galleryId': gallery.id},
                );
              }
            },
          ),
        );
      },
    );
  }
}
