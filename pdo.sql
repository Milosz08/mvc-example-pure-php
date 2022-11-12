-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2022 at 02:20 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdo`
--
CREATE DATABASE IF NOT EXISTS `pdo` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `pdo`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `authors` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `copies` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `authors`, `copies`) VALUES
(1, 'Podstawy Techniki Mikroprocesorowej', 'Jerzy Jakubiec', 5),
(2, 'Java - Kompendium programisty, wydanie X', 'Herbert Shildt', 8),
(3, 'Spring Framework w akcji', 'Craig Walls', 12),
(4, 'Technika cyfrowa i mikroprocesorowa w ćwiczeniach laboratoryjnych', 'Ryszard Bogacz, Jerzy Roj, Janusz Tokarski', 6),
(5, 'Calculus and linear algebra in exercises', 'Ewa Łobos, Janina Macura, Beata Sikora', 1),
(6, 'Programowanie równoległe i rozproszone', 'Andrzej Karbowski, Ewa Niewiadomska-Szynkiewicz', 3),
(7, 'Oracle. Projektowanie rozproszonych baz danych', 'Robert Wrembel, Bartosz Bębel', 6);

-- --------------------------------------------------------

--
-- Table structure for table `books_users_binding`
--

CREATE TABLE `books_users_binding` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `books_users_binding`
--

INSERT INTO `books_users_binding` (`user_id`, `book_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'administrator'),
(2, 'czytelnik');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `login` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `password` char(40) COLLATE utf8_polish_ci NOT NULL,
  `age` int(10) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `login`, `password`, `age`, `role_id`) VALUES
(1, 'Jan', 'Kowalski', 'user', 'df9c96eae17282db25da4a0c5595181b96ed52a5', 25, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `books_users_binding`
--
ALTER TABLE `books_users_binding`
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books_users_binding`
--
ALTER TABLE `books_users_binding`
  ADD CONSTRAINT `books_users_binding_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `books_users_binding_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
