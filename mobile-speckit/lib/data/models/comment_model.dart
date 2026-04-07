import 'package:json_annotation/json_annotation.dart';

part 'comment_model.g.dart';

@JsonSerializable()
class CommentModel {
  final String id;
  @JsonKey(name: 'author_name')
  final String authorName;
  final String content;
  @JsonKey(name: 'created_at')
  final DateTime createdAt;
  @JsonKey(name: 'photo_id')
  final String photoId;

  CommentModel({
    required this.id,
    required this.authorName,
    required this.content,
    required this.createdAt,
    required this.photoId,
  });

  factory CommentModel.fromJson(Map<String, dynamic> json) =>
      _$CommentModelFromJson(json);

  Map<String, dynamic> toJson() => _$CommentModelToJson(this);
}
