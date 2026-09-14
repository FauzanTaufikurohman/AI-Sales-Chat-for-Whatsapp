import 'package:flutter/material.dart';

import '../components/app_shell.dart';
import '../components/button.dart';
import '../theme/app_theme.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key, required this.onLogin});

  final VoidCallback onLogin;

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final login = TextEditingController(text: 'demo@aichatbot.id');
  final password = TextEditingController(text: 'password');
  String? error;

  @override
  void dispose() {
    login.dispose();
    password.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => AppShell(
    child: Center(
      child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 430),
        child: Card(
          color: AppColors.panel,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
            side: const BorderSide(color: Color(0x1affffff)),
          ),
          child: Padding(
            padding: const EdgeInsets.all(32),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'AI WHATSAPP',
                  style: TextStyle(
                    color: AppColors.cyan,
                    fontSize: 12,
                    letterSpacing: 2,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 12),
                const Text(
                  'Selamat datang kembali',
                  style: TextStyle(fontSize: 28, fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Kelola chatbot dan percakapan customer dari satu workspace.',
                  style: TextStyle(color: AppColors.muted, height: 1.5),
                ),
                if (error != null) ...[
                  const SizedBox(height: 18),
                  Text(
                    error!,
                    style: const TextStyle(color: Color(0xffffa3b5)),
                  ),
                ],
                const SizedBox(height: 28),
                AppField(label: 'Email / Nomor WhatsApp', controller: login),
                const SizedBox(height: 16),
                AppField(
                  label: 'Password',
                  controller: password,
                  obscureText: true,
                ),
                const SizedBox(height: 22),
                AppButton(
                  label: 'Masuk ke workspace',
                  icon: Icons.arrow_forward_rounded,
                  onPressed: _submit,
                ),
                const SizedBox(height: 22),
                const Center(
                  child: Text(
                    'Demo lokal • data dummy',
                    style: TextStyle(color: AppColors.muted, fontSize: 12),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    ),
  );

  void _submit() {
    if (login.text.trim().isEmpty || password.text.isEmpty) {
      setState(() => error = 'Email dan password wajib diisi.');
      return;
    }
    widget.onLogin();
  }
}
