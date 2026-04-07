import 'package:photo_gallery/core/http/dio_client.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/data/datasources/gallery_remote_datasource.dart';
import 'package:photo_gallery/data/models/gallery_model.dart';

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
      final response = await dioClient.get(
        '${Config.privateGalleryEndpoint}/$galleryId/privee',
        queryParameters: {
          'code': code,
        },
      );

      return response.data;
    } catch (e) {
      rethrow;
    }
  }
}
