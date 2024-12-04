<?php 
class HomeController {
    function index() {
        // lấy 4 sản phẩm nổi bật
        $conds = [];
        $sorts = ['featured' => 'DESC'];
        $page = 1;
        $item_per_page = 4;
        $productRepsitory = new ProductRepository();
        $featuredProducts = $productRepsitory->getBy($conds, $sorts, $page, $item_per_page);

        // Lấy 4 sản phẩm mới nhất
        $sorts = ['created_date' => 'DESC'];
        $latestProducts = $productRepsitory->getBy($conds, $sorts, $page, $item_per_page);

        // Lấy tất cả các danh mục
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();
        
        // biến chứa toàn bộ cấu trúc dữ liệu để đỗ ra view
        $categoryProducts = [];
        
        // Duyệt từng danh mục để lấy sản phẩm tương ứng
        foreach ($categories as $category) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category->getId()//3
                ]
            ];

            $products = $productRepsitory->getBy($conds, $sorts, $page, $item_per_page);
            // SELECT * FORM view_product WHERE category_id=3

            // Dấu [] bên trái dấu bằng nghĩa là thêm 1 phần tử vào cuối danh sách
            $categoryProducts[] = [
                'categoryName' => $category->getName(),
                'products' => $products
            ];
        }
        require ABSPATH_SITE . 'view/home/index.php';
    }
}
?>