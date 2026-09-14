import 'package:flutter/material.dart';

const _cyan = Color(0xff67e8f9);
const _ink = Color(0xff07111f);

class AppButton extends StatelessWidget {
  const AppButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.icon,
  });

  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;

  @override
  Widget build(BuildContext context) => SizedBox(
    height: 48,
    child: FilledButton.icon(
      onPressed: onPressed,
      icon: icon == null ? const SizedBox.shrink() : Icon(icon, size: 18),
      label: Text(label),
      style: FilledButton.styleFrom(
        backgroundColor: _cyan,
        foregroundColor: _ink,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        textStyle: const TextStyle(fontWeight: FontWeight.w700),
      ),
    ),
  );
}

class AppField extends StatelessWidget {
  const AppField({
    super.key,
    required this.label,
    required this.controller,
    this.obscureText = false,
  });

  final String label;
  final TextEditingController controller;
  final bool obscureText;

  @override
  Widget build(BuildContext context) => TextField(
    controller: controller,
    obscureText: obscureText,
    style: const TextStyle(color: Colors.white),
    decoration: InputDecoration(
      labelText: label,
      labelStyle: const TextStyle(color: Color(0xff94a3b8)),
      filled: true,
      fillColor: const Color(0x6607111f),
      border: _border(const Color(0x26ffffff)),
      enabledBorder: _border(const Color(0x26ffffff)),
      focusedBorder: _border(_cyan),
    ),
  );

  OutlineInputBorder _border(Color color) => OutlineInputBorder(
    borderRadius: BorderRadius.circular(10),
    borderSide: BorderSide(color: color),
  );
}
