import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';

class GalleryListItem extends StatelessWidget {
  final GalleryEntity gallery;
  final VoidCallback onTap;

  const GalleryListItem({
    Key? key,
    required this.gallery,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      child: ListTile(
        onTap: onTap,
        leading: gallery.coverPhotoId != null
            ? _buildCoverImage()
            : const Icon(Icons.image_not_supported),
        title: Text(gallery.name),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              gallery.type,
              style: Theme.of(context).textTheme.bodySmall,
            ),
            if (gallery.description != null)
              Text(
                gallery.description!,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: Theme.of(context).textTheme.bodySmall,
              ),
          ],
        ),
        trailing: const Icon(Icons.arrow_forward),
      ),
    );
  }

  Widget _buildCoverImage() {
    return Container(
      width: 56,
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(4),
        color: Colors.grey[300],
      ),
      child: const Placeholder(
        fallbackHeight: 56,
        fallbackWidth: 56,
      ),
    );
  }
}
