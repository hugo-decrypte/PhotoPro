import 'package:json_annotation/json_annotation.dart';

part 'gallery_model.g.dart';

@JsonSerializable()
class GalleryModel {
  final String id;
  final String title;
  final String? description;
  final String type;
  @JsonKey(name: 'coverPhotoId')
  final String? coverPhotoId;
  @JsonKey(name: 'createdAt', includeIfNull: false)
  final DateTime? createdAt;
  @JsonKey(name: 'photographerId')
  final String photographerId;
  final bool status;

  GalleryModel({
    required this.id,
    required this.title,
    this.description,
    required this.type,
    this.coverPhotoId,
    this.createdAt,
    required this.photographerId,
    required this.status,
  });

  factory GalleryModel.fromJson(Map<String, dynamic> json) =>
      _$GalleryModelFromJson(json);

  Map<String, dynamic> toJson() => _$GalleryModelToJson(this);

  bool get isPublic => type == 'PUBLIC';
  bool get isPrivate => type == 'PRIVATE';
}
