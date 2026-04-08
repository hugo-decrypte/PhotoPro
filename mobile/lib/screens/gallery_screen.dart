import 'package:flutter/material.dart';
import '../models/gallery.dart';

class GalleryScreen extends StatelessWidget {
  final Gallery gallery;

  const GalleryScreen({super.key, required this.gallery});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(gallery.title),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Text(
          'Galerie : ${gallery.title}',
          style: const TextStyle(
            color: Color(0xFF1F2430),
            fontSize: 18,
          ),
        ),
      ),
    );
  }
}