// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'photo_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

PhotoModel _$PhotoModelFromJson(Map<String, dynamic> json) => PhotoModel(
  id: json['photo_id'] as String,
  order: (json['order'] as num).toInt(),
  addedAt: json['added_at'] as String?,
);

Map<String, dynamic> _$PhotoModelToJson(PhotoModel instance) =>
    <String, dynamic>{
      'photo_id': instance.id,
      'order': instance.order,
      'added_at': instance.addedAt,
    };
