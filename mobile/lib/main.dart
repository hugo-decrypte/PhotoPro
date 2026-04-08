import 'package:flutter/material.dart';
import 'screens/home_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'PhotoPro',

      theme: ThemeData(
        scaffoldBackgroundColor: const Color(0xFFF3F1F6),

        appBarTheme: const AppBarTheme(
          backgroundColor: Color.fromARGB(255, 27, 27, 27),
          foregroundColor: Colors.white,
          elevation: 0,
        ),

        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(
            borderSide: const BorderSide(color: Color(0xFFD8DCE8)),
          ),
          enabledBorder: OutlineInputBorder(
            borderSide: const BorderSide(color: Color(0xFFD8DCE8)),
          ),
          focusedBorder: OutlineInputBorder(
            borderSide: const BorderSide(color: Color(0xFF8EA0F0)),
          ),
          labelStyle: const TextStyle(color: Color(0xFF6D7385)),
        ),

        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color.fromARGB(255, 27, 27, 27),
            foregroundColor: Colors.white,
          ),
        ),

        textTheme: const TextTheme(
          bodyMedium: TextStyle(color: Color(0xFF1F2430)),
        ),
      ),

      home: const HomeScreen(),
    );
  }
}
