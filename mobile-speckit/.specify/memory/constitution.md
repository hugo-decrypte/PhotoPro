# Photo Gallery Flutter App Constitution

## Core Principles

### I. Clean Architecture
All code must follow clean architecture with clear separation of layers: Presentation, Domain, and Data. API endpoints must be configurable at a single location (constants/config) to enable seamless API migration. Dependencies flow inward only (outer layers depend on inner layers, never vice versa).

### II. State Management via Provider Pattern
Use Riverpod for state management. All API calls and business logic must be wrapped in providers (StateNotifier, FutureProvider, etc.). Presentation layer must ONLY interact with providers, never directly with repositories or services.

### III. Data Layer Abstraction
Implement repositories as abstractions with concrete implementations. All API calls happen through repositories via the Dio HTTP client. Database models must be separate from domain entities (DTOs for serialization, entities for business logic).

### IV. Type Safety & Null Safety
Leverage Dart's null safety (non-nullable by default). Use sealed classes for error handling and response states. All API responses must be validated before reaching domain layer.

### V. Code Organization
- `lib/presentation/` - UI widgets, pages, controllers
- `lib/domain/` - entities, repositories (abstract), usecases
- `lib/data/` - datasources, repositories (implementations), models
- `lib/constants/` - API base URL, routes, app constants
- `lib/core/` - utilities, extensions, shared widgets

## Feature Specifications

### Gallery Access
- **Public Galleries**: 
  - List all galleries: GET /galeries (displays cover_photo_id for each)
  - Get gallery detail + photos: GET /galeries/{id}/photos (returns all photos in gallery)
- **Private Galleries**: 
  - Require access code validation: GET /galerie/{id}/privee?code=CODE
  - Returns gallery detail + photos array in single response
- **Gallery Cover**: Display single cover_photo_id when listing all galleries
- **Gallery Detail**: Display all photos returned from respective endpoints

### Photo Data Model
- id (uuid), photographer_id (uuid), title, mime_type, size_bytes, original_filename, s3_key, uploaded_at
- Images stored in AWS S3-compatible storage via s3_key

### Anonymous User Features
- Browse public galleries (list view with cover photo)
- Access private gallery with code
- Add comments to private gallery photos:
  - POST /galeries/{id}/photos/{photoId}/comments
  - Body: { authorName, content, createdAt (auto-filled with current date) }

### Future Authentication (Design for extensibility)
- Prepare provider architecture to support photographer login (JWT token storage, auth state)
- Separate anonymous vs authenticated user flows at presentation layer
- Do NOT implement authentication logic yet; ensure architecture allows it

## Technical Stack
- **Framework**: Flutter (null-safe)
- **State Management**: Riverpod (Pub.dev version or Flutter Riverpod)
- **HTTP Client**: Dio with interceptors for error handling & base URL
- **Architecture**: Clean Architecture with Provider pattern
- **API Base URL**: Single source of truth (constants/config.dart)

## Security & Error Handling
- All HTTP errors wrapped in custom exception classes
- User-friendly error messages in presentation layer
- API responses validated against expected schemas
- No sensitive data logged to console in production

## Governance
Constitution supersedes all other practices. All PRs must verify clean architecture compliance and provider usage. Changes to API structure must be reflected in data layer first, ensuring minimal disruption to presentation and domain layers.

**Version**: 1.0.0 | **Ratified**: 2026-04-07 | **Last Amended**: 2026-04-07
