-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Dec 28, 2025 at 04:24 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `organic_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_cart_user` (`user_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Rau củ quả', 'Rau củ quả tươi hữu cơ từ nông trại', '2025-11-16 16:40:37'),
(2, 'Trái cây', 'Trái cây sạch, không thuốc trừ sâu', '2025-11-16 16:40:37'),
(3, 'Thực phẩm khô', 'Ngũ cốc, hạt dinh dưỡng', '2025-11-16 16:40:37'),
(4, 'Sản phẩm sữa', 'Sữa và các sản phẩm từ sữa', '2025-11-16 16:40:37'),
(5, 'Quà Tặng Thực Phẩm', 'Quà Tặng Thực Phẩm Hữu Cơ', '2025-12-05 17:26:36'),
(7, 'Kẹo dẻo hữu cơ', 'Kẹo hữu cơ là loại kẹo được làm từ nguyên liệu tự nhiên, không hóa chất độc hại, không phẩm màu nhân tạo, không hương liệu tổng hợp.', '2025-12-07 09:19:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`)
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `total_amount`, `status`, `note`, `created_at`) VALUES
(1, NULL, 'Nguyễn Trúc Linh', '0336880135', '228-230 Cao Lỗ ', 20000.00, 'pending', 'Giao giờ hành chính', '2025-12-04 22:48:56'),
(2, 2, 'Nguyễn Trúc Linh', '0336880135', '228-230 Cao Lỗ', 35000.00, 'pending', '', '2025-12-04 22:57:03'),
(3, 2, 'Nguyễn Trúc Linh', '0336880135', '228-230 Cao Lỗ', 55000.00, 'pending', '', '2025-12-04 23:07:33'),
(4, 3, 'Nguyễn Minh Hoàng', '037508379433', '228-230 Cao Lỗ', 20000.00, 'pending', '', '2025-12-05 15:18:39'),
(5, 3, 'Nguyễn Minh Hoàng', '037508379433', '228-230 Cao Lỗ', 50000.00, 'pending', '', '2025-12-05 17:32:00'),
(6, 3, 'Nguyễn Minh Hoàng', '037508379433', '228-230 Cao Lỗ', 40000.00, 'pending', '', '2025-12-05 17:35:56'),
(7, 3, 'Nguyễn Minh Hoàng', '037508379433', '228-230 Cao Lỗ', 159000.00, 'pending', 'gọi trước khi giao', '2025-12-23 18:17:17');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `product_name` varchar(200) NOT NULL COMMENT 'Snapshot tên sản phẩm tại thời điểm đặt',
  `price` decimal(10,2) NOT NULL COMMENT 'Snapshot giá gốc tại thời điểm đặt',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT 'Snapshot giá sale tại thời điểm đặt',
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `price`, `sale_price`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 'Rau cải xanh hữu cơ', 20000.00, NULL, 1, 20000.00),
(2, 2, 2, 'Cà chua bi', 35000.00, NULL, 1, 35000.00),
(3, 3, 3, 'Táo Fuji', 55000.00, NULL, 1, 55000.00),
(4, 4, 1, 'Rau cải xanh hữu cơ', 20000.00, NULL, 1, 20000.00),
(5, 5, 6, 'Sữa tươi hữu cơ', 50000.00, NULL, 1, 50000.00),
(6, 6, 4, 'Cam sành', 40000.00, NULL, 1, 40000.00),
(7, 7, 11, 'Cherry đỏ Úc size 28 - 30 (200G/Hộp)', 159000.00, NULL, 1, 159000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `unit` varchar(50) DEFAULT 'kg',
  `weight` decimal(8,2) DEFAULT NULL COMMENT 'Trọng lượng tính theo gram',
  `is_organic` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1: Đang bán, 0: Ngưng bán',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_active` (`is_active`)
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `category_id`, `price`, `sale_price`, `description`, `image`, `stock`, `unit`, `weight`, `is_organic`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'VEG-001', 'Rau cải xanh hữu cơ', 1, 25000.00, 20000.00, 'Rau cải xanh tươi, không hóa chất', 'cai-xanh.jpg', 50, 'bó', 300.00, 1, 1, '2025-11-16 16:40:37', '2025-11-16 16:40:37'),
(2, 'VEG-002', 'Cà chua bi', 1, 35000.00, NULL, 'Cà chua bi ngọt tự nhiên', 'ca-chua.jpg', 30, 'kg', 1000.00, 1, 1, '2025-11-16 16:40:37', '2025-11-16 16:40:37'),
(3, 'FRU-001', 'Táo Fuji', 2, 65000.00, 55000.00, 'Táo Fuji nhập khẩu', 'tao.jpg', 40, 'kg', 1000.00, 1, 1, '2025-11-16 16:40:37', '2025-11-16 16:40:37'),
(4, 'FRU-002', 'Cam sành', 2, 40000.00, NULL, 'Cam sành Cao Phong', 'cam.jpg', 59, 'kg', 1000.00, 1, 1, '2025-11-16 16:40:37', '2025-12-05 17:35:56'),
(6, 'DAI-001', 'Sữa tươi hữu cơ', 4, 70000.00, 50000.00, 'Sữa tươi không đường', 'sua-tuoi.jpg', 25, 'hộp', 1000.00, 1, 1, '2025-11-16 16:40:37', '2025-12-05 17:31:35'),
(7, NULL, 'Cá hồi xông khói vị hương vị tiêu Caspiar 200g', 5, 279000.00, 250000.00, 'Cá Hồi Xông Khói Hương Vị Tiêu Caspiar 200g – Đậm Đà, Thơm Nồng, Chuẩn Vị Châu Âu\r\nMô tả sản phẩm:\r\nCá Hồi Xông Khói Hương Vị Tiêu Caspiar 200g được chế biến từ cá hồi Nauy tươi chọn lọc, kết hợp cùng tiêu đen xay mịn và quy trình xông khói lạnh truyền thống châu Âu. Từng lát cá hồi mềm mịn, thấm vị tiêu cay nhẹ, tạo nên hương vị đậm đà, tinh tế và hấp dẫn khó cưỡng.\r\n\r\nSản phẩm không chỉ giàu Omega-3, protein, vitamin D mà còn là lựa chọn lý tưởng cho salad, sandwich, pizza, sushi hoặc món khai vị cao cấp.\r\n\r\nĐặc điểm nổi bật:\r\n\r\n???? Cá hồi Nauy tươi, xông khói lạnh giữ nguyên độ mềm và màu tự nhiên.\r\n\r\n???? Tẩm ướp tiêu đen tạo hương vị cay nhẹ, kích thích vị giác.\r\n\r\n???? Dùng trực tiếp hoặc kết hợp đa dạng trong món Âu – Á.\r\n\r\n???? Giàu dinh dưỡng, tốt cho tim mạch, não bộ và làn da.\r\n\r\n???????? Thương hiệu Caspiar – biểu tượng của hải sản cao cấp chuẩn châu Âu.\r\n\r\nCách dùng & bảo quản:\r\n\r\nĂn trực tiếp sau khi mở bao bì, ngon hơn khi dùng lạnh.\r\n\r\nBảo quản lạnh ở 0–4°C, tránh ánh sáng trực tiếp.\r\n\r\n- Mua cá hồi xông khói ở đâu?\r\n\r\nNếu bạn chưa biết mua cá hồi xông khói chất lượng ở đâu tại TP.HCM. Bạn có thể tham khảo và đến với hệ thống của hàng HoliColi để lựa chọn cho mình sản phẩm đạt chuẩn chất lượng, an tâm về nguồn gốc với giá thành tốt nhất.', '1764955741_c__h_i_x_ng_kh_i_v__h__ng_v__ti_u_caspiar_200g_9f55857636a248e7a894f07cbb48c561_master.jpg', 50, 'kg', NULL, 1, 1, '2025-12-05 17:29:01', '2025-12-05 17:29:01'),
(10, NULL, 'Kẹo Dẻo Trái Cây Hữu Cơ Black Forest Organic Gummy Bears Pouches, Hộp 65 Túi, 1.47 Kg (52 Oz.)', 7, 835000.00, 800000.00, 'chính hãng có chất liệu kẹo mềm, nhẹ, không dai, hình dạng là các chú gấu rất đáng yêu; được làm từ trái cây tự nhiên KHÔNG có phân bón, KHÔNG chất trừ sâu.\r\nKhông hương liệu nhân tạo\r\nKhông Biến Đổi Gen (non GMO Project Verified)\r\nKhông Gluten (Gluten Free)\r\nKosher Pareve (không chứa thịt, bơ, sữa)\r\nKhông đậu hạt\r\nChứng nhận hữu cơ USDA.\r\n23g/ 1 gói kẹo.\r\nSản xuất tại Mỹ.', '1765100705_Screenshot 2025-12-07 163821.png', 50, 'hộp', NULL, 1, 1, '2025-12-07 09:45:05', '2025-12-07 09:59:09'),
(9, NULL, 'Kẹo Dẻo Hữu Cơ Hình Gấu Black Forest Organic Gummy Bears', 7, 12000.00, NULL, 'chính hãng có chất liệu kẹo mềm, nhẹ, không dai, hình dạng là các chú gấu rất đáng yêu; được làm từ trái cây tự nhiên KHÔNG có phân bón, KHÔNG chất trừ sâu.\r\nKhông hương liệu nhân tạo\r\nKhông Biến Đổi Gen (non GMO Project Verified)\r\nKhông Gluten (Gluten Free)\r\nKosher Pareve (không chứa thịt, bơ, sữa)\r\nKhông đậu hạt\r\nChứng nhận hữu cơ USDA.\r\n23g/ 1 gói kẹo.\r\nSản xuất tại Mỹ.', '1765100489_keo-deo-huu-co-black-forest-organic-gummy-bears-65-goi-3-1660878575012.webp', 100, 'kg', NULL, 1, 1, '2025-12-07 09:41:29', '2025-12-07 09:41:29'),
(11, NULL, 'Cherry đỏ Úc size 28 - 30 (200G/Hộp)', 2, 239000.00, 159000.00, 'Cherry đỏ Úc size 28 - 30 \r\n- Xuất xứ: Úc\r\n\r\n- Chất lượng: Nhập khẩu\r\n\r\n- Size 28 - 30\r\n\r\n- Đặc điểm nổi bật: \r\n\r\nĐạt tiêu chuẩn chất lượng nhập khẩu. \r\nCherry Úc đỏ thẫm, trái cứng, mọng nước, ngọt đậm.\r\n- Thông tin dinh dưỡng:\r\n\r\nQuả cherry rất tốt cho những người bị cao huyết áp, giúp bạn loại bỏ được mức độ cholesterol xấu trong cơ thể.\r\nĂn cherry thường xuyên giúp cải thiện tình trạng mất ngủ của bạn.\r\nCherry là thực phẩm tốt cho não bộ, tăng trí nhớ bởi chúng chứa các chất chống ôxy hóa anthocyanin.\r\nTrong loại trái cây này có chứa rất nhiều vitamin, đặc biệt là vitamin A. Các vitamin này không chỉ giúp tăng sức đề kháng mà còn rất tốt cho mắt.\r\nVị ngọt của cherry hoàn toàn tự nhiên, chỉ số đường tương đối thấp, đem đến công dụng tuyệt vời cho việc điều trị tiểu đường.\r\n- Hướng dẫn sử dụng:\r\n\r\nRửa nhẹ nhàng trái \r\n\r\nChỉ nên rửa một lượng vừa đủ ăn.\r\n\r\nCherry có thể ăn trực tiếp, làm nước ép, sinh tố, làm bánh\r\n\r\n- Cách lựa chọn:\r\n\r\nKhách hàng nên chọn những quả căng bóng, có màu đỏ đều, không bị dập nát, thối rữa.\r\n\r\nVới Farmers Market tiêu chí hàng đầu là sản phảm đạt chất lượng cao, nguồn gốc rõ ràng, mang đến trải nghiệm tốt nhất cho khách hàng. \r\n\r\n- Cách bảo quản: \r\n\r\n+ Bảo quản trong tủ lạnh:\r\n\r\nSau khi mua về, nhẹ nhàng nhặt bỏ những quả bị dập nát, thối rữa.\r\nCho cherry vào hộp kín hoặc túi nilon có đục lỗ.\r\nBảo quản cherry trong ngăn mát tủ lạnh.\r\nKiểm tra thường xuyên và loại bỏ những trái hư hỏng\r\nKhông nên rửa cherry trước khi bảo quản trong tủ lạnh vì sẽ khiến quả dễ bị thối rữa.\r\nNếu muốn bảo quản cherry lâu hơn, có thể đông lạnh cherry.\r\n+ Bảo quản ở nhiệt độ phòng:\r\n\r\nNếu bạn muốn ăn cherry ngay trong ngày, có thể bảo quản cherry ở nhiệt độ phòng.\r\nTuy nhiên, cherry sẽ chỉ tươi ngon trong vòng 1-2 ngày.\r\nNên đặt cherry ở nơi thoáng mát, tránh ánh nắng trực tiếp.\r\n- Những lưu ý khi thưởng thức cherry:\r\n\r\nNên rửa sạch cherry trước khi ăn để đảm bảo vệ sinh.\r\nNếu bạn có dị ứng với cherry hoặc bất kỳ loại trái cây nào khác, hãy tránh ăn cherry.\r\nTránh cho trẻ ăn cherry trước khi đi ngủ vì có thể gây khó tiêu.\r\n- Phụ nữ mang thai và trẻ em ăn cherry có được không?\r\n\r\nPhụ nữ mang thai nên ăn nho với lượng vừa phải, khoảng 100-150 gram mỗi ngày\r\n\r\nTrẻ em trên 1 tuổi có thể ăn cherry, cắt nho thành từng miếng nhỏ để trẻ dễ ăn và tránh nguy cơ nghẹn.\r\n\r\nCho trẻ ăn nho với lượng vừa phải, khoảng 50-100 gram mỗi ngày\r\n\r\n- Mua cherry Úc ở đâu?', '1765101844_cherry_d___c_size_28_-_30_3b6ecdff6142482da046df836a80b6b3_master.png', 9, 'hộp', NULL, 1, 1, '2025-12-07 10:04:04', '2025-12-23 18:17:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `fullname`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'adminholicoli', 'adminholicoli@gmail.com', '$2y$10$aNPMfssZ/zOX6R1sLPNFdOvxkqiznVFmA4iQUcnOb/Kp.6dEIJzm.', 'ADMIN', '0336880135', '228-230 Cao Lỗ', 'admin', '2025-12-05 17:06:22'),
(2, 'Linmct39', 'truclinh5534@gmail.com', '$2y$10$tPazhGUs/OC7WYQ/O3e2zefhoIfHHZc95AORJkzocUYQ1x5Bm5HCe', 'Nguyễn Trúc Linh', '0336880135', '228-230 Cao Lỗ', 'customer', '2025-12-04 22:56:46'),
(3, 'MinhHoang294', 'nguyenhoang29.4@gmail.com', '$2y$10$9N6HL1x4yZjqSWs5Vtwmf.TdUu6WcouBCZfCsq0NhVxBG/9ISsewq', 'Nguyễn Minh Hoàng', '037508379433', '228-230 Cao Lỗ', 'customer', '2025-12-05 15:16:55');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
