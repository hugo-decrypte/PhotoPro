import 'dart:convert';
import 'package:http/http.dart' as http;
import '../core/api_config.dart';
import '../models/gallery.dart';

class GalleryService {
  Future<Gallery> accessPrivateGallery(String id, String code) async {
    final url = '${ApiConfig.baseUrl}/galeries/$id/privee?code=$code';
    print('REQUEST URL: $url');

    final response = await http.get(Uri.parse(url));
    print('STATUS CODE: ${response.statusCode}');
    print('RAW BODY: ${response.body}');

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
}