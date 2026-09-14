import 'package:flutter/material.dart';

import '../models/conversation.dart';
import '../theme/app_theme.dart';

class ConversationTile extends StatelessWidget {
  const ConversationTile({
    super.key,
    required this.item,
    required this.selected,
    required this.onTap,
  });

  final Conversation item;
  final bool selected;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Card(
    elevation: 0,
    color: selected ? const Color(0x1a67e8f9) : AppColors.panel,
    child: ListTile(
      onTap: onTap,
      leading: CircleAvatar(
        backgroundColor: AppColors.navy,
        child: Text(item.name[0]),
      ),
      title: Text(
        item.name,
        style: const TextStyle(fontWeight: FontWeight.w700),
      ),
      subtitle: Text('${item.preview}\n${item.status}', maxLines: 2),
      isThreeLine: true,
      trailing: Text(
        item.time,
        style: const TextStyle(color: AppColors.muted, fontSize: 11),
      ),
    ),
  );
}
