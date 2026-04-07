// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'photo_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

PhotoModel _$PhotoModelFromJson(Map<String, dynamic> json) => PhotoModel(
  id: json['id'] as String,
  title: json['title'] as String,
  mimeType: json['mime_type'] as String,
  sizeBytes: (json['size_bytes'] as num).toInt(),
  originalFilename: json['original_filename'] as String,
  s3Key: json['s3_key'] as String,
  uploadedAt: DateTime.parse(json['uploaded_at'] as String),
  photographerId: json['photographer_id'] as String,
);

Map<String, dynamic> _$PhotoModelToJson(PhotoModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'mime_type': instance.mimeType,
      'size_bytes': instance.sizeBytes,
      'original_filename': instance.originalFilename,
      's3_key': instance.s3Key,
      'uploaded_at': instance.uploadedAt.toIso8601String(),
      'photographer_id': instance.photographerId,
    };
