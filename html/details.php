<?php
include '../admin/config.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

// include 'process_order.php';

$product_id = $_GET['product_id'];

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

$sql = "SELECT DISTINCT color, size FROM product_variants WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$variants_result = $stmt->get_result();

$sql = "SELECT quantity FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$stmt->bind_result($stock);
$stmt->fetch();
$stmt->close();

$variants = [];
while ($variant = $variants_result->fetch_assoc()) {
    $variants[] = $variant;
}

// comment 
$comments_sql = "SELECT c.content, c.created_at, u.username AS user_username 
                 FROM comments c
                 JOIN users u ON c.user_id = u.user_id
                 WHERE c.product_id = $product_id
                 ORDER BY c.created_at DESC";
$comments_result = mysqli_query($conn, $comments_sql);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4dd88e3847.js" crossorigin="anonymous"></script>
    <style>
        .text-muted {
            color: #777;
        }

        .list-group-item {
            border: none;
            padding: 0;
        }

        .image-container {
            display: flex;
            /* Sử dụng Flexbox */
            flex-direction: column;
            /* Sắp xếp hình ảnh theo chiều dọc */
            align-items: center;
            /* Canh giữa các hình ảnh theo chiều ngang */
            gap: 10px;
            /* Khoảng cách giữa các hình ảnh */
        }

        .image-item {
            width: 100px;
            /* Đặt chiều rộng của ảnh */
            height: 100px;
            /* Đặt chiều cao của ảnh */
            object-fit: cover;
            /* Đảm bảo ảnh không bị méo và sẽ được cắt cho vừa khung */
        }

        /* General Styles */
        .comment-container {
            width: 100%;
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .add-comment-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .comment-textarea {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        .submit-btn {
            width: 20%;
            padding: 10px 20px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 0 auto;
        }


        .submit-btn:hover {
            background-color: blueviolet;
        }

        /* Comments Section */
        .comments-section {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .no-comments {
            color: #888;
            font-style: italic;
        }

        .comments-list {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .comment-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-name {
            font-size: 16px;
            font-weight: bold;
        }

        .comment-time {
            font-size: 12px;
            color: #888;
        }

        .comment-content {
            font-size: 14px;
            color: #333;
            margin: 10px 0;
        }

        .star-rating {
            color: #ff9800;
        }

        .star {
            font-size: 18px;
            margin-right: 2px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include '../include/navbar.php'; ?>

    <!-- Hiển thị thông tin sản phẩm -->
    <div class="product-details container">
        <div class="row" style="padding: 20px;">

            <!-- Phần ảnh sản phẩm -->
            <div class="col-md-5 product-image">
                <!-- Ảnh chính -->
                <?php
                $sql = "SELECT image_path FROM products_image WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">';
                    echo '<div class="carousel-inner">';

                    $isActive = true;
                    $imagePaths = []; // Mảng chứa các đường dẫn ảnh
                
                    while ($row = $result->fetch_assoc()) {
                        $image_path = $row['image_path'];
                        $imagePaths[] = $image_path; // Lưu các đường dẫn vào mảng
                
                        echo '<div class="carousel-item ' . ($isActive ? 'active' : '') . '">';
                        echo '<img src="' . $image_path . '" class="d-block w-100" alt="Ảnh sản phẩm" id="mainImage">';
                        echo '</div>';

                        $isActive = false;
                    }

                    echo '</div>';

                    echo '<button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">';
                    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Previous</span>';
                    echo '</button>';

                    echo '<button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">';
                    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Next</span>';
                    echo '</button>';

                    echo '</div>';
                } else {
                    echo '<p>Không có hình ảnh cho sản phẩm này.</p>';
                }
                ?>
            </div>

            <!-- Phần ảnh phụ -->
            <div class="col-md-2 image-container">
                <?php
                // Hiển thị ảnh phụ
                if (!empty($imagePaths)) {
                    foreach ($imagePaths as $index => $image_path) {
                        echo '<div class="thumb-item">';
                        echo '<img style="cursor: pointer; width: 180px; height: 180px;" src="' . $image_path . '" class="img-thumbnail" alt="Ảnh phụ" data-bs-target="#carouselExample" data-bs-slide-to="' . $index . '" onclick="changeMainImage(\'' . $image_path . '\')">';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <!-- Phần thông tin sản phẩm -->
            <div class="col-md-5 product-info">
                <h6 style="font-size: 20px; margin-bottom: 44px;font-weight: 300; "><?php echo $product['name']; ?></h6>
                <h2 class="h6">Giá: <span
                        class="price"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></span></h2>
                <!-- Số lượng với nút tăng/giảm -->
                <!-- so san pham con trong gio hang -->
                <span class="h6">
                    Số lượng còn lại: <span id="available-quantity"><?php echo $product['quantity']; ?></span>

                </span>
                <div class="input-group" style="margin-left: 5px;">
                    <button type="button" class="btn btn-secondary" id="decrease"
                        onclick="updateQuantity(-1)">-</button>
                    <input style="text-align: center;" type="number" name="quantity" id="quantity" value="1" min="1"
                        max="<?php echo $product['quantity']; ?>">
                    <button type="button" class="btn btn-secondary" id="increase" onclick="updateQuantity(1)">+</button>
                </div>

                <!-- Thêm trường ẩn để chứa số lượng tồn kho (được lấy từ PHP) -->
                <input type="hidden" id="quantity" value="<?php echo $quantity; ?>">
                <!-- Số lượng tồn kho của sản phẩm -->

                <img src="https://media.canifa.com/attribute/swatch/f/r/freeship_tagdetail_desktop-02oct_1.webp" alt=""
                    style="max-width: 100%; height: auto;">

                <div class="form-group">
                    <label for="color" class="h6">Chọn màu</label>
                    <select id="color" name="color" class="form-control" style="margin: 10px 0;">
                        <?php
                        // Mảng lưu các màu sắc để tránh trùng lặp
                        $colors = [];
                        $variants_result->data_seek(0);
                        while ($variant = $variants_result->fetch_assoc()) {
                            if (!in_array($variant['color'], $colors)) {
                                $colors[] = $variant['color'];
                                echo "<option value='{$variant['color']}'>{$variant['color']}</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="size" class="h6">Chọn Size</label>
                    <select id="size" name="size" class="form-control" style="margin: 10px 0;">
                        <?php
                        // Mảng lưu các kích thước để tránh trùng lặp
                        $sizes = [];
                        $variants_result->data_seek(0); // Reset kết quả về đầu
                        while ($variant = $variants_result->fetch_assoc()) {
                            if (!in_array($variant['size'], $sizes)) {
                                $sizes[] = $variant['size'];
                                echo "<option value='{$variant['size']}'>{$variant['size']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <h6 id="total-price">Tổng tiền: <span
                        id="total"><?php echo number_format($product['price'], 0, ) . ' ₫'; ?></span></h6>

                <div class="form-group" style="display: flex;">
                    <!-- Form cho "Mua hàng" -->
                    <form method="POST" style="width: 100%; margin-right: 5px;" action="checkout.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                        <input type="hidden" name="quantity" id="quantity-form" value="4">

                        <button type="submit" name="action" value="buy" class="btn btn-danger"
                            style="width: 100%; padding: 8px;" onclick="updateQuantityInForm()">Mua hàng</button>
                    </form>


                </div>

                </form>
                <!-- Form cho "Thêm vào giỏ hàng" -->
                <form method="POST" style="width: 100%;" action="cart_page.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" name="quantity" id="quantity-form" value="1">

                    <button type="submit" name="action" class="btn btn-outline-light"
                        style=" width: 100%; padding: 8px; color: #000; border: 1px solid #000;">Thêm vào giỏ
                        hàng</button>
                </form>
            </div>


            <hr>

            <!-- Mô tả sản phẩm -->
            <div class="form-group">
                <h4>Mô tả</h4>
                <p class="lead"><?php echo $product['description']; ?></p>
            </div>
        </div>
    </div>
    </div>
    <hr />
    <div class="container mx-auto p-6">
        <h3 class="text-3xl font-bold text-center mb-8">Đánh giá sản phẩm</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            // Kiểm tra xem có sản phẩm nào không
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <!-- Product Card -->
                    <div
                        class="bg-white rounded-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <div class="relative">
                            <a href="details.php?product_id=<?php echo $row['product_id']; ?>">
                                <img class="w-full h-64 object-cover transition-transform duration-300 hover:scale-105"
                                    src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['name']; ?>">
                            </a>
                            <!-- Nút Yêu thích -->
                            <button
                                class="absolute top-2 right-2 bg-white p-2 rounded-full hover:bg-gray-100 group transition duration-200">
                                <svg class="w-5 h-5 text-red-500 group-hover:hidden" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                </svg>
                                <span class="hidden group-hover:inline-block text-red-500 text-sm font-semibold">Yêu
                                    thích</span>
                            </button>
                            <!-- Hiển thị số lượng -->
                            <div class="absolute bottom-2 left-2">
                                <span style="color: #ffffff;">Số lượng còn lại:</span>
                                <span
                                    class="font-bold <?php echo ($row["quantity"] > 10) ? 'text-green-500' : (($row["quantity"] > 0) ? 'text-yellow-500' : 'text-red-500'); ?>">
                                    <?php echo $row["quantity"]; ?>
                                </span>
                                <?php if ($row["quantity"] > 10): ?>
                                    <svg class="w-4 h-4 inline text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 15l-5-5 1.41-1.41L10 12.17l7.59-7.59L19 5z" />
                                    </svg>
                                <?php elseif ($row["quantity"] > 0): ?>
                                    <svg class="w-4 h-4 inline text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 15l-5-5 1.41-1.41L10 12.17l7.59-7.59L19 5z" />
                                    </svg>
                                <?php else: ?>
                                    <span class="text-red-500">Hết hàng</span>
                                    <svg class="w-4 h-4 inline text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 10.59l-5.29-5.3L5.3 5.3 12 12l6.7-6.7 1.41 1.41z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo $row["name"]; ?></h3>

                            <div class="mt-3 flex justify-between items-center">
                                <span
                                    class="text-lg font-bold text-blue-500"><?php echo number_format($row["price"], 0, ',', '.') . 'đ'; ?></span>
                            </div>
                            <div class="mt-3 flex space-x-2">
                                <form action="cart_page.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <button class="btn btn-success" type="submit">Thêm vào Giỏ</button>
                                </form>

                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <a href="details.php?product_id=<?php echo $row['product_id']; ?>"
                                    class="flex items-center bg-blue-500 text-white px-2 py-1 rounded-lg hover:bg-blue-600 transition-colors duration-200"
                                    type="submit">
                                    Mua Ngay
                                </a>

                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<p class='col-span-full text-center'>Không có sản phẩm nào</p>";
            }
            ?>
        </div>
    </div>

    <!-- comment form -->
    <!-- Form Đánh giá -->
    <div class="comment-container">
        <form action="add_comment.php?product_id=<?php echo $product_id; ?>" method="POST" class="add-comment-form">
            <textarea name="comment" class="comment-textarea" rows="4" placeholder="Viết Đánh giá của bạn..."
                required></textarea>
            <button type="submit" class="submit-btn">Gửi Đánh giá</button>
        </form>

        <!-- Hiển thị Đánh giá -->
        <div class="comments-section">
            <?php if (mysqli_num_rows($comments_result) == 0) { ?>
                <p class="no-comments">Chưa có Đánh giá nào. Hãy là người đầu tiên Đánh giá!</p>
            <?php } else { ?>
                <ul class="comments-list">
                    <?php while ($comment = mysqli_fetch_assoc($comments_result)) { ?>
                        <li class="comment-item">
                            <div class="comment-header">
                                <h4 class="user-name"><?php echo htmlspecialchars($comment['user_username']); ?></h4>
                                <small class="comment-time"><?php echo $comment['created_at']; ?></small>
                            </div>
                            <p class="comment-content"><?php echo htmlspecialchars($comment['content']); ?></p>
                            <div class="star-rating">
                                <span class="star">&#9733;</span>
                                <span class="star">&#9733;</span>
                                <span class="star">&#9733;</span>
                                <span class="star">&#9733;</span>
                                <span class="star">&#9733;</span>
                            </div>
                            <div class="comment-actions">
                                <button class="delete-btn" onclick="deleteComment()"><i class="fa-solid fa-trash"></i></button>

                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>

<!-- footer -->
    <?php include '../include/footer.php'; ?>

</body>

<script>

    const mainImage = document.getElementById('mainImage');
    mainImage.src = imagePath;
    }
    function updateQuantity(change) {
        var quantityInput = document.getElementById('quantity');
        var currentQuantity = parseInt(quantityInput.value);

        // Tính toán số lượng mới
        var newQuantity = currentQuantity + change;

        // Kiểm tra điều kiện min và max
        if (newQuantity < 1) {
            newQuantity = 1;  // Không giảm số lượng nhỏ hơn 1
        } else if (newQuantity > <?php echo $product['quantity']; ?>) {
            newQuantity = <?php echo $product['quantity']; ?>;  // Không tăng số lượng lớn hơn số lượng tồn
        }

        // Cập nhật giá trị số lượng
        quantityInput.value = newQuantity;
    }

    // Cập nhật giá trị số lượng trong form ẩn khi người dùng bấm "Mua hàng"
    function updateQuantityInForm() {
        var quantity = document.getElementById('quantity').value;
        document.getElementById('quantity-form').value = quantity;  // Cập nhật giá trị số lượng vào trường ẩn
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Lắng nghe sự kiện thay đổi chọn màu và kích thước
    document.getElementById('color').addEventListener('change', updateTotalPrice);
    document.getElementById('size').addEventListener('change', updateTotalPrice);

    // Lắng nghe sự kiện thay đổi số lượng
    document.getElementById('quantity').addEventListener('change', updateTotalPrice);

    // Hàm tăng giảm số lượng
    function updateQuantity(change) {
        var quantityInput = document.getElementById('quantity');
        var newQuantity = parseInt(quantityInput.value) + change;
        if (newQuantity >= 1 && newQuantity <= 99) {
            quantityInput.value = newQuantity;
            updateTotalPrice(); // Cập nhật lại tổng tiền
        }
    }

    // Hàm tính toán lại tổng tiền
    function updateTotalPrice() {
        var quantity = parseInt(document.getElementById('quantity').value);
        var selectedColor = document.getElementById('color').value;
        var selectedSize = document.getElementById('size').value;

        // Kiểm tra xem người dùng đã chọn cả màu và kích thước chưa
        if (selectedColor && selectedSize) {
            // Gửi yêu cầu AJAX để lấy giá của biến thể từ server
            fetch(`get_variant_price.php?product_id=<?php echo $product_id; ?>&color=${selectedColor}&size=${selectedSize}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var price = data.price;
                        var total = price * quantity;
                        document.getElementById('total').innerText = total.toLocaleString() + ' ₫'; // Cập nhật tổng tiền
                    } else {
                        document.getElementById('total').innerText = 'Không tìm thấy biến thể';
                    }
                })
                .catch(error => {
                    console.log('Lỗi: ', error);
                });
        }
    }


    // Lắng nghe sự kiện thay đổi chọn màu và kích thước
    document.getElementById('color').addEventListener('change', updatePrice);
    document.getElementById('size').addEventListener('change', updatePrice);

    function updatePrice() {
        const selectedColor = document.getElementById('color').value;
        const selectedSize = document.getElementById('size').value;

        if (selectedColor && selectedSize) {
            // Gửi yêu cầu AJAX để lấy giá của biến thể từ server
            fetch(`get_variant_price.php?product_id=<?php echo $product_id; ?>&color=${selectedColor}&size=${selectedSize}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật giá hiển thị trên trang
                        document.getElementById('total').textContent = numberWithCommas(data.price) + ' ₫';
                    } else {
                        document.getElementById('total').textContent = 'Không tìm thấy biến thể';
                    }
                })
                .catch(error => {
                    console.log('Lỗi: ', error);
                });
        }
    }

    // Hàm định dạng số với dấu phẩy
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>


</html>