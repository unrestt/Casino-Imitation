<?php
session_start();

$message_register = "";
$message_login = "";


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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action === "register") {
        $login = trim($_POST['reg-user']);
        $email = trim($_POST['reg-email']);
        $password = password_hash(trim($_POST['reg-password']), PASSWORD_BCRYPT);
        $birth_date = $_POST['reg-date'];
        $today = new DateTime();
        $birthDate = new DateTime($birth_date);
        $age = $today->diff($birthDate)->y;

        if ($age < 18) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $sql = "SELECT * FROM users WHERE login = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['message_register'] = "Istnieje już konto o takim loginie lub adresie e-mail";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $sql = "INSERT INTO users (login, email, password, birth_date, balance, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $login, $email, $password, $birth_date);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $sql_stats = "INSERT INTO user_stats (user_id, wins, losses, total_won, total_lost, max_win, max_loss) 
                              VALUES (?, 0, 0, 0, 0, 0, 0)";
                $stmt_stats = $conn->prepare($sql_stats);
                $stmt_stats->bind_param("i", $user_id);

                if ($stmt_stats->execute()) {
                    $_SESSION['message_register'] = "Pomyślnie utworzono użytkownika";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "Error: " . $stmt_stats->error;
                }
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } elseif ($action === "login") {
        $login = trim($_POST['login-user']);
        $password = trim($_POST['login-password']);

        $sql = "SELECT * FROM users WHERE login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['message_login'] = "Pomyślnie zalogowano";
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['message_login'] = "Nieprawidłowy login lub hasło";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            $_SESSION['message_login'] = "Nieprawidłowy login lub hasło";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $stmt->close();
    }

    $conn->close();
}
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
    <link rel="stylesheet" href="css/games.css">
    <link rel="stylesheet" href="css/login-reg.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>
  <script>window.onload = function(){
      showPopup("popup-register");
      showPopup("popup-login");
  }</script>
   <!-- --------popup succes dla rejestracji---------- -->

   <?php
if (isset($_SESSION['message_register'])) {
    $message_register = $_SESSION['message_register'];
    unset($_SESSION['message_register']);
} else {
    $message_register = '';
}
?>
<div id="popup-register" class="popup-info <?= !empty($message_register) ? '' : 'hidden-popup'; ?>" data-message="<?= htmlspecialchars($message_register); ?>">
    <p></p>
</div>



<!-- --------popup succes dla logowania---------- -->
<?php
if (isset($_SESSION['message_login'])) {
    $message_login = $_SESSION['message_login'];
    unset($_SESSION['message_login']);
} else {
    $message_login = '';
}
?>
<div id="popup-login" class="popup-info <?= !empty($message_login) ? '' : 'hidden-popup'; ?>" data-message="<?= htmlspecialchars($message_login); ?>">
    <p></p>
</div>


<?php
$isLoggedIn = isset($_SESSION['user_id']);
?>
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
        <?php if (!$isLoggedIn): ?>
            <button class="first-button" id="login-button">Zaloguj się</button>
            <button class="first-button" id="register-button">Zarejestruj się</button>
            <div class="user-icon" onclick="toggleUser()" id="responsive-reg-log-open">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="user-login-reg-container" id="user-login-reg-container">
                <button id="login-account">Zaloguj się</button>
                <button id="create-account">Zarejestruj się</button>
            </div>
        <?php else: ?>

         
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
        <?php endif; ?>
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


  <!-- popupy dla logowania i rejestracji -->
  <div id="overlay" class="overlay" style="display: none;"></div>
    <!------------------ logowanie------------------------->
    <div class="popup" id="login-popup" style="display: none;">
        <div class="popup-content">
            <div onclick="closeAllPopups()" class="close-btn-form"><i class="fa-solid fa-xmark"></i></div>
            <div class="top-header-popup">
                <h2>Logowanie</h2>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <label for="user">
                    <div class="label-row-text">
                        <span>Nazwa użytkownika</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="user" id="label-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="user" name="login-user" required>
                    </label>
                </label>
                <label for="password">
                    <div class="label-row-text">
                        <span>Hasło</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="password" id="label-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="login-password" required>
                    </label>
                </label>
                <button type="submit" class="submit-button">Zaloguj</button>
                <p class="form-below-text">Nie masz konta? <span id="createAccount">Załóż konto</span></p>
            </form>


        </div>
    </div>


    <!------------------ rejestracja------------------------->
    <div class="popup" id="register-popup" style="display: none;">
            <div onclick="closeAllPopups()" class="close-btn-form"><i class="fa-solid fa-xmark"></i></div>
            <div class="popup-content">

            
            <div class="top-header-popup">
                <h2>Rejestracja</h2>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <label for="userr">
                    <div class="label-row-text">
                        <span>Nazwa użytkownika</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="userr" id="label-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="userr" name="reg-user" required>
                    </label>
                </label>
                <label for="email">
                    <div class="label-row-text">
                        <span>Email</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="user" id="label-icon">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="email" name="reg-email" required>
                    </label>
                </label>
                <label for="passwordd">
                    <div class="label-row-text">
                        <span>Hasło</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="password" id="label-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="passwordd" name="reg-password" required>
                    </label>
                </label>
                <label for="date">
                    <div class="label-row-text">
                        <span>Data urodzenia</span>
                        <div class="star-char">*</div>
                    </div>
                    <label for="date" id="label-icon">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="date" id="date" name="reg-date" required>
                        <div class="warning-icon" style="display: none;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div class="popup-warning">Użytkownik powinien mieć minimum 18 lat</div>
                        </div>
                    </label>
                </label>


                <button type="submit" class="submit-button" id="register-submit">Zarejestruj</button>
                <p class="form-below-text">Masz już konto? <span id="loginAccount">Zaloguj się</span></p>
            </form>

        </div>
    </div>

    <main>
    <?php
      $conn = new mysqli("localhost", "root", "", "zsmeie_casino");

      if ($conn->connect_error) {
          die("Błąd połączenia: " . $conn->connect_error);
      }

      $sql = "SELECT name, image_url, description, company, link FROM games";
      $result = $conn->query($sql);
      ?>
   <section id="games-showcase">
  <div class="games-intro">
    <h1>Odkryj Nasze Najlepsze Gry</h1>
    <p>Wyjątkowe tytuły, które podbiją Twoje serce. Zagraj i poczuj różnicę!</p>
  </div>
  <div class="games-cards">
    <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<div class="game-card">
                      <div class="game-card-image">
                          <img src="' . $row['image_url'] . '" alt="' . $row['name'] . '">
                      </div>
                      <div class="game-card-content">
                          <h2>' . $row['name'] . '</h2>
                          <h4>' . $row['company'] . '</h4>
                          <p class="game-description">
                                '.$row['description'].'
                          </p>
                          <a href="'.$row['link'].'" class="cta-play-now">Zagraj Teraz</a>
                      </div>
                  </div>';
          }
      } else {
          echo "Brak gier w bazie danych.";
      }
      $conn->close();
      ?>
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

    <script src="scripts/games.js"></script>
    <script src="scripts/nav.js"></script>
    <script src="scripts/main.js"></script>
    <script src="scripts/login-reg.js"></script>
    <script src="scripts/popup.js"></script>
    <script src="https://kit.fontawesome.com/70f2470b08.js" crossorigin="anonymous"></script>
</body>
</html>