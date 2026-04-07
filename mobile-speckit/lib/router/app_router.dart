import 'package:go_router/go_router.dart';
import 'package:photo_gallery/presentation/pages/gallery_list_page.dart';
import 'package:photo_gallery/presentation/pages/gallery_detail_page.dart';
import 'package:photo_gallery/presentation/pages/private_gallery_page.dart';

enum AppRoute {
  home('home', '/'),
  galleryDetail('gallery-detail', '/gallery/:galleryId'),
  privateGallery('private-gallery', '/private-gallery/:galleryId'),
  ;

  final String name;
  final String path;

  const AppRoute(this.name, this.path);
}

final appRouterProvider = GoRouter(
  initialLocation: AppRoute.home.path,
  routes: [
    GoRoute(
      path: AppRoute.home.path,
      name: AppRoute.home.name,
      builder: (context, state) => const GalleryListPage(),
    ),
    GoRoute(
      path: AppRoute.galleryDetail.path,
      name: AppRoute.galleryDetail.name,
      builder: (context, state) {
        final galleryId = state.pathParameters['galleryId']!;
        return GalleryDetailPage(galleryId: galleryId);
      },
    ),
    GoRoute(
      path: AppRoute.privateGallery.path,
      name: AppRoute.privateGallery.name,
      builder: (context, state) {
        final galleryId = state.pathParameters['galleryId']!;
        return PrivateGalleryPage(galleryId: galleryId);
      },
    ),
  ],
);
