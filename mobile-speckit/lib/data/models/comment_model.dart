import 'package:json_annotation/json_annotation.dart';

part 'comment_model.g.dart';

@JsonSerializable(includeIfNull: false)
class CommentModel {
  final String? id;
  final String authorName;
  final String content;
  final String createdAt;
  final String? photoId;

  CommentModel({
    this.id,
    required this.authorName,
    required this.content,
    required this.createdAt,
    this.photoId,
  });

  factory CommentModel.fromJson(Map<String, dynamic> json) =>
      _$CommentModelFromJson(json);

  Map<String, dynamic> toJson() => _$CommentModelToJson(this);
}
