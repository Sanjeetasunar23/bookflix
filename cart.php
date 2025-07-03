<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'User';

// Redirect if not logged in
if (!$user_id) {
    header('location:login.php');
    exit();
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$remove_id'") or die(mysqli_error($conn));
    $message[] = 'Item removed successfully.';
    header('location:cart.php');
    exit();
}

// Update cart quantity
if (isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $price = $_POST['book_price'];
    $quantity = $_POST['update_quantity'];
    $total = $price * $quantity;

    mysqli_query($conn, "UPDATE cart SET quantity = '$quantity', total = '$total' WHERE id = '$cart_id'") or die(mysqli_error($conn));
    $message[] = "$user_name, your cart was updated successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="css/hello.css">
    <style>
        .cart-btn1, .cart-btn2 {
            display: inline-block;
            padding: 0.8rem 1.2rem;
            font-size: 15px;
            border-radius: 0.5rem;
            margin: 20px 5px;
            text-decoration: none;
        }

        .cart-btn1 {
            background-color: #ffa41c;
            color: black;
        }

        .cart-btn2 {
            background-color: rgb(0, 167, 245);
            color: black;
        }

        .message {
            position: sticky;
            top: 0;
            margin: 10px auto;
            width: 60%;
            background-color: #fff;
            padding: 10px;
            border: 2px solid rgb(68, 203, 236);
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            color: rgb(240, 18, 18);
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: center;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }

        img {
            height: 90px;
        }
    </style>
</head>
<body>

<?php include 'index_header.php'; ?>

<div class="cart_form">
    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<div class="message" id="messages">' . $msg . '</div>';
        }
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total (₹)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grand_total = 0;
            $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
            if (mysqli_num_rows($select_cart) > 0) {
                while ($row = mysqli_fetch_assoc($select_cart)) {
                    $subtotal = $row['price'] * $row['quantity'];
                    $grand_total += $subtotal;
                    ?>
                    <tr>
                        <td><img src="added_books/<?php echo $row['image']; ?>" alt=""></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>₹<?php echo $row['price']; ?></td>
                        <td>
                            <form method="post">
                                <input type="number" name="update_quantity" value="<?php echo $row['quantity']; ?>" min="1" max="10">
                                <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="book_price" value="<?php echo $row['price']; ?>">
                                <button type="submit" name="update">Update</button>
                            </form>
                        </td>
                        <td>₹<?php echo number_format($subtotal); ?></td>
                        <td><a href="cart.php?remove=<?php echo $row['id']; ?>" style="color: red;">Remove</a></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="6">Your cart is empty!</td></tr>';
            }
            ?>
            <tr>
                <td colspan="4"><strong>Grand Total</strong></td>
                <td colspan="2"><strong>₹<?php echo number_format($grand_total); ?>/-</strong></td>
            </tr>
        </tbody>
    </table>

    <?php if ($grand_total > 0) { ?>
        <a href="checkout.php" class="cart-btn1">Proceed to Checkout</a>
    <?php } ?>
    <a href="index.php" class="cart-btn2">Continue Shopping</a>
</div>

<?php include 'index_footer.php'; ?>

<script>
// Hide message after 5 seconds
setTimeout(() => {
    const msg = document.getElementById('messages');
    if (msg) msg.style.display = 'none';
}, 5000);
</script>

</body>
</html>
