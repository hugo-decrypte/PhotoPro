class CommentModel {
  final String id;
  final String photoId;
  final String galleryId;
  final String? authorName;
  final String content;
  final String? createdAt;

  CommentModel({
    required this.id,
    required this.photoId,
    required this.galleryId,
    required this.content,
    this.authorName,
    this.createdAt,
  });

  factory CommentModel.fromJson(Map<String, dynamic> json) {
    return CommentModel(
      id: (json['id'] ?? '').toString(),
      photoId: (json['photo_id'] ?? '').toString(),
      galleryId: (json['gallery_id'] ?? '').toString(),
      authorName: json['author_name']?.toString(),
      content: (json['content'] ?? '').toString(),
      createdAt: json['created_at']?.toString(),
    );
  }
}