import 'dart:convert';
import 'dart:typed_data';
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

if (response.statusCode < 200 || response.statusCode >= 300) {
throw Exception('Erreur API photo: ${response.body}');
}

final payload =
data is Map<String, dynamic> && data['data'] is Map<String, dynamic>
? data['data'] as Map<String, dynamic>
: data as Map<String, dynamic>;

		final s3Key = payload['s3Key']?.toString();

if (s3Key == null || s3Key.isEmpty) {
print('[GALLERY_SERVICE][ERREUR] s3_key introuvable dans la réponse : $payload');
throw Exception('s3_key introuvable pour la photo');
}
final url2 = '${ApiConfig.s3BaseUrl}/$s3Key';
print('[GALLERY_SERVICE] URL utilisée pour la photo : $url2');
return url2;
}

Future<Uint8List> getPhotoBytes(String photoId) async {
		final imageUrl = await getPhotoUrl(photoId);
		print('[GALLERY_SERVICE] Tentative de chargement de l\'image : $imageUrl');
		final response = await http.get(Uri.parse(imageUrl));
		print('[GALLERY_SERVICE] Status code image : ${response.statusCode}');
		if (response.statusCode < 200 || response.statusCode >= 300) {
			print('[GALLERY_SERVICE][ERREUR] Réponse image : ${response.body}');
			throw Exception("Impossible de charger l'image");
		}
		return response.bodyBytes;
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
	// authorName supprimé, anonymat forcé
}) async {
	final url = '${ApiConfig.baseUrl}/galeries/$galleryId/photos/$photoId/comments';
	final now = DateTime.now();
	final formattedDate = "${now.year.toString().padLeft(4, '0')}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')} ${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}:${now.second.toString().padLeft(2, '0')}";
	final body = jsonEncode({
		'authorName': 'Anonyme',
		'content': content,
		'galleryId': galleryId,
		'photoId': photoId,
		'createdAt': formattedDate,
	});
	print('[GALLERY_SERVICE] POST commentaire URL : $url');
	print('[GALLERY_SERVICE] POST commentaire BODY : $body');
	final response = await http.post(
		Uri.parse(url),
		headers: {'Content-Type': 'application/json'},
		body: body,
	);

	print('[GALLERY_SERVICE] Réponse POST commentaire : ${response.statusCode} ${response.body}');
	final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};

	if (response.statusCode < 200 || response.statusCode >= 300) {
		throw Exception(
			data is Map<String, dynamic>
					? (data['error'] ?? data['message'] ?? 'Erreur API')
					: 'Erreur API',
		);
	}
}

Future<List<Gallery>> getPublicGalleries() async {
  final url = '${ApiConfig.baseUrl}/galeries/public';
  final response = await http.get(Uri.parse(url));
  print('[DEBUG] Réponse galeries publiques : \\nStatus: \\${response.statusCode}\\nBody: \\${response.body}');
  final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};
  if (response.statusCode < 200 || response.statusCode >= 300) {
    throw Exception('Erreur API galeries publiques: \\${response.body}');
  }
  final payload = data is Map<String, dynamic> && data['data'] is List
      ? data['data'] as List
      : <dynamic>[];
  return payload
      .whereType<Map<String, dynamic>>()
      .map(Gallery.fromJson)
      .toList();
}

Future<Gallery> accessPrivateGalleryByCode(String code) async {
    final url = '${ApiConfig.baseUrl}/galeries/privee?code=$code';
    final response = await http.get(Uri.parse(url));
    final data = response.body.isNotEmpty ? jsonDecode(response.body) : {};
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception('Erreur API galerie privée: ${response.body}');
    }
    final payload = data is Map<String, dynamic> && data['data'] is Map<String, dynamic>
        ? data['data'] as Map<String, dynamic>
        : data as Map<String, dynamic>;
    return Gallery.fromJson(payload);
  }
}