import 'dart:typed_data';
import 'package:flutter/material.dart';
import '../models/gallery.dart';
import '../services/gallery_service.dart';
import 'comments_screen.dart';

class GalleryScreen extends StatelessWidget {
final Gallery gallery;

const GalleryScreen({super.key, required this.gallery});

@override
Widget build(BuildContext context) {
final service = GalleryService();

return Scaffold(
appBar: AppBar(
title: Text(gallery.title),
),
body: Padding(
padding: const EdgeInsets.all(16),
child: gallery.photos.isEmpty
? const Center(child: Text('Aucune photo dans cette galerie'))
: Column(
crossAxisAlignment: CrossAxisAlignment.start,
children: [
Text(
'Galerie : ${gallery.title}',
style: const TextStyle(
color: Color(0xFF1F2430),
fontSize: 20,
),
),
const SizedBox(height: 16),
Expanded(
child: GridView.builder(
itemCount: gallery.photos.length,
gridDelegate:
const SliverGridDelegateWithFixedCrossAxisCount(
crossAxisCount: 2,
crossAxisSpacing: 12,
mainAxisSpacing: 12,
childAspectRatio: 0.8,
),
itemBuilder: (context, index) {
final photo = gallery.photos[index];

return Card(
clipBehavior: Clip.antiAlias,
child: Column(
children: [
Expanded(
child: FutureBuilder<Uint8List>(
future: service.getPhotoBytes(photo.photoId),
builder: (context, snapshot) {
if (snapshot.connectionState ==
ConnectionState.waiting) {
return const Center(
child: CircularProgressIndicator(),
);
}

if (snapshot.hasError || !snapshot.hasData) {
return Container(
color: const Color(0xFFEAEAF2),
alignment: Alignment.center,
child: Column(
mainAxisAlignment:
MainAxisAlignment.center,
children: [
const Icon(
Icons.broken_image,
size: 40,
color: Color(0xFF6D7385),
),
const SizedBox(height: 8),
Text(
photo.photoId,
style:
const TextStyle(fontSize: 10),
textAlign: TextAlign.center,
),
],
),
);
}

return Image.memory(
snapshot.data!,
width: double.infinity,
fit: BoxFit.cover,
);
},
),
),
Padding(
padding: const EdgeInsets.all(8),
child: SizedBox(
width: double.infinity,
child: ElevatedButton(
onPressed: () {
Navigator.push(
context,
MaterialPageRoute(
builder: (_) => CommentsScreen(
galleryId: gallery.id,
photoId: photo.photoId,
),
),
);
},
child: const Text('Commentaires'),
),
),
),
],
),
);
},
),
),
],
),
),
);
}
}

