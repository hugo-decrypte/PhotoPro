import 'package:flutter/material.dart';
import '../models/comment_model.dart';
import '../services/gallery_service.dart';

class CommentsScreen extends StatefulWidget {
  final String galleryId;
  final String photoId;

  const CommentsScreen({
    super.key,
    required this.galleryId,
    required this.photoId,
  });

  @override
  State<CommentsScreen> createState() => _CommentsScreenState();
}

class _CommentsScreenState extends State<CommentsScreen> {
  final service = GalleryService();
  final authorController = TextEditingController();
  final contentController = TextEditingController();

  List<CommentModel> comments = [];
  bool loading = true;
  bool sending = false;
  String error = '';

  @override
  void initState() {
    super.initState();
    loadComments();
  }

  Future<void> loadComments() async {
    setState(() {
      loading = true;
      error = '';
    });

    try {
      final allComments = await service.getComments(widget.galleryId);
      comments =
          allComments.where((c) => c.photoId == widget.photoId).toList();
    } catch (e) {
      setState(() {
            error = "Impossible de charger les commentaires";
      });
    } finally {
      if (mounted) {
        setState(() {
          loading = false;
        });
      }
    }
  }

  Future<void> sendComment() async {
    if (contentController.text.trim().isEmpty) return;

    setState(() {
      sending = true;
      error = '';
    });
try {
      await service.addComment(
        galleryId: widget.galleryId,
        photoId: widget.photoId,
        authorName: authorController.text.trim().isEmpty
            ? null
            : authorController.text.trim(),
        content: contentController.text.trim(),
      );

      authorController.clear();
      contentController.clear();
      await loadComments();
    } catch (e) {
      setState(() {
        error = "Impossible d'ajouter le commentaire : $e";
      });
    } finally {
      if (mounted) {
        setState(() {
          sending = false;
        });
      }
    }
  }

  @override
  void dispose() {
    authorController.dispose();
    contentController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Commentaires'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            Expanded(
              child: loading
                  ? const Center(child: CircularProgressIndicator())
                  : comments.isEmpty
                      ? const Center(child: Text('Aucun commentaire'))
                      : ListView.builder(
                          itemCount: comments.length,
                          itemBuilder: (context, index) {
                            final comment = comments[index];
                            return Card(
                              child: ListTile(
                                title: Text(
                                        (comment.authorName != null &&
                                      comment.authorName!.trim().isNotEmpty)
                                    ? comment.authorName!
                                      : 'Anonyme',
                                ),
                                subtitle: Text(comment.content),
                              ),
                            );
                          },
                        ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: authorController,
              decoration: const InputDecoration(
                labelText: 'Nom (optionnel)',
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: contentController,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'Votre commentaire',
              ),
            ),
            const SizedBox(height: 12),
            ElevatedButton(
              onPressed: sending ? null : sendComment,
              child: Text(sending ? 'Envoi...' : 'Ajouter le commentaire'),
            ),
            if (error.isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(
                error,
                style: const TextStyle(color: Color(0xFFD93B3B)),
              ),
            ],
          ],
        ),
      ),
    );
  }
}