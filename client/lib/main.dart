import 'package:flutter/material.dart';

void main() => runApp(const SalesDashboardApp());

class SalesDashboardApp extends StatelessWidget {
  const SalesDashboardApp({super.key});

  @override
  Widget build(BuildContext context) => MaterialApp(
    title: 'Sales Console',
    debugShowCheckedModeBanner: false,
    theme: ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: const Color(0xff0e7490),
        brightness: Brightness.light,
      ),
      scaffoldBackgroundColor: const Color(0xfff5f7f8),
      useMaterial3: true,
    ),
    home: const DashboardPage(),
  );
}

class Conversation {
  const Conversation(
    this.name,
    this.preview,
    this.priority,
    this.status,
    this.time,
  );
  final String name;
  final String preview;
  final String priority;
  final String status;
  final String time;
}

const conversations = [
  Conversation(
    'Andi Pratama',
    'Talent A tersedia tanggal 20?',
    'HIGH',
    'Needs attention',
    '09:42',
  ),
  Conversation(
    'Budi Santoso',
    'Request video talent untuk campaign.',
    'NORMAL',
    'AI active',
    '09:18',
  ),
  Conversation(
    'Citra Wijaya',
    'Bagaimana detail harganya?',
    'LOW',
    'Waiting sales',
    '08:56',
  ),
  Conversation(
    'Dewi Lestari',
    'Terima kasih, saya tunggu kabarnya.',
    'NORMAL',
    'Closed',
    'Yesterday',
  ),
];

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  Conversation selected = conversations.first;
  String filter = 'All';

  @override
  Widget build(BuildContext context) {
    final filtered =
        filter == 'All'
            ? conversations
            : conversations.where((item) => item.status == filter).toList();
    return Scaffold(
      body: SafeArea(
        child: Row(
          children: [
            const _Sidebar(),
            Expanded(child: _content(context, filtered)),
          ],
        ),
      ),
    );
  }

  Widget _content(BuildContext context, List<Conversation> items) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final compact = constraints.maxWidth < 850;
        return SingleChildScrollView(
          padding: EdgeInsets.all(compact ? 20 : 36),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Good morning, Raka',
                          style: TextStyle(color: Color(0xff64748b)),
                        ),
                        SizedBox(height: 4),
                        Text(
                          'Sales overview',
                          style: TextStyle(
                            fontSize: 30,
                            fontWeight: FontWeight.w800,
                            color: Color(0xff102a43),
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    onPressed: () {},
                    icon: const Icon(Icons.notifications_none_rounded),
                  ),
                  const CircleAvatar(
                    backgroundColor: Color(0xffd5f5ef),
                    child: Text('RP'),
                  ),
                ],
              ),
              const SizedBox(height: 28),
              const Wrap(
                spacing: 12,
                runSpacing: 12,
                children: [
                  StatTile(
                    'Total conversations',
                    '128',
                    '+12%',
                    Icons.forum_outlined,
                    Color(0xff0e7490),
                  ),
                  StatTile(
                    'Unread',
                    '12',
                    '+4 today',
                    Icons.mark_email_unread_outlined,
                    Color(0xffd97706),
                  ),
                  StatTile(
                    'Human required',
                    '04',
                    'Needs action',
                    Icons.priority_high_rounded,
                    Color(0xffdc2626),
                  ),
                  StatTile(
                    'AI active',
                    '21',
                    'Processing',
                    Icons.auto_awesome_outlined,
                    Color(0xff7c3aed),
                  ),
                ],
              ),
              const SizedBox(height: 32),
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(child: _conversationList(items)),
                  if (!compact) ...[
                    const SizedBox(width: 24),
                    Expanded(child: _detailPanel()),
                  ],
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _conversationList(List<Conversation> items) => Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      const Text(
        'Recent conversations',
        style: TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.bold,
          color: Color(0xff102a43),
        ),
      ),
      const SizedBox(height: 10),
      SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children:
              ['All', 'Needs attention', 'AI active', 'Waiting sales', 'Closed']
                  .map(
                    (item) => Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(item),
                        selected: filter == item,
                        onSelected: (_) => setState(() => filter = item),
                      ),
                    ),
                  )
                  .toList(),
        ),
      ),
      const SizedBox(height: 12),
      ...items.map(_conversationRow),
    ],
  );

  Widget _conversationRow(Conversation item) => Card(
    elevation: 0,
    color: item.name == selected.name ? const Color(0xffe9f7f7) : Colors.white,
    child: ListTile(
      onTap: () => setState(() => selected = item),
      leading: CircleAvatar(
        backgroundColor: const Color(0xffdbeafe),
        child: Text(item.name.substring(0, 1)),
      ),
      title: Text(
        item.name,
        style: const TextStyle(fontWeight: FontWeight.bold),
      ),
      subtitle: Text(
        '${item.preview}\n${item.priority}  |  ${item.status}',
        maxLines: 2,
      ),
      isThreeLine: true,
      trailing: Text(
        item.time,
        style: const TextStyle(fontSize: 11, color: Color(0xff94a3b8)),
      ),
    ),
  );

  Widget _detailPanel() => Card(
    elevation: 0,
    color: Colors.white,
    child: Padding(
      padding: const EdgeInsets.all(22),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            selected.name,
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
          ),
          const SizedBox(height: 4),
          const Text(
            '+62 812 3456 7890',
            style: TextStyle(color: Color(0xff64748b)),
          ),
          const Divider(height: 30),
          const Text(
            'AI insight',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: Color(0xff102a43),
            ),
          ),
          const SizedBox(height: 10),
          const Text(
            'Customer needs availability confirmation and portfolio for Talent A on September 20.',
            style: TextStyle(height: 1.4, color: Color(0xff7c2d12)),
          ),
          const SizedBox(height: 20),
          const Text(
            'Recommended action',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: Color(0xff102a43),
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Check the talent schedule, then send the available portfolio.',
            style: TextStyle(color: Color(0xff64748b)),
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            child: FilledButton.icon(
              onPressed: () {},
              icon: const Icon(Icons.reply_rounded),
              label: const Text('Open conversation'),
            ),
          ),
        ],
      ),
    ),
  );
}

class StatTile extends StatelessWidget {
  const StatTile(
    this.label,
    this.value,
    this.delta,
    this.icon,
    this.color, {
    super.key,
  });
  final String label;
  final String value;
  final String delta;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: 190,
    child: Card(
      elevation: 0,
      color: Colors.white,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color),
            const SizedBox(height: 14),
            Text(
              label,
              style: const TextStyle(fontSize: 12, color: Color(0xff64748b)),
            ),
            const SizedBox(height: 4),
            FittedBox(
              fit: BoxFit.scaleDown,
              alignment: Alignment.centerLeft,
              child: Row(
                children: [
                  Text(
                    value,
                    style: const TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.w800,
                      color: Color(0xff102a43),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    delta,
                    style: TextStyle(
                      fontSize: 11,
                      color: color,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    ),
  );
}

class _Sidebar extends StatelessWidget {
  const _Sidebar();

  @override
  Widget build(BuildContext context) => Container(
    width: 76,
    color: const Color(0xff102a43),
    child: Column(
      children: [
        const SizedBox(height: 26),
        const Icon(Icons.hub_rounded, color: Color(0xff6ee7d8), size: 28),
        const SizedBox(height: 40),
        ...[
          Icons.dashboard_rounded,
          Icons.forum_outlined,
          Icons.people_outline_rounded,
          Icons.smart_toy_outlined,
          Icons.settings_outlined,
        ].map(
          (icon) => Padding(
            padding: const EdgeInsets.only(bottom: 24),
            child: IconButton(
              onPressed: () {},
              icon: Icon(icon, color: Colors.white70),
            ),
          ),
        ),
      ],
    ),
  );
}
