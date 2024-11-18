<?php
session_start();
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng xuất thành công</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Đăng xuất thành công",
            showConfirmButton: false,
            timer: 1500
        }).then(function () {
            window.location.href = "../html/index.php";
        });
    </script>
</body>

</html>