import 'package:json_annotation/json_annotation.dart';

part 'photo_model.g.dart';

@JsonSerializable()
class PhotoModel {
  final String id;
  final String title;
  @JsonKey(name: 'mime_type')
  final String mimeType;
  @JsonKey(name: 'size_bytes')
  final int sizeBytes;
  @JsonKey(name: 'original_filename')
  final String originalFilename;
  @JsonKey(name: 's3_key')
  final String s3Key;
  @JsonKey(name: 'uploaded_at')
  final DateTime uploadedAt;
  @JsonKey(name: 'photographer_id')
  final String photographerId;

  PhotoModel({
    required this.id,
    required this.title,
    required this.mimeType,
    required this.sizeBytes,
    required this.originalFilename,
    required this.s3Key,
    required this.uploadedAt,
    required this.photographerId,
  });

  factory PhotoModel.fromJson(Map<String, dynamic> json) =>
      _$PhotoModelFromJson(json);

  Map<String, dynamic> toJson() => _$PhotoModelToJson(this);
}
