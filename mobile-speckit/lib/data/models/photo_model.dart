import 'package:json_annotation/json_annotation.dart';

part 'photo_model.g.dart';

@JsonSerializable()
class PhotoModel {
  @JsonKey(name: 'photo_id')
  final String id;
  final int order;
  @JsonKey(name: 'added_at')
  final String? addedAt;

  PhotoModel({
    required this.id,
    required this.order,
    this.addedAt,
  });

  factory PhotoModel.fromJson(Map<String, dynamic> json) =>
      _$PhotoModelFromJson(json);

  Map<String, dynamic> toJson() => _$PhotoModelToJson(this);
}
