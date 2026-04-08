import 'dart:convert';
import 'package:http/http.dart' as http;
import '../core/api_config.dart';
import '../models/comment_model.dart';
import '../models/gallery.dart';

class GalleryService {
  Future<Gallery> accessPrivateGallery(String id, String code) async {
    final url = '${ApiConfig.baseUrl}/galeries/$id/privee?code=$code';

    final response = await http.get(Uri.parse(url));
    final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception(
        data is Map<String, dynamic>
            ? (data['error'] ?? data['message'] ?? 'Erreur API')
            : 'Erreur API',
      );
    }

    final payload =
        data is Map<String, dynamic> && data['data'] is Map<String, dynamic>
            ? data['data'] as Map<String, dynamic>
            : data as Map<String, dynamic>;

    return Gallery.fromJson(payload);
  }

 Future<String> getPhotoUrl(String photoId) async {
  final url = '${ApiConfig.baseUrl}/photos/$photoId';

  final response = await http.get(Uri.parse(url));
  final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};

  final payload =
      data is Map<String, dynamic> && data['data'] is Map<String, dynamic>
          ? data['data'] as Map<String, dynamic>
          : data as Map<String, dynamic>;

  final s3Key = payload['s3_key']?.toString();

  if (s3Key == null || s3Key.isEmpty) {
    throw Exception('s3_key introuvable pour la photo');
  }

  return 'http://localhost:8888/uploads/$s3Key';
}

  Future<List<CommentModel>> getComments(String galleryId) async {
    final url = '${ApiConfig.baseUrl}/galeries/$galleryId/comments';

    final response = await http.get(Uri.parse(url));
    final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception(
        data is Map<String, dynamic>
            ? (data['error'] ?? data['message'] ?? 'Erreur API')
            : 'Erreur API',
      );
    }

    final payload =
        data is Map<String, dynamic> && data['data'] is List
            ? data['data'] as List
            : <dynamic>[];

    return payload
        .whereType<Map<String, dynamic>>()
        .map(CommentModel.fromJson)
        .toList();
  }

  Future<void> addComment({
    required String galleryId,
    required String photoId,
    required String content,
    String? authorName,
  }) async {
    final url =
        '${ApiConfig.baseUrl}/galeries/$galleryId/photos/$photoId/comments';

    final response = await http.post(
      Uri.parse(url),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'author_name': authorName,
        'content': content,
      }),
    );

    final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception(
        data is Map<String, dynamic>
            ? (data['error'] ?? data['message'] ?? 'Erreur API')
            : 'Erreur API',
      );
    }
  }
}