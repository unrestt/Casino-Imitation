<?php
if (isset($_GET['q'])) {
    $query = $_GET['q'];
    
    $conn = new mysqli("localhost", "root", "", "zsmeie_casino");
    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, name, image_url, company, link FROM games WHERE name LIKE ?");
    $searchTerm = $query . '%';
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $gameId = $row['id'];
            $gameName = $row['name'];
            $gameImage = $row['image_url'] ? $row['image_url'] : 'assets/uploads/default-game.png';
            $gameCompany = $row['company'];
            $link = $row['link'];

            echo '<div class="search-result">';
            echo '    <div class="img-result">';
            echo '        <img src="' . htmlspecialchars($gameImage) . '" alt="Gra">';
            echo '        <div class="search-result-active">';
            echo '             <button onclick="window.location.href=\'' . htmlspecialchars($link) . '\'">Zagraj</button>';
            echo '        </div>';
            echo '    </div>';
            echo '    <div class="search-text">';
            echo '        <span>' . htmlspecialchars($gameName) . '</span>';
            echo '        <p>' . htmlspecialchars($gameCompany) . '</p>';
            echo '    </div>';
            echo '</div>';
        }
    } else {
        echo '<p>Brak wyników wyszukiwania.</p>';
    }

    $stmt->close();
    $conn->close();
}
?>
