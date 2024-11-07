<?php
// Enable error reporting 
ini_set('display_errors', 'On'); 
ini_set('display_startup_errors', "On"); 
error_reporting(E_ALL);

include 'db.php';

class Order_Model {
    // Мок-функции для API бронирования и подтверждения

    function devtest() {
        return 'devtest=ok';
    }
    
    function mockBookApi($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $barcode) { 
        // Log inputs 
        error_log("Inputs: event_id=$event_id, event_date=$event_date, ticket_adult_price=$ticket_adult_price, ticket_adult_quantity=$ticket_adult_quantity, ticket_kid_price=$ticket_kid_price, ticket_kid_quantity=$ticket_kid_quantity, barcode=$barcode"); 

        // Define possible responses 
        $responses = [ 
            ['message' => 'order successfully booked'], 
            ['error' => 'barcode already exists'] 
        ]; 

        // Select and log random response 
        $response = $responses[array_rand($responses)]; 
        error_log("Response: " . print_r($response, true)); 

        return ["message" => 'order successfully booked'];
        return $response; 
    }

    function mockApproveApi($barcode) {
        $errorResponses = [
            ['error' => 'event cancelled'],
            ['error' => 'no tickets'],
            ['error' => 'no seats'],
            ['error' => 'fan removed']
        ];
        $isBooked = random_int(0, 1);

        return ['message' => 'order successfully approved'];

        return $isBooked ? ['message' => 'order successfully approved'] : $errorResponses[array_rand($errorResponses)];
    }

    public function getOrders() {
        $pdo = connectToDb();
        $stmt = $pdo->prepare("SELECT * FROM test1_orders");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $orders;
    }
    
    public function addOrder(
        $event_id,
        $event_date,
        $ticket_adult_price,
        $ticket_adult_quantity,
        $ticket_kid_price,
        $ticket_kid_quantity
    ) {
        $ticket_adult_price = intval($ticket_adult_price);
        $ticket_adult_quantity = intval($ticket_adult_quantity);
        $ticket_kid_price = intval($ticket_kid_price);
        $ticket_kid_quantity = intval($ticket_kid_quantity);
        // Функция для генерации уникального barcode
        function generateBarcode() {
            $digits = '';
                for ($i = 0; $i < 12; $i++) {
                    // Генерируем случайное число от 0 до 9
                    $digits .= random_int(0, 9);
                }
                return $digits;
        }
        
        // Генерация уникального barcode и отправка брони на API
        $barcode = generateBarcode();
        $max_attempts = 5;
        $attempts = 0;
        $isBooked = false;
    
        while (!$isBooked && $attempts < $max_attempts) {
            $attempts++;
    
            // Отправка брони на API
            $bookResponse = $this->mockBookApi($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $barcode);
    
            if (isset($bookResponse['message']) && $bookResponse['message'] == 'order successfully booked') {
                $isBooked = true;
            } elseif (isset($bookResponse['error']) && $bookResponse['error'] == 'barcode already exists') {
                // Генерация нового barcode и повтор запроса
                $barcode = generateBarcode();
            } else {
                throw new Exception("Error booking order: " . json_encode($bookResponse));
            }
        }
    
        if (!$isBooked) {
            throw new Exception("Unable to book order after multiple attempts.");
        }
    
        // Отправка подтверждения
        $approveResponse = $this->mockApproveApi($barcode);
    
        if (!isset($approveResponse['message']) || $approveResponse['message'] != 'order successfully approved') {
            throw new Exception("Error approving order: " . json_encode($approveResponse));
        }
    
        // Подсчет общей стоимости заказа
        $equal_price = ($ticket_adult_price * $ticket_adult_quantity) + ($ticket_kid_price * $ticket_kid_quantity);
    
        // Подключение к базе данных
        $pdo = connectToDb();
    
        // Сохранение заказа в БД
        $stmt = $pdo->prepare("
            INSERT INTO test1_orders (event_id, event_date, ticket_adult_price, ticket_adult_quantity, ticket_kid_price, ticket_kid_quantity, barcode, equal_price, created)
            VALUES (:event_id, :event_date, :ticket_adult_price, :ticket_adult_quantity, :ticket_kid_price, :ticket_kid_quantity, :barcode, :equal_price, NOW())
        ");
        $stmt->execute([
            ':event_id' => $event_id,
            ':event_date' => $event_date,
            ':ticket_adult_price' => $ticket_adult_price,
            ':ticket_adult_quantity' => $ticket_adult_quantity,
            ':ticket_kid_price' => $ticket_kid_price,
            ':ticket_kid_quantity' => $ticket_kid_quantity,
            ':barcode' => $barcode,
            ':equal_price' => $equal_price
        ]);
    }
}