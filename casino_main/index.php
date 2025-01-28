<?php
session_start();

$email = '';
$message_register = "";
$message_login = "";
$message_contact = "";


$conn = new mysqli("localhost", "root", "", "zsmeie_casino");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT email, balance FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];
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
    }elseif ($action === "login") {
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
    }elseif($action == "contact"){
        $imie = $_POST['imie'];
        $nazwisko = $_POST['nazwisko'];
        $email = $_POST['email'];
        $telefon = $_POST['numer_tel'];
        $wiadomosc = $_POST['wiadomosc'];
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
      
        $query = "INSERT INTO contact (user_id, imie, nazwisko, email, numer_tel, wiadomosc) 
        VALUES " . 
        (is_null($user_id) ? "(NULL, '$imie', '$nazwisko', '$email', '$telefon', '$wiadomosc')" : 
        "('$user_id', '$imie', '$nazwisko', '$email', '$telefon', '$wiadomosc')");
      
        if (mysqli_query($conn, $query)) {
              $_SESSION['message_contact'] = "Wiadomość została wysłana";
              header("Location: " . $_SERVER['PHP_SELF']);
              exit;
        } else {
            echo "Błąd: " . mysqli_error($conn);
        }
      
        mysqli_close($conn);
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
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/game-cards.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/info.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/login-reg.css">
    <link rel="stylesheet" href="css/whyworth.css">
    <link rel="stylesheet" href="css/payments.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>
    <script>window.onload = function(){
        showPopup("popup-register");
        showPopup("popup-login");
        showPopup("popup-contact");
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
if (isset($_SESSION['message_contact'])) {
    $message_contact = $_SESSION['message_contact'];
    unset($_SESSION['message_contact']);
} else {
  $message_contact = '';
}
?>
<div id="popup-contact" class="popup-info <?= !empty($message_contact) ? '' : 'hidden-popup'; ?>" data-message="<?= htmlspecialchars($message_contact); ?>">
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
            <li><a href="#home-section">HOME</a></li>
            <li><a href="games.php">GRY</a></li>
            <li><a href="#about-section">O NAS</a></li>
            <li><a href="info.php">INFO</a></li>
            <li><a href="#contact-section">KONTAKT</a></li>
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



    <!------------ popupy dla logowania i rejestracji ------------>
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

    <header>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="images/slide1.png" alt="slide-1">
                </div>
                <div class="swiper-slide">
                <img src="images/slide2.png" alt="slide-2">
                </div>
                <div class="swiper-slide">
                <img src="images/slide3.png" alt="slide-3">
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </header>
    <main>
            <!-- ---------sekcja o grach------- -->
            <?php
$conn = new mysqli("localhost", "root", "", "zsmeie_casino");

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$sql = "SELECT name, image_url, link FROM games";
$result = $conn->query($sql);
?>

<section id="games-card-section">
    <div class="header-main">
        <i class="fa-solid fa-gamepad"></i>
        <h1>Gry</h1>
    </div>
    <div class="games-cards-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="game-card">
                        <div class="game-card-active">
                            <button onclick="window.location.href=\'' . $row['link'] . '\'">Zagraj</button>
                        </div>
                        <div class="game-card-img">
                            <img src="' . $row['image_url'] . '" alt="Gra">
                        </div>
                        <div class="below-game-name">
                            <p>' . $row['name'] . '</p>
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

            <section id="logos-section">
                <div class="logos-container">
                    <div class="logo suppliers">
                        <img src="images/suppliers/betsoft.png" alt="betsoft">
                    </div>  
                    <div class="logo suppliers">
                        <img src="images/suppliers/evolution.png" alt="evolution">
                    </div>
                    <div class="logo suppliers">
                        <img src="images/suppliers/MicroGaming.webp" alt="MicroGaming">
                    </div>
                    <div class="logo suppliers">
                        <img src="images/suppliers/playtech.png" alt="playtech">
                    </div>
                    <div class="logo suppliers">
                        <img src="images/suppliers/pragmaticplay.png" alt="pragmaticplay">
                    </div>
                    <div class="logo suppliers">
                        <img src="images/suppliers/netent.png" alt="netent">
                    </div>
                </div>
            </section>
            <!-- ---------sekcja o nas------- -->
            <section id="about-section">
                <div class="header-main">
                    <i class="fa-solid fa-user"></i>
                    <h1>O nas</h1>
                </div>
                <div class="about-container">
                    <div class="left-about">
                        <span>Zsmeie Casino</span><br><p> to miejsce, gdzie pasja do rozrywki i emocje płynące z gry łączą się w niezapomnianym doświadczeniu. Od samego początku naszym celem jest dostarczanie najwyższej jakości rozrywki, która zachwyca każdego gracza – niezależnie od jego preferencji czy doświadczenia. Nasza oferta obejmuje szeroki wybór gier kasynowych, takich slotsy, dice czy blackjack. Stawiamy na uczciwość i przejrzystość, dlatego wszystkie nasze gry są objęte najwyższymi standardami bezpieczeństwa i regulacjami prawnymi. Zsmeie Casino wierzymy, że każda gra to więcej niż tylko szansa na wygraną – to również sposób na spędzenie czasu w ekscytującej atmosferze pełnej pozytywnych emocji. Nasz zespół to profesjonaliści, którzy dbają o komfort i bezpieczeństwo naszych graczy, oferując wsparcie techniczne 24/7.
                        </p>
                    </div>
                    <div class="right-about">
                        <video autoplay loop muted playsinline>
                            <source src="images/aboutus.mp4" type="video/mp4">
                            Twój przeglądarka nie obsługuje odtwarzania wideo.
                        </video>

                    </div>
                </div>




            </section>
            <section id="whyworth-section">
                <div class="worth-container">
                    <div class="worth">
                        <div class="top-icon">
                            <i class="fa-solid fa-bolt-lightning"></i>
                        </div>
                        <div class="worth-middle">
                            <h2>NATYCHMIASTOWE</h2>
                            <h2>WYPŁATY</h2>
                        </div>
                        <div class="worth-bottom">
                            <p>Pożegnaj się z czekaniem - duże wygrane są wypłacane natychmiast.</p>
                        </div>
                    </div>
                    <div class="worth">
                        <div class="top-icon">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <div class="worth-middle">
                            <h2>WYSOKIE LIMITY</h2>
                            <h2>ZAKŁADÓW</h2>
                        </div>
                        <div class="worth-bottom">
                            <p>Zwiększ swoje wrażenia z gry - większe zakłady to szansa na większe wygrane.</p> 
                        </div>
                    </div>
                    <div class="worth">
                        <div class="top-icon">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>
                        <div class="worth-middle">
                            <h2>COTYGODNIOWY</h2>
                            <h2>CASHBACK</h2>
                        </div>
                        <div class="worth-bottom">
                            <p>W każdy poniedziałek wypłacamy 10% tygodniowego cashbacku za darmo.</p>
                        </div>
                    </div>
                </div>
            </section>
            <section id="logos-section">
                <div class="logos-container">
                    <div class="logo">
                        <img src="images/payments/przelewy24.png" alt="przelewy24" id="przelewy24-row">
                    </div>  
                    <div class="logo">
                        <img src="images/payments/blik.png" alt="blik" id="blik-row">
                    </div>
                    <div class="logo">
                        <img src="images/payments/creditcard.png" alt="creditcard" id="creditcard-row">
                    </div>
                    <div class="logo">
                        <img src="images/payments/applepay.png" alt="applepay" id="applepay-row">
                    </div>
                    <div class="logo">
                        <img src="images/payments/googlepay.png" alt="googlepay" id="googlepay-row">
                    </div>
                    <div class="logo">
                        <img src="images/payments/skrill.png" alt="skrill" id="skrill-row">
                    </div>  
                    <div class="logo">
                        <img src="images/payments/paysafecard.png" alt="paysafecard" id="paysafecard-row">
                    </div>
                    <div class="logo">
                        <img src="images/payments/paypal.png" alt="paypal" id="paypal-row">
                    </div>
                </div>
            </section>


            <!------------sekcja kontakt------------->
            <section id="contact-section">
                <div class="header-main">
                    <i class="fa-solid fa-message"></i>
                    <h1>Kontakt</h1>
                </div>
                <div class="contact-container">
                    <form method="POST">
                        <input type="hidden" name="action" value="contact">
                        <label for="imie">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" placeholder="Twoje imię" name="imie" id="imie">
                        </label>
                        <label for="nazwisko">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" placeholder="Twoje nazwisko" name="nazwisko" id="nazwisko">
                        </label>
                        <label for="email">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" placeholder="Twój E-mail" name="email" id="email" value="<?php echo $email; ?>">
                        </label>
                        <label for="tel">
                            <i class="fa-solid fa-phone"></i>
                            <input type="tel" placeholder="Twój numer telefonu" name="numer_tel" id="tel">
                        </label>
                        <textarea placeholder="Twoja wiadomosc..." name="wiadomosc"></textarea>
                        <button type="submit">Wyślij</button>
                    </form>
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
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="scripts/login-reg.js"></script>
    <script src="scripts/nav.js"></script>
    <script src="scripts/main.js"></script>
    <script src="scripts/slider.js"></script>
    <script src="scripts/popup.js"></script>
    <script src="https://kit.fontawesome.com/70f2470b08.js" crossorigin="anonymous"></script>
</body>
</html>