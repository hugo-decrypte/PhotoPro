import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/constants/config.dart';
import 'package:photo_gallery/presentation/providers/providers_setup.dart';

/// Récupère l'URL complète d'une photo
/// 1. Appelle /photos/{photoId} pour obtenir le s3Key
/// 2. Construit l'URL: http://s3Server/{s3Key}
final photoImageUrlProvider =
    FutureProvider.family<String, String>((ref, photoId) async {
  final photoRepository = ref.read(photoRepositoryProvider);
  
  // Appel API pour récupérer le s3Key
  final s3Key = await photoRepository.getPhotoS3Key(photoId);
  
  // Construction de l'URL
  return '${Config.s3ServerUrl}/$s3Key';
});
