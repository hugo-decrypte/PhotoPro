import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:photo_gallery/core/extensions/context_extension.dart';
import 'package:photo_gallery/presentation/providers/private_gallery_provider.dart';

class PrivateGalleryAccessDialog extends ConsumerStatefulWidget {
  final String galleryId;

  const PrivateGalleryAccessDialog({
    Key? key,
    required this.galleryId,
  }) : super(key: key);

  @override
  ConsumerState<PrivateGalleryAccessDialog> createState() =>
      _PrivateGalleryAccessDialogState();
}

class _PrivateGalleryAccessDialogState
    extends ConsumerState<PrivateGalleryAccessDialog> {
  late TextEditingController _codeController;

  @override
  void initState() {
    super.initState();
    _codeController = TextEditingController();
  }

  @override
  void dispose() {
    _codeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final galleryState = ref.watch(privateGalleryProvider);

    return AlertDialog(
      title: const Text('Private Gallery'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Text('Enter the access code to view this gallery'),
          const SizedBox(height: 16),
          TextField(
            controller: _codeController,
            decoration: InputDecoration(
              hintText: 'Access code',
              errorText: galleryState.error,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
              ),
            ),
            obscureText: true,
            enabled: !galleryState.isLoading,
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: galleryState.isLoading
              ? null
              : () {
                  Navigator.pop(context);
                  ref.read(privateGalleryProvider.notifier).reset();
                },
          child: const Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: galleryState.isLoading ? null : _submit,
          child: galleryState.isLoading
              ? const SizedBox(
                  width: 20,
                  height: 20,
                  child: CircularProgressIndicator(strokeWidth: 2),
                )
              : const Text('Access'),
        ),
      ],
    );
  }

  Future<void> _submit() async {
    final code = _codeController.text.trim();

    if (code.isEmpty) {
      context.showErrorSnackBar('Please enter an access code');
      return;
    }

    await ref
        .read(privateGalleryProvider.notifier)
        .accessPrivateGallery(widget.galleryId, code);

    if (mounted) {
      final state = ref.read(privateGalleryProvider);
      if (state.isAccessGranted) {
        Navigator.pop(context);
      }
    }
  }
}
