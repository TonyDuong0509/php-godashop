<?php
class ProductController
{
    // hiển thị danh sách sản phẩm
    public function index($category_id = null)
    {
        $conds = [];
        $sorts = [];
        $page = $_GET['page'] ?? 1;
        $item_per_page = 10;

        if ($category_id) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id, //3
                ],
            ];
            // SELECT * FROM view_product WHERE category_id = 3
        }

        // price-range=300000-500000
        $priceRange = $_GET['price-range'] ?? null;
        if ($priceRange) {
            $temp = explode('-', $priceRange);
            $start_price = $temp[0];
            $end_price = $temp[1];
            $conds = [
                'sale_price' => [
                    'type' => 'BETWEEN',
                    'val' => "$start_price AND $end_price",
                ],
            ];
            // SELECT * FROM view_product WHERE sale_price BETWEEN 300000 AND 500000

            // price-range=1000000-greater
            if ($end_price == 'greater') {
                $conds = [
                    'sale_price' => [
                        'type' => '>=',
                        'val' => $start_price,
                    ],
                ];
            }
            // SELECT * FROM view_product WHERE sale_price >= 1000000
        }

        // sort=price-desc
        $sort = $_GET['sort'] ?? null;
        if ($sort) {
            $map = [
                'price' => 'sale_price',
                'alpha' => 'name',
                'created' => 'created_date',
            ];
            $temp = explode('-', $sort);
            $dummyCol = $temp[0]; //price
            $col_name = $map[$dummyCol]; //sale_price
            $order = $temp[1]; //desc
            $sorts = [
                $col_name => $order,
            ];
        }

        // search=kem
        $search = $_GET['search'] ?? null;
        if ($search) {
            $conds = [
                'name' => [
                    'type' => 'LIKE',
                    'val' => "'%$search%'", //
                ],
            ];
            // SELECT * FROM view_product WHERE name LIKE '%kem%'
        }

        $productRepository = new ProductRepository();
        $products = $productRepository->getBy($conds, $sorts, $page, $item_per_page);
        $totalProducts = $productRepository->getBy($conds, $sorts);
        // later
        $totalPage = ceil(count($totalProducts) / $item_per_page);
        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();
        require ABSPATH_SITE . 'view/product/index.php';
    }

    public function detail($id)
    {
        $productRepository = new ProductRepository();
        $product = $productRepository->find($id);

        $category_id = $product->getCategoryId();
        if ($category_id) {
            $conds = [
                'category_id' => [
                    'type' => '=',
                    'val' => $category_id, //3
                ],
                'id' => [
                    'type' => '!=',
                    'val' => $id, //2
                ],
            ];
            // SELECT * FROM view_product WHERE category_id = 3 AND id != 2
        }
        $relatedProducts = $productRepository->getBy($conds, [], 1, 10);

        $categoryRepository = new CategoryRepository();
        $categories = $categoryRepository->getAll();

        require ABSPATH_SITE . 'view/product/detail.php';
    }

    public function storeComment()
    {
        $data = [];
        $data["email"] = $_POST['email'];
        $data["fullname"] = $_POST['fullname'];
        $data["star"] = $_POST['rating'];
        $data["created_date"] = date('Y-m-d H:i:s'); //2023-08-21 23:04:17
        $data["description"] = $_POST['description'];
        $data["product_id"] = $_POST['product_id'];

        // lưu comment xuống database
        $commentRepository = new CommentRepository();
        $commentRepository->save($data);

        $productRepository = new ProductRepository();
        $product = $productRepository->find($_POST['product_id']);
        require ABSPATH_SITE . 'view/product/comments.php';
    }
}
