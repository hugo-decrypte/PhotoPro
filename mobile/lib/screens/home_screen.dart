import 'package:flutter/material.dart';
import '../services/gallery_service.dart';
import 'gallery_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final idController = TextEditingController();
  final codeController = TextEditingController();
  final service = GalleryService();

  String error = '';
  bool loading = false;

  Future<void> access() async {
    setState(() {
      loading = true;
      error = '';
    });

    try {
      final gallery = await service.accessPrivateGallery(
        idController.text.trim(),
        codeController.text.trim(),
      );

      if (!mounted) return;

      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => GalleryScreen(gallery: gallery),
        ),
      );
    } catch (e) {
      setState(() {
        error = "Erreur d'accès";
      });
    } finally {
      if (mounted) {
        setState(() {
          loading = false;
        });
      }
    }
  }

  @override
  void dispose() {
    idController.dispose();
    codeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('PhotoPro'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: idController,
              decoration: const InputDecoration(
                labelText: 'Gallery ID',
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: codeController,
              decoration: const InputDecoration(
                labelText: 'Code',
              ),
            ),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: loading ? null : access,
              child: Text(loading ? 'Chargement...' : 'Accéder'),
            ),
            const SizedBox(height: 12),
            if (error.isNotEmpty)
              Text(
                error,
                style: const TextStyle(
                  color: Color(0xFFD93B3B),
                ),
              ),
          ],
        ),
      ),
    );
  }
}