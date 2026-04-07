import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:photo_gallery/core/widgets/loading_widget.dart';
import 'package:photo_gallery/core/widgets/error_widget.dart' as error_widget;

void main() {
  group('Widget Tests', () {
    testWidgets('LoadingWidget renders properly', (WidgetTester tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: LoadingWidget(),
          ),
        ),
      );

      expect(find.byType(CircularProgressIndicator), findsOneWidget);
    });

    testWidgets('ErrorWidget displays message and retry button',
        (WidgetTester tester) async {
      await tester.pumpWidget(
        MaterialApp(
          home: Scaffold(
            body: error_widget.ErrorWidget(
              message: 'Test error',
              onRetry: () {},
            ),
          ),
        ),
      );

      expect(find.text('Test error'), findsOneWidget);
      expect(find.text('Retry'), findsOneWidget);
    });
  });
}
