class PhotoEntity {
  final String id;
  final String title;
  final String mimeType;
  final int sizeBytes;
  final String originalFilename;
  final String s3Key;
  final DateTime uploadedAt;
  final String photographerId;

  PhotoEntity({
    required this.id,
    required this.title,
    required this.mimeType,
    required this.sizeBytes,
    required this.originalFilename,
    required this.s3Key,
    required this.uploadedAt,
    required this.photographerId,
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is PhotoEntity &&
          runtimeType == other.runtimeType &&
          id == other.id;

  @override
  int get hashCode => id.hashCode;
}
