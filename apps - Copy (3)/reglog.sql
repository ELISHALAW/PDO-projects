-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2025 at 06:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reglog`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(5) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category`) VALUES
(1, 'Asus'),
(2, 'Dell'),
(3, 'Huawei'),
(4, 'Acer');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `Product_name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `category_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `Product_name`, `price`, `image`, `quantity`, `detail`, `category_id`) VALUES
(10, 'Asus Vivo', 3400, 'asuslaptop.jpg', 34, 'Powered by the Snapdragon® X processor, it delivers up to 45 TOPS NPU, offering 1.5X better performance compared to previous generation', 1),
(11, 'Huawei Matebook', 5000, 'huaweiLaptop.jpg', 30, 'HUAWEI MateBook D 16 2024 13th Gen Core i5 16GB+1TB 16 inches Mystic Silver', 3);

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` varchar(100) NOT NULL,
  `expires` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `username`, `email`, `status`, `password`, `image`) VALUES
(1, 'Law Seong Chun', 'law', 'seongchunlaw050@gmail.com', '', '$2y$10$6w7xBfI3Zc6StNlvpKjgb.iqPpJX3gLr9o5JtvvOoE3i9Q4JlN5um', 'fau001.png'),
(2, 'Law', 'Kwong Han', 'seongseonghanhan@gmail.com', '', '$2y$10$fxEQW.XKSnhH/PUzyB5yRO3sN49o1qSH0isZhuVT1fcT.laW/B6va', ''),
(3, 'Yeo Ar Kung', 'ArKung', 'yeoyeokangkang@gmail.com', '', '$2y$10$plqp740pg1hzowUvqjtAhOaZmDXvy2FzhRi9vxyJ8jJVgoXy/sMs2', ''),
(4, 'Soo Winson', 'soowinson', 'soowinson@gmail.com', '', '$2y$10$nBAreDv/ZKE1cSvgYhxobealuKLq0e.XLmtc66uwJGGVFIeSvsu1e', ''),
(5, 'sim wei kian', 'weijian', 'simweikian729@gmail.com', '', '$2y$10$YH2GngoqPe8HQG0Nhd7PSOiLQF2ng4n30vsVweXP31l4NqNk3.5.K', ''),
(7, 'Hao Yi Sheng', 'YiSheng', 'HaoYiSheng@gmail.com', '', '$2y$10$XZnGu6DlR2CcKNS8kFjatuZ09lCQuNFFpJ8qpzbgqeLcDmUMH082i', ''),
(9, 'Teh Boon Chuan', 'Teh Boon Chuan Tutor', 'tehboonchuan@gmail.com', '', '$2y$10$87qf7lRhCBxCPYbAxSwPt.9n1NxLDwg6Q9rRPz6tQo6PQN2HlYg5q', ''),
(10, 'wong zi hao', 'wong', 'wong@gmail.com', '', '$2y$10$7azy.IA2Wka8uRvmBRS7q.0FcueAUeEVl6aI.xXr9IJwmYjNt9yzO', ''),
(12, 'teyhonghua', 'honghua', 'teyhonghua0417@gmail.com', '', '$2y$10$Apk/laqocwAtbeu/AYsVDuUh/Y.InAJsnuMMKM3MEKKQ.VrGNDocq', ''),
(13, 'Yong Song Sun', 'SongSun', 'songson@gmail.com', '', '$2y$10$JVgu3Wr6aAmC34GD6CXjmuLPFt8TtyuBXzgp0xDlDPJFo0WZ/cUQO', ''),
(14, 'admin', 'admin', 'admin@gmail.com', 'admin', '$2y$10$pTfCZ190pDamivpuDuPyjuzxJUE3DUokQTS4LJJIeKrgsMr4Ui3QW', ''),
(15, 'Woon Wong Xiao Ken', 'Ken', 'wwxiaoken@gmail.com', '', '$2y$10$HmmIP.v3A.a17321CzdfkuZ5RX07FQrck9pJ/VwHnN0Ne0EyFGBiC', ''),
(16, 'Lee Jia Hong', 'fafa', 'leejiahong333@gmail.com', '', '$2y$10$psusKd78p9ObxaB9emR2/ev7jBKz3f2EhwPmKYO2QcJCg4vwyKcLC', ''),
(19, 'Lee Chun Kit', 'chunkit', 'leechunkit@gmail.com', '', '$2y$10$iR1xS0cfbWguHgAF7KivX.xZMvpiAndHTB3BXvPZTO772TSAKxWQa', ''),
(20, 'Nick Wong Kai Xuan', 'KaiXuan', 'nickwong@gmail.com', '', '$2y$10$4zqDxG/R4f2s5AUXqqAOOO4mck0TqTBwgVkMdWwrWasTz/U.O5HEa', ''),
(24, 'Wong Jun Keat', 'JunKeat', 'wongjunkeat@gmail.com', '', '$2y$10$W35/GxIJBxMSqeMIPWizjeuAJxq/glqWep7LhJJHn9.eZ1gWnmWFC', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
