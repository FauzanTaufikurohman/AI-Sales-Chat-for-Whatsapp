class Conversation {
  const Conversation({
    required this.name,
    required this.preview,
    required this.time,
    required this.status,
  });

  final String name;
  final String preview;
  final String time;
  final String status;
}

const demoConversations = [
  Conversation(
    name: 'Andi Pratama',
    preview: 'Talent A tersedia tanggal 20?',
    time: '09:42',
    status: 'Needs attention',
  ),
  Conversation(
    name: 'Budi Santoso',
    preview: 'Request video talent untuk campaign.',
    time: '09:18',
    status: 'AI active',
  ),
  Conversation(
    name: 'Citra Wijaya',
    preview: 'Bagaimana detail harganya?',
    time: '08:56',
    status: 'Waiting sales',
  ),
  Conversation(
    name: 'Dewi Lestari',
    preview: 'Terima kasih, saya tunggu kabarnya.',
    time: 'Yesterday',
    status: 'Closed',
  ),
];
