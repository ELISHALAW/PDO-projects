-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2026 at 03:17 PM
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
-- Database: `db`
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
  `count` int(11) NOT NULL,
  `total` float NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `date`, `count`, `total`, `user_id`) VALUES
(134, '2025-04-27', 1, 4000, 1),
(135, '2025-04-27', 1, 6000, 1),
(136, '2025-04-27', 1, 6000, 1),
(137, '2025-04-27', 2, 8000, 1),
(138, '2025-04-27', 1, 5000, 1),
(139, '2025-04-27', 1, 4500, 1),
(140, '2025-04-27', 1, 6000, 1),
(141, '2025-04-27', 1, 6000, 1),
(142, '2025-04-27', 2, 12000, 1),
(143, '2025-04-28', 1, 6000, 1),
(144, '2025-04-28', 2, 12000, 1),
(145, '2025-04-28', 1, 7000, 1),
(146, '2025-04-28', 8, 24792, 1),
(147, '2025-04-28', 1, 7900, 1),
(148, '2025-04-28', 2, 15800, 29),
(149, '2025-04-28', 1, 4000, 1),
(150, '2025-04-29', 1, 7900, 1),
(151, '2025-04-29', 1, 6000, 1),
(152, '2025-04-29', 2, 12000, 30),
(153, '2025-04-29', 2, 15800, 30),
(154, '2025-04-29', 1, 7900, 31),
(155, '2025-04-29', 1, 5000, 1),
(156, '2025-04-29', 2, 12000, 1),
(157, '2025-04-29', 3, 18000, 1),
(158, '2025-04-29', 1, 6000, 1),
(159, '2025-04-29', 10, 50000, 1),
(160, '2025-04-29', 9, 45000, 1),
(161, '2025-04-29', 10, 50000, 1),
(162, '2025-04-29', 10, 50000, 1),
(163, '2025-04-29', 1, 5000, 1),
(164, '2025-04-29', 1, 5000, 1),
(165, '2025-04-29', 9, 45000, 1),
(166, '2025-04-29', 1, 5000, 1),
(167, '2025-04-29', 1, 5000, 1),
(168, '2025-04-29', 2, 10000, 1),
(169, '2025-04-29', 2, 10000, 1),
(170, '2025-04-29', 1, 6000, 1),
(171, '2025-04-29', 1, 6000, 1),
(172, '2025-04-29', 1, 4500, 26),
(173, '2025-04-29', 1, 6000, 26),
(174, '2025-04-29', 1, 6000, 26),
(175, '2025-04-29', 1, 6000, 1),
(176, '2025-04-29', 1, 6000, 1),
(177, '2025-04-29', 1, 4500, 1),
(178, '2025-04-29', 1, 4500, 1),
(179, '2025-04-29', 1, 4500, 1),
(180, '2025-04-29', 1, 4500, 1),
(181, '2025-04-29', 1, 4500, 26),
(182, '2025-04-29', 1, 4500, 1),
(183, '2025-04-30', 1, 5000, 1),
(184, '2025-05-02', 1, 4500, 1),
(185, '2025-05-07', 1, 6000, 1),
(186, '2025-05-07', 10, 60000, 1),
(187, '2025-05-07', 2, 12000, 1),
(188, '2025-05-07', 10, 60000, 1),
(189, '2025-05-09', 5, 25000, 1),
(190, '2025-05-12', 1, 5000, 1),
(191, '2025-05-18', 1, 5000, 1),
(192, '2025-05-23', 1, 4500, 1),
(193, '2025-06-01', 1, 6000, 1),
(194, '2025-07-16', 1, 6000, 1),
(195, '2025-07-31', 1, 6000, 1),
(196, '2025-09-28', 2, 6198, 1),
(197, '2025-12-26', 1, 4000, 1),
(198, '2026-02-02', 1, 6000, 1),
(199, '2026-02-02', 1, 3099, 1),
(200, '2026-02-02', 1, 7000, 1),
(201, '2026-02-02', 1, 7000, 1),
(202, '2026-02-02', 1, 5000, 1),
(203, '2026-02-02', 1, 5000, 1),
(204, '2026-02-02', 1, 5000, 1),
(205, '2026-02-02', 1, 5000, 1),
(206, '2026-02-05', 1, 4000, 1),
(207, '2026-02-05', 1, 6000, 1),
(208, '2026-02-12', 1, 5000, 1),
(209, '2026-02-12', 1, 6000, 1),
(210, '2026-03-22', 1, 6000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `subtotal` float NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `price`, `unit`, `subtotal`, `order_id`, `product_id`) VALUES
(15, 4000, 1, 4000, 134, 28),
(16, 6000, 1, 6000, 135, 31),
(17, 6000, 1, 6000, 136, 39),
(18, 4000, 2, 8000, 137, 30),
(19, 5000, 1, 5000, 138, 35),
(20, 4500, 1, 4500, 139, 38),
(21, 6000, 1, 6000, 140, 39),
(22, 6000, 1, 6000, 141, 39),
(23, 6000, 2, 12000, 142, 39),
(24, 6000, 1, 6000, 143, 39),
(25, 6000, 2, 12000, 144, 39),
(26, 7000, 1, 7000, 145, 40),
(27, 3099, 8, 24792, 146, 49),
(28, 7900, 1, 7900, 147, 41),
(29, 7900, 2, 15800, 148, 41),
(30, 4000, 1, 4000, 149, 33),
(31, 7900, 1, 7900, 150, 41),
(32, 6000, 1, 6000, 151, 50),
(33, 6000, 2, 12000, 152, 50),
(34, 7900, 2, 15800, 153, 41),
(35, 7900, 1, 7900, 154, 41),
(36, 5000, 1, 5000, 155, 52),
(37, 6000, 2, 12000, 156, 50),
(38, 6000, 3, 18000, 157, 50),
(39, 6000, 1, 6000, 158, 50),
(40, 5000, 10, 50000, 159, 51),
(41, 5000, 9, 45000, 160, 52),
(42, 5000, 10, 50000, 161, 52),
(43, 5000, 10, 50000, 162, 52),
(44, 5000, 1, 5000, 163, 52),
(45, 5000, 1, 5000, 164, 52),
(46, 5000, 9, 45000, 165, 52),
(47, 5000, 1, 5000, 166, 52),
(48, 5000, 1, 5000, 167, 52),
(49, 5000, 2, 10000, 168, 51),
(50, 5000, 2, 10000, 169, 51),
(51, 6000, 1, 6000, 170, 39),
(52, 6000, 1, 6000, 171, 39),
(53, 4500, 1, 4500, 172, 38),
(54, 6000, 1, 6000, 173, 39),
(55, 6000, 1, 6000, 174, 39),
(56, 6000, 1, 6000, 175, 39),
(57, 6000, 1, 6000, 176, 39),
(58, 4500, 1, 4500, 177, 38),
(59, 4500, 1, 4500, 178, 38),
(60, 4500, 1, 4500, 179, 38),
(61, 4500, 1, 4500, 180, 38),
(62, 4500, 1, 4500, 181, 38),
(63, 4500, 1, 4500, 182, 38),
(64, 5000, 1, 5000, 183, 52),
(65, 4500, 1, 4500, 184, 38),
(66, 6000, 1, 6000, 185, 50),
(67, 6000, 10, 60000, 186, 39),
(68, 6000, 2, 12000, 187, 50),
(69, 6000, 10, 60000, 188, 50),
(70, 5000, 5, 25000, 189, 51),
(71, 5000, 1, 5000, 190, 52),
(72, 5000, 1, 5000, 191, 51),
(73, 4500, 1, 4500, 192, 36),
(74, 6000, 1, 6000, 193, 50),
(75, 6000, 1, 6000, 194, 50),
(76, 6000, 1, 6000, 195, 50),
(77, 3099, 2, 6198, 196, 49),
(78, 4000, 1, 4000, 197, 37),
(79, 6000, 1, 6000, 198, 50),
(80, 3099, 1, 3099, 199, 49),
(81, 7000, 1, 7000, 200, 40),
(82, 7000, 1, 7000, 201, 40),
(83, 5000, 1, 5000, 202, 51),
(84, 5000, 1, 5000, 203, 51),
(85, 5000, 1, 5000, 204, 51),
(86, 5000, 1, 5000, 205, 52),
(87, 4000, 1, 4000, 206, 30),
(88, 6000, 1, 6000, 207, 51),
(89, 5000, 1, 5000, 208, 52),
(90, 6000, 1, 6000, 209, 51),
(91, 6000, 1, 6000, 210, 51);

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
(26, 'Acer Swift Go 16 AI', 4000, 'acerSwift.jpg', 30, 'Swift Go 16 combines effortless thin and light mobility with a streamlined, laser-etched cover that opens 180° and makes a refined statement anywhere. A multi-control touchpad enables precise control over your media and video calls.', 4),
(27, 'Acer Nitro V 17 AI', 5000, 'AcerNitro.jpg', 30, 'the Acer Nitro V 17 AI is more than a machine—it\'s a gateway to boundless creativity and gaming precision. With AI-driven insights that adjust to every demand, this device blends raw power with intuitive control, taking you from intense battles to endless', 4),
(28, 'Acer Enduro N3', 4000, 'AcerEnduno.jpg', 30, 'Acer Enduro Urban N3 EUN314-51W-72QE 14\'\' FHD Laptop Denim Blue ( I7-1165G7, 8GB, 512GB SSD, Intel, W10, HS )', 4),
(29, 'Acer Predator Helios 16 AI', 5000, 'AcerPredator.jpg', 30, 'Built for gamers who demand precision and style, the Predator Helios Neo 16 AI combines cutting-edge hardware with OLED brilliance and AI-powered tools. Experience unparalleled performance with up to an Intel® Core™ Ultra 9 processor and up to GeForce RTX', 4),
(30, 'Asus Vivobook Pro 15', 4000, 'AsusVivobook.jpg', 29, 'mid-range laptop designed for creators, students, and professionals who prioritize display quality and performance. It offers a balance of power, portability, and visual fidelity.', 1),
(31, 'Asus Vivo S 16', 6000, 'AsusVivobookS16.jpg', 30, 'sleek and powerful laptop designed for users who prioritize display quality, performance, and portability. It\'s particularly well-suited for creative professionals, students, and business users.​', 1),
(32, 'Asus Zenbook 14', 3000, 'AsusZenbook14.jpg', 30, 'premium ultraportable laptop that combines cutting-edge performance with a sleek design, making it ideal for professionals, students, and creatives seeking a powerful yet lightweight device.​', 1),
(33, 'Asus Zenbook S14', 4000, 'AsusZenbookS14.jpg', 29, 'premium ultraportable laptop that combines cutting-edge AI capabilities with a sleek, lightweight design. It\'s particularly well-suited for professionals, creatives, and students who prioritize performance, portability, and advanced features.​', 1),
(34, 'Dell 14 Plus', 5500, 'Dell14Plus.jpg', 30, 'premium ultraportable laptop that combines cutting-edge AI capabilities with a sleek, lightweight design. It\'s particularly well-suited for professionals, creatives, and students who prioritize performance, portability, and advanced features.​', 2),
(35, 'Dell 16 Plus', 5000, 'dell16plus.jpg', 30, 'premium ultraportable laptop that combines cutting-edge AI capabilities with a sleek, lightweight design. It\'s particularly well-suited for professionals, creatives, and students who prioritize performance, portability, and advanced features.​', 2),
(36, 'Dell Alienware X17', 4500, 'DelAlienwareX17.jpg', 29, 'premium ultraportable laptop that combines cutting-edge AI capabilities with a sleek, lightweight design. It\'s particularly well-suited for professionals, creatives, and students who prioritize performance, portability, and advanced features.​', 2),
(37, 'Dell Pro14', 4000, 'DellPRo4.jpg', 29, 'series encompasses several models tailored for professionals seeking a balance between performance, portability, and advanced features.', 2),
(38, 'Huawei Matebook Pro X', 4500, 'HuaweiMatebookProX.jpg', 22, 'Huawei\'s latest flagship ultraportable laptop, combining cutting-edge performance with an elegant design. It is tailored for professionals and creatives who prioritize both power and portability.​', 3),
(39, 'Huawei Matebook D14', 6000, 'HuaweiMatebookD14.jpg', 14, 'sleek and lightweight laptop designed for everyday productivity, offering a balance of performance, portability, and modern features. It\'s particularly suitable for students, professionals, and casual users in Malaysia seeking a reliable device for work a', 3),
(40, 'Huawei Matebook 16S', 7000, 'HuaweiMatebook16S.jpg', 27, 'premium 16-inch laptop designed for professionals and power users who require robust performance, a spacious display, and seamless productivity features. In Malaysia, it is available in a single configuration featuring a 13th Gen Intel® Core™ i9 processor', 3),
(41, 'Huawei Matebook X Pro', 7900, 'HuaweiMatebookXPro.jpg', 23, 'Huawei\'s flagship ultraportable laptop, combining cutting-edge performance with an elegant design. It\'s tailored for professionals and creatives who prioritize both power and portability.​', 3),
(49, 'Asus TUF Gaming A15', 3099, 'AsusT.jpg', 17, ', the ASUS TUF Gaming A15 (FA506N-FRHN666W) is a compelling choice for gamers seeking a reliable and affordable laptop with decent performance and features. If you need assistance comparing it with other models or finding the best deals, feel free to ask!', 1),
(50, 'Asus Vivobook X16', 6000, 'X16.jpg', 14, '​The ASUS Vivobook 16X is a versatile 16-inch laptop series available in various configurations to cater to different user needs, ranging from everyday computing to creative work and gaming', 1),
(51, 'Asus Zenbook 14 OLED', 6000, 'AsusZenbook14OLED.jpg', 14, 'With a 75 Wh battery capacity for up to 15+ hours battery life, ASUS Zenbook 14 OLED has the day-long stamina you need, and more. And when it’s time for a top-up, USB-C® Easy Charge is ultra-quick and ultra-convenient!', 1),
(52, 'Dell14plus2', 5000, 'dell14plus2.jpg', 28, 'The Dell Inspiron 14 Plus 2-in-1 (Model 7440) is a premium convertible laptop designed for both work and entertainment. It features a 14-inch touch display with resolution options up to 2.8K, offering sharp visuals and vibrant color.', 2);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `textarea` varchar(1000) NOT NULL,
  `number_of_star` int(5) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `name`, `textarea`, `number_of_star`, `user_id`) VALUES
