<?php
session_start();

$conn = new mysqli("localhost", "root", "", "zsmeie_casino");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Użytkownik nie jest zalogowany']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Pobierz dane z żądania
$data = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : null;

if (!$action) {
    echo json_encode(['success' => false, 'error' => 'Brak akcji do wykonania']);
    exit;
}

// Obsługa akcji
switch ($action) {
    case 'get_balance':
        // Pobierz aktualny balans
        $sql = "SELECT balance FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($balance);
        $stmt->fetch();
        $stmt->close();

        if ($balance !== null) {
            echo json_encode(['success' => true, 'balance' => $balance]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Nie udało się pobrać balansu']);
        }
        break;

    case 'place_bet':
        // Odejmij zakład z balansu
        $bet = isset($data['bet']) ? (float)$data['bet'] : 0;

        // Pobierz aktualny balans
        $sql = "SELECT balance FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($currentBalance);
        $stmt->fetch();
        $stmt->close();

        if ($currentBalance !== null && $bet > 0 && $bet <= $currentBalance) {
            $newBalance = $currentBalance - $bet;
            $updateSql = "UPDATE users SET balance = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("di", $newBalance, $user_id);
            $updateStmt->execute();
            $updateStmt->close();

            echo json_encode(['success' => true, 'new_balance' => $newBalance]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Nieprawidłowy zakład lub brak środków']);
        }
        break;

    case 'update_balance':
        // Dodaj wygraną kwotę do balansu
        $winAmount = isset($data['winAmount']) ? (float)$data['winAmount'] : 0;

        if ($winAmount > 0) {
            $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $winAmount, $user_id);
            $stmt->execute();
            $stmt->close();

            echo json_encode(['success' => true, 'added_amount' => $winAmount]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Nieprawidłowa kwota do dodania']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Nieznana akcja']);
}

$conn->close();
?>
