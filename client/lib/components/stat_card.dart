import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class StatCard extends StatelessWidget {
  const StatCard(
    this.value,
    this.label,
    this.delta,
    this.icon,
    this.color, {
    super.key,
  });

  final String value;
  final String label;
  final String delta;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: 160,
    child: Card(
      elevation: 0,
      color: AppColors.panel,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color),
            const SizedBox(height: 14),
            Text(
              label,
              style: const TextStyle(color: AppColors.muted, fontSize: 12),
            ),
            const SizedBox(height: 5),
            Row(
              children: [
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 25,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(width: 8),
                Flexible(
                  child: Text(
                    delta,
                    style: TextStyle(color: color, fontSize: 11),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    ),
  );
}
