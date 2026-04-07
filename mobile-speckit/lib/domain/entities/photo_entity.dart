class PhotoEntity {
  final String id;
  final int order;
  final String? addedAt;
  String? _cachedImageUrl;

  PhotoEntity({
    required this.id,
    required this.order,
    this.addedAt,
  });

  String? get imageUrl => _cachedImageUrl;
  set imageUrl(String? url) => _cachedImageUrl = url;

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is PhotoEntity &&
          runtimeType == other.runtimeType &&
          id == other.id;

  @override
  int get hashCode => id.hashCode;
}
