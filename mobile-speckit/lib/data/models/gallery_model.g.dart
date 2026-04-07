// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'gallery_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

GalleryModel _$GalleryModelFromJson(Map<String, dynamic> json) => GalleryModel(
  id: json['id'] as String,
  title: json['title'] as String,
  description: json['description'] as String?,
  type: json['type'] as String,
  coverPhotoId: json['coverPhotoId'] as String?,
  createdAt:
      json['createdAt'] == null
          ? null
          : DateTime.parse(json['createdAt'] as String),
  photographerId: json['photographerId'] as String,
  status: json['status'] as bool,
);

Map<String, dynamic> _$GalleryModelToJson(GalleryModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'description': instance.description,
      'type': instance.type,
      'coverPhotoId': instance.coverPhotoId,
      if (instance.createdAt?.toIso8601String() case final value?)
        'createdAt': value,
      'photographerId': instance.photographerId,
      'status': instance.status,
    };
