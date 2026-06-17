<?php
// filepath: app/Exports/OrdersExport.php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get the collection of orders based on filters
     */
    public function collection()
    {
        $query = Order::with(['items.product', 'user']);

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Apply payment status filter
        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        // Apply date range filter
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Mã đơn hàng',
            'Tên khách hàng',
            'Email',
            'Số điện thoại',
            'Địa chỉ giao hàng',
            'Sản phẩm',
            'Số lượng sản phẩm',
            'Tạm tính',
            'Giảm giá',
            'Phí vận chuyển',
            'Tổng tiền',
            'Phương thức thanh toán',
            'Trạng thái đơn hàng',
            'Trạng thái thanh toán',
            'Ngày tạo',
            'Ghi chú'
        ];
    }

    /**
     * Map each row data
     */
    public function map($order): array
    {
        // Get product details
        $products = $order->items->map(function($item) {
            return $item->product_name . ' (x' . $item->quantity . ')';
        })->join('; ');

        $totalQuantity = $order->items->sum('quantity');

        // Status translations
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        $paymentStatusLabels = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->shipping_address,
            $products,
            $totalQuantity,
            number_format($order->subtotal, 0, ',', '.') . '₫',
            number_format($order->discount, 0, ',', '.') . '₫',
            number_format($order->shipping_fee, 0, ',', '.') . '₫',
            number_format($order->total + $order->shipping_fee, 0, ',', '.') . '₫',
            $order->payment_method,
            $statusLabels[$order->status] ?? $order->status,
            $paymentStatusLabels[$order->payment_status] ?? $order->payment_status,
            $order->created_at->format('d/m/Y H:i'),
            $order->notes ?? ''
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style for data rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:P{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Set row height for better readability
        for ($i = 2; $i <= $highestRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        // Header row height
        $sheet->getRowDimension(1)->setRowHeight(30);

        return $sheet;
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Mã đơn hàng
            'B' => 20, // Tên khách hàng
            'C' => 25, // Email
            'D' => 15, // Số điện thoại
            'E' => 30, // Địa chỉ
            'F' => 40, // Sản phẩm
            'G' => 12, // Số lượng
            'H' => 15, // Tạm tính
            'I' => 12, // Giảm giá
            'J' => 15, // Phí ship
            'K' => 15, // Tổng tiền
            'L' => 20, // Phương thức TT
            'M' => 15, // Trạng thái
            'N' => 18, // TT Thanh toán
            'O' => 18, // Ngày tạo
            'P' => 25  // Ghi chú
        ];
    }

    /**
     * Set the title for the worksheet
     */
    public function title(): string
    {
        return 'Danh sách đơn hàng';
    }
}
