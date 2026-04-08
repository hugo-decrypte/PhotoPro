class Gallery {
  final String id;
  final String title;
  final String? description;
  final String type;
  final String status;

  Gallery({
    required this.id,
    required this.title,
    required this.type,
    required this.status,
    this.description,
  });

  factory Gallery.fromJson(Map<String, dynamic> json) {
    return Gallery(
      id: json['id'].toString(),
      title: json['title'] ?? '',
      description: json['description'],
      type: json['type'] ?? '',
      status: json['status'] ?? '',
    );
  }
}