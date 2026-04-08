CREATE TABLE `Categories` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `category_name` text
);

CREATE TABLE `Products` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `product_name` varchar(255),
  `price` decimal,
  `short_description` text,
  `description` text,
  `stock` int,
  `category_id` int
);

CREATE TABLE `Product_images` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `product_id` int,
  `filename` varchar(255)
);

CREATE TABLE `Users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(255) UNIQUE,
  `phone` text,
  `first_name` varchar(255),
  `last_name` varchar(255),
  `address` text,
  `role` varchar(255)
);

CREATE TABLE `Orders` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(255),
  `user_id` int,
  `address` text,
  `phone` varchar(255),
  `quantity` int,
  `note` text,
  `subtotal` decimal,
  `shipping_fee` decimal,
  `VAT` decimal,
  `total` decimal,
  `payment` varchar(255),
  `status` varchar(255),
  `created_at` timestamp,
  `updated_at` timestamp
);

CREATE TABLE `Order_details` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  `product_id` int,
  `quantity` int,
  `price` decimal
);

ALTER TABLE `Products` ADD FOREIGN KEY (`category_id`) REFERENCES `Categories` (`id`);

ALTER TABLE `Product_images` ADD FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`);

ALTER TABLE `Orders` ADD FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`);

ALTER TABLE `Order_details` ADD FOREIGN KEY (`order_id`) REFERENCES `Orders` (`id`);

ALTER TABLE `Order_details` ADD FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`);
