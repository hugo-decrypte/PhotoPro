class GalleryPhoto {
  final String photoId;
  final int order;
  final String? addedAt;

  GalleryPhoto({
    required this.photoId,
    required this.order,
    this.addedAt,
  });

  factory GalleryPhoto.fromJson(Map<String, dynamic> json) {
    return GalleryPhoto(
      photoId: (json['photo_id'] ?? '').toString(),
      order: json['order'] is int
          ? json['order'] as int
          : int.tryParse('${json['order']}') ?? 0,
      addedAt: json['added_at']?.toString(),
    );
  }
}

class Gallery {
  final String id;
  final String title;
  final String? description;
  final String type;
  final String status;
  final List<GalleryPhoto> photos;

  Gallery({
    required this.id,
    required this.title,
    required this.type,
    required this.status,
    required this.photos,
    this.description,
  });

  factory Gallery.fromJson(Map<String, dynamic> json) {
    final rawPhotos = (json['photos'] as List?) ?? [];

    return Gallery(
      id: (json['id'] ?? '').toString(),
      title: (json['title'] ?? '').toString(),
      description: json['description']?.toString(),
      type: (json['type'] ?? '').toString(),
      status: (json['status'] ?? '').toString(),
      photos: rawPhotos
          .whereType<Map<String, dynamic>>()
          .map(GalleryPhoto.fromJson)
          .toList(),
    );
  }
}