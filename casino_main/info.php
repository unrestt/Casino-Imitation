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
    <link rel="stylesheet" href="css/info.css">
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
    <section id="info-section">
                <div class="header-main">
                    <i class="fa-solid fa-circle-info"></i>
                    <h1>Info</h1>
                </div>
                <div class="info-container">
                    <div class="sidebar">
                        <ul>
                            <li data-section="about" class="active">Informacje o firmie</li>
                            <li data-section="rules">Zasady i regulaminy</li>
                            <li data-section="payments">Metody płatności</li>
                            <li data-section="licenses">Licencje i certyfikaty</li>
                            <li data-section="responsible">Odpowiedzialna gra</li>
                            <li data-section="faq">FAQ</li>
                            <li data-section="promotions">Promocje i bonusy</li>
                            <li data-section="technical">Warunki techniczne</li>
                        </ul>
                    </div>
                    <div class="content">
                        <section id="about" class="visible">
                            <div class="content-top">
                                <h2>Informacje o firmie</h2>
                                <p>Nasze kasyno online jest zarządzane przez firmę <strong>Luminary Game Studios</strong>, zarejestrowaną pod adresem: Świętego Józefa 26, 87-100 Toruń, Polska. Jesteśmy licencjonowani przez Toruń Gaming Authority (licencja numer TGA/123/456/2024). </p>

                            </div>
                            <div class="content-bottom">
                                <br>
                                <p>Możesz się z nami skontaktować przez:</p>
                                <ul>
                                    <li>Email: <a href="mailto:zsmeiecasino@zsmeie.pl">zsmeiecasino@zsmeie.pl</a></li>
                                    <li>Telefon: (+48) 123 456 789</li>
                                    <li><a href="index.php#contact-section">Formularz kontaktowy</a> dostępny na stronie.</li>
                                </ul>
                            </div>
                          
                        </section>
                        <section id="rules">
                            <div class="content-top">
                                <h2>Zasady i regulaminy</h2>
                                <p>Zapraszamy do zapoznania się z regulaminem użytkowania, który opisuje szczegółowe zasady korzystania z naszej platformy. Znajdziesz tam również politykę prywatności dotyczącą przetwarzania danych osobowych. Pamiętaj, że przestrzegamy zasad odpowiedzialnej gry, oferując narzędzia wspierające bezpieczne korzystanie z kasyna.</p><br><br>
                            </div>
                            <div class="content-bottom">
                                <a href="assets/Regulamin.pdf" class="pdf-button" download>
                                    <div class="pdf-icon">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <p>Pobierz regulamin</p>
                                </a>
                            </div>
                           
                        </section>
                        <section id="payments">
                            <div class="content-top">
                                <h2>Metody płatności</h2>
                                <p>Akceptujemy różne metody płatności, takie jak:<br><br></p>

                                <div class="payments-info-container">
                                    <div class="payment-info">
                                        <i class="fa-solid fa-credit-card" ></i>
                                        <img src="images/payments/creditcard.png"  id="credit-card" alt="Visa-Mastercard">
                                    </div>
                                    <div class="payment-info">
                                        <i class="fa-solid fa-wallet"></i>
                                        <img src="images/payments/paypal.png" alt="paypal" id="paypal">
                                        <img src="images/payments/skrill.png" alt="skrill" id="skrill">
                                        <img src="images/payments/netteler.png" alt="netteler" id="netteler">
                                    </div>
                                    <div class="payment-info">
                                        <i class="fa-brands fa-bitcoin"></i>
                                        <img src="images/payments/bitcoin.png" alt="bitcoin" id="bitcoin">
                                        <img src="images/payments/etherum.png" alt="eth" id="eth">
                                        <img src="images/payments/dogecoin.png" alt="doge" id="doge">
                                    </div>
                                </div>

                            </div>
                            <div class="content-bottom">
                            <br>
                            <p>Wpłaty są przetwarzane natychmiast, a czas realizacji wypłat zależy od wybranej metody (zwykle od 1 do 5 dni roboczych). Prosimy pamiętać, że czas wypłaty może się różnić w zależności od banku lub dostawcy usług płatniczych. W przypadku jakichkolwiek opóźnień zalecamy kontakt z naszym działem obsługi klienta, który chętnie pomoże rozwiązać problem.</p>
                            </div>
                        
                        </section>
                        <section id="licenses">
                            <div class="content-top">
                                <h2>Licencje i certyfikaty</h2>
                                <p>Nasze kasyno działa zgodnie z najwyższymi standardami branżowymi, posiadając licencje wydane przez uznane władze regulacyjne. Oto niektóre z naszych licencji i certyfikatów:</p>
                                <ul>
                                    <li><strong>Licencja Luminary Gaming Authority</strong> – Licencja nr LGA/2345/2024, wydana przez Luminary Gaming Authority, zapewniająca pełną zgodność z obowiązującymi przepisami prawa w zakresie hazardu online.</li>
                                    <li><strong>Certyfikat FairPlay</strong> – Certyfikat potwierdzający, że wszystkie nasze gry są sprawiedliwe i oparte na sprawdzonych algorytmach losowości, zatwierdzony przez eCOGRA.</li>
                                    <li><strong>Licencja Global Gaming Commission</strong> – Licencja nr GGC/789/2024, która gwarantuje przestrzeganie międzynarodowych norm odpowiedzialnej gry i ochrony graczy.</li>
                                    <li><strong>Certyfikat Responsible Gambling</strong> – Zatwierdzony przez GamCare, certyfikat wskazujący, że nasze kasyno wspiera odpowiedzialną grę, oferując narzędzia do samowykluczenia, ustalania limitów oraz monitorowania czasu spędzanego na platformie.</li>
                                </ul>
                                </div>
                                <div class="content-bottom">
                                    <br>
                                    <p>Wszystkie nasze licencje i certyfikaty są regularnie weryfikowane przez niezależne audyty, co zapewnia naszym graczom pełne bezpieczeństwo i uczciwość w trakcie korzystania z naszej platformy.</p>
                                </div>
                              

                       
                        </section>
                        <section id="responsible">
                            <div class="content-top">
                                <h2>Odpowiedzialna gra</h2>
                                <p>W naszym kasynie priorytetem jest zapewnienie bezpiecznego i odpowiedzialnego środowiska do gry. Dbamy o to, by nasi gracze mieli pełną kontrolę nad swoją aktywnością, oferując szereg narzędzi i zasobów wspierających odpowiedzialną grę. Oferujemy następujące opcje:</p>
                                <ul>
                                    <li><strong>Limity wpłat</strong> – Możesz ustawić dzienne, tygodniowe lub miesięczne limity wpłat, aby kontrolować wydatki na grę.</li>
                                    <li><strong>Czasowe zawieszenie konta</strong> – Jeśli czujesz, że potrzebujesz przerwy od gry, umożliwiamy zawieszenie konta na określony czas, w celu odpoczynku.</li>
                                    <li><strong>Możliwość samowykluczenia</strong> – Jeśli czujesz, że Twoje nawyki związane z grą mogą stanowić problem, oferujemy możliwość całkowitego samowykluczenia z platformy na stałe lub na określony okres czasu.</li>
                                    <li><strong>Informacje o ryzyku hazardowym</strong> – Zawsze przypominamy o możliwych ryzykach związanych z hazardem, dostarczając dostęp do edukacyjnych zasobów i materiałów.</li>
                                    <li><strong>Ograniczenia dostępu do funkcji gry</strong> – Możliwość zablokowania dostępu do określonych funkcji kasynowych, takich jak zakłady wysokiego ryzyka, przez określony czas lub do momentu ponownego włączenia opcji.</li>
                                </ul>
                            </div>
                        </section>
                        <section id="faq">
                            <div class="content-top">
                                <h2>FAQ</h2>
                                <p>Najczęściej zadawane pytania:</p><br>
                                <div class="faq-item">
                                    <button class="faq-question">Jak odzyskać hasło?</button>
                                    <div class="faq-answer">
                                        <p>Kliknij "Zapomniałeś hasła?" na stronie logowania i postępuj zgodnie z instrukcjami, aby zresetować swoje hasło.</p>
                                    </div>
                                </div>
                                <div class="faq-item">
                                    <button class="faq-question">Jak dokonać wypłaty?</button>
                                    <div class="faq-answer">
                                        <p>Przejdź do sekcji "Wypłaty" w swoim profilu, wybierz metodę wypłaty i postępuj zgodnie z instrukcjami na stronie.</p>
                                    </div>
                                </div>
                                <div class="faq-item">
                                    <button class="faq-question">Czy mogę zmienić metodę płatności?</button>
                                    <div class="faq-answer">
                                        <p>Tak, możesz zmienić metodę płatności w ustawieniach swojego konta. Przejdź do sekcji "Płatności" i wybierz nową metodę.</p>
                                    </div>
                                </div>
                            </div>
                         
                        </section>
                        <section id="promotions">
                            <div class="content-top">
                            <h2>Promocje i bonusy</h2>
                            <p>Oferujemy różne promocje, które pozwalają naszym graczom cieszyć się dodatkowymi korzyściami. Nasze promocje obejmują między innymi:</p>
                            <ul>
                                <li><strong>Bonus powitalny</strong> – dla nowych graczy, którzy dołączają do naszej platformy. Bonus jest dostępny po dokonaniu pierwszej wpłaty.</li>
                                <li><strong>Darmowe spiny</strong> – oferujemy darmowe spiny co tydzień, które można wykorzystać w wybranych grach.</li>
                                <li><strong>Specjalne promocje sezonowe</strong> – w trakcie roku organizujemy promocje związane z określonymi wydarzeniami, takimi jak Święta, Black Friday czy Nowy Rok.</li>
                            </ul>
                            </div>
                            <div class="content-bottom">
                                <br>
                                <p>Pełny regulamin promocji oraz szczegóły dotyczące dostępnych bonusów znajdziesz na naszej stronie w sekcji "Regulamin bonusów". Upewnij się, że zapoznałeś się z warunkami przed skorzystaniem z oferty.</p>
                            </div>


                          
                        </section>
                        <section id="technical">
                            <div class="content-top">
                                <h2>Warunki techniczne</h2>
                                <p>Nasze kasyno działa na większości urządzeń. Minimalne wymagania systemowe:</p>
                                <ul>
                                    <li>Przeglądarka: Chrome, Firefox, Safari, Edge (najnowsza wersja)</li>
                                    <li>System operacyjny: Windows 10, macOS 10.12, Android 8.0, iOS 12</li>
                                    <li>Połączenie internetowe: 5 Mbps lub więcej</li>
                                </ul>
                            </div>
                       
                        </section>
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

    <script src="scripts/info.js"></script>
    <script src="scripts/nav.js"></script>
    <script src="scripts/main.js"></script>
    <script src="scripts/login-reg.js"></script>
    <script src="scripts/popup.js"></script>
    <script src="https://kit.fontawesome.com/70f2470b08.js" crossorigin="anonymous"></script>
</body>
</html>