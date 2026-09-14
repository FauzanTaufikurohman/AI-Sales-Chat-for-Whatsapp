import 'package:flutter/material.dart';

import '../components/app_shell.dart';
import '../components/bot_status_card.dart';
import '../components/conversation_tile.dart';
import '../components/sidebar.dart';
import '../components/stat_card.dart';
import '../models/conversation.dart';
import '../theme/app_theme.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key, required this.onLogout});

  final VoidCallback onLogout;

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  int selected = 0;

  @override
  Widget build(BuildContext context) => AppShell(
    child: LayoutBuilder(
      builder: (context, constraints) {
        final compact = constraints.maxWidth < 760;
        return Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (!compact) const AppSidebar(),
            Expanded(child: _content(context, compact)),
          ],
        );
      },
    ),
  );

  Widget _content(BuildContext context, bool compact) => SingleChildScrollView(
    padding: EdgeInsets.all(compact ? 20 : 42),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _header(),
        const SizedBox(height: 30),
        const Wrap(
          spacing: 12,
          runSpacing: 12,
          children: [
            StatCard(
              '128',
              'Percakapan',
              '+12%',
              Icons.forum_outlined,
              AppColors.cyan,
            ),
            StatCard(
              '12',
              'Belum dibaca',
              'Hari ini',
              Icons.mark_email_unread_outlined,
              Color(0xfffbbf24),
            ),
            StatCard(
              '04',
              'Perlu bantuan',
              'Action',
              Icons.priority_high_rounded,
              Color(0xfffb7185),
            ),
            StatCard(
              '21',
              'AI aktif',
              'Processing',
              Icons.auto_awesome_outlined,
              Color(0xffa78bfa),
            ),
          ],
        ),
        const SizedBox(height: 30),
        const BotStatusCard(),
        const SizedBox(height: 30),
        Text(
          'Percakapan customer',
          style: Theme.of(
            context,
          ).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w700),
        ),
        const SizedBox(height: 12),
        ...demoConversations.asMap().entries.map(
          (entry) => ConversationTile(
            item: entry.value,
            selected: entry.key == selected,
            onTap: () => setState(() => selected = entry.key),
          ),
        ),
      ],
    ),
  );

  Widget _header() => Row(
    children: [
      const Expanded(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'WORKSPACE',
              style: TextStyle(
                color: AppColors.cyan,
                fontSize: 12,
                letterSpacing: 2,
                fontWeight: FontWeight.w700,
              ),
            ),
            SizedBox(height: 10),
            Text(
              'Dashboard',
              style: TextStyle(fontSize: 32, fontWeight: FontWeight.w700),
            ),
            SizedBox(height: 6),
            Text(
              'Pantau chatbot WhatsApp dan percakapan customer.',
              style: TextStyle(color: AppColors.muted),
            ),
          ],
        ),
      ),
      IconButton(
        onPressed: widget.onLogout,
        tooltip: 'Logout',
        icon: const Icon(Icons.logout_rounded),
      ),
      const CircleAvatar(backgroundColor: AppColors.navy, child: Text('RP')),
    ],
  );
}