(18, 'Law Seong Chun ', 'Absolutely love my new laptop! The performance is super fast, battery life easily lasts all day, and the customer service at the store was outstanding. They helped me choose the perfect model for my needs. Highly recommend to anyone looking for a reliable laptop!', 5, 1),
(19, 'Lim Ming Long', 'Great experience overall! Found the perfect laptop for work and gaming. The staff was knowledgeable and took the time to explain different options. Prices were fair and they even helped me set everything up before I left. Would definitely shop here again!', 5, 26),
(20, 'Law Seong Chun ', 'Highly satisfied with my purchase! The laptop runs smoothly even with heavy programs, and the screen quality is amazing. The team made the buying process simple and stress-free. I’ll definitely recommend this store to friends and family!', 5, 1),
(22, 'Law Seong Chun ', 'Powered by AMD Ryzen 7000-series CPUs (up to Ryzen 9 7940HS) and NVIDIA GeForce RTX 40-series GPUs (up to RTX 4070), the A15 delivers robust performance for gaming and content creation. The inclusion of a MUX Switch with Advanced Optimus technology allows seamless switching between integrated and discrete graphics, optimizing performance and battery life ', 5, 1),
(23, 'Aiman Hakim', 'The Asus Vivobook 16X (model M1603) is a budget-friendly 16-inch laptop that offers solid performance for everyday tasks, making it a suitable choice for students and professionals seeking a larger display without a hefty price tag.', 5, 30);

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
  `phone_number` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(1000) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `block_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `username`, `email`, `status`, `phone_number`, `address`, `password`, `image`, `login_attempts`, `block_until`) VALUES
(1, 'Daniel Law Seong Chun', 'law', 'seongchunlaw050@gmail.com', '', '011-3390-3509', '304 Block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$4KKpaxNDVE5UnOMWx8K5/.UD.viaNipoNCIF0qyRlkLh5w.q3cAtK', 'fau001.png', 0, NULL),
(2, 'Law', 'Kwong Han', 'seongseonghanhan@gmail.com', '', '0163435958', '304 Block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$fxEQW.XKSnhH/PUzyB5yRO3sN49o1qSH0isZhuVT1fcT.laW/B6va', '', 0, NULL),
(3, 'Yeo Ar Kung', 'ArKung', 'yeoyeokangkang@gmail.com', '', '0166058019', '304 Block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$plqp740pg1hzowUvqjtAhOaZmDXvy2FzhRi9vxyJ8jJVgoXy/sMs2', '', 0, NULL),
(4, 'Soo Winson', 'soowinson', 'soowinson@gmail.com', '', '016-425-9767', 'No. 15, Jalan Ampang, 50450 Kuala Lumpur, Malaysia', '$2y$10$nBAreDv/ZKE1cSvgYhxobealuKLq0e.XLmtc66uwJGGVFIeSvsu1e', 'car.jpg', 0, NULL),
(5, 'sim wei kian', 'weijian', 'simweikian729@gmail.com', 'admin', '011-6233-2394', ' 72, Jalan Tun Razak, 50400 Kuala Lumpur, Malaysia', '$2y$10$YH2GngoqPe8HQG0Nhd7PSOiLQF2ng4n30vsVweXP31l4NqNk3.5.K', '', 0, NULL),
(7, 'Hao Yi Sheng', 'YiSheng', 'HaoYiSheng@gmail.com', '', '012-395-1009', ' 8, Taman Desa, 58100 Kuala Lumpur, Malaysia', '$2y$10$XZnGu6DlR2CcKNS8kFjatuZ09lCQuNFFpJ8qpzbgqeLcDmUMH082i', '', 0, NULL),
(9, 'Teh Boon Chuan', 'Teh Boon Chuan Tutor', 'tehboonchuan@gmail.com', '', '017-2233445 ', '12, Jalan Setia 3/2, Setia Alam, 40170 Shah Alam, Selangor', '$2y$10$87qf7lRhCBxCPYbAxSwPt.9n1NxLDwg6Q9rRPz6tQo6PQN2HlYg5q', '', 0, NULL),
(10, 'wong zi hao', 'wong', 'wong@gmail.com', '', '011-3617-5361', ' 66, Jalan Dato\' Onn, 80000 Johor Bahru, Johor', '$2y$10$7azy.IA2Wka8uRvmBRS7q.0FcueAUeEVl6aI.xXr9IJwmYjNt9yzO', '', 0, NULL),
(12, 'teyhonghua', 'honghua', 'teyhonghua0417@gmail.com', '', '019-3344556 ', '34, Jalan Tanjung Bungah, 11200 Tanjung Bungah, Penang', '$2y$10$Apk/laqocwAtbeu/AYsVDuUh/Y.InAJsnuMMKM3MEKKQ.VrGNDocq', '', 0, NULL),
(13, 'Yong Song Sun', 'SongSun', 'songson@gmail.com', '', '012-611-1682', '18, Jalan Bukit Indah, 81200 Johor Bahru, Johor\r\n', '$2y$10$JVgu3Wr6aAmC34GD6CXjmuLPFt8TtyuBXzgp0xDlDPJFo0WZ/cUQO', '', 0, NULL),
(14, 'admin', 'admin', 'admin@gmail.com', 'admin', '018-111-2223', '20, Jalan Gasing, 46000 Petaling Jaya, Selangor', '$2y$10$pTfCZ190pDamivpuDuPyjuzxJUE3DUokQTS4LJJIeKrgsMr4Ui3QW', '', 0, NULL),
(15, 'Woon Wong Xiao Ken', 'Ken', 'wwxiaoken@gmail.com', '', '013-507-6698', '20, Jalan Gasing, 46000 Petaling Jaya, Selangor', '$2y$10$HmmIP.v3A.a17321CzdfkuZ5RX07FQrck9pJ/VwHnN0Ne0EyFGBiC', '', 0, NULL),
(16, 'Lee Jia Hong', 'fafa', 'leejiahong333@gmail.com', '', '011-2786-0803', '55, Jalan Telawi, Bangsar, 59100 Kuala Lumpur', '$2y$10$psusKd78p9ObxaB9emR2/ev7jBKz3f2EhwPmKYO2QcJCg4vwyKcLC', '', 0, NULL),
(19, 'Lee Chun Kit', 'chunkit', 'leechunkit@gmail.com', '', '016-3332221 ', 'Jalan USJ 10/1A, 47620 Subang Jaya, Selangor', '$2y$10$iR1xS0cfbWguHgAF7KivX.xZMvpiAndHTB3BXvPZTO772TSAKxWQa', '', 0, NULL),
(20, 'Nick Wong Kai Xuan', 'KaiXuan', 'nickwong@gmail.com', '', '018-5566778', '19, Jalan Ecohill 1, 43500 Semenyih, Selangor', '$2y$10$4zqDxG/R4f2s5AUXqqAOOO4mck0TqTBwgVkMdWwrWasTz/U.O5HEa', '', 0, NULL),
(24, 'Wong Jun Keat', 'JunKeat', 'wongjunkeat@gmail.com', '', '016-391-9238', '19, Jalan Ecohill 1, 43500 Semenyih, Selangor', '$2y$10$W35/GxIJBxMSqeMIPWizjeuAJxq/glqWep7LhJJHn9.eZ1gWnmWFC', '', 0, NULL),
(25, 'xx xxx  xxxxx', 'xxxxxxx', 'xxxxxxx@gmail.com', '', '', '', '$2y$10$5dgs3hQVAntaBrvwW3YVju4ung7rzy4bOar4u8BGC.6ra2K8NBYOS', '', 0, NULL),
(26, 'Lim Ming Long', 'minglong', 'Javierlim520@gmail.com', '', '012-835-2620', '304 Block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$sR1XU.TKXElGFZ2yrb80n.TEZPnWK/GPRvRlC4F6fC7kztk49/LeK', 'ming.jpg', 0, NULL),
(27, 'Low Kai Wen', 'KaiWen', 'kaiwen050@gmail.com', '', '011-909-0909', '304 Block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$q.PKZa3FhTz0aKN5JhFJIOZVk06Y/0w2gMvdSJds9Qwyfe1sI1qGa', '', 0, NULL),
(28, 'Eason Law Shen kun', 'ShenKun', 'shenkun060@gmail.com', '', '01136903509', '304 block C5 Wangsa Maju Seksyen 2 53300 Kuala Lumpur', '$2y$10$N36GRRyTk4FGGexh1HofQuU6JgXbb4QDk2fHCq80BuUS1/S7YcU0y', '', 0, NULL),
(29, 'Ahmad bin Ismail', 'ahmadismail', 'ahmad.ismail88@gmail.com', '', '012-3456780', '12, Jalan Melati 3, Taman Seri Indah, 50450 Kuala Lumpur, Malaysia', '$2y$10$AJ1mf2PwRICyKCR6iY.3KeMWZhn8ulMhW1bamiuXq0XhUO0m/uWEu', 'chefwan.jpg', 0, NULL),
(30, 'Aiman Hakim', 'aimanhk', 'aiman.hakim89@gmail.com', '', '012-3456789', 'No. 25, Jalan Bukit Indah, 68000 Ampang, Selangor', '$2y$10$usGuy53aTyi8Z/1HCJl5sukvDuImAg3WY3I0.C4kIoua7dAa7eODu', 'aiman.jpg', 0, NULL),
(31, 'Nurul Izzati Binti Ahmad', 'nurulizzati', 'nurul.izzati98@gmail.com', '', '011-3390-9898', 'No. 7, Jalan Merdeka, Taman Sentosa, 80100 Johor Bahru, Johor', '$2y$10$ZB4MdC6YcNkxZ0yEp9IGkeAkO3uZeJmpyHwodbPnKVMn2ZpJidf26', 'izzati.jpg', 0, NULL),
(32, 'Muhammad Danish Bin Farhan', 'danishfh', 'danish.farhan99@gmail.com', '', '017-5842-1130', 'No. 12A, Lorong Kenari 5, Taman Wira, 09000 Kulim, Kedah', '$2y$10$WmWvDy49o8x7pNiSi/9Tm.yPKoSb8.ivGoWyNz3p61cUwMPW4LAS6', 'danishBoy.png', 0, NULL),
(33, 'John Doe', 'johndoe', 'johndoe@gmail.com', '', '012-3090-3009', '123 Main Street, Kuala Lumpur, Malaysia', '$2y$10$BpEpzIjAz1hIwldeHSXvBOKUBSKgjDeJwLfrat.nFR0TXjTRrkyJa', '', 0, NULL);

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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `unique_name_image` (`Product_name`,`image`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
