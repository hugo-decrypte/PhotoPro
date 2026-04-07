import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/core/http/dio_client.dart';
import 'package:photo_gallery/data/datasources/comment_remote_datasource_impl.dart';
import 'package:photo_gallery/data/datasources/gallery_remote_datasource_impl.dart';
import 'package:photo_gallery/data/datasources/photo_remote_datasource_impl.dart';
import 'package:photo_gallery/data/repositories/comment_repository_impl.dart';
import 'package:photo_gallery/data/repositories/gallery_repository_impl.dart';
import 'package:photo_gallery/data/repositories/photo_repository_impl.dart';
import 'package:photo_gallery/domain/repositories/comment_repository.dart';
import 'package:photo_gallery/domain/repositories/gallery_repository.dart';
import 'package:photo_gallery/domain/repositories/photo_repository.dart';

final dioClientProvider = Provider<DioClient>((ref) {
  return DioClient();
});

final galleryRemoteDataSourceProvider = Provider<GalleryRemoteDataSourceImpl>((ref) {
  final dio = ref.watch(dioClientProvider);
  return GalleryRemoteDataSourceImpl(dioClient: dio);
});

final photoRemoteDataSourceProvider = Provider<PhotoRemoteDataSourceImpl>((ref) {
  final dio = ref.watch(dioClientProvider);
  return PhotoRemoteDataSourceImpl(dioClient: dio);
});

final commentRemoteDataSourceProvider = Provider<CommentRemoteDataSourceImpl>((ref) {
  final dio = ref.watch(dioClientProvider);
  return CommentRemoteDataSourceImpl(dioClient: dio);
});

final galleryRepositoryProvider = Provider<GalleryRepository>((ref) {
  final dataSource = ref.watch(galleryRemoteDataSourceProvider);
  return GalleryRepositoryImpl(remoteDataSource: dataSource);
});

final photoRepositoryProvider = Provider<PhotoRepository>((ref) {
  final dataSource = ref.watch(photoRemoteDataSourceProvider);
  return PhotoRepositoryImpl(remoteDataSource: dataSource);
});

final commentRepositoryProvider = Provider<CommentRepository>((ref) {
  final dataSource = ref.watch(commentRemoteDataSourceProvider);
  return CommentRepositoryImpl(remoteDataSource: dataSource);
});
