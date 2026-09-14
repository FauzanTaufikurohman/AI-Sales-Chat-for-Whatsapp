import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class BotStatusCard extends StatelessWidget {
  const BotStatusCard({super.key});

  @override
  Widget build(BuildContext context) => Card(
    elevation: 0,
    color: const Color(0x1a67e8f9),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(16),
      side: const BorderSide(color: Color(0x3367e8f9)),
    ),
    child: Padding(
      padding: const EdgeInsets.all(20),
      child: Row(
        children: [
          const CircleAvatar(
            backgroundColor: Color(0x3367e8f9),
            child: Icon(Icons.smart_toy_outlined, color: AppColors.cyan),
          ),
          const SizedBox(width: 14),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'WhatsApp Chatbot',
                  style: TextStyle(fontWeight: FontWeight.w700),
                ),
                SizedBox(height: 5),
                Text(
                  '+62 812 3456 7890',
                  style: TextStyle(color: AppColors.muted, fontSize: 12),
                ),
              ],
            ),
          ),
          const Chip(
            avatar: Icon(Icons.circle, size: 9, color: Color(0xff34d399)),
            label: Text('Aktif'),
          ),
        ],
      ),
    ),
  );
}
