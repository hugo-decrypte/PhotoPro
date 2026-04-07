// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'gallery_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

GalleryModel _$GalleryModelFromJson(Map<String, dynamic> json) => GalleryModel(
  id: json['id'] as String,
  name: json['name'] as String,
  description: json['description'] as String?,
  galleryType: json['type'] as String,
  coverPhotoId: json['cover_photo_id'] as String?,
  createdAt: DateTime.parse(json['created_at'] as String),
  photographerId: json['photographer_id'] as String,
);

Map<String, dynamic> _$GalleryModelToJson(GalleryModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'name': instance.name,
      'description': instance.description,
      'type': instance.galleryType,
      'cover_photo_id': instance.coverPhotoId,
      'created_at': instance.createdAt.toIso8601String(),
      'photographer_id': instance.photographerId,
    };
