import 'package:flutter/material.dart';

import 'pages/dashboard_page.dart';
import 'pages/login_page.dart';
import 'theme/app_theme.dart';

class AIChatbotApp extends StatefulWidget {
  const AIChatbotApp({super.key});

  @override
  State<AIChatbotApp> createState() => _AIChatbotAppState();
}

class _AIChatbotAppState extends State<AIChatbotApp> {
  bool authenticated = false;

  @override
  Widget build(BuildContext context) => MaterialApp(
    debugShowCheckedModeBanner: false,
    title: 'AI WhatsApp',
    theme: AppTheme.dark,
    home:
        authenticated
            ? DashboardPage(onLogout: _logout)
            : LoginPage(onLogin: _login),
  );

  void _login() => setState(() => authenticated = true);
  void _logout() => setState(() => authenticated = false);
}
