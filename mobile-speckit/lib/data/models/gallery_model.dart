import 'package:json_annotation/json_annotation.dart';

part 'gallery_model.g.dart';

@JsonSerializable()
class GalleryModel {
  final String id;
  final String name;
  final String? description;
  @JsonKey(name: 'type')
  final String galleryType;
  @JsonKey(name: 'cover_photo_id')
  final String? coverPhotoId;
  @JsonKey(name: 'created_at')
  final DateTime createdAt;
  @JsonKey(name: 'photographer_id')
  final String photographerId;

  GalleryModel({
    required this.id,
    required this.name,
    this.description,
    required this.galleryType,
    this.coverPhotoId,
    required this.createdAt,
    required this.photographerId,
  });

  factory GalleryModel.fromJson(Map<String, dynamic> json) =>
      _$GalleryModelFromJson(json);

  Map<String, dynamic> toJson() => _$GalleryModelToJson(this);

  bool get isPublic => galleryType == 'PUBLIC';
  bool get isPrivate => galleryType == 'PRIVATE';
}
