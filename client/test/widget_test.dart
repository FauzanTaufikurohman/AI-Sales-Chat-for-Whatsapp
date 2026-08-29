import 'package:client/main.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('dashboard renders key sales metrics', (tester) async {
    await tester.pumpWidget(const SalesDashboardApp());
    expect(find.text('Sales overview'), findsOneWidget);
    expect(find.text('Total conversations'), findsOneWidget);
    expect(find.text('Andi Pratama'), findsOneWidget);
  });
}
