import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/presentation/providers/photo_image_provider.dart';
import 'package:photo_gallery/presentation/providers/add_comment_provider.dart';
import 'package:photo_gallery/data/models/comment_model.dart';
import 'package:intl/intl.dart';

class PhotoGrid extends StatelessWidget {
  final List<PhotoEntity> photos;
  final String galleryId;
  final bool isPrivate;

  const PhotoGrid({
    Key? key,
    required this.photos,
    required this.galleryId,
    this.isPrivate = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    if (photos.isEmpty) {
      return const Center(
        child: Text('No photos available'),
      );
    }

    return GridView.builder(
      padding: const EdgeInsets.all(8),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 8,
        mainAxisSpacing: 8,
      ),
      itemCount: photos.length,
      itemBuilder: (context, index) {
        final photo = photos[index];
        return PhotoGridItem(
          photo: photo,
          galleryId: galleryId,
          isPrivate: isPrivate,
        );
      },
    );
  }
}

class PhotoGridItem extends ConsumerWidget {
  final PhotoEntity photo;
  final String galleryId;
  final bool isPrivate;

  const PhotoGridItem({
    Key? key,
    required this.photo,
    required this.galleryId,
    this.isPrivate = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final imageUrlAsyncValue = ref.watch(photoImageUrlProvider(photo.id));

    return GestureDetector(
      onTap: () {
        _showPhotoDetail(context);
      },
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8),
          color: Colors.grey[300],
        ),
        child: Stack(
          fit: StackFit.expand,
          children: [
            imageUrlAsyncValue.when(
              data: (imageUrl) {
                if (imageUrl != null) {
                  return ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: CachedNetworkImage(
                      imageUrl: imageUrl,
                      fit: BoxFit.cover,
                      placeholder: (context, url) => const Center(
                        child: CircularProgressIndicator(),
                      ),
                      errorWidget: (context, url, error) => const Icon(
                        Icons.image_not_supported,
                        color: Colors.grey,
                      ),
                    ),
                  );
                }
                return const Icon(Icons.image_not_supported);
              },
              loading: () => const Center(
                child: CircularProgressIndicator(),
              ),
              error: (error, stackTrace) => const Icon(
                Icons.error,
                color: Colors.red,
              ),
            ),
            Positioned(
              bottom: 0,
              left: 0,
              right: 0,
              child: Container(
                color: Colors.black.withOpacity(0.6),
                padding: const EdgeInsets.all(8),
                child: Text(
                  'Photo ${photo.order}',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(color: Colors.white, fontSize: 12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showPhotoDetail(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => _PhotoEnlargedDialog(
        photo: photo,
        galleryId: galleryId,
        isPrivate: isPrivate,
      ),
    );
  }
}

class _PhotoEnlargedDialog extends ConsumerWidget {
  final PhotoEntity photo;
  final String galleryId;
  final bool isPrivate;

  const _PhotoEnlargedDialog({
    Key? key,
    required this.photo,
    required this.galleryId,
    this.isPrivate = false,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final imageUrlAsyncValue = ref.watch(photoImageUrlProvider(photo.id));

    return Dialog.fullscreen(
      child: Stack(
        children: [
          Container(
            color: Colors.black,
            child: Center(
              child: imageUrlAsyncValue.when(
                data: (imageUrl) {
                  if (imageUrl != null) {
                    return CachedNetworkImage(
                      imageUrl: imageUrl,
                      fit: BoxFit.contain,
                      placeholder: (context, url) => const CircularProgressIndicator(),
                      errorWidget: (context, url, error) => const Icon(
                        Icons.image_not_supported,
                        color: Colors.white,
                        size: 48,
                      ),
                    );
                  }
                  return const Icon(
                    Icons.image_not_supported,
                    color: Colors.white,
                    size: 48,
                  );
                },
                loading: () => const CircularProgressIndicator(),
                error: (error, stackTrace) => const Icon(
                  Icons.error,
                  color: Colors.red,
                  size: 48,
                ),
              ),
            ),
          ),
          Positioned(
            top: 16,
            right: 16,
            child: IconButton(
              onPressed: () => Navigator.pop(context),
              icon: const Icon(Icons.close),
              color: Colors.white,
              iconSize: 28,
            ),
          ),
          if (isPrivate)
            Positioned(
              bottom: 16,
              left: 16,
              right: 16,
              child: ElevatedButton.icon(
                onPressed: () {
                  _showCommentForm(context, ref);
                },
                icon: const Icon(Icons.comment),
                label: const Text('Ajouter un commentaire'),
              ),
            ),
        ],
      ),
    );
  }

  void _showCommentForm(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => _CommentFormDialog(
        galleryId: galleryId,
        photoId: photo.id,
      ),
    );
  }
}

class _CommentFormDialog extends ConsumerStatefulWidget {
  final String galleryId;
  final String photoId;

  const _CommentFormDialog({
    Key? key,
    required this.galleryId,
    required this.photoId,
  }) : super(key: key);

  @override
  ConsumerState<_CommentFormDialog> createState() => _CommentFormDialogState();
}

class _CommentFormDialogState extends ConsumerState<_CommentFormDialog> {
  late final TextEditingController _authorNameController;
  late final TextEditingController _contentController;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _authorNameController = TextEditingController();
    _contentController = TextEditingController();
  }

  @override
  void dispose() {
    _authorNameController.dispose();
    _contentController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Ajouter un commentaire'),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: _authorNameController,
              decoration: const InputDecoration(
                labelText: 'Votre nom',
                hintText: 'Entrez votre nom',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _contentController,
              decoration: const InputDecoration(
                labelText: 'Commentaire',
                hintText: 'Entrez votre commentaire',
                border: OutlineInputBorder(),
              ),
              maxLines: 4,
            ),
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Annuler'),
        ),
        ElevatedButton(
          onPressed: _isSubmitting ? null : _submitComment,
          child: _isSubmitting
              ? const SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(strokeWidth: 2),
                )
              : const Text('Envoyer'),
        ),
      ],
    );
  }

  Future<void> _submitComment() async {
    final authorName = _authorNameController.text.trim();
    final content = _contentController.text.trim();

    if (authorName.isEmpty || content.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Veuillez remplir tous les champs')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final now = DateTime.now();
      final formattedDate = DateFormat('yyyy-MM-dd HH:mm:ss').format(now);

      final comment = CommentModel(
        authorName: authorName,
        content: content,
        createdAt: formattedDate,
      );

      await ref.read(addCommentProvider(
        (widget.galleryId, widget.photoId, comment),
      ).future);

      if (mounted) {
        Navigator.pop(context);
        _showSuccessDialog();
      }
    } catch (e, stackTrace) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erreur : $e')),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Succès'),
        content: const Text('Votre commentaire a été ajouté avec succès.'),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }
}
