import 'package:flutter/material.dart';

abstract final class AppColors {
  static const cyan = Color(0xff67e8f9);
  static const muted = Color(0xff94a3b8);
  static const panel = Color(0x12ffffff);
  static const navy = Color(0xff164e63);
}

abstract final class AppTheme {
  static ThemeData get dark => ThemeData(
    brightness: Brightness.dark,
    scaffoldBackgroundColor: const Color(0xff0f172a),
    colorScheme: ColorScheme.fromSeed(
      seedColor: AppColors.cyan,
      brightness: Brightness.dark,
    ),
    useMaterial3: true,
  );
}
