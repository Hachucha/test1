<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include 'model.php';

    $model = new Order_Model();
    try {
        $orders = $model->getOrders();

    }
    catch (Exception $e) {
        echo $e->getMessage();
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test "addOrder" Function</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container my-5">
    <h2>Orders</h2>
    <a><a href="test_add_order.php">Add Order</a></a>
    <ul>
        <?php foreach ($orders as $order) : ?>
            <li>
                Order ID: <?= $order['id'] ?><br>
                Event ID: <?= $order['event_id'] ?><br>
                Event Date: <?= $order['event_date'] ?><br>
                Adult Ticket Price: <?= $order['ticket_adult_price'] ?><br>
                Adult Ticket Quantity: <?= $order['ticket_adult_quantity'] ?><br>
                Kid Ticket Price: <?= $order['ticket_kid_price'] ?><br>
                Kid Ticket Quantity: <?= $order['ticket_kid_quantity'] ?><br>
                Total Price: <?= $order['equal_price'] ?><br>
            </li>
        <?php endforeach; ?>
        <?php if (empty($orders)) : ?>
            <li>No orders found.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
