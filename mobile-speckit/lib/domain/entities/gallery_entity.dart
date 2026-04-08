class GalleryEntity {
  final String id;
  final String title;
  final String? description;
  final String type;
  final String? coverPhotoId;
  final DateTime? createdAt;
  final String photographerId;
  final bool status;

  GalleryEntity({
    required this.id,
    required this.title,
    this.description,
    required this.type,
    this.coverPhotoId,
    this.createdAt,
    required this.photographerId,
    required this.status,
  });

  bool get isPublic => type == 'PUBLIC';
  bool get isPrivate => type == 'PRIVATE';

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is GalleryEntity &&
          runtimeType == other.runtimeType &&
          id == other.id;

  @override
  int get hashCode => id.hashCode;
}
