class GalleryEntity {
  final String id;
  final String name;
  final String? description;
  final String type;
  final String? coverPhotoId;
  final DateTime createdAt;
  final String photographerId;

  GalleryEntity({
    required this.id,
    required this.name,
    this.description,
    required this.type,
    this.coverPhotoId,
    required this.createdAt,
    required this.photographerId,
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
