# Photo Gallery - Flutter Mobile App

A professional Flutter mobile application for photographers to share gallery collections with clients. Built with clean architecture, Riverpod state management, and comprehensive error handling.

## Features

### ✅ Public Galleries
- Browse all public galleries from home screen
- Infinite scroll pagination with auto-load
- Gallery cover photo display
- Tap to view all photos in a gallery

### ✅ Public Gallery Details
- View photos in responsive 2-column grid layout
- Photo titles and metadata
- Photo detail modal with full information
- Pagination for large galleries

### ✅ Private Galleries
- Access private galleries with secure code validation
- Beautiful code input dialog
- Error handling for invalid codes
- Same photo grid view as public galleries

### ✅ Comments
- Add comments to photos in private galleries
- Author name + content validation
- Automatic timestamp generation
- Clean form submission with error handling

## Architecture

### Clean Architecture Pattern
```
Presentation Layer → State Management → Domain Layer → Data Layer
```

- **Data Layer**: Models, remote datasources, repository implementations
- **Domain Layer**: Pure entities, abstract repositories, business logic
- **Presentation Layer**: Pages, widgets, Riverpod providers
- **Core Layer**: Shared utilities, error handling, HTTP client

### State Management
- **Riverpod**: Functional reactive state management
- **StateNotifier**: Mutable state for galleries, photos, comments
- **FutureProvider**: Async data fetching
- **Provider**: Dependency injection (Dio client, repositories)

## Dependencies

- **Flutter 3.29.3**
- **riverpod 2.6.1** - State management
- **go_router 13.2.5** - Navigation
- **dio 5.9.2** - HTTP client
- **json_serializable 6.9.5** - JSON serialization
- **cached_network_image 3.4.1** - Image loading from S3
- **intl 0.19.0** - Date formatting

## Project Structure

```
lib/
├── constants/
│   └── config.dart                 # API configuration
├── core/
│   ├── error/                      # Custom exceptions & failures
│   ├── http/                       # Dio HTTP client
│   ├── extensions/                 # BuildContext helpers
│   └── widgets/                    # Shared UI components
├── data/
│   ├── datasources/               # Remote API calls
│   ├── models/                    # DTOs with JSON serialization
│   └── repositories/              # Data layer implementations
├── domain/
│   ├── entities/                  # Pure business objects
│   └── repositories/              # Abstract interfaces
├── presentation/
│   ├── pages/                     # Full screens
│   ├── widgets/                   # Reusable components
│   └── providers/                 # Riverpod state management
├── router/
│   └── app_router.dart           # go_router configuration
├── main.dart                      # App entry point
└── test/                         # Widget tests
```

## API Configuration

Base URL: `http://localhost:6083`

### Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/galeries` | GET | List all public galleries |
| `/galeries/{id}/photos` | GET | Get public gallery detail with photos |
| `/galerie/{id}/privee?code=CODE` | GET | Get private gallery with access code |
| `/galeries/{id}/photos/{photoId}/comments` | POST | Add comment to photo |

## Running the App

### Installation
```bash
flutter pub get
```

### Run
```bash
flutter run
```

### Build APK
```bash
flutter build apk --release
```

### Build Web
```bash
flutter build web
```

## Testing

### Run Tests
```bash
flutter test
```

### Run Specific Test File
```bash
flutter test test/widgets_test.dart
```

## Code Quality

### Analyze Code
```bash
flutter analyze
```

### Fix Issues
```bash
dart fix --apply
```

## Architecture Principles

1. **Single Responsibility**: Each class has one reason to change
2. **Dependency Inversion**: Depend on abstractions, not concretions
3. **Separation of Concerns**: Clear layer boundaries
4. **Configuration Over Code**: API URLs in constants
5. **Type Safety**: Full null safety throughout
6. **Error Handling**: Comprehensive exception handling with user-friendly messages

## Extensibility

### Adding New Features
1. Create domain entity in `domain/entities/`
2. Create abstract repository in `domain/repositories/`
3. Create model in `data/models/`
4. Create datasource in `data/datasources/`
5. Implement repository in `data/repositories/`
6. Create providers in `presentation/providers/`
7. Create UI pages/widgets in `presentation/`

### Changing API Base URL
Edit `lib/constants/config.dart`:
```dart
static const String baseUrl = 'https://your-new-url.com';
```

### Adding Authentication
1. Create auth provider in `presentation/providers/`
2. Inject JWT token in Dio interceptor
3. Update repository methods to use authenticated endpoints

## Performance Considerations

- Pagination with offset-based loading
- Image caching with cached_network_image
- Provider memoization for performance
- Efficient widget rebuilds with Riverpod

## Future Enhancements

- [ ] Photographer authentication with JWT
- [ ] Local database caching (Hive/SQLite)
- [ ] AWS S3 direct image URLs
- [ ] Offline support with sync
- [ ] Image upload for photographers
- [ ] Comment moderation dashboard
- [ ] Analytics integration
- [ ] Push notifications

## License

Proprietary - Photo Gallery Application

## Support

For issues or questions, contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: 2026-04-07  
**Status**: Production-Ready ✅
