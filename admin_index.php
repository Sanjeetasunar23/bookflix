<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  header('location:login.php');
  exit();
}

// Helper function to count rows from a table with optional condition
function getCount($conn, $table, $condition = '') {
  $query = "SELECT * FROM $table";
  if ($condition) $query .= " WHERE $condition";
  $result = mysqli_query($conn, $query) or die('Query failed');
  return mysqli_num_rows($result);
}

// Helper function to calculate total price based on payment status
function getTotalPrice($conn, $status) {
  $total = 0;
  $query = "SELECT total_price FROM confirm_order WHERE payment_status='$status'";
  $result = mysqli_query($conn, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $total += $row['total_price'];
  }
  return $total;
}

// Get data
$pendingTotal = getTotalPrice($conn, 'pending');
$completedTotal = getTotalPrice($conn, 'completed');
$orderCount = getCount($conn, 'confirm_order');
$bookCount = getCount($conn, 'book_info');
$userCount = getCount($conn, 'users_info');
$adminCount = getCount($conn, 'users_info', "user_type='Admin'");
$msgCount = getCount($conn, 'msg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bookflix Admin</title>
  <link rel="stylesheet" href="./css/admin.css">
  <style>
    .main_box {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      padding: 30px;
    }
    .card {
      width: 15rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
      text-align: center;
      background: #fff;
    }
    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .card-body {
      padding: 15px;
    }
    .card-title {
      font-size: 1.1rem;
      margin-bottom: 10px;
    }
    .btn {
      background: #007bff;
      color: white;
      padding: 6px 12px;
      margin: 5px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
    }
    .btn:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

  <?php include 'admin_header.php'; ?>

  <div class="main_box">

    <div class="card">
      <img src="./images/pen3.png" alt="Pending">
      <div class="card-body">
        <h5 class="card-title">Pending Orders (₨)</h5>
        <p><?= $pendingTotal ?></p>
        <a href="admin_orders.php" class="btn">Details</a>
      </div>
    </div>

    <div class="card">
      <img src="./images/compn.png" alt="Completed">
      <div class="card-body">
        <h5 class="card-title">Completed Orders (₨)</h5>
        <p><?= $completedTotal ?></p>
        <a href="admin_orders.php" class="btn">Details</a>
      </div>
    </div>

    <div class="card">
      <img src="./images/orderpn.png" alt="Orders">
      <div class="card-body">
        <h5 class="card-title">Total Orders</h5>
        <p><?= $orderCount ?></p>
        <a href="admin_orders.php" class="btn">Details</a>
      </div>
    </div>

    <div class="card">
      <img src="./images/nu. books.png" alt="Books">
      <div class="card-body">
        <h5 class="card-title">Books Available</h5>
        <p><?= $bookCount ?></p>
        <a href="total_books.php" class="btn">View</a>
        <a href="add_books.php" class="btn">Add</a>
      </div>
    </div>

    

    <div class="card">
      <img src="./images/adminpn2.png" alt="Admins">
      <div class="card-body">
        <h5 class="card-title">Registered Admins</h5>
        <p><?= $adminCount ?></p>
        <a href="users_detail.php" class="btn">Details</a>
      </div>
    </div>

    <div class="card">
      <img src="./images/userspm.png" alt="Users">
      <div class="card-body">
        <h5 class="card-title">Registered Users</h5>
        <p><?= $userCount ?></p>
        <a href="users_detail.php" class="btn">Details</a>
      </div>
    </div>

  </div>

</body>
</html>
