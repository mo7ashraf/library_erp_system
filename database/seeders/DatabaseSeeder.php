<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::factory()->create([
                'name' => 'مشرف النظام',
                'email' => 'mohammed.ashraf2011@hotmail.com',
                'password' => bcrypt('P@$$w0rd!'),
            ]);

            $branchCairoId = DB::table('branches')->insertGetId([
                'code' => 'BR-CAIRO',
                'name' => 'فرع القاهرة',
                'phone' => '01000000001',
                'address' => 'شارع التحرير، القاهرة',
                'notes' => 'الفرع الرئيسي لمكتبة المدرسة.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $branchAlexId = DB::table('branches')->insertGetId([
                'code' => 'BR-ALEX',
                'name' => 'فرع الإسكندرية',
                'phone' => '01000000002',
                'address' => 'شارع البحر، الإسكندرية',
                'notes' => 'فرع احتياطي للمبيعات والمخزون.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $warehouseCairoId = DB::table('warehouses')->insertGetId([
                'branch_id' => $branchCairoId,
                'code' => 'WH-CAIRO-1',
                'name' => 'المستودع الرئيسي بالقاهرة',
                'account_code' => '1001',
                'notes' => 'مخزن البضاعة الرئيسي لفرع القاهرة.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $warehouseAlexId = DB::table('warehouses')->insertGetId([
                'branch_id' => $branchAlexId,
                'code' => 'WH-ALEX-1',
                'name' => 'مستودع الإسكندرية',
                'account_code' => '1002',
                'notes' => 'مستودع فرعي بالإسكندرية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $unitPieceId = DB::table('units')->insertGetId([
                'code' => 'U-PCE',
                'name' => 'قطعة',
                'symbol' => 'pcs',
                'notes' => 'الوحدة الأساسية للمنتجات.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $unitBoxId = DB::table('units')->insertGetId([
                'code' => 'U-BOX',
                'name' => 'صندوق',
                'symbol' => 'box',
                'notes' => 'يحتوي عدة قطع.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $unitCartonId = DB::table('units')->insertGetId([
                'code' => 'U-CTN',
                'name' => 'كرتونة',
                'symbol' => 'ctn',
                'notes' => 'تعبئة بالجملة.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $groupBooksId = DB::table('item_groups')->insertGetId([
                'code' => 'G-BOOK',
                'name' => 'الكتب المدرسية',
                'notes' => 'مجموعة الكتب التعليمية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $groupStationeryId = DB::table('item_groups')->insertGetId([
                'code' => 'G-STN',
                'name' => 'الأدوات المكتبية',
                'notes' => 'مجموعة الأدوات المكتبية والقرطاسية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $subgroupMathId = DB::table('item_subgroups')->insertGetId([
                'item_group_id' => $groupBooksId,
                'code' => 'SG-MATH',
                'name' => 'رياضيات',
                'notes' => 'كتب رياضيات لجميع المراحل.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $subgroupArabicId = DB::table('item_subgroups')->insertGetId([
                'item_group_id' => $groupBooksId,
                'code' => 'SG-ARAB',
                'name' => 'اللغة العربية',
                'notes' => 'كتب اللغة العربية والقراءة.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $subgroupArtId = DB::table('item_subgroups')->insertGetId([
                'item_group_id' => $groupStationeryId,
                'code' => 'SG-ART',
                'name' => 'أدوات الرسم',
                'notes' => 'أدوات الرسم والتلوين.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemMathBookId = DB::table('items')->insertGetId([
                'item_group_id' => $groupBooksId,
                'item_subgroup_id' => $subgroupMathId,
                'base_unit_id' => $unitPieceId,
                'middle_unit_id' => $unitBoxId,
                'large_unit_id' => $unitCartonId,
                'code' => 'ITEM-MATH-01',
                'origin_code' => 'OR-MATH-001',
                'barcode' => '6281256480013',
                'name' => 'كتاب الرياضيات الابتدائي',
                'source' => 'المنهج المصري',
                'publisher' => 'مكتبة الشروق',
                'purchase_price' => 12.00,
                'first_discount_percent' => 0,
                'second_discount_percent' => 0,
                'net_purchase_price' => 12.00,
                'total_cost' => 12.00,
                'profit_margin_percent' => 25.00,
                'student_price' => 15.00,
                'teacher_price' => 13.50,
                'representative_price' => 14.00,
                'retail_price' => 18.00,
                'wholesale_price' => 16.00,
                'teacher_discount_percent' => 10.00,
                'representative_discount_percent' => 7.50,
                'return_percent' => 5.00,
                'max_stock' => 200,
                'min_stock' => 20,
                'reorder_level' => 40,
                'units_per_middle' => 10,
                'units_per_large' => 100,
                'image_path' => null,
                'details' => 'كتاب رياضيات للمرحلة الابتدائية مع تمارين محلولة.',
                'notes' => 'مناسب للعرض في قسم الكتب المدرسية.',
                'continue_balance' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemArabicBookId = DB::table('items')->insertGetId([
                'item_group_id' => $groupBooksId,
                'item_subgroup_id' => $subgroupArabicId,
                'base_unit_id' => $unitPieceId,
                'middle_unit_id' => $unitBoxId,
                'large_unit_id' => $unitCartonId,
                'code' => 'ITEM-ARAB-01',
                'origin_code' => 'OR-ARAB-001',
                'barcode' => '6281256480020',
                'name' => 'كتاب اللغة العربية',
                'source' => 'المنهج المصري',
                'publisher' => 'دار النشر الحديثة',
                'purchase_price' => 14.00,
                'first_discount_percent' => 0,
                'second_discount_percent' => 0,
                'net_purchase_price' => 14.00,
                'total_cost' => 14.00,
                'profit_margin_percent' => 20.00,
                'student_price' => 17.00,
                'teacher_price' => 15.30,
                'representative_price' => 16.00,
                'retail_price' => 20.00,
                'wholesale_price' => 18.00,
                'teacher_discount_percent' => 10.00,
                'representative_discount_percent' => 5.88,
                'return_percent' => 5.00,
                'max_stock' => 150,
                'min_stock' => 15,
                'reorder_level' => 30,
                'units_per_middle' => 8,
                'units_per_large' => 80,
                'image_path' => null,
                'details' => 'كتاب اللغة العربية للمرحلة الابتدائية مع تطبيقات',
                'notes' => 'مكتوب باللغة العربية ومناسب للطلاب.',
                'continue_balance' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemMarkerId = DB::table('items')->insertGetId([
                'item_group_id' => $groupStationeryId,
                'item_subgroup_id' => $subgroupArtId,
                'base_unit_id' => $unitPieceId,
                'middle_unit_id' => $unitBoxId,
                'large_unit_id' => $unitCartonId,
                'code' => 'ITEM-MARKER-01',
                'origin_code' => 'OR-MARK-001',
                'barcode' => '6281256480037',
                'name' => 'طقم أقلام تلوين',
                'source' => 'مصنع الأدوات',
                'publisher' => null,
                'purchase_price' => 8.00,
                'first_discount_percent' => 0,
                'second_discount_percent' => 0,
                'net_purchase_price' => 8.00,
                'total_cost' => 8.00,
                'profit_margin_percent' => 30.00,
                'student_price' => 10.50,
                'teacher_price' => 9.45,
                'representative_price' => 9.80,
                'retail_price' => 12.00,
                'wholesale_price' => 11.00,
                'teacher_discount_percent' => 10.00,
                'representative_discount_percent' => 6.67,
                'return_percent' => 5.00,
                'max_stock' => 120,
                'min_stock' => 10,
                'reorder_level' => 25,
                'units_per_middle' => 12,
                'units_per_large' => 120,
                'image_path' => null,
                'details' => 'مجموعة أقلام تلوين للأطفال.',
                'notes' => 'تباع في قسم القرطاسية.',
                'continue_balance' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $customerStudentId = DB::table('customers')->insertGetId([
                'branch_id' => $branchCairoId,
                'code' => 'CUST-001',
                'name' => 'طالب محمود أحمد',
                'type' => 'student',
                'phone' => '01110000001',
                'mobile' => '01111110001',
                'email' => 'mahmoud@example.com',
                'governorate' => 'القاهرة',
                'city' => 'المنيل',
                'address' => 'شارع النيل، القاهرة',
                'opening_balance' => 0,
                'balance_type' => 'debit',
                'discount_percent' => 5.00,
                'sales_at_purchase_price' => false,
                'notes' => 'طالب يشتري كتب مدرسية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $customerSchoolId = DB::table('customers')->insertGetId([
                'branch_id' => $branchAlexId,
                'code' => 'CUST-002',
                'name' => 'مدرسة النخبة',
                'type' => 'wholesale',
                'phone' => '01110000002',
                'mobile' => '01111110002',
                'email' => 'elite@example.com',
                'governorate' => 'الإسكندرية',
                'city' => 'محرم بك',
                'address' => 'شارع البحر، الإسكندرية',
                'opening_balance' => 1200.00,
                'balance_type' => 'credit',
                'discount_percent' => 10.00,
                'sales_at_purchase_price' => false,
                'notes' => 'عميل جملة خاص بالمدارس.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $supplierShoroukId = DB::table('suppliers')->insertGetId([
                'branch_id' => $branchCairoId,
                'code' => 'SUP-001',
                'name' => 'مكتبة الشروق',
                'phone' => '01120000001',
                'mobile' => '01122220001',
                'email' => 'shorouk@example.com',
                'governorate' => 'القاهرة',
                'city' => 'المعادي',
                'address' => 'شارع المعادي، القاهرة',
                'opening_balance' => 0,
                'balance_type' => 'credit',
                'notes' => 'المورد الرئيسي للكتب.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $supplierModernId = DB::table('suppliers')->insertGetId([
                'branch_id' => $branchAlexId,
                'code' => 'SUP-002',
                'name' => 'دار النشر الحديثة',
                'phone' => '01120000002',
                'mobile' => '01122220002',
                'email' => 'modern@example.com',
                'governorate' => 'الإسكندرية',
                'city' => 'جمرك الإسكندرية',
                'address' => 'شارع المؤيد، الإسكندرية',
                'opening_balance' => 500.00,
                'balance_type' => 'credit',
                'notes' => 'مورد كتب اللغة العربية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $openingStockDocumentId = DB::table('opening_stock_documents')->insertGetId([
                'warehouse_id' => $warehouseCairoId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'reference_number' => 'OSD-1001',
                'document_date' => now()->subDays(20)->toDateString(),
                'status' => 'posted',
                'posted_at' => now()->subDays(20),
                'notes' => 'رصيد افتتاحي لمخزن القاهرة.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);

            DB::table('opening_stock_document_items')->insert([
                'opening_stock_document_id' => $openingStockDocumentId,
                'item_id' => $itemArabicBookId,
                'quantity' => 30,
                'unit_cost' => 14.00,
                'total_cost' => 420.00,
                'notes' => 'الرصيد الابتدائي لكتاب اللغة العربية.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);

            DB::table('warehouse_item_balances')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemArabicBookId,
                'quantity' => 30,
                'average_cost' => 14.00,
                'total_cost' => 420.00,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemArabicBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'opening_balance',
                'direction' => 'in',
                'reference_type' => 'App\\Models\\OpeningStockDocument',
                'reference_id' => $openingStockDocumentId,
                'reference_number' => 'OSD-1001',
                'movement_date' => now()->subDays(20)->toDateString(),
                'quantity' => 30,
                'unit_cost' => 14.00,
                'total_cost' => 420.00,
                'balance_after' => 30,
                'notes' => 'رصيد افتتاحي لكتاب اللغة العربية.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);

            $purchaseInvoiceId = DB::table('purchase_invoices')->insertGetId([
                'supplier_id' => $supplierShoroukId,
                'warehouse_id' => $warehouseCairoId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'invoice_number' => 'PI-1001',
                'supplier_invoice_number' => 'SUP-INV-5001',
                'invoice_date' => now()->subDays(10)->toDateString(),
                'due_date' => now()->subDays(3)->toDateString(),
                'payment_type' => 'cash',
                'status' => 'posted',
                'subtotal' => 920.00,
                'discount_amount' => 0,
                'additional_cost' => 20.00,
                'grand_total' => 940.00,
                'posted_at' => now()->subDays(10),
                'notes' => 'فاتورة شراء كتب وأدوات مورد الشروق.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            $purchaseInvoiceItem1Id = DB::table('purchase_invoice_items')->insertGetId([
                'purchase_invoice_id' => $purchaseInvoiceId,
                'item_id' => $itemMathBookId,
                'unit_id' => $unitPieceId,
                'quantity' => 50,
                'unit_price' => 12.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 12.00,
                'line_total' => 600.00,
                'notes' => 'شراء كتاب الرياضيات.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            $purchaseInvoiceItem2Id = DB::table('purchase_invoice_items')->insertGetId([
                'purchase_invoice_id' => $purchaseInvoiceId,
                'item_id' => $itemMarkerId,
                'unit_id' => $unitPieceId,
                'quantity' => 40,
                'unit_price' => 8.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 8.00,
                'line_total' => 320.00,
                'notes' => 'شراء طقم أقلام تلوين.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            DB::table('warehouse_item_balances')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMathBookId,
                'quantity' => 50,
                'average_cost' => 12.00,
                'total_cost' => 600.00,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            DB::table('warehouse_item_balances')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMarkerId,
                'quantity' => 40,
                'average_cost' => 8.00,
                'total_cost' => 320.00,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMathBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'purchase',
                'direction' => 'in',
                'reference_type' => 'App\\Models\\PurchaseInvoice',
                'reference_id' => $purchaseInvoiceId,
                'reference_number' => 'PI-1001',
                'movement_date' => now()->subDays(10)->toDateString(),
                'quantity' => 50,
                'unit_cost' => 12.00,
                'total_cost' => 600.00,
                'balance_after' => 50,
                'notes' => 'مخزون شراء كتاب الرياضيات.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMarkerId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'purchase',
                'direction' => 'in',
                'reference_type' => 'App\\Models\\PurchaseInvoice',
                'reference_id' => $purchaseInvoiceId,
                'reference_number' => 'PI-1001',
                'movement_date' => now()->subDays(10)->toDateString(),
                'quantity' => 40,
                'unit_cost' => 8.00,
                'total_cost' => 320.00,
                'balance_after' => 40,
                'notes' => 'مخزون شراء طقم أقلام تلوين.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);

            $salesInvoiceId = DB::table('sales_invoices')->insertGetId([
                'customer_id' => $customerStudentId,
                'warehouse_id' => $warehouseCairoId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'invoice_number' => 'SI-1001',
                'invoice_date' => now()->subDays(5)->toDateString(),
                'due_date' => null,
                'payment_type' => 'cash',
                'price_type' => 'student',
                'status' => 'posted',
                'subtotal' => 300.00,
                'discount_amount' => 0,
                'service_amount' => 0,
                'commission_percent' => 0,
                'commission_amount' => 0,
                'grand_total' => 300.00,
                'posted_at' => now()->subDays(5),
                'notes' => 'فاتورة بيع لكتاب الرياضيات وكتاب العربية لطالب.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            $salesInvoiceItem1Id = DB::table('sales_invoice_items')->insertGetId([
                'sales_invoice_id' => $salesInvoiceId,
                'item_id' => $itemMathBookId,
                'unit_id' => $unitPieceId,
                'quantity' => 10,
                'unit_price' => 15.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 15.00,
                'line_total' => 150.00,
                'notes' => 'بيع كتاب الرياضيات.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            $salesInvoiceItem2Id = DB::table('sales_invoice_items')->insertGetId([
                'sales_invoice_id' => $salesInvoiceId,
                'item_id' => $itemArabicBookId,
                'unit_id' => $unitPieceId,
                'quantity' => 10,
                'unit_price' => 15.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 15.00,
                'line_total' => 150.00,
                'notes' => 'بيع كتاب اللغة العربية.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMathBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'sale',
                'direction' => 'out',
                'reference_type' => 'App\\Models\\SalesInvoice',
                'reference_id' => $salesInvoiceId,
                'reference_number' => 'SI-1001',
                'movement_date' => now()->subDays(5)->toDateString(),
                'quantity' => 10,
                'unit_cost' => 12.00,
                'total_cost' => 120.00,
                'balance_after' => 40,
                'notes' => 'صرف مخزون كتاب الرياضيات لفاتورة بيع.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemArabicBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'sale',
                'direction' => 'out',
                'reference_type' => 'App\\Models\\SalesInvoice',
                'reference_id' => $salesInvoiceId,
                'reference_number' => 'SI-1001',
                'movement_date' => now()->subDays(5)->toDateString(),
                'quantity' => 10,
                'unit_cost' => 14.00,
                'total_cost' => 140.00,
                'balance_after' => 20,
                'notes' => 'صرف مخزون كتاب اللغة العربية لفاتورة بيع.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);

            $salesReturnId = DB::table('sales_returns')->insertGetId([
                'sales_invoice_id' => $salesInvoiceId,
                'customer_id' => $customerStudentId,
                'warehouse_id' => $warehouseCairoId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'return_number' => 'SR-1001',
                'return_date' => now()->subDays(2)->toDateString(),
                'refund_type' => 'cash',
                'status' => 'posted',
                'subtotal' => 75.00,
                'discount_amount' => 0,
                'grand_total' => 75.00,
                'posted_at' => now()->subDays(2),
                'notes' => 'مرجع جزئي من فاتورة البيع.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            DB::table('sales_return_items')->insert([
                'sales_return_id' => $salesReturnId,
                'sales_invoice_item_id' => $salesInvoiceItem1Id,
                'item_id' => $itemMathBookId,
                'unit_id' => $unitPieceId,
                'quantity' => 5,
                'unit_price' => 15.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 15.00,
                'line_total' => 75.00,
                'notes' => 'مرتجع 5 نسخ من كتاب الرياضيات.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMathBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'sale_return',
                'direction' => 'in',
                'reference_type' => 'App\\Models\\SalesReturn',
                'reference_id' => $salesReturnId,
                'reference_number' => 'SR-1001',
                'movement_date' => now()->subDays(2)->toDateString(),
                'quantity' => 5,
                'unit_cost' => 15.00,
                'total_cost' => 75.00,
                'balance_after' => 45,
                'notes' => 'إرجاع 5 نسخ من كتاب الرياضيات.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]);

            $purchaseReturnId = DB::table('purchase_returns')->insertGetId([
                'purchase_invoice_id' => $purchaseInvoiceId,
                'supplier_id' => $supplierShoroukId,
                'warehouse_id' => $warehouseCairoId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'return_number' => 'PR-1001',
                'return_date' => now()->subDays(1)->toDateString(),
                'refund_type' => 'supplier_balance',
                'status' => 'posted',
                'subtotal' => 80.00,
                'discount_amount' => 0,
                'grand_total' => 80.00,
                'posted_at' => now()->subDays(1),
                'notes' => 'مرتجع لعدد 10 أقلام تلوين.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            DB::table('purchase_return_items')->insert([
                'purchase_return_id' => $purchaseReturnId,
                'purchase_invoice_item_id' => $purchaseInvoiceItem2Id,
                'item_id' => $itemMarkerId,
                'unit_id' => $unitPieceId,
                'quantity' => 10,
                'unit_price' => 8.00,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_unit_price' => 8.00,
                'line_total' => 80.00,
                'notes' => 'مرتجع 10 أقلام تلوين للمورد.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMarkerId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'purchase_return',
                'direction' => 'out',
                'reference_type' => 'App\\Models\\PurchaseReturn',
                'reference_id' => $purchaseReturnId,
                'reference_number' => 'PR-1001',
                'movement_date' => now()->subDays(1)->toDateString(),
                'quantity' => 10,
                'unit_cost' => 8.00,
                'total_cost' => 80.00,
                'balance_after' => 30,
                'notes' => 'خصم من مخزون الأقلام بسبب مرجع المورد.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            $stockTransferId = DB::table('stock_transfers')->insertGetId([
                'from_warehouse_id' => $warehouseCairoId,
                'to_warehouse_id' => $warehouseAlexId,
                'from_branch_id' => $branchCairoId,
                'to_branch_id' => $branchAlexId,
                'user_id' => $user->id,
                'transfer_number' => 'TR-1001',
                'transfer_date' => now()->subDays(3)->toDateString(),
                'status' => 'posted',
                'total_quantity' => 5,
                'total_cost' => 75.00,
                'posted_at' => now()->subDays(3),
                'notes' => 'نقل 5 نسخ من كتاب الرياضيات لمخزن الاسكندرية.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);

            DB::table('stock_transfer_items')->insert([
                'stock_transfer_id' => $stockTransferId,
                'item_id' => $itemMathBookId,
                'unit_id' => $unitPieceId,
                'quantity' => 5,
                'unit_cost' => 15.00,
                'total_cost' => 75.00,
                'notes' => 'نقل 5 نسخ من كتاب الرياضيات.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseCairoId,
                'item_id' => $itemMathBookId,
                'branch_id' => $branchCairoId,
                'user_id' => $user->id,
                'movement_type' => 'transfer_out',
                'direction' => 'out',
                'reference_type' => 'App\\Models\\StockTransfer',
                'reference_id' => $stockTransferId,
                'reference_number' => 'TR-1001',
                'movement_date' => now()->subDays(3)->toDateString(),
                'quantity' => 5,
                'unit_cost' => 15.00,
                'total_cost' => 75.00,
                'balance_after' => 35,
                'notes' => 'نقل كتاب الرياضيات من القاهرة إلى الإسكندرية.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);

            DB::table('stock_movements')->insert([
                'warehouse_id' => $warehouseAlexId,
                'item_id' => $itemMathBookId,
                'branch_id' => $branchAlexId,
                'user_id' => $user->id,
                'movement_type' => 'transfer_in',
                'direction' => 'in',
                'reference_type' => 'App\\Models\\StockTransfer',
                'reference_id' => $stockTransferId,
                'reference_number' => 'TR-1001',
                'movement_date' => now()->subDays(3)->toDateString(),
                'quantity' => 5,
                'unit_cost' => 15.00,
                'total_cost' => 75.00,
                'balance_after' => 5,
                'notes' => 'استلام 5 نسخ من كتاب الرياضيات في مستودع الإسكندرية.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);

            DB::table('warehouse_item_balances')->insert([
                'warehouse_id' => $warehouseAlexId,
                'item_id' => $itemMathBookId,
                'quantity' => 5,
                'average_cost' => 15.00,
                'total_cost' => 75.00,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]);
        });
    }
}
