<?php
include 'config.php';
session_start();
error_reporting(0);

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        $message[] = 'Please Login to get your books';
    } else {
        $book_name = $_POST['book_name'];
        $book_id = $_POST['book_id'];
        $book_image = $_POST['book_image'];
        $book_price = $_POST['book_price'];
        $book_quantity = 1;
        $total_price = $book_price * $book_quantity;

        $check = mysqli_query($conn, "SELECT * FROM cart WHERE book_id='$book_id' AND user_id='$user_id'");
        if (mysqli_num_rows($check) > 0) {
            $message[] = 'This book is already in your cart';
        } else {
            mysqli_query($conn, "INSERT INTO cart (user_id, book_id, name, price, image, quantity, total) VALUES ('$user_id', '$book_id', '$book_name', '$book_price', '$book_image', '$book_quantity', '$total_price')");
            $message[] = 'Book added to cart successfully';
            header('location:index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bookflix & Chill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message {
            position: sticky; top: 0; margin: 0 auto; width: 60%;
            background: #fff; padding: 10px; border: 2px solid #00c3ff;
            border-radius: 8px; text-align: center; color: red;
        }
        .box { width: 250px; height: 350px; margin: 10px; display: inline-block; text-align: center; border: 1px solid #ddd; padding: 10px; }
        .box img { height: 200px; width: 120px; }
    </style>
</head>
<body>

<?php include 'index_header.php'; ?>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo "<div class='message'>{$msg}</div>";
    }
}
?>

<!-- New Books Section -->
<section>
    <div class="container text-center my-4">
        <h2 style="color: #00a7f5;">New Arrivals</h2>
        <div class="d-flex flex-wrap justify-content-center">
        <?php
        $books = mysqli_query($conn, "SELECT * FROM book_info ORDER BY date DESC LIMIT 8");
        while ($book = mysqli_fetch_assoc($books)) {
        ?>
            <div class="box">
                <a href="book_details.php?details=<?= $book['bid'] ?>"><?= $book['name'] ?></a>
                <img src="added_books/<?= $book['image'] ?>" alt="">
                <div>₹ <?= $book['price'] ?> /-</div>
                <form method="POST">
                    <input type="hidden" name="book_name" value="<?= $book['name'] ?>">
                    <input type="hidden" name="book_id" value="<?= $book['bid'] ?>">
                    <input type="hidden" name="book_image" value="<?= $book['image'] ?>">
                    <input type="hidden" name="book_price" value="<?= $book['price'] ?>">
                    <button name="add_to_cart" class="btn btn-sm btn-primary mt-2">Add to Cart</button>
                </form>
            </div>
        <?php } ?>
        </div>
    </div>
</section>

<?php include 'index_footer.php'; ?>

<script>
    setTimeout(() => {
        let msg = document.querySelector('.message');
        if (msg) msg.style.display = 'none';
    }, 8000);
</script>
</body>
</html>
