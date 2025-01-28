<?php
session_start();
$userPanelMessage = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "zsmeie_casino");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT balance FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $balance = $row['balance'];
    }
}

$user_id = $_SESSION['user_id'];
$sql = "
    SELECT u.login, u.email, u.balance, s.wins, s.losses
    FROM users u
    LEFT JOIN user_stats s ON u.id = s.user_id
    WHERE u.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Błąd: użytkownik nie istnieje.";
    exit();
}



if (isset($_POST['change_password'])) {
    $currentpassword = trim($_POST['currentpassword']);
    $newpassword1 = trim($_POST['newpassword-1']);
    $newpassword2 = trim($_POST['newpassword-2']);

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if (password_verify($currentpassword, $user['password'])) {
        if ($newpassword1 === $newpassword2) {
            $newpassword_hashed = password_hash($newpassword1, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newpassword_hashed, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $_SESSION['message_userpanel'] = "Hasło zostało zmienione.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $_SESSION['message_userpanel'] = "Błąd zmiany hasła.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            $_SESSION['message_userpanel'] = "Nowe hasła nie pasują do siebie.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['message_userpanel'] = "Błędne aktualne hasło.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}




if (isset($_POST['change_nickname'])) {
    $newnickname = trim($_POST['newnickname']);

    $sql = "SELECT id FROM users WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $newnickname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message_userpanel'] = "Login już istnieje.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $sql = "UPDATE users SET login = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newnickname, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $_SESSION['message_userpanel'] = "Login został zmieniony.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['message_userpanel'] = "Błąd zmiany loginu.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}



if (isset($_POST['deposit_money'])) {
    $deposit_amount = floatval($_POST['money']);

    if ($deposit_amount > 0) {
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $deposit_amount, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $_SESSION['message_userpanel'] = "Pieniądze zostały dodane do salda.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['message_userpanel'] = "Błąd podczas dodawania pieniędzy.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}





$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zsmeie Casino</title>
    <link rel="icon" type="image/x-icon" href="images/icon.ico">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/userpanel.css">
    <link rel="stylesheet" href="css/login-reg.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>
    <script>window.onload = () => showPopup("popup-panel");</script>
<?php
if (isset($_SESSION['message_userpanel'])) {
    $userPanelMessage = $_SESSION['message_userpanel'];
    unset($_SESSION['message_userpanel']);
} else {
    $userPanelMessage = '';
}
?>
<div id="popup-panel" class="popup-info <?= !empty($userPanelMessage) ? '' : 'hidden-popup'; ?>" data-message="<?= htmlspecialchars($userPanelMessage); ?>">
    <p></p>
</div>
<nav id="home-section">
        <div class="nav-left">
            <div class="hamburger" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <ul id="nav-ul">
                <li><a href="index.php#home-section">HOME</a></li>
                <li><a href="games.php">GRY</a></li>
                <li><a href="index.php#about-section">O NAS</a></li>
                <li><a href="info.php">INFO</a></li>
                <li><a href="index.php#contact-section">KONTAKT</a></li>
            </ul>
        </div>
        <div class="nav-mid">
            <img src="images/logo.png" alt="Logo Zsmeie-Casino" id="normal-size-logo">
            <img src="images/logo-responsive.png" alt="Logo Zsmeie-Casino" id="responsive-size-logo">
        </div>
        <div class="nav-right">
        <div class="balance">
                <div class="balance-text">
                    <p><?= htmlspecialchars($balance); ?> $</p>
                </div>
                <div class="add-balance" onclick="window.location.href='user-panel.php'">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </div>
            <div class="icons-toggle" style="display: none;">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
            <div class="icons-container">
                <div class="user-icon" onclick="window.location.href='user-panel.php'">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="logout-icon" onclick="window.location.href='logout.php'">
                <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <div class="search-button">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <div class="search-container">
            <label for="search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" onkeyup="searchUsers()">
            </label>
            <div class="searching-results" id="search-results">
                    <!------ wyniki wyszukiwania------->
            </div>
        </div>
        </div>
    </nav>

    <main id="panel-main">
    <section id="user-panel">
        <div class="user-panel-container">
            <div class="user-panel-row">
                <div class="user-inf">
                    <span>Witaj, <?= htmlspecialchars($user['login']); ?></span>
                    <div class="user-panel-p">
                        <p>ID użytkownika: #<?= htmlspecialchars($user_id); ?></p>
                        <p>E-mail: <?= htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
                <div class="current-measures">
                    <span>Aktualne środki:</span>
                    <p>$<?= htmlspecialchars(number_format($user['balance'], 2, '.', ',')); ?></p>
                </div>
            </div>

            <div class="user-panel-row">
                <div class="password-change">
                    <span>Zmiana hasła</span>
                    <form method="POST">
                        <label for="currentpassword" id="panel-label">
                            <input type="password" placeholder="Aktualne hasło" name="currentpassword" required>
                            <i class="fa-solid fa-lock"></i>
                        </label>
                        <label for="newpassword-1" id="panel-label">
                            <input type="password" placeholder="Nowe hasło" name="newpassword-1" required>
                            <i class="fa-solid fa-lock"></i>
                        </label>
                        <label for="newpassword-2" id="panel-label">
                            <input type="password" placeholder="Powtórz nowe hasło" name="newpassword-2" required>
                            <i class="fa-solid fa-lock"></i>
                        </label>
                        <button type="submit" name="change_password">Zmień hasło</button>
                    </form>
                </div>
                
                <div class="nickname-change">
                    <span>Zmiana loginu</span>
                    <form method="POST">
                        <label for="newnickname" id="panel-label">
                            <input type="text" placeholder="Nowy nick" name="newnickname" required>
                            <i class="fa-solid fa-user"></i>
                        </label>
                        <button type="submit" name="change_nickname">Zmień nick</button>
                    </form>
                </div>
                
                <div class="deposit-money">
                    <span>Wpłata pieniędzy</span>
                    <form method="POST">
                        <label for="money" id="panel-label">
                            <input type="number" id="money" name="money" placeholder="Kwota do wpłaty" required min="0">
                            <i class="fa-solid fa-coins"></i>
                        </label>
                        <button type="submit" name="deposit_money">Wpłać</button>
                    </form>
                </div>

            </div>

            <div class="statistics">
                <span>Statystyki</span>
                <div class="user-panel-p">
                    <p>Wygrane: <?= htmlspecialchars($user['wins']); ?></p>
                    <p>Przegrane: <?= htmlspecialchars($user['losses']); ?></p>
                </div>
            </div>
        </div>
    </section>
</main>



    <footer>
      <div class="footer-social">
        <a href="https://www.facebook.com/" target="_blank">
          <i class="fa-brands fa-facebook"></i>
        </a>
        <a href="https://www.instagram.com/" target="_blank">
          <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://www.tiktok.com/pl-PL/" target="_blank">
          <i class="fa-brands fa-tiktok"></i>
        </a>
      </div>
      <div class="footer-text">
        <p>Copyright © ZSMEIE Casino, Wszelkie prawa zastrzeżone 2024</p>
      </div>
    </footer>
    <script src="scripts/popup.js"></script>
    <script src="scripts/games.js"></script>
    <script src="scripts/nav.js"></script>
    <script src="scripts/main.js"></script>
    <script src="https://kit.fontawesome.com/70f2470b08.js" crossorigin="anonymous"></script>
</body>
</html>