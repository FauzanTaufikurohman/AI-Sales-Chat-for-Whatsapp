import 'package:client/main.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('login opens dashboard with dummy data', (tester) async {
    await tester.pumpWidget(const AIChatbotApp());
    expect(find.text('Selamat datang kembali'), findsOneWidget);

    await tester.tap(find.text('Masuk ke workspace'));
    await tester.pumpAndSettle();

    expect(find.text('Dashboard'), findsOneWidget);
    expect(find.text('Percakapan'), findsOneWidget);
    expect(find.text('Andi Pratama'), findsOneWidget);
  });
}
