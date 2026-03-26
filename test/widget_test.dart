import 'package:flutter_test/flutter_test.dart';
import 'package:sitio_facil/main.dart';

void main() {
  testWidgets('SitioFacilApp builds', (WidgetTester tester) async {
    await tester.pumpWidget(const SitioFacilApp());
    await tester.pump();
  });
}
