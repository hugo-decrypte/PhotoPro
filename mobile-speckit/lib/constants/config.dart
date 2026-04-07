class Config {
  static const String baseUrl = 'http://localhost:6083';
  
  // Endpoints
  static const String galleriesEndpoint = '/galeries';
  static const String galleryDetailsEndpoint = '/galeries';
  static const String privateGalleryEndpoint = '/galerie';
  static const String commentsEndpoint = '/galeries';
  
  // Pagination
  static const int pageSize = 10;
  
  // Timeouts
  static const int connectTimeout = 30000; // ms
  static const int receiveTimeout = 30000; // ms
}
