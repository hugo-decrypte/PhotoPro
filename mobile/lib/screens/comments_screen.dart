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
  // Suppression du champ auteur, anonymat obligatoire
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
        content: contentController.text.trim(),
      );

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
                                title: Text(comment.content),
                                subtitle: comment.authorName != null
                                    ? Text(comment.authorName!)
                                    : null,
                              ),
                            );
                          },
                        ),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: contentController,
              minLines: 2,
              maxLines: 4,
              decoration: const InputDecoration(
                labelText: 'Votre commentaire',
              ),
            ),
            const SizedBox(height: 8),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: sending ? null : sendComment,
                child: sending
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : const Text('Ajouter le commentaire'),
              ),
            ),
            if (error.isNotEmpty)
              Padding(
                padding: const EdgeInsets.only(top: 8),
                child: Text(
                  error,
                  style: const TextStyle(color: Colors.red),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
