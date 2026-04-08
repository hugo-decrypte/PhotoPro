class Config {
  static const String baseUrl = 'http://docketu.iutnc.univ-lorraine.fr:56063';
  static const String s3ServerUrl = 'http://docketu.iutnc.univ-lorraine.fr:56070';
  
  // Endpoints
  static const String galleriesEndpoint = '/galeries';
  static const String galleryDetailsEndpoint = '/galeries';
  static const String privateGalleryEndpoint = '/galeries';
  static const String commentsEndpoint = '/galeries';
  
  // Pagination
  static const int pageSize = 10;
  
  // Timeouts
  static const int connectTimeout = 30000; // ms
  static const int receiveTimeout = 30000; // ms
}
