<?php
include 'model.php';

$model = new Order_Model();
$errorText ="";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $event_id = isset($_POST['event_id']) ? $_POST['event_id'] : null;
        $event_date = isset($_POST['event_date']) ? $_POST['event_date'] : null;
        $ticket_adult_price = isset($_POST['ticket_adult_price']) ? $_POST['ticket_adult_price'] : null;
        $ticket_adult_quantity = isset($_POST['ticket_adult_quantity']) ? $_POST['ticket_adult_quantity'] : null;
        $ticket_kid_price = isset($_POST['ticket_kid_price']) ? $_POST['ticket_kid_price'] : null;
        $ticket_kid_quantity = isset($_POST['ticket_kid_quantity']) ? $_POST['ticket_kid_quantity'] : null;
        
        //валидация ввода
        //основные поля
        if (empty($event_id)) {
            $errorText = 'Event ID is required';
        } elseif (!is_numeric($event_id)) {
            $errorText = 'Event ID must be a number';
        } elseif (empty($event_date)) {
            $errorText = 'Event Date is required';
        }
//на минимальное количество билетов
        if (empty($ticket_kid_quantity) && empty($ticket_adult_quantity)) {
            $errorText = 'Ticket quantity is required';
        }
//на типы данных количества и цены и допустимые значения
        if (!empty($ticket_kid_quantity)) {
            if (!is_numeric($ticket_kid_quantity) || $ticket_kid_quantity < 0) {
                $errorText = 'Ticket kid quantity must be a positive number';
            } elseif (!is_numeric($ticket_kid_price) || $ticket_kid_price < 0) {
                $errorText = 'Ticket kid price must be a positive number';
            }
        }
        if (!empty($ticket_adult_quantity)) {
            if (!is_numeric($ticket_adult_quantity) || $ticket_adult_quantity < 0) {
                $errorText = 'Ticket adult quantity must be a positive number, current value: ' . $ticket_adult_quantity;
            } elseif (!is_numeric($ticket_adult_price) || $ticket_adult_price < 0) {
                $errorText = 'Ticket adult price must be a positive number';
            }
        }

        
        if ($errorText == "") {
            try {
                $model->addOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity);
            } catch (Exception $e) {
                $errorText = $e->getMessage();
            }
        }
        if ($errorText == "") {
            header('Location: index.php');
            exit;
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test "addOrder" Function</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light">
    <div class="container my-5">
        <a href="index.php" class="btn btn-outline-secondary mb-3">Back</a>
        <h1 class="text-center">Test "addOrder" Function</h1>

        <form method="post" class="gap-3 p-4 bg-white rounded shadow-sm" style="max-width: 500px; margin: 20px auto">
            <?php if (!empty($errorText)): ?>
                <div class="alert alert-danger"><?= $errorText ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="event_id">Event ID:</label>
                <input type="number" id="event_id" name="event_id" class="form-control" value="<?= $event_id ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="event_date">Event Date:</label>
                <input type="datetime-local" id="event_date" name="event_date" class="form-control"
                    value="<?= $event_date ?>" required>
            </div>

            <div class="form-group">
                <label for="ticket_adult_price">Adult Ticket Price:</label>
                <input type="number" min="0" id="ticket_adult_price" name="ticket_adult_price" class="form-control"
                    value="<?= $ticket_adult_price ?>">
            </div>

            <div class="form-group">
                <label for="ticket_adult_quantity">Adult Ticket Quantity:</label>
                <input type="number" min="0" id="ticket_adult_quantity" name="ticket_adult_quantity"
                    class="form-control" value="<?= $ticket_adult_quantity ?>">
            </div>

            <div class="form-group">
                <label for="ticket_kid_price">Kid Ticket Price:</label>
                <input type="number" min="0" id="ticket_kid_price" name="ticket_kid_price" class="form-control"
                    value="<?= $ticket_kid_price ?>">
            </div>

            <div class="form-group">
                <label for="ticket_kid_quantity">Kid Ticket Quantity:</label>
                <input type="number" min="0" id="ticket_kid_quantity" name="ticket_kid_quantity" class="form-control"
                    value="<?= $ticket_kid_quantity ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        form>.form-group:not(:last-child) {
            margin-bottom: 1rem;

        }
    </style>
</body>

</html>