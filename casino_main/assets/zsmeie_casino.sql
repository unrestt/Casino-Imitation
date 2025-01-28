-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 11:36 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zsmeie_casino`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `numer_tel` varchar(20) NOT NULL,
  `wiadomosc` text NOT NULL,
  `data_wiadomosci` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `user_id`, `imie`, `nazwisko`, `email`, `numer_tel`, `wiadomosc`, `data_wiadomosci`) VALUES
(1, 14, 'test1', 'test1', '1edsad23@g', '12312312', 'xddddd', '2024-12-07 23:20:45'),
(2, 14, 'ewsdasd', 'asdasdasd', '1edsad23@g', 'asdasdasd', '123', '2024-12-07 23:21:33'),
(3, NULL, 'bezid', 'bezid', 'bezid@g', 'bezid', 'bezid', '2024-12-07 23:24:18'),
(4, 23, 'skibidis', 'skibidis', 'skibidis@g', '534124124', 'skibidis', '2024-12-08 20:12:49');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `link` varchar(50) NOT NULL,
  `company` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `name`, `image_url`, `link`, `company`, `description`) VALUES
(1, 'Plinko', 'http://localhost/kasyno-main/images/games_logo/plinko.png', 'plinko.php', 'BetSoft', 'Plinko to gra, w której piłeczka spada na planszę pełną kolców, odbijając się od nich i trafiając do jednego z punktów z przypisaną wygraną. Wynik zależy od losowego toru, który piłeczka pokona. '),
(2, 'Dice', 'http://localhost/kasyno-main/images/games_logo/dice.png', 'dice.php', 'NetEnt', 'Dice to gra, w której gracz obstawia wynik rzutu kostką, wybierając minimalny i maksymalny wynik. Jeśli wynik rzutu mieści się w wybranym zakresie, gracz wygrywa. Gra opiera się na losowości, a wysokość wygranej zależy od stawki i szansy na trafienie.'),
(3, 'Mines', 'http://localhost/kasyno-main/images/games_logo/mines.png', 'bombs.php', 'Microgaming', 'Mines to gra, w której gracz odkrywa pola na planszy, starając się unikać min. Im więcej pól zostanie odkrytych bez trafienia w minę, tym wyższa wygrana. Gra opiera się na losowości i ryzyku, a wynik zależy od decyzji gracza o liczbie odkrywanych pól.'),
(4, 'Slots', 'http://localhost/kasyno-main/images/games_logo/slotss.png', 'slots.php', 'Evolution', 'Sloty to popularna gra hazardowa, która odbywa się na specjalnym automacie. Celem gry jest uzyskanie określonych kombinacji symboli na bębnach, które przynoszą wygraną. Gracz ustawia stawkę, a następnie uruchamia bębny, które kręcą się i zatrzymują w losowej kolejności. Wygrane zależą od tego, jakie symbole pojawią się na linii wygrywającej.'),
(5, 'Poker', 'http://localhost/kasyno-main/images/games_logo/poker.png', 'poker.php', 'Playtech', 'Poker to gra karciana, w której celem jest stworzenie najlepszej kombinacji pięciu kart. Gracze rywalizują o wygraną, stawiając zakłady na podstawie siły swojej ręki. Wygrywa gracz z najwyższą kombinacją kart lub ten, który zmusi innych do spasowania.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `password`, `birth_date`, `balance`, `created_at`) VALUES
(13, 'test2', '123@g', '$2y$10$xiVnbhmSSFVlLRDBccSAge053B.Nhcr/E8lBDP7JTuJVOZ8j9GiNW', '2024-12-04', 281.00, '2024-12-07 20:15:19'),
(14, '123', '1edsad23@g', '$2y$10$6oFII4J6.pP5vmuaH6GygOhNaXfuVF8QZgIK5BwK4qoh6jovopPj2', '2024-12-13', 68.00, '2024-12-07 22:00:37'),
(15, 'unrest', '12323123@g', '$2y$10$UOancMxMoZ/B.VxpBLmsfuQnwpX4tpXQwKs.gY/EodQ95GZWY67d.', '2024-12-03', 10.00, '2024-12-08 17:24:51'),
(16, 'wykrzynik', 'wykrzynik@g', '$2y$10$KdXVEdlf9YxA5Jjn5Kb8bOfio3nrFrwLcSbEyXhIJIsvKGbDtSaqa', '2024-12-18', 0.00, '2024-12-08 18:12:01'),
(17, 'wykrzynik2', 'wykrzynik2@g', '$2y$10$Kl2U/IToC7B2uSe.UEf3ge5/7dpucstsVJQF3WAKa6HSUhShvB5Sa', '2024-12-11', 0.00, '2024-12-08 18:17:00'),
(18, 'wykrzyknik3', 'wykrzyknik3g@g', '$2y$10$LXs8VUn6L3Y1PcLn.6B6/.PUCDSZgYVRol4eFMe75AbfyowQvR0Mi', '2024-12-12', 0.00, '2024-12-08 18:32:29'),
(19, 'test5', 'test5@g', '$2y$10$RX49b4Y6PRuy0L23EpiodOlrYcr0LU.Qs2YzmmFzpGey24W6gGwLS', '2000-12-18', 0.00, '2024-12-08 18:42:13'),
(20, 'wykrzykniczek', 'wykrzykniczek@g', '$2y$10$2iO2a1YneUdEf5VEZaFSaucFYCpzD55JssNfvJ2MuvqFsB1SoiwFK', '2005-12-12', 0.00, '2024-12-08 18:43:23'),
(21, 'DASDASDASD', 'DASDASDASD@g', '$2y$10$kRitiMwhu2/K48tC3T4E3eS.WQiln00APGpUR3zBTDTSi75pKuCme', '1995-12-12', 0.00, '2024-12-08 18:44:15'),
(22, 'JulkaBulka', 'JulkaBulka@g', '$2y$10$9VAQNX1HYumNLlBeQmsAAuAngwnG2/mHjCvXusu.usJlT.dSAChyq', '2004-12-19', 0.00, '2024-12-08 18:47:55'),
(23, 'skibidis', 'skibidis@g', '$2y$10$fpFICu17jx6Psy86JAWwSOE4j8ftDHUBwR6MOFj6/9HfHClK0ylZ2', '1989-12-19', 1.00, '2024-12-08 19:12:10');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_stats`
--

CREATE TABLE `user_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `total_won` decimal(10,2) DEFAULT 0.00,
  `total_lost` decimal(10,2) DEFAULT 0.00,
  `max_win` decimal(10,2) DEFAULT 0.00,
  `max_loss` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_stats`
--

INSERT INTO `user_stats` (`id`, `user_id`, `wins`, `losses`, `total_won`, `total_lost`, `max_win`, `max_loss`) VALUES
(1, 13, 0, 0, 0.00, 0.00, 0.00, 0.00),
(2, 14, 0, 0, 0.00, 0.00, 0.00, 0.00),
(3, 15, 0, 0, 0.00, 0.00, 0.00, 0.00),
(4, 16, 0, 0, 0.00, 0.00, 0.00, 0.00),
(5, 17, 0, 0, 0.00, 0.00, 0.00, 0.00),
(6, 18, 0, 0, 0.00, 0.00, 0.00, 0.00),
(7, 19, 0, 0, 0.00, 0.00, 0.00, 0.00),
(8, 20, 0, 0, 0.00, 0.00, 0.00, 0.00),
(9, 21, 0, 0, 0.00, 0.00, 0.00, 0.00),
(10, 22, 0, 0, 0.00, 0.00, 0.00, 0.00),
(11, 23, 0, 0, 0.00, 0.00, 0.00, 0.00);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `user_stats`
--
ALTER TABLE `user_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user_stats`
--
ALTER TABLE `user_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_stats`
--
ALTER TABLE `user_stats`
  ADD CONSTRAINT `user_stats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
