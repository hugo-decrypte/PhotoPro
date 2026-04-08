import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:photo_gallery/core/extensions/context_extension.dart';
import 'package:photo_gallery/presentation/providers/private_gallery_provider.dart';
import 'package:photo_gallery/router/app_router.dart';

class AccessPrivateGalleryDialog extends ConsumerStatefulWidget {
  const AccessPrivateGalleryDialog({Key? key}) : super(key: key);

  @override
  ConsumerState<AccessPrivateGalleryDialog> createState() =>
      _AccessPrivateGalleryDialogState();
}

class _AccessPrivateGalleryDialogState
    extends ConsumerState<AccessPrivateGalleryDialog> {
  late TextEditingController _galleryIdController;
  late TextEditingController _codeController;
  bool _isSubmitting = false;
  String? _errorMessage;

  @override
  void initState() {
    super.initState();
    _galleryIdController = TextEditingController();
    _codeController = TextEditingController();
  }

  @override
  void dispose() {
    _galleryIdController.dispose();
    _codeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Accéder à une galerie privée'),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: _galleryIdController,
              decoration: InputDecoration(
                labelText: 'ID de la galerie',
                hintText: 'Entrez l\'identifiant de la galerie',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              enabled: !_isSubmitting,
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _codeController,
              decoration: InputDecoration(
                labelText: 'Code d\'accès',
                hintText: 'Entrez le code d\'accès',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              obscureText: true,
              enabled: !_isSubmitting,
            ),
            if (_errorMessage != null) ...[
              const SizedBox(height: 16),
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  border: Border.all(color: Colors.red),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  'Erreur',
                  style: TextStyle(color: Colors.red.shade700),
                ),
              ),
            ],
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: _isSubmitting
              ? null
              : () {
                  Navigator.pop(context);
                },
          child: const Text('Annuler'),
        ),
        ElevatedButton(
          onPressed: _isSubmitting ? null : _submit,
          child: _isSubmitting
              ? const SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(strokeWidth: 2),
                )
              : const Text('Accéder'),
        ),
      ],
    );
  }

  Future<void> _submit() async {
    final galleryId = _galleryIdController.text.trim();
    final code = _codeController.text.trim();

    if (galleryId.isEmpty) {
      setState(() => _errorMessage = 'Veuillez entrer un identifiant de galerie');
      return;
    }

    if (code.isEmpty) {
      setState(() => _errorMessage = 'Veuillez entrer le code d\'accès');
      return;
    }

    setState(() {
      _isSubmitting = true;
      _errorMessage = null;
    });

    try {
      await ref
          .read(privateGalleryProvider.notifier)
          .accessPrivateGallery(galleryId, code);

      if (mounted) {
        final state = ref.read(privateGalleryProvider);
        if (state.isAccessGranted && state.gallery != null) {
          Navigator.pop(context);
          context.pushNamed(
            AppRoute.galleryDetail.name,
            pathParameters: {'galleryId': galleryId},
            extra: state.gallery,
          );
        } else if (state.error != null) {
          setState(() => _errorMessage = state.error);
        }
      }
    } catch (e) {
      setState(() => _errorMessage = 'Erreur: $e');
    } finally {
      if (mounted) {
        setState(() => _isSubmitting = false);
      }
    }
  }
}
