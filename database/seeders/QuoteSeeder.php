<?php

/**
 * QuoteSeeder - Seeds patriotic Vietnamese quotes for the application
 * Contains famous quotes from Vietnamese leaders and patriotic sayings
 */

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = [
            [
                'quote' => 'Không có gì quý hơn độc lập, tự do.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Nước Việt Nam là một, dân tộc Việt Nam là một.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Tổ quốc là linh hồn, là cội nguồn. Dù đi đâu, vẫn nhớ về nguồn cội.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Hoàng Sa & Trường Sa là của Việt Nam!',
                'author' => 'Patriotic Saying',
                'status' => 'active'
            ],
            [
                'quote' => 'Đâu cần thanh niên có, việc gì khó có thanh niên.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Thanh niên là tương lai của đất nước.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Học hỏi là một việc phải tiếp tục suốt đời.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Có tài mà không có đức là người vô dụng, có đức mà không có tài thì làm việc gì cũng khó.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Cần, kiệm, liêm, chính, chí công vô tư.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Độc lập, tự do, hạnh phúc.',
                'author' => 'Vietnamese Motto',
                'status' => 'active'
            ],
            [
                'quote' => 'Nhiệm vụ của thanh niên không phải là đòi hỏi Tổ quốc đã cho mình những gì, mà phải tự hỏi mình đã làm gì cho Tổ quốc hôm nay.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Nói thì phải làm, đã nói thì phải làm cho kỳ được.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Muốn người ta giúp cho thì trước hết mình phải tự giúp mình đã.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Cần phải học tập tinh thần tự lực cánh sinh, không ỷ lại.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Đoàn kết, đoàn kết, đại đoàn kết. Thành công, thành công, đại thành công.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Yêu Tổ quốc, yêu đồng bào.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Vì lợi ích mười năm trồng cây, vì lợi ích trăm năm trồng người.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Không có việc gì khó, chỉ sợ lòng không bền. Đào núi và lấp biển, quyết chí ắt làm nên.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Phải luôn luôn tự học, tự rèn luyện để tiến bộ mãi.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Cần phải học, phải tiến bộ. Ai không học là lùi.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Muốn tiến bộ phải ham học, học nữa, học mãi.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Phải biết quý thời gian, đừng để thì giờ trôi qua một cách vô ích.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
            [
                'quote' => 'Dân ta phải biết sử ta. Cho tường gốc tích nước nhà Việt Nam.',
                'author' => 'Chủ tịch Hồ Chí Minh',
                'status' => 'active'
            ],
        ];

        foreach ($quotes as $quoteData) {
            Quote::create($quoteData);
        }
    }
}
