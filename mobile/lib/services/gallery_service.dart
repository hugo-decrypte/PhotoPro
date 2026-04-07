import '../core/api_client.dart';
import '../core/api_config.dart';
import '../models/gallery.dart';

class GalleryService {
  final ApiClient _client = ApiClient();

  Future<Gallery> accessPrivateGallery(String id, String code) async {
    final data = await _client.get(
      '${ApiConfig.baseUrl}/galeries/$id/privee?code=$code',
    );

    final payload = data is Map<String, dynamic> && data['data'] is Map<String, dynamic>
        ? data['data'] as Map<String, dynamic>
        : data as Map<String, dynamic>;

    return Gallery.fromJson(payload);
  }
}