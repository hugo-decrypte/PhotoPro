import 'package:photo_gallery/core/error/exceptions.dart';
import 'package:photo_gallery/data/datasources/photo_remote_datasource.dart';
import 'package:photo_gallery/data/models/photo_model.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/domain/repositories/photo_repository.dart';

class PhotoRepositoryImpl implements PhotoRepository {
  final PhotoRemoteDataSource remoteDataSource;

  PhotoRepositoryImpl({required this.remoteDataSource});

  @override
  Future<List<PhotoEntity>> getGalleryPhotos(
    String galleryId, {
    required int offset,
    required int limit,
  }) async {
    try {
      final models = await remoteDataSource.getGalleryPhotos(
        galleryId,
        offset: offset,
        limit: limit,
      );
      return models.map((m) => _modelToEntity(m)).toList();
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to fetch photos: $e');
    }
  }

  @override
  Future<String> getPhotoS3Key(String photoId) async {
    try {
      return await remoteDataSource.getPhotoS3Key(photoId);
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to fetch photo details: $e');
    }
  }

  PhotoEntity _modelToEntity(PhotoModel model) => PhotoEntity(
        id: model.id,
        order: model.order,
        addedAt: model.addedAt,
      );
}
