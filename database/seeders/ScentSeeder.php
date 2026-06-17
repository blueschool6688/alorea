<?php

namespace Database\Seeders;

use App\Models\Scent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing scents
        Scent::truncate();

        $scents = [
            // === HOA (FLORAL) ===
            [
                'name' => 'Rose',
                'description' => 'Hương hoa hồng cổ điển, ngọt ngào và lãng mạn',
                'type' => 'middle',
                'category' => 'floral',
                'color_hex' => '#FF69B4',
                'is_popular' => true,
                'intensity' => 7,
                'notes' => 'Biểu tượng của tình yêu và sự lãng mạn'
            ],
            [
                'name' => 'Jasmine',
                'description' => 'Hương hoa nhài quyến rũ, ngọt ngào và gợi cảm',
                'type' => 'middle',
                'category' => 'floral',
                'color_hex' => '#F8F8FF',
                'is_popular' => true,
                'intensity' => 8,
                'notes' => 'Hương đêm quyến rũ, bloom vào ban đêm'
            ],
            [
                'name' => 'Lavender',
                'description' => 'Hương oải hương tím thư giãn, dịu nhẹ',
                'type' => 'top',
                'category' => 'floral',
                'color_hex' => '#9966CC',
                'is_popular' => true,
                'intensity' => 6,
                'notes' => 'Có tác dụng thư giãn, giảm stress'
            ],
            [
                'name' => 'Peony',
                'description' => 'Hương hoa mẫu đơn nhẹ nhàng, thanh tao',
                'type' => 'middle',
                'category' => 'floral',
                'color_hex' => '#FFB6C1',
                'is_popular' => false,
                'intensity' => 5,
                'notes' => 'Hương hoa nữ tính, tinh tế và thanh khiết'
            ],
            [
                'name' => 'Lily',
                'description' => 'Hương hoa loa kèn trắng tinh khôi, tươi mát',
                'type' => 'middle',
                'category' => 'floral',
                'color_hex' => '#FFFFFF',
                'is_popular' => false,
                'intensity' => 6,
                'notes' => 'Hương hoa trắng tinh khôi, sang trọng'
            ],

            // === GỖ (WOODY) ===
            [
                'name' => 'Sandalwood',
                'description' => 'Hương gỗ đàn hương ấm áp, mềm mại và sang trọng',
                'type' => 'base',
                'category' => 'woody',
                'color_hex' => '#F4A460',
                'is_popular' => true,
                'intensity' => 7,
                'notes' => 'Hương gỗ quý hiếm, có tác dụng an thần'
            ],
            [
                'name' => 'Cedar',
                'description' => 'Hương gỗ tuyết tùng khô ráo, mạnh mẽ và nam tính',
                'type' => 'base',
                'category' => 'woody',
                'color_hex' => '#8B4513',
                'is_popular' => true,
                'intensity' => 8,
                'notes' => 'Hương gỗ nam tính, bền vững và mạnh mẽ'
            ],
            [
                'name' => 'Oud',
                'description' => 'Hương trầm hương quý hiếm, bí ẩn và đẳng cấp',
                'type' => 'base',
                'category' => 'woody',
                'color_hex' => '#654321',
                'is_popular' => true,
                'intensity' => 9,
                'notes' => 'Vua của các loại hương, quý hiếm và đắt tiền'
            ],
            [
                'name' => 'Vetiver',
                'description' => 'Hương cỏ hương bài đất ẩm, xanh mát',
                'type' => 'base',
                'category' => 'woody',
                'color_hex' => '#8FBC8F',
                'is_popular' => false,
                'intensity' => 6,
                'notes' => 'Hương đất ẩm, gần gũi với thiên nhiên'
            ],
            [
                'name' => 'Patchouli',
                'description' => 'Hương hoắc hương đậm đà, bí ẩn và quyến rũ',
                'type' => 'base',
                'category' => 'woody',
                'color_hex' => '#800000',
                'is_popular' => false,
                'intensity' => 8,
                'notes' => 'Hương hippie cổ điển, đậm đà và bí ẩn'
            ],

            // === NGỌT (GOURMAND) ===
            [
                'name' => 'Vanilla',
                'description' => 'Hương vani ngọt ngào, ấm áp và quyến rũ',
                'type' => 'base',
                'category' => 'gourmand',
                'color_hex' => '#F3E5AB',
                'is_popular' => true,
                'intensity' => 7,
                'notes' => 'Hương ngọt tự nhiên, tạo cảm giác ấm cúng'
            ],
            [
                'name' => 'Caramel',
                'description' => 'Hương caramel ngọt béo, ấm áp và hấp dẫn',
                'type' => 'middle',
                'category' => 'gourmand',
                'color_hex' => '#AF6E4D',
                'is_popular' => true,
                'intensity' => 6,
                'notes' => 'Hương kẹo ngọt, tạo cảm giác thèm muốn'
            ],
            [
                'name' => 'Honey',
                'description' => 'Hương mật ong ngọt dịu, ấm áp và tự nhiên',
                'type' => 'middle',
                'category' => 'gourmand',
                'color_hex' => '#FFA500',
                'is_popular' => false,
                'intensity' => 5,
                'notes' => 'Hương ngọt tự nhiên từ thiên nhiên'
            ],
            [
                'name' => 'Chocolate',
                'description' => 'Hương chocolate đắng ngọt, quyến rũ và xa xỉ',
                'type' => 'base',
                'category' => 'gourmand',
                'color_hex' => '#7B3F00',
                'is_popular' => true,
                'intensity' => 8,
                'notes' => 'Hương ngọt đắng, tạo cảm giác xa xỉ'
            ],
            [
                'name' => 'Coconut',
                'description' => 'Hương dừa ngọt dịu, nhiệt đới và tươi mát',
                'type' => 'middle',
                'category' => 'gourmand',
                'color_hex' => '#FFFDD0',
                'is_popular' => false,
                'intensity' => 6,
                'notes' => 'Hương nhiệt đới, gợi nhớ đến bãi biển'
            ],

            // === TƯƠI MÁT (FRESH) ===
            [
                'name' => 'Bergamot',
                'description' => 'Hương cam bergamot tươi mát, chua ngọt sảng khoái',
                'type' => 'top',
                'category' => 'fresh',
                'color_hex' => '#FFE135',
                'is_popular' => true,
                'intensity' => 8,
                'notes' => 'Hương cam chanh tươi mát, năng lượng tích cực'
            ],
            [
                'name' => 'Lemon',
                'description' => 'Hương chanh vàng tươi, sảng khoái và tỉnh táo',
                'type' => 'top',
                'category' => 'fresh',
                'color_hex' => '#FFFF00',
                'is_popular' => true,
                'intensity' => 9,
                'notes' => 'Mang lại cảm giác tỉnh táo, phù hợp ban ngày'
            ],
            [
                'name' => 'Mint',
                'description' => 'Hương bạc hà mát lạnh, sảng khoái tức thì',
                'type' => 'top',
                'category' => 'fresh',
                'color_hex' => '#00FF7F',
                'is_popular' => false,
                'intensity' => 9,
                'notes' => 'Tạo cảm giác mát mẻ và tỉnh táo ngay lập tức'
            ],
            [
                'name' => 'Ocean Breeze',
                'description' => 'Hương gió biển tươi mát, sảng khoái như làn gió biển',
                'type' => 'top',
                'category' => 'fresh',
                'color_hex' => '#00CED1',
                'is_popular' => true,
                'intensity' => 7,
                'notes' => 'Mang lại cảm giác như đang ở bờ biển'
            ],
            [
                'name' => 'Green Tea',
                'description' => 'Hương trà xanh thanh mát, tươi mới và thư giãn',
                'type' => 'middle',
                'category' => 'fresh',
                'color_hex' => '#90EE90',
                'is_popular' => false,
                'intensity' => 5,
                'notes' => 'Hương zen, mang lại cảm giác thư thái'
            ]
        ];

        foreach ($scents as $scent) {
            Scent::create($scent);
        }
    }
}
