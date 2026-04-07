class CommentEntity {
  final String id;
  final String authorName;
  final String content;
  final DateTime createdAt;
  final String photoId;

  CommentEntity({
    required this.id,
    required this.authorName,
    required this.content,
    required this.createdAt,
    required this.photoId,
  });

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is CommentEntity &&
          runtimeType == other.runtimeType &&
          id == other.id;

  @override
  int get hashCode => id.hashCode;
}
