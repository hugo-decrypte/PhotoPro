import 'package:photo_gallery/core/http/dio_client.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/data/datasources/gallery_remote_datasource.dart';
import 'package:photo_gallery/data/models/gallery_model.dart';
import 'package:photo_gallery/data/models/comment_model.dart';

class GalleryRemoteDataSourceImpl implements GalleryRemoteDataSource {
  final DioClient dioClient;

  GalleryRemoteDataSourceImpl({required this.dioClient});

  @override
  Future<List<GalleryModel>> getAllGalleries({
    required int offset,
    required int limit,
  }) async {
    try {
      final response = await dioClient.get(
        Config.galleriesEndpoint,
        queryParameters: {
          'offset': offset,
          'limit': limit,
        },
      );

      final List<dynamic> data = response.data['data'] ?? [];
      return data.map((json) => GalleryModel.fromJson(json)).toList();
    } catch (e) {
      rethrow;
    }
  }

  @override
  Future<Map<String, dynamic>> getGalleryDetails(String galleryId) async {
    try {
      final response = await dioClient.get(
        '${Config.galleryDetailsEndpoint}/$galleryId/photos',
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }

  @override
  Future<Map<String, dynamic>> getPrivateGallery(String galleryId, String code) async {
    try {
      // Récupérer les infos de la galerie privée
      final galleryResponse = await dioClient.get(
        '${Config.privateGalleryEndpoint}/$galleryId/privee',
        queryParameters: {
          'code': code,
        },
      );

      print('[DEBUG] Gallery response: ${galleryResponse.data}');

      // Récupérer les photos de la galerie
      final photosResponse = await dioClient.get(
        '${Config.galleryDetailsEndpoint}/$galleryId/photos',
      );

      print('[DEBUG] Photos response: ${photosResponse.data}');

      // Combiner les deux réponses: convertir snake_case en camelCase
      final galleryData = galleryResponse.data['data'] as Map<String, dynamic>;
      final photosData = photosResponse.data['data'] as List<dynamic>;
      
      print('[DEBUG] Original galleryData keys: ${galleryData.keys}');
      
      // Convertir snake_case en camelCase
      final convertedGalleryData = _convertSnakeToCamelCase(galleryData);
      print('[DEBUG] Converted galleryData keys: ${convertedGalleryData.keys}');
      
      convertedGalleryData['data'] = photosData;

      print('[DEBUG] Final result: $convertedGalleryData');
      return convertedGalleryData;
    } catch (e) {
      print('[ERROR] Error in getPrivateGallery: $e');
      rethrow;
    }
  }

  Map<String, dynamic> _convertSnakeToCamelCase(Map<String, dynamic> data) {
    final result = <String, dynamic>{};
    
    data.forEach((key, value) {
      String newKey = key;
      dynamic newValue = value;
      
      // Convertir les champs snake_case courants en camelCase
      if (key == 'photographer_id') {
        newKey = 'photographerId';
      } else if (key == 'cover_photo_id') {
        newKey = 'coverPhotoId';
      } else if (key == 'created_at') {
        newKey = 'createdAt';
      } else if (key == 'status') {
        // Convertir status string en boolean: DRAFT=false, PUBLISHED=true
        newValue = value == 'PUBLISHED' || value == true;
      }
      
      result[newKey] = newValue;
    });
    
    return result;
  }

  @override
  Future<void> addComment(
    String galleryId,
    String photoId,
    CommentModel comment,
  ) async {
    try {
      await dioClient.post(
        '${Config.galleriesEndpoint}/$galleryId/photos/$photoId/comments',
        data: comment.toJson(),
      );
    } catch (e) {
      rethrow;
    }
  }
}
