// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'comment_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

CommentModel _$CommentModelFromJson(Map<String, dynamic> json) => CommentModel(
  id: json['id'] as String,
  authorName: json['author_name'] as String,
  content: json['content'] as String,
  createdAt: DateTime.parse(json['created_at'] as String),
  photoId: json['photo_id'] as String,
);

Map<String, dynamic> _$CommentModelToJson(CommentModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'author_name': instance.authorName,
      'content': instance.content,
      'created_at': instance.createdAt.toIso8601String(),
      'photo_id': instance.photoId,
    };
