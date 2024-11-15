<?php
session_start();
require_once "config.php";

$sql_products = "SELECT * FROM products";
$sql_categories = "SELECT * FROM categories";

$result_products = $conn->query($sql_products);
$result_categories = $conn->query($sql_categories);

$sql = "SELECT * FROM products";
$resuilt = $conn->query($sql_products);


?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            overflow-y: auto;
        }
        .sidebar h2 {
            padding: 15px;
            font-size: 1.5rem;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .list-group-item {
            background-color: #343a40;
            border: none;
        }
        .list-group-item a {
            color: white;
        }
        .list-group-item a:hover {
            background-color: #495057;
            color: white;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 class="text-center">Welcome admin</h2>
        <ul class="list-group">
            <li class="list-group-item"><a href="index.php">Trang chủ</a></li>
            <li class="list-group-item"><a href="manage_products.php">Quản lý sản phẩm</a></li>
            <li class="list-group-item"><a href="manage_orders.php">Quản lý đơn hàng</a></li>
            <li class="list-group-item"><a href="manage_users.php">Quản lý người dùng</a></li>
            <li class="list-group-item"><a href="manage_categories.php">Quản lý danh mục</a></li>
            <li class="list-group-item"><a href="manage_coupons.php">Mã giảm giá</a></li>
            <li class="list-group-item"><a href="add_variant.php">Biến Thể</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Chào mừng đến với bảng điều khiển quản trị</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Tổng sản phẩm</div>
                    <div class="card-body">
                        <h5 class="card-title">100</h5>
                        <p class="card-text">Số lượng sản phẩm hiện có trong kho.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Tổng đơn hàng</div>
                    <div class="card-body">
                        <h5 class="card-title">50</h5>
                        <p class="card-text">Số lượng đơn hàng đã đặt.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Tổng người dùng</div>
                    <div class="card-body">
                        <h5 class="card-title">30</h5>
                        <p class="card-text">Số lượng người dùng đã đăng ký.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h1>Danh sách sản phẩm</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Mô tả</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Hình ảnh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kiểm tra xem có sản phẩm nào không
                    if ($result_products->num_rows > 0) {
                        while ($row = $result_products->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["product_id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["description"] . "</td>";
                            echo "<td>" . number_format($row["price"], ) . " VNĐ</td>";
                            echo "<td>" . $row["quantity"] . "</td>";
                            echo "<td><img src='" . $row["image_path"] . "' alt='" . $row["name"] . "' title='" . $row["name"] . "' style='width: 100px;'></td>";
 
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Không có sản phẩm nào</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 
