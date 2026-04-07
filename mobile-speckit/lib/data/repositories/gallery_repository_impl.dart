import 'package:photo_gallery/core/error/exceptions.dart';
import 'package:photo_gallery/data/datasources/gallery_remote_datasource.dart';
import 'package:photo_gallery/data/models/gallery_model.dart';
import 'package:photo_gallery/data/models/photo_model.dart';
import 'package:photo_gallery/domain/entities/gallery_entity.dart';
import 'package:photo_gallery/domain/entities/photo_entity.dart';
import 'package:photo_gallery/domain/repositories/gallery_repository.dart';

class GalleryRepositoryImpl implements GalleryRepository {
  final GalleryRemoteDataSource remoteDataSource;

  GalleryRepositoryImpl({required this.remoteDataSource});

  @override
  Future<List<GalleryEntity>> getAllGalleries({
    required int offset,
    required int limit,
  }) async {
    try {
      final models = await remoteDataSource.getAllGalleries(
        offset: offset,
        limit: limit,
      );
      return models.map((m) => _modelToEntity(m)).toList();
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to fetch galleries: $e');
    }
  }

  @override
  Future<(GalleryEntity, List<PhotoEntity>)> getGalleryDetails(String galleryId) async {
    try {
      final data = await remoteDataSource.getGalleryDetails(galleryId);
      final galleryModel = GalleryModel.fromJson(data);
      final gallery = _modelToEntity(galleryModel);

      final List<dynamic> photosJson = data['photos'] ?? [];
      final photos = photosJson
          .map((p) => _photoModelToEntity(PhotoModel.fromJson(p)))
          .toList();

      return (gallery, photos);
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to fetch gallery details: $e');
    }
  }

  @override
  Future<(GalleryEntity, List<PhotoEntity>)> getPrivateGallery(
    String galleryId,
    String code,
  ) async {
    try {
      final data = await remoteDataSource.getPrivateGallery(galleryId, code);
      final galleryModel = GalleryModel.fromJson(data);
      final gallery = _modelToEntity(galleryModel);

      final List<dynamic> photosJson = data['photos'] ?? [];
      final photos = photosJson
          .map((p) => _photoModelToEntity(PhotoModel.fromJson(p)))
          .toList();

      return (gallery, photos);
    } on ServerException catch (e) {
      if (e.statusCode == 404) {
        throw NotFoundException('Gallery not found');
      } else if (e.statusCode == 401) {
        throw UnauthorizedException('Invalid access code');
      }
      rethrow;
    } on AppException {
      rethrow;
    } catch (e) {
      throw NetworkException('Failed to fetch private gallery: $e');
    }
  }

  GalleryEntity _modelToEntity(GalleryModel model) => GalleryEntity(
        id: model.id,
        title: model.title,
        description: model.description,
        type: model.type,
        coverPhotoId: model.coverPhotoId,
        createdAt: model.createdAt,
        photographerId: model.photographerId,
        status: model.status,
      );

  PhotoEntity _photoModelToEntity(PhotoModel model) => PhotoEntity(
        id: model.id,
        title: model.title,
        mimeType: model.mimeType,
        sizeBytes: model.sizeBytes,
        originalFilename: model.originalFilename,
        s3Key: model.s3Key,
        uploadedAt: model.uploadedAt,
        photographerId: model.photographerId,
      );
}
