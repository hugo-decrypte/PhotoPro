import 'package:flutter/material.dart';
import '../services/gallery_service.dart';
import 'gallery_screen.dart';
import '../models/gallery.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final idController = TextEditingController();
  final codeController = TextEditingController();
  final service = GalleryService();

  List<Gallery> publicGalleries = [];
  String error = '';
  bool loading = true;

  @override
  void initState() {
    super.initState();
    loadPublicGalleries();
  }

  Future<void> loadPublicGalleries() async {
    setState(() {
      loading = true;
      error = '';
    });
    try {
      publicGalleries = await service.getPublicGalleries();
    } catch (e) {
      error = 'Erreur lors du chargement des galeries publiques';
    } finally {
      setState(() {
        loading = false;
      });
    }
  }

  void accessPrivateGallery() async {
    final id = idController.text.trim();
    final code = codeController.text.trim();
    if (id.isEmpty || code.isEmpty) return;
    setState(() => loading = true);
    try {
      final gallery = await service.accessPrivateGallery(id, code);
      if (!mounted) return;
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => GalleryScreen(gallery: gallery),
        ),
      );
    } catch (e) {
      setState(() {
        error = "ID ou code invalide, ou galerie privée introuvable";
      });
    } finally {
      setState(() => loading = false);
    }
  }

  @override
  void dispose() {
    codeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('PhotoPro')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Galeries publiques', style: Theme.of(context).textTheme.titleLarge),
            const SizedBox(height: 8),
            Expanded(
              child: loading
                  ? const Center(child: CircularProgressIndicator())
                  : publicGalleries.isEmpty
                      ? const Center(child: Text('Aucune galerie publique'))
                      : ListView.builder(
                          itemCount: publicGalleries.length,
                          itemBuilder: (context, index) {
                            final gallery = publicGalleries[index];
                            return ListTile(
                              title: Text(gallery.title),
                              subtitle: Text(gallery.description ?? ''),
                              onTap: () {
                                Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => GalleryScreen(gallery: gallery),
                                  ),
                                );
                              },
                            );
                          },
                        ),
            ),
            const SizedBox(height: 16),
            Text('Accéder à une galerie privée', style: Theme.of(context).textTheme.titleMedium),
            TextField(
              controller: idController,
              decoration: const InputDecoration(
                labelText: 'ID de la galerie',
              ),
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: codeController,
                    decoration: const InputDecoration(
                      labelText: 'Code d\'accès',
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                ElevatedButton(
                  onPressed: loading ? null : accessPrivateGallery,
                  child: const Text('Valider'),
                ),
              ],
            ),
            if (error.isNotEmpty)
              Padding(
                padding: const EdgeInsets.only(top: 8),
                child: Text(error, style: const TextStyle(color: Colors.red)),
              ),
          ],
        ),
      ),
    );
  }
}