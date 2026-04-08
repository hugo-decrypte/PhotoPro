// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'comment_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CommentModel _$CommentModelFromJson(Map<String, dynamic> json) => CommentModel(
  id: json['id'] as String?,
  authorName: json['authorName'] as String,
  content: json['content'] as String,
  createdAt: json['createdAt'] as String,
  photoId: json['photoId'] as String?,
);

Map<String, dynamic> _$CommentModelToJson(CommentModel instance) =>
    <String, dynamic>{
      if (instance.id case final value?) 'id': value,
      'authorName': instance.authorName,
      'content': instance.content,
      'createdAt': instance.createdAt,
      if (instance.photoId case final value?) 'photoId': value,
    };
