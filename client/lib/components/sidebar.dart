import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class AppSidebar extends StatelessWidget {
  const AppSidebar({super.key});

  @override
  Widget build(BuildContext context) => Container(
    width: 72,
    margin: const EdgeInsets.only(right: 8),
    decoration: const BoxDecoration(
      color: Color(0x3307111f),
      border: Border(right: BorderSide(color: Color(0x12ffffff))),
    ),
    child: Column(
      children: [
        const SizedBox(height: 24),
        const Icon(Icons.hub_rounded, color: AppColors.cyan, size: 28),
        const SizedBox(height: 38),
        ...[
          Icons.dashboard_rounded,
          Icons.forum_outlined,
          Icons.people_outline_rounded,
          Icons.smart_toy_outlined,
          Icons.settings_outlined,
        ].map(
          (icon) => IconButton(
            onPressed: () {},
            icon: Icon(icon, color: Colors.white70),
          ),
        ),
      ],
    ),
  );
}
