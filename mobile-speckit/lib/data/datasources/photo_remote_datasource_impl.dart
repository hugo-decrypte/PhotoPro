import 'package:photo_gallery/core/http/dio_client.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/data/datasources/photo_remote_datasource.dart';
import 'package:photo_gallery/data/models/photo_model.dart';

class PhotoRemoteDataSourceImpl implements PhotoRemoteDataSource {
  final DioClient dioClient;

  PhotoRemoteDataSourceImpl({required this.dioClient});

  @override
  Future<List<PhotoModel>> getGalleryPhotos(
    String galleryId, {
    required int offset,
    required int limit,
  }) async {
    try {
      final response = await dioClient.get(
        '${Config.galleryDetailsEndpoint}/$galleryId/photos',
        queryParameters: {
          'offset': offset,
          'limit': limit,
        },
      );

      final List<dynamic> data = response.data['photos'] ?? [];
      return data.map((json) => PhotoModel.fromJson(json)).toList();
    } catch (e) {
      rethrow;
    }
  }
}
