-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 19, 2026 at 10:09 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u615191172_india_day_trip`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `category_id` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `view_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots_meta` varchar(100) DEFAULT 'INDEX,FOLLOW',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(500) DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `schemas` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `slug`, `content`, `excerpt`, `author`, `publication_date`, `tags`, `category_id`, `featured_image`, `status`, `view_count`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `robots_meta`, `og_title`, `og_description`, `og_image`, `twitter_title`, `twitter_description`, `twitter_image`, `schema_markup`, `schemas`) VALUES
(3, 'Welcome to India Day Trip', 'welcome-to-india-day-trip', '<p>This is our first test blog post.</p>\r\n', 'Welcome to our new blog.', 'Admin', '2026-03-31', '[\"\"]', 4, 'Gemini_Generated_Image_c3h6vac3h6vac3h6.png', 'published', 21, '2026-03-31 07:36:53', '2026-04-15 03:29:20', 'shivam thakur title', '', 'India Day Trip, Agra tours, Taj Mahal tours, Golden Triangle tours, Same Day tours', '', 'INDEX,FOLLOW', '', '', '', '', '', '', '[{\"id\":\"0\",\"type\":\"custom\",\"data\":{\"@context\":\"https://schema.org\",\"@type\":\"Article\",\"headline\":\"Article Title\",\"description\":\"Article description\",\"author\":{\"@type\":\"Person\",\"name\":\"Author Name\"},\"datePublished\":\"2026-03-31\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"India Day Trip\"}}},{\"id\":\"1\",\"type\":\"custom\",\"data\":{\"@context\":\"https://schema.org\",\"@type\":\"Article\",\"headline\":\"Article Title\",\"description\":\"Article description\",\"author\":{\"@type\":\"Person\",\"name\":\"Author Name\"},\"datePublished\":\"2026-03-31\",\"publisher\":{\"@type\":\"Organization\",\"name\":\"India Day Trip\"}}}]', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `author_name` varchar(100) NOT NULL,
  `author_email` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('tour','blog') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `type`, `created_at`) VALUES
(1, 'Same Day Tours', 'Tours completed in one day', 'tour', '2025-12-28 08:46:45'),
(2, 'Taj Mahal Tours', 'Tours focusing on Taj Mahal', 'tour', '2025-12-28 08:46:45'),
(3, 'Golden Triangle Tours', 'Delhi, Agra, Jaipur tours', 'tour', '2025-12-28 08:46:45'),
(4, 'Travel Tips', 'Tips and guides for travelers', 'blog', '2025-12-28 08:46:45'),
(5, 'Destinations', 'Information about destinations', 'blog', '2025-12-28 08:46:45'),
(6, 'Short Tours', 'Short duration tours', 'tour', '2026-01-04 17:52:26'),
(7, 'Day Tours', 'Day tours', 'tour', '2026-01-04 17:52:26'),
(8, 'Extended Golden Triangle Tours', 'Extended Golden Triangle tours', 'tour', '2026-01-04 17:52:26'),
(9, 'Train Tours', 'Train based tours', 'tour', '2026-01-04 17:52:26'),
(10, 'Luxury Tours', 'Luxury tours', 'tour', '2026-01-04 17:52:26'),
(11, 'Wildlife Tours', 'Wildlife tours', 'tour', '2026-01-04 17:52:26'),
(12, 'Extended Tours', 'Extended tours', 'tour', '2026-01-04 17:52:26'),
(13, 'Day Trips', 'Day trips', 'tour', '2026-01-04 17:52:26'),
(14, 'Rajasthan Tours', 'Rajasthan tours', 'tour', '2026-01-04 17:52:26'),
(15, 'Food Tours', 'Food tasting tours', 'tour', '2026-01-04 17:52:26'),
(16, 'City Tours', 'City tours', 'tour', '2026-01-04 17:52:26'),
(17, 'Short Tours', 'Short duration tours', 'tour', '2026-01-04 18:03:38'),
(18, 'Day Tours', 'Day tours', 'tour', '2026-01-04 18:03:38'),
(19, 'Extended Golden Triangle Tours', 'Extended Golden Triangle tours', 'tour', '2026-01-04 18:03:38'),
(20, 'Train Tours', 'Train based tours', 'tour', '2026-01-04 18:03:38'),
(21, 'Luxury Tours', 'Luxury tours', 'tour', '2026-01-04 18:03:38'),
(22, 'Wildlife Tours', 'Wildlife tours', 'tour', '2026-01-04 18:03:38'),
(23, 'Extended Tours', 'Extended tours', 'tour', '2026-01-04 18:03:38'),
(24, 'Day Trips', 'Day trips', 'tour', '2026-01-04 18:03:38'),
(25, 'Rajasthan Tours', 'Rajasthan tours', 'tour', '2026-01-04 18:03:38'),
(26, 'Food Tours', 'Food tasting tours', 'tour', '2026-01-04 18:03:38'),
(27, 'City Tours', 'City tours', 'tour', '2026-01-04 18:03:38'),
(28, 'Short Tours', 'Short duration tours', 'tour', '2026-01-04 18:04:55'),
(29, 'Day Tours', 'Day tours', 'tour', '2026-01-04 18:04:55'),
(30, 'Extended Golden Triangle Tours', 'Extended Golden Triangle tours', 'tour', '2026-01-04 18:04:55'),
(31, 'Train Tours', 'Train based tours', 'tour', '2026-01-04 18:04:55'),
(32, 'Luxury Tours', 'Luxury tours', 'tour', '2026-01-04 18:04:55'),
(33, 'Wildlife Tours', 'Wildlife tours', 'tour', '2026-01-04 18:04:55'),
(34, 'Extended Tours', 'Extended tours', 'tour', '2026-01-04 18:04:55'),
(35, 'Day Trips', 'Day trips', 'tour', '2026-01-04 18:04:55'),
(36, 'Rajasthan Tours', 'Rajasthan tours', 'tour', '2026-01-04 18:04:55'),
(37, 'Food Tours', 'Food tasting tours', 'tour', '2026-01-04 18:04:55'),
(38, 'City Tours', 'City tours', 'tour', '2026-01-04 18:04:55'),
(39, 'Short Tours', 'Short duration tours', 'tour', '2026-01-04 18:05:09'),
(40, 'Day Tours', 'Day tours', 'tour', '2026-01-04 18:05:09'),
(41, 'Extended Golden Triangle Tours', 'Extended Golden Triangle tours', 'tour', '2026-01-04 18:05:09'),
(42, 'Train Tours', 'Train based tours', 'tour', '2026-01-04 18:05:09'),
(43, 'Luxury Tours', 'Luxury tours', 'tour', '2026-01-04 18:05:09'),
(44, 'Wildlife Tours', 'Wildlife tours', 'tour', '2026-01-04 18:05:09'),
(45, 'Extended Tours', 'Extended tours', 'tour', '2026-01-04 18:05:09'),
(46, 'Day Trips', 'Day trips', 'tour', '2026-01-04 18:05:09'),
(47, 'Rajasthan Tours', 'Rajasthan tours', 'tour', '2026-01-04 18:05:09'),
(48, 'Food Tours', 'Food tasting tours', 'tour', '2026-01-04 18:05:09'),
(49, 'City Tours', 'City tours', 'tour', '2026-01-04 18:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `filename`, `title`, `tags`, `alt_text`, `created_at`) VALUES
(1, 'hg1.webp', 'Gallery Image 1', '[]', 'Gallery Image 1 - Updated', '2025-12-28 08:46:45'),
(2, 'hg2.webp', 'Gallery Image 2', '[]', 'Gallery Image 2', '2025-12-28 08:46:45'),
(3, 'hg3.webp', 'Gallery Image 3', '[]', 'Gallery Image 3', '2025-12-28 08:46:45'),
(5, 'hg5.webp', 'Gallery Image 5', '[]', 'Gallery Image 5', '2025-12-28 08:46:45'),
(6, 'hg6.webp', 'Gallery Image 6', '[]', 'Gallery Image 68', '2025-12-28 08:46:45'),
(7, 'hg7.webp', 'Gallery Image 7', '[]', 'Gallery Image 7', '2025-12-28 08:46:45');

-- --------------------------------------------------------

--
-- Table structure for table `page_seo`
--

CREATE TABLE `page_seo` (
  `id` int(11) NOT NULL,
  `page_key` varchar(100) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots_meta` varchar(100) DEFAULT 'INDEX,FOLLOW',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'website',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(500) DEFAULT NULL,
  `twitter_card` varchar(50) DEFAULT 'summary_large_image',
  `schema_markup` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_seo`
--

INSERT INTO `page_seo` (`id`, `page_key`, `page_title`, `meta_description`, `meta_keywords`, `canonical_url`, `robots_meta`, `og_title`, `og_description`, `og_image`, `og_type`, `twitter_title`, `twitter_description`, `twitter_image`, `twitter_card`, `schema_markup`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Home - India Day Trip Testing', 'India Day Trip - Agra based tour and travel company offering Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'India Day Trip, Agra tours, Taj Mahal tours, Golden Triangle tours, Same Day tours', 'https://indiadaytrip.com/', 'INDEX,FOLLOW', 'India Day Trip - Agra Based Tour & Travel Company', 'India Day Trip - Agra based tour and travel company offering Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'https://indiadaytrip.com/assets/img/hero/hero-agra.webp', 'website', 'India Day Trip - Agra Based Tour & Travel Company', 'India Day Trip - Agra based tour and travel company offering Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'https://indiadaytrip.com/assets/img/hero/hero-agra.webp', 'summary_large_image', '{\r\n        \"@context\": \"https://schema.org\",\r\n        \"@type\": \"TravelAgency\",\r\n        \"name\": \"India Day Trip\",\r\n        \"image\": \"https://indiadaytrip.com/assets/img/logo/logo-header.webp\",\r\n        \"description\": \"Agra based tour and travel company offering Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours\",\r\n        \"address\": {\r\n            \"@type\": \"PostalAddress\",\r\n            \"streetAddress\": \"Shop No. 2, Gupta Market, Tajganj\",\r\n            \"addressLocality\": \"Agra\",\r\n            \"addressRegion\": \"Uttar Pradesh\",\r\n            \"postalCode\": \"282001\",\r\n            \"addressCountry\": \"IN\"\r\n        },\r\n        \"telephone\": \"+919897030802\",\r\n        \"email\": \"info@indiadaytrip.com\",\r\n        \"url\": \"https://indiadaytrip.com\",\r\n        \"priceRange\": \"$\",\r\n        \"openingHours\": \"Mo-Su 08:00-20:00\",\r\n        \"geo\": {\r\n            \"@type\": \"GeoCoordinates\",\r\n            \"latitude\": \"27.1767\",\r\n            \"longitude\": \"78.0081\"\r\n        },\r\n        \"sameAs\": [\r\n            \"https://www.facebook.com\",\r\n            \"https://www.twitter.com\",\r\n            \"https://www.instagram.com\",\r\n            \"https://www.linkedin.com\"\r\n        ]\r\n    }', '2026-03-30 09:14:13', '2026-03-31 07:51:47'),
(2, 'about', 'About Us - India Day Trip | Agra Tour & Travel Company', 'Learn about India Day Trip, your trusted Agra-based tour and travel company specializing in Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'about India Day Trip, Agra tour company, travel agency Agra, Same Day Tours, Taj Mahal Tours, Golden Triangle Tours', 'https://indiadaytrip.com/about/', 'INDEX,FOLLOW', 'About Us - India Day Trip | Agra Tour & Travel Company', 'Learn about India Day Trip, your trusted Agra-based tour and travel company specializing in Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'https://indiadaytrip.com/assets/img/about/about-hero.webp', 'website', 'About Us - India Day Trip | Agra Tour & Travel Company', 'Learn about India Day Trip, your trusted Agra-based tour and travel company specializing in Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours', 'https://indiadaytrip.com/assets/img/about/about-hero.webp', 'summary_large_image', '{\n        \"@context\": \"https://schema.org\",\n        \"@type\": \"AboutPage\",\n        \"name\": \"About India Day Trip\",\n        \"description\": \"India Day Trip is an Agra-based tour and travel company offering exceptional travel experiences across India\'s most iconic destinations.\",\n        \"url\": \"https://indiadaytrip.com/about/\"\n    }', '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(3, 'tour_listing', 'Tours - India Day Trip | Same Day Tours, Taj Mahal Tours, Golden Triangle Tours', 'Explore our comprehensive collection of tours including Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Book your perfect India adventure today.', 'tours India, Same Day Tours Agra, Taj Mahal Tours, Golden Triangle Tours Delhi Agra Jaipur, India travel packages', 'https://indiadaytrip.com/tour/', 'INDEX,FOLLOW', 'Tours - India Day Trip | Same Day Tours, Taj Mahal Tours, Golden Triangle Tours', 'Explore our comprehensive collection of tours including Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Book your perfect India adventure today.', 'https://indiadaytrip.com/assets/img/tour/tour-hero.webp', 'website', 'Tours - India Day Trip | Same Day Tours, Taj Mahal Tours, Golden Triangle Tours', 'Explore our comprehensive collection of tours including Same Day Tours, Taj Mahal Tours, and Golden Triangle Tours. Book your perfect India adventure today.', 'https://indiadaytrip.com/assets/img/tour/tour-hero.webp', 'summary_large_image', '{\n        \"@context\": \"https://schema.org\",\n        \"@type\": \"ItemList\",\n        \"name\": \"Tour Packages\",\n        \"description\": \"Comprehensive list of tour packages offered by India Day Trip\",\n        \"url\": \"https://indiadaytrip.com/tour/\"\n    }', '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(4, 'blog_listing', 'Travel Blog - India Day Trip | Travel Tips & Destination Guides', 'Read our travel blog for tips, guides, and insights about India\'s most popular destinations including Delhi, Agra, Jaipur, and more.', 'travel blog India, India travel tips, destination guides, Delhi travel, Agra travel, Jaipur travel, India tourism', 'https://indiadaytrip.com/blog/', 'INDEX,FOLLOW', 'Travel Blog - India Day Trip | Travel Tips & Destination Guides', 'Read our travel blog for tips, guides, and insights about India\'s most popular destinations including Delhi, Agra, Jaipur, and more.', 'https://indiadaytrip.com/assets/img/blog/blog-hero.webp', 'website', 'Travel Blog - India Day Trip | Travel Tips & Destination Guides', 'Read our travel blog for tips, guides, and insights about India\'s most popular destinations including Delhi, Agra, Jaipur, and more.', 'https://indiadaytrip.com/assets/img/blog/blog-hero.webp', 'summary_large_image', '{\n        \"@context\": \"https://schema.org\",\n        \"@type\": \"Blog\",\n        \"name\": \"India Day Trip Travel Blog\",\n        \"description\": \"Travel tips, destination guides, and travel insights for India\",\n        \"url\": \"https://indiadaytrip.com/blog/\"\n    }', '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(5, 'gallery', 'Gallery - India Day Trip | Travel Photos & Destination Images', 'Explore our photo gallery showcasing the beauty of India\'s iconic destinations including Taj Mahal, Delhi, Jaipur, and more.', 'India travel gallery, Taj Mahal photos, Delhi images, Jaipur pictures, India tourism photos, travel photography', 'https://indiadaytrip.com/gallery/', 'INDEX,FOLLOW', 'Gallery - India Day Trip | Travel Photos & Destination Images', 'Explore our photo gallery showcasing the beauty of India\'s iconic destinations including Taj Mahal, Delhi, Jaipur, and more.', 'https://indiadaytrip.com/assets/img/gallery/gallery-hero.webp', 'website', 'Gallery - India Day Trip | Travel Photos & Destination Images', 'Explore our photo gallery showcasing the beauty of India\'s iconic destinations including Taj Mahal, Delhi, Jaipur, and more.', 'https://indiadaytrip.com/assets/img/gallery/gallery-hero.webp', 'summary_large_image', '{\n        \"@context\": \"https://schema.org\",\n        \"@type\": \"ImageGallery\",\n        \"name\": \"India Day Trip Photo Gallery\",\n        \"description\": \"Photo gallery showcasing India\'s most beautiful destinations\",\n        \"url\": \"https://indiadaytrip.com/gallery/\"\n    }', '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(6, 'contact', 'Contact Us - India Day Trip | Get in Touch for Tour Bookings', 'Contact India Day Trip for tour bookings, inquiries, and travel information. We\'re here to help plan your perfect India adventure.', 'contact India Day Trip, tour booking, travel inquiry, Agra contact, India travel contact, tour reservations', 'https://indiadaytrip.com/contact/', 'INDEX,FOLLOW', 'Contact Us - India Day Trip | Get in Touch for Tour Bookings', 'Contact India Day Trip for tour bookings, inquiries, and travel information. We\'re here to help plan your perfect India adventure.', 'https://indiadaytrip.com/assets/img/contact/contact-hero.webp', 'website', 'Contact Us - India Day Trip | Get in Touch for Tour Bookings', 'Contact India Day Trip for tour bookings, inquiries, and travel information. We\'re here to help plan your perfect India adventure.', 'https://indiadaytrip.com/assets/img/contact/contact-hero.webp', 'summary_large_image', '{\n        \"@context\": \"https://schema.org\",\n        \"@type\": \"ContactPage\",\n        \"name\": \"Contact India Day Trip\",\n        \"description\": \"Get in touch with India Day Trip for tour bookings and travel inquiries\",\n        \"url\": \"https://indiadaytrip.com/contact/\"\n    }', '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(7, 'same_day_tours', 'Same Day Tours - India Day Trip | Day Trips from Agra', 'Explore our Same Day Tours from Agra including Taj Mahal day trips, Agra Fort, Fatehpur Sikri, and more. Perfect for short visits to India.', 'Same Day Tours Agra, day trips Agra, Taj Mahal day tour, Agra Fort day trip, Fatehpur Sikri tour, short India tours', 'https://indiadaytrip.com/same-day-tours/', 'INDEX,FOLLOW', 'Same Day Tours - India Day Trip | Day Trips from Agra', 'Explore our Same Day Tours from Agra including Taj Mahal day trips, Agra Fort, Fatehpur Sikri, and more. Perfect for short visits to India.', 'https://indiadaytrip.com/assets/img/tour/same-day-hero.webp', 'website', 'Same Day Tours - India Day Trip | Day Trips from Agra', 'Explore our Same Day Tours from Agra including Taj Mahal day trips, Agra Fort, Fatehpur Sikri, and more. Perfect for short visits to India.', 'https://indiadaytrip.com/assets/img/tour/same-day-hero.webp', 'summary_large_image', NULL, '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(8, 'taj_mahal_tours', 'Taj Mahal Tours - India Day Trip | Complete Taj Mahal Experience', 'Experience the Taj Mahal with our comprehensive tours including sunrise visits, private tours, and combined packages with Agra Fort and Fatehpur Sikri.', 'Taj Mahal tours, Taj Mahal sunrise, Taj Mahal private tour, Agra Taj Mahal, Taj Mahal Agra Fort, Taj Mahal Fatehpur Sikri', 'https://indiadaytrip.com/taj-mahal-tours/', 'INDEX,FOLLOW', 'Taj Mahal Tours - India Day Trip | Complete Taj Mahal Experience', 'Experience the Taj Mahal with our comprehensive tours including sunrise visits, private tours, and combined packages with Agra Fort and Fatehpur Sikri.', 'https://indiadaytrip.com/assets/img/tour/taj-mahal-hero.webp', 'website', 'Taj Mahal Tours - India Day Trip | Complete Taj Mahal Experience', 'Experience the Taj Mahal with our comprehensive tours including sunrise visits, private tours, and combined packages with Agra Fort and Fatehpur Sikri.', 'https://indiadaytrip.com/assets/img/tour/taj-mahal-hero.webp', 'summary_large_image', NULL, '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(9, 'golden_triangle_tours', 'Golden Triangle Tours - India Day Trip | Delhi Agra Jaipur Tours', 'Discover India\'s Golden Triangle with our comprehensive tours covering Delhi, Agra, and Jaipur. Experience the best of North India in one amazing journey.', 'Golden Triangle tours, Delhi Agra Jaipur, India Golden Triangle, North India tours, Delhi tours, Agra tours, Jaipur tours', 'https://indiadaytrip.com/golden-triangle-tours/', 'INDEX,FOLLOW', 'Golden Triangle Tours - India Day Trip | Delhi Agra Jaipur Tours', 'Discover India\'s Golden Triangle with our comprehensive tours covering Delhi, Agra, and Jaipur. Experience the best of North India in one amazing journey.', 'https://indiadaytrip.com/assets/img/tour/golden-triangle-hero.webp', 'website', 'Golden Triangle Tours - India Day Trip | Delhi Agra Jaipur Tours', 'Discover India\'s Golden Triangle with our comprehensive tours covering Delhi, Agra, and Jaipur. Experience the best of North India in one amazing journey.', 'https://indiadaytrip.com/assets/img/tour/golden-triangle-hero.webp', 'summary_large_image', NULL, '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(10, 'privacy_policy', 'Privacy Policy - India Day Trip', 'Read our privacy policy to understand how we collect, use, and protect your personal information when you use our services.', 'privacy policy, data protection, personal information, privacy terms, India Day Trip privacy', 'https://indiadaytrip.com/privacy-policy/', 'INDEX,FOLLOW', 'Privacy Policy - India Day Trip', 'Read our privacy policy to understand how we collect, use, and protect your personal information when you use our services.', NULL, 'website', 'Privacy Policy - India Day Trip', 'Read our privacy policy to understand how we collect, use, and protect your personal information when you use our services.', NULL, 'summary_large_image', NULL, '2026-03-30 09:14:13', '2026-03-30 09:14:13'),
(11, 'terms_conditions', 'Terms & Conditions - India Day Trip', 'Read our terms and conditions for booking tours, travel services, and using our website. Understand your rights and responsibilities.', 'terms and conditions, booking terms, travel terms, service conditions, India Day Trip terms', 'https://indiadaytrip.com/terms-conditions/', 'INDEX,FOLLOW', 'Terms & Conditions - India Day Trip', 'Read our terms and conditions for booking tours, travel services, and using our website. Understand your rights and responsibilities.', NULL, 'website', 'Terms & Conditions - India Day Trip', 'Read our terms and conditions for booking tours, travel services, and using our website. Understand your rights and responsibilities.', NULL, 'summary_large_image', NULL, '2026-03-30 09:14:13', '2026-03-30 09:14:13');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'contact_address', 'Shop No. 2, Gupta Market, Tajganj, Agra', '2025-12-28 08:46:45', '2025-12-28 08:46:45'),
(2, 'contact_email', 'info@indiadaytrip.com', '2025-12-28 08:46:45', '2025-12-28 08:46:45'),
(3, 'contact_mobile', '+91 81260 52755', '2025-12-28 08:46:45', '2025-12-28 08:46:45'),
(4, 'social_facebook', 'https://www.facebook.com/indiadaytrip', '2025-12-28 08:46:45', '2025-12-28 08:46:45'),
(5, 'social_twitter', 'https://www.twitter.com/indiadaytrip', '2025-12-28 08:46:45', '2025-12-28 08:46:45'),
(6, 'social_instagram', 'https://www.instagram.com/indiadaytrip', '2025-12-28 08:46:45', '2025-12-28 08:46:45');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `included` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`included`)),
  `excluded` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`excluded`)),
  `itinerary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`itinerary`)),
  `pricing` decimal(10,2) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `dates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dates`)),
  `availability` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 5.00,
  `view_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots_meta` varchar(100) DEFAULT 'INDEX,FOLLOW',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(500) DEFAULT NULL,
  `schema_markup` longtext DEFAULT NULL,
  `internal_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `included_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `excluded_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `faq` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `show_price` tinyint(1) DEFAULT 1,
  `schemas` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `slug`, `description`, `highlights`, `included`, `excluded`, `itinerary`, `pricing`, `images`, `dates`, `availability`, `category_id`, `location`, `duration`, `rating`, `view_count`, `created_at`, `updated_at`, `meta_title`, `meta_description`, `meta_keywords`, `canonical_url`, `robots_meta`, `og_title`, `og_description`, `og_image`, `twitter_title`, `twitter_description`, `twitter_image`, `schema_markup`, `internal_items`, `included_points`, `excluded_points`, `faq`, `show_price`, `schemas`) VALUES
(18, 'From Delhi 2 Days Agra and Jaipur Tour By Car - Edited Again', 'from-delhi-2-days-agra-and-jaipur-tour-by-car-edited-again', '<p>This 2 days Agra and Jaipur tour from Delhi by car is ideal for travelers who want to experience India&rsquo;s most iconic Mughal and Rajput heritage in a short time. Starting from Delhi, the journey takes you to Agra to explore the majestic Taj Mahal, Agra Fort, and local handicraft markets. On the second day, continue towards Jaipur, the Pink City of Rajasthan, known for its royal palaces, forts, and vibrant culture. This tour is designed for comfort with a private air-conditioned car, professional driver, and flexible sightseeing schedule. It suits couples, families, and first-time visitors seeking a balanced mix of history, architecture, and local experiences. The route offers smooth highways, safe travel, and well-paced sightseeing without rush. With expert planning and reliable services, this tour ensures a memorable North India travel experience while covering two UNESCO World Heritage destinations efficiently.</p>', '[\"Visit the Taj Mahal at Agra\",\"Explore Agra Fort and city attractions\",\"Sightseeing in Jaipur including Amber Fort\",\"Private AC car with experienced driver\",\"Comfortable overnight stay in Jaipur\"]', '[\"Private air-conditioned car\",\"Hotel accommodation\",\"Professional driver\",\"All sightseeing as per itinerary\",\"Fuel, parking, and toll taxes\"]', '[\"Monument entrance fees\",\"Personal expenses\",\"Meals not mentioned\",\"Camera or video charges\",\"Travel insurance\"]', '[{\"title\":\"Day 1\",\"points\":[\"Delhi to Agra sightseeing and drive to Jaipur\"]},{\"title\":\"Day 2\",\"points\":[\"Jaipur sightseeing and return\\/drop\"]}]', 9999.00, '[\"tours-image\\/cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 1, 'Delhi – Agra – Jaipur', '2 Days', 5.00, 63, '2026-01-04 14:49:45', '2026-04-12 19:30:06', '', '', '', NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '[{\"id\":\"0\",\"type\":\"custom\",\"data\":{\"@context\":\"https://schema.org\",\"@type\":\"TouristTrip\"}}]'),
(40, 'Taj Mahal Sunrise Tour From Delhi', 'taj-mahal-sunrise-tour-from-delhi', 'The Taj Mahal Sunrise Tour from Delhi offers a magical opportunity to witness India’s most famous monument in the soft morning light. Departing early from Delhi, this tour ensures you reach Agra before sunrise to experience the Taj Mahal at its most serene and photogenic moment. The changing colors of the marble at dawn create an unforgettable visual experience. After the Taj Mahal visit, explore Agra Fort, a UNESCO World Heritage Site that reflects the grandeur of Mughal architecture. This tour is perfect for travelers with limited time who want a premium cultural experience in one day. With private transportation, a knowledgeable guide, and a well-planned schedule, the journey is smooth and comfortable. Ideal for couples, photographers, and history lovers, this sunrise tour provides a peaceful and crowd-free way to explore Agra’s iconic landmarks.', '[\"Sunrise visit to the Taj Mahal\",\"Guided tour of Agra Fort\",\"Same-day return to Delhi\",\"Private air-conditioned vehicle\",\"Professional local guide\"]', '[\"Private AC car\",\"Driver allowance\",\"Local tour guide\",\"Sightseeing as planned\",\"All taxes and tolls\"]', '[\"Monument entrance tickets\",\"Breakfast or meals\",\"Personal shopping expenses\",\"Camera fees\",\"Tips and gratuities\"]', '[{\"title\":\"Day 1\",\"points\":[\"Early morning departure from Delhi\",\"Sunrise Taj Mahal visit\",\"Agra Fort sightseeing\",\"Return to Delhi by evening\"]}]', 5999.00, '[\"tours-image\\/cropped_1767810062847_cropped_1766849852474_night-taj-mahal.webp\",\"tours-image\\/cropped_1767810089236_cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767810108066_cropped_1766848989644_udaipur-tour.webp\",\"tours-image\\/cropped_1767810128201_delhi-food-taste.webp\",\"tours-image\\/cropped_1767810206831_amritsar-tour.webp\"]', NULL, 20, 2, 'Delhi – Agra – Delhi', '1 Day', 5.00, 10, '2026-01-04 18:06:08', '2026-04-12 18:05:35', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(41, 'From Delhi Taj Mahal And Agra Overnight Tour', 'from-delhi-taj-mahal-and-agra-overnight-tour', 'This overnight Agra tour from Delhi allows travelers to explore the city at a relaxed pace without rushing through major attractions. Starting from Delhi, the journey takes you to Agra where you visit the Taj Mahal, Agra Fort, and local markets. An overnight stay in Agra gives you time to enjoy the city’s heritage, cuisine, and atmosphere beyond a single day. The tour is well-suited for couples and families who prefer comfortable travel with private transportation and flexible sightseeing. With experienced drivers and optional guided tours, this package ensures a smooth and enriching travel experience. The overnight stay also allows you to avoid long same-day travel fatigue while enjoying Agra’s Mughal charm. This tour combines convenience, comfort, and cultural depth, making it a popular choice for short North India getaways.', '[\"Visit Taj Mahal and Agra Fort\",\"Overnight stay in Agra\",\"Private car from Delhi\",\"Local market exploration\",\"Relaxed sightseeing schedule\"]', '[\"Private AC vehicle\",\"Hotel accommodation\",\"Driver allowance\",\"Sightseeing as per plan\",\"All road taxes\"]', '[\"Monument entry fees\",\"Meals unless specified\",\"Personal expenses\",\"Camera charges\",\"Insurance\"]', '[{\"title\":\"Day 1\",\"points\":[\"Delhi to Agra sightseeing\"]},{\"title\":\"Day 2\",\"points\":[\"Optional sunrise visit and return to Delhi\"]}]', 7999.00, '[\"tours-image\\/cropped_1767810298767_cropped_1766848989644_udaipur-tour.webp\",\"tours-image\\/cropped_1766849017264_udaipur-tour.webp\",\"tours-image\\/cropped_1766849235504_amritsar-tour.webp\",\"tours-image\\/cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\"]', NULL, 20, 2, 'Delhi – Agra', '2 Days', 5.00, 33, '2026-01-05 18:56:58', '2026-04-15 20:24:42', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(42, 'Taj Mahal Sunrise And Old Delhi Tour', 'taj-mahal-sunrise-and-old-delhi-tour', 'The Taj Mahal Sunrise and Old Delhi Tour combines two iconic experiences in one memorable journey. Begin early with a sunrise visit to the Taj Mahal in Agra, where the soft morning light enhances the monument’s beauty and calm atmosphere. After exploring Agra Fort, return to Delhi to discover the vibrant charm of Old Delhi. This part of the tour includes narrow lanes, historic mosques, bustling markets, and traditional street life. The contrast between Mughal elegance and Delhi’s lively heritage offers a complete cultural experience in a single day. Designed for travelers who want both monument sightseeing and authentic local exploration, the tour includes private transport and expert guidance. It is ideal for visitors interested in history, photography, and traditional Indian culture, all within a well-managed and time-efficient itinerary.', '[\"Sunrise Taj Mahal visit\",\"Agra Fort exploration\",\"Old Delhi heritage walk\",\"Private AC transportation\",\"Cultural and historical contrast\"]', '[\"Private car with driver\",\"Local guide services\",\"Sightseeing as per itinerary\",\"Fuel and toll taxes\",\"All parking charges\"]', '[\"Monument entrance fees\",\"Meals\",\"Personal expenses\",\"Camera charges\",\"Tips\"]', '[{\"title\":\"Day 1\",\"points\":[\"Early departure from Delhi\",\"Sunrise Taj Mahal and Agra Fort\",\"Return to Delhi\",\"Old Delhi sightseeing\"]}]', 6999.00, '[\"tours-image\\/cropped_1767810330464_cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 1, 'Delhi – Agra', '1 Day', 5.00, 18, '2026-01-05 19:02:23', '2026-04-11 01:51:32', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(43, 'golden triangle tour 4n5d', 'golden-triangle-tour-4n5d', 'The Golden Triangle Tour 4N5D is one of the most popular travel circuits in India, covering Delhi, Agra, and Jaipur. This well-paced itinerary allows travelers to explore historical landmarks, royal palaces, and cultural heritage across North India. Starting in Delhi, the tour moves to Agra to witness the Taj Mahal and Mughal architecture, followed by Jaipur, known for its forts, palaces, and colorful markets. The five-day duration ensures comfortable travel without rushing, making it suitable for families and first-time visitors. With private transportation, guided sightseeing, and quality accommodations, this tour offers a complete introduction to India’s rich history and traditions. The Golden Triangle route is ideal for understanding the diversity, architecture, and lifestyle of North India in a structured and enjoyable way.', '[\"Delhi city sightseeing\",\"Taj Mahal and Agra Fort\",\"Jaipur forts and palaces\",\"Private car tour\",\"Balanced travel pace\"]', '[\"Private AC vehicle\",\"Hotel accommodation\",\"Driver allowance\",\"Sightseeing as per plan\",\"All tolls and taxes\"]', '[\"Monument entry tickets\",\"Meals not mentioned\",\"Personal expenses\",\"Camera fees\",\"Insurance\"]', '[{\"title\":\"Day 1\",\"points\":[\"Arrival in Delhi sightseeing\"]},{\"title\":\"Day 2\",\"points\":[\"Delhi to Agra\"]},{\"title\":\"Day 3\",\"points\":[\"Agra to Jaipur\"]},{\"title\":\"Day 4\",\"points\":[\"Jaipur sightseeing\"]},{\"title\":\"Day 5\",\"points\":[\"Departure\"]}]', 18999.00, '[\"tours-image\\/cropped_1767810389330_cropped_1766850086738_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\",\"tours-image\\/cropped_1766859100725_night-taj-mahal.webp\",\"tours-image\\/cropped_1766859130236_gatiman.webp\"]', NULL, 20, 3, 'Delhi – Agra – Jaipur', '5 Days', 5.00, 83, '2026-01-05 19:06:45', '2026-04-12 10:08:55', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(44, 'golden triangle tour with amritsar 6n7d', 'golden-triangle-tour-with-amritsar-6n7d', 'The Golden Triangle Tour with Amritsar 6N7D extends the classic Delhi, Agra, and Jaipur circuit by adding the spiritual and cultural city of Amritsar. Along with iconic attractions like the Taj Mahal and Jaipur’s royal forts, this tour includes a visit to the Golden Temple, one of India’s most sacred sites. The itinerary blends history, spirituality, and cultural diversity, offering travelers a deeper understanding of North India. Comfortable travel arrangements, guided sightseeing, and well-planned routes ensure a smooth experience across multiple cities. This tour is ideal for travelers seeking both heritage and spiritual enrichment within a single journey. From Mughal monuments to Sikh traditions, the tour offers a balanced and meaningful travel experience.', '[\"Fast Gatimaan Express train journey\",\"Guided Taj Mahal visit\",\"Agra Fort sightseeing\",\"Same-day return\",\"Comfortable and time-saving travel\"]', '[\"Train tickets\",\"Local AC transport in Agra\",\"Tour guide\",\"Sightseeing as planned\",\"All applicable taxes\"]', '[\"Monument entrance fees\",\"Meals other than train service\",\"Personal expenses\",\"Camera charges\",\"Tips\"]', '[{\"title\":\"Day 1\",\"points\":[\"Morning train from Delhi\",\"Taj Mahal and Agra Fort visit\",\"Evening return by train\"]}]', 6499.00, '[\"tours-image\\/cropped_1767810432484_cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 2, 'Delhi – Agra – Delhi', '1 Day', 5.00, 24, '2026-01-05 19:07:15', '2026-04-15 07:49:54', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(45, 'taj mahal tour by gatimaan express train', 'taj-mahal-tour-by-gatimaan-express-train', 'The Taj Mahal Tour by Gatimaan Express Train is the fastest and most comfortable way to visit Agra from Delhi. Gatimaan Express offers a smooth and time-efficient rail journey, allowing travelers to maximize sightseeing time in Agra. This tour includes a guided visit to the Taj Mahal and Agra Fort, making it ideal for travelers who prefer train travel over long road journeys. With early morning departure and same-day return, the tour is perfect for business travelers, families, and senior citizens. The combination of modern train facilities and well-organized local transfers ensures a hassle-free experience. This tour provides a convenient, reliable, and comfortable option to explore Agra’s iconic monuments in one day.', '[\"Fast Gatimaan Express train journey\",\"Guided Taj Mahal visit\",\"Agra Fort sightseeing\",\"Same-day return\",\"Comfortable and time-saving travel\"]', '[\"Train tickets\",\"Local AC transport in Agra\",\"Tour guide\",\"Sightseeing as planned\",\"All applicable taxes\"]', '[\"Monument entrance fees\",\"Meals other than train service\",\"Personal expenses\",\"Camera charges\"]', '[{\"title\":\"Day 1\",\"points\":[\"Morning train from Delhi\",\"Taj Mahal and Agra Fort visit\",\"Evening return by train\"]}]', 6499.00, '[\"tours-image\\/cropped_1767810456823_cropped_1766848989644_udaipur-tour.webp\",\"tours-image\\/cropped_1766849017264_udaipur-tour.webp\",\"tours-image\\/cropped_1766849235504_amritsar-tour.webp\",\"tours-image\\/cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\"]', NULL, 20, 2, 'Delhi – Agra – Delhi', '1 Day', 5.00, 18, '2026-01-07 17:41:49', '2026-04-14 11:49:02', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(46, 'golden triangle tours with varanasi 5n6d', 'golden-triangle-tours-with-varanasi-5n6d', 'The Golden Triangle Tour with Varanasi combines North India’s most famous heritage cities with one of the world’s oldest living spiritual centers. Along with Delhi, Agra, and Jaipur, this tour includes Varanasi, known for its sacred ghats, temples, and spiritual rituals along the Ganges River. The itinerary offers a perfect mix of history, culture, and spirituality. Travelers experience Mughal monuments, Rajput architecture, and deeply rooted religious traditions in one comprehensive journey. Comfortable transportation, guided sightseeing, and well-planned stays ensure a smooth experience. This tour is ideal for travelers seeking both cultural exploration and spiritual insight within a limited timeframe', '[\"Ganga Aarti in Varanasi\",\"Golden Triangle sightseeing\",\"Taj Mahal and Jaipur forts\",\"Cultural and spiritual blend\",\"Private guided travel\"]', '[\"Private AC transport\",\"Hotel accommodation\",\"Driver allowance\",\"Sightseeing as per plan\",\"All taxes\"]', '[\"Monument entry fees\",\"Meals not specified\",\"Personal expenses\",\"Camera charges\",\"Insurance\"]', '[]', 26999.00, '[\"tours-image\\/cropped_1767810522120_cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767810542316_cropped_1766849852474_night-taj-mahal.webp\"]', NULL, 20, 3, 'Delhi – Agra – Jaipur – Varanasi', '6 Days', 5.00, 23, '2026-01-07 17:45:48', '2026-04-14 19:36:26', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(47, 'taj mahal and agra tour by premium cars', 'taj-mahal-and-agra-tour-by-premium-cars', 'The Taj Mahal and Agra Tour by Premium Cars is designed for travelers who value comfort, privacy, and luxury while exploring Agra’s iconic attractions. This tour includes a private premium vehicle with professional chauffeur service, ensuring a smooth and stylish journey from Delhi to Agra. Visit the Taj Mahal, Agra Fort, and other local highlights at a relaxed pace. Ideal for couples, business travelers, and luxury seekers, the tour focuses on personalized service and flexible scheduling. The premium car experience enhances overall comfort, making the journey as enjoyable as the destination. This tour offers a refined way to explore Agra in a single day or with optional extensions', '[\"Luxury premium car travel\",\"Taj Mahal and Agra Fort visit\",\"Personalized itinerary\",\"Professional chauffeur\",\"Comfort-focused experience\"]', '[\"Premium AC vehicle\",\"Driver allowance\",\"Sightseeing as planned\",\"Fuel and tolls\",\"All taxes\"]', '[\"Monument entry tickets\",\"Meals\",\"Personal expenses\",\"Camera charges\",\"Tips\"]', '[{\"title\":\"Day 1\",\"points\":[\"Morning departure from Delhi\",\"Agra sightseeing\",\"Return to Delhi\"]}]', 8999.00, '[\"tours-image\\/cropped_1767810561821_cropped_1766850086738_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\",\"tours-image\\/cropped_1766859100725_night-taj-mahal.webp\",\"tours-image\\/cropped_1766859130236_gatiman.webp\"]', NULL, 20, 2, 'Delhi – Agra', '1 Day', 5.00, 219, '2026-01-07 17:46:21', '2026-04-16 19:46:49', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(48, 'golden triangle tour 2n3d', 'golden-triangle-tour-2n3d', 'The Golden Triangle Tour 2N3D is a compact itinerary covering Delhi, Agra, and Jaipur within three days. Designed for travelers with limited time, this tour focuses on key highlights such as the Taj Mahal, Jaipur’s forts, and Delhi’s historic landmarks. The route is efficient and well-organized, ensuring comfortable travel and maximum sightseeing. Ideal for first-time visitors, the tour provides a quick yet meaningful introduction to North India’s cultural heritage. Private transportation, guided sightseeing, and carefully selected stops make this tour convenient and enjoyable. Despite its short duration, the tour captures the essence of the Golden Triangle experience.', '[\"Short Golden Triangle circuit\",\"Taj Mahal visit\",\"Jaipur heritage sightseeing\",\"Private AC car\",\"Time-efficient itinerary\"]', '[\"Private transportation\",\"Hotel accommodation\",\"Driver allowance\",\"Sightseeing as planned\",\"All road taxes\"]', '[\"Monument entry fees\",\"Meals not mentioned\",\"Personal expenses\",\"Camera charges\",\"Insurance\"]', '[{\"title\":\"Day 1\",\"points\":[\" Delhi to Agra\"]}]', 14999.00, '[\"tours-image\\/cropped_1767810596825_cropped_1766849017264_udaipur-tour.webp\",\"tours-image\\/cropped_1766849235504_amritsar-tour.webp\",\"tours-image\\/cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\",\"tours-image\\/cropped_1766850086738_night-taj-mahal.webp\"]', NULL, 20, 3, 'Delhi – Agra – Jaipur', '3 Days', 5.00, 16, '2026-01-07 17:52:50', '2026-04-12 11:05:08', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(49, 'from delhi day trip to taj mahal and agra fort by car', 'from-delhi-day-trip-to-taj-mahal-and-agra-fort-by-car', 'This day trip from Delhi to the Taj Mahal and Agra Fort by car is ideal for travelers seeking a comfortable and flexible same-day excursion. The journey begins early in the morning with a private air-conditioned car, allowing ample time to explore Agra’s most famous landmarks. Visit the Taj Mahal, one of the Seven Wonders of the World, followed by a guided tour of Agra Fort. The itinerary is designed to avoid rush and ensure a smooth return to Delhi by evening. Suitable for families, couples, and solo travelers, this tour offers convenience, safety, and a well-managed schedule for a memorable Agra visit.', '[\"Private car day trip\"]', '[\"Private AC car\"]', '[]', '[{\"title\":\"Day 1\",\"points\":[\"Morning departure from Delhi\"]}]', 4999.00, '[\"tours-image\\/cropped_1767810617961_cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 1, 'Delhi – Agra', '1 Day', 5.00, 22, '2026-01-07 17:54:28', '2026-04-12 18:13:11', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(50, 'golden triangle tour with udaipur 7n8d', 'golden-triangle-tour-with-udaipur-7n8d', 'The Golden Triangle Tour with Udaipur 7N8D combines North India’s iconic heritage cities with the romantic charm of Udaipur. Along with Delhi, Agra, and Jaipur, this tour includes Udaipur, known for its lakes, palaces, and serene atmosphere. The itinerary offers a balanced mix of history, royal architecture, and scenic beauty. Travelers enjoy comfortable travel, guided sightseeing, and carefully planned stays across multiple destinations. This extended tour is ideal for travelers seeking a deeper exploration of Rajasthan along with classic Golden Triangle highlights.', '[\"Golden Triangle sightseeing\"]', '[\"Private AC transport\"]', '[]', '[{\"title\":\"Day 1\",\"points\":[\"Delhi sightseeing\"]}]', 34999.00, '[\"tours-image\\/cropped_1767810637615_cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\",\"tours-image\\/cropped_1766859100725_night-taj-mahal.webp\",\"tours-image\\/cropped_1766859130236_gatiman.webp\",\"tours-image\\/cropped_1766912391026_gatiman.webp\"]', NULL, 20, 3, 'Delhi – Agra – Jaipur – Udaipur', '8 Days', 5.00, 21, '2026-01-07 17:56:04', '2026-04-13 17:38:29', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(51, 'golden triangle tour with ranthambore 4n5d', 'golden-triangle-tour-with-ranthambore-4n5d', 'The Golden Triangle Tour with Ranthambore 4N5D adds a wildlife experience to the classic Delhi, Agra, and Jaipur circuit. Along with visiting the Taj Mahal and Jaipur’s forts, travelers enjoy a jungle safari in Ranthambore National Park, known for its tiger population. This tour offers a unique blend of heritage and nature, making it ideal for wildlife enthusiasts and families. The itinerary is well-balanced, ensuring comfortable travel and sufficient time for sightseeing and safari activities. With professional planning and reliable transport, the tour provides a memorable North India experience beyond traditional city tours.', '[\"Ranthambore jungle safari\"]', '[\"Private AC transportation\"]', '[\"Monument entry fees\"]', '[{\"title\":\"Day 1\",\"points\":[\"Delhi sightseeing\"]}]', 27999.00, '[\"tours-image\\/cropped_1767810659541_cropped_1766848989644_udaipur-tour.webp\",\"tours-image\\/cropped_1766849017264_udaipur-tour.webp\",\"tours-image\\/cropped_1766849235504_amritsar-tour.webp\",\"tours-image\\/cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\"]', NULL, 20, 3, 'Delhi – Agra – Ranthambore – Jaipur', '5 Days', 5.00, 16, '2026-01-07 17:57:55', '2026-04-15 03:16:09', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(52, 'golden triangle toui 3n4d', 'golden-triangle-toui-3n4d', 'The Golden Triangle Toui 3N4D is a well-structured tour covering Delhi, Agra, and Jaipur over four days. Designed for travelers seeking a slightly relaxed pace compared to shorter itineraries, this tour allows more time at each destination. Explore Delhi’s historic sites, witness the beauty of the Taj Mahal in Agra, and experience Jaipur’s royal heritage. The itinerary balances travel and sightseeing, ensuring comfort and cultural immersion. Ideal for families and first-time visitors, the tour offers a comprehensive introduction to North India’s most famous travel circuit.', '[]', '[]', '[]', '[{\"title\":\"Day 1\",\"points\":[\"Delhi sightseeing\"]}]', 17999.00, '[\"tours-image\\/cropped_1767810683021_cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\",\"tours-image\\/cropped_1766850086738_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\"]', NULL, 20, 3, 'Delhi – Agra – Jaipur', '4 Days', 5.00, 20, '2026-01-07 17:59:38', '2026-04-11 13:39:34', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(53, 'royal rajasthan tour 5n6d', 'royal-rajasthan-tour-5n6d', 'The Royal Rajasthan Tour 5N6D showcases the grandeur, culture, and royal heritage of Rajasthan. This tour covers major cities known for palaces, forts, and vibrant traditions. Travelers experience historic architecture, colorful markets, and traditional Rajasthani hospitality. The itinerary is designed for comfortable travel with guided sightseeing and quality accommodations. Ideal for cultural explorers, this tour highlights Rajasthan’s royal past and living traditions. From majestic forts to local experiences, the journey offers a deep insight into one of India’s most culturally rich states.', '[\"Royal forts and palaces\"]', '[\"Private AC transport\"]', '[]', '[{\"title\":\"Day 1\",\"points\":[\"Arrival and city sightseeing\"]}]', 24999.00, '[\"tours-image\\/cropped_1767810723721_cropped_1766849852474_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\"]', NULL, 20, 14, 'Rajasthan', '6 Days', 5.00, 13, '2026-01-07 18:02:14', '2026-04-13 23:17:19', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(54, 'old delhi food tasting tour evening 4 hours', 'old-delhi-food-tasting-tour-evening-4-hours', 'The Old Delhi Food Tasting Tour is a guided evening experience that introduces travelers to the rich culinary heritage of Delhi. Over four hours, explore narrow lanes, historic markets, and iconic food spots known for traditional recipes. The tour offers insight into local culture, history, and flavors through curated tastings. Ideal for food lovers and cultural explorers, this experience focuses on authenticity and safety. Guided by local experts, travelers enjoy a flavorful journey through Old Delhi’s vibrant street food scene while learning about the area’s heritage.', '[\"Guided food tasting experience\"]', '[\"Local food tastings\"]', '[\"Personal purchases\"]', '[{\"title\":\"Day 1\",\"points\":[\"Evening meeting point\"]}]', 2499.00, '[\"tours-image\\/cropped_1767810745560_cropped_1766848989644_udaipur-tour.webp\",\"tours-image\\/cropped_1766849235504_amritsar-tour.webp\",\"tours-image\\/cropped_1766859130236_gatiman.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 1, 'Old Delhi', '4 Hours', 5.00, 34, '2026-01-07 18:03:52', '2026-04-15 20:24:44', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(55, 'colorful rajasthan tour 8d9n', 'colorful-rajasthan-tour-8d9n', 'The Colorful Rajasthan Tour 8D9N offers an in-depth exploration of Rajasthan’s diverse culture, architecture, and landscapes. This extended itinerary covers multiple cities known for forts, palaces, deserts, and traditional lifestyles. Travelers experience vibrant markets, local traditions, and historical landmarks at a relaxed pace. Designed for cultural enthusiasts, the tour highlights Rajasthan’s royal legacy and everyday life. Comfortable transportation, guided sightseeing, and thoughtfully planned stays ensure a rich and immersive travel experience across the state.', '[]', '[]', '[]', '[]', NULL, '[\"tours-image\\/cropped_1767810768802_cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766850086738_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\"]', NULL, 20, 14, 'Rajasthan', '39999', 5.00, 16, '2026-01-07 18:04:40', '2026-04-15 21:20:21', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(56, 'rajasthan heritage tour 4n5d', 'rajasthan-heritage-tour-4n5d', 'The Rajasthan Heritage Tour 4N5D focuses on the historical and architectural legacy of Rajasthan. This tour includes visits to famous forts, palaces, and heritage sites that reflect the royal past of the region. Designed for travelers interested in history and culture, the itinerary offers guided sightseeing and comfortable travel. The tour provides a concise yet enriching experience of Rajasthan’s heritage within a manageable timeframe.', '[\"Historic forts and palaces\"]', '[\"Private AC transport\"]', '[\"Monument entry fees\"]', '[{\"title\":\"Day 1\",\"points\":[\"Arrival and city tour\"]}]', 22999.00, '[\"tours-image\\/cropped_1767810701055_cropped_1767538320858_sunrise-taj-2.webp\",\"tours-image\\/cropped_1767538358273_agra-tour-1.webp\",\"tours-image\\/cropped_1767538419287_udaipur-tour.webp\",\"tours-image\\/cropped_1767538451873_varanashi-tour.webp\",\"tours-image\\/cropped_1767538716104_delhi-food-taste.webp\"]', NULL, 20, 14, 'Rajasthan', '5 Days', 5.00, 25, '2026-01-07 18:07:01', '2026-04-19 08:00:05', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(57, 'rajasthan city tour 3n4d', 'rajasthan-city-tour-3n4d', 'The Rajasthan City Tour 3N4D is a short and focused itinerary covering key urban destinations in Rajasthan. The tour highlights city-based attractions, including palaces, forts, markets, and cultural landmarks. Ideal for travelers with limited time, this tour provides a structured introduction to Rajasthan’s urban heritage. Comfortable travel arrangements and guided sightseeing ensure a smooth experience while capturing the essence of the region.', '[\"Key Rajasthan city attractions\"]', '[\"Private AC transportation\"]', '[\"Monument entry fees\"]', '[{\"title\":\"Day 1\",\"points\":[\"Arrival and city tour\"]}]', 18499.00, '[\"tours-image\\/cropped_1767810502482_cropped_1766849432245_amritsar-tour.webp\",\"tours-image\\/cropped_1766849852474_night-taj-mahal.webp\",\"tours-image\\/cropped_1766850086738_night-taj-mahal.webp\",\"tours-image\\/cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\"]', NULL, 20, 14, 'Rajasthan', '4 Days', 5.00, 27, '2026-01-07 18:08:36', '2026-04-14 18:05:23', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(58, 'delhi private day tour by car', 'delhi-private-day-tour-by-car', 'The Delhi Private Day Tour by Car offers a comfortable and personalized way to explore India’s capital city. This tour covers major historical, cultural, and modern attractions of Delhi with a flexible schedule. Ideal for first-time visitors and families, the private car ensures convenience and safety. With optional guided commentary, travelers gain insights into Delhi’s rich past and vibrant present. The tour is designed to be relaxed, informative, and customizable according to interests.', '[\"Private car city tour\",\"Major Delhi attractions\"]', '[\"Private AC vehicle\",\"Driver allowance\"]', '[\"Monument entry fees\",\"Meals\"]', '[{\"title\":\"Day 1\",\"points\":[\"Morning pickup\"]}]', 3999.00, '[\"tours-image\\/cropped_1767810482802_cropped_1766858356515_delhi-food-taste.webp\",\"tours-image\\/cropped_1766858392129_night-delhi.webp\",\"tours-image\\/cropped_1766859100725_night-taj-mahal.webp\",\"tours-image\\/cropped_1766859130236_gatiman.webp\",\"tours-image\\/cropped_1766912391026_gatiman.webp\"]', NULL, 20, 1, 'Delhi', '1 Days', 5.00, 26, '2026-01-07 18:10:27', '2026-04-19 08:20:24', NULL, NULL, NULL, NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(59, 'evening delhi city tour 4 hours', 'evening-delhi-city-tour-4-hours', '<p><span style=\"color: rgb(0, 0, 0); background-color: transparent;\">This memorable </span><a href=\"https://indiadaytrip.com/tour/evening-delhi-city-tour-4-hours\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(17, 85, 204); background-color: transparent;\"><strong>Evening Delhi City Tour 4 Hours</strong></a><span style=\"color: rgb(0, 0, 0); background-color: transparent;\"> is ideal for travelers with limited time who wish to discover Delhi highlights and Local Street in an informative yet entertaining fashion. Visit iconic sites such as India Gate, </span><a href=\"https://en.wikipedia.org/wiki/Chandni_Chowk\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0); background-color: transparent;\">Chandni Chowk</a><span style=\"color: rgb(0, 0, 0); background-color: transparent;\"> Rashtrapati Bhavan Parliament House as the sun sets and witness beautiful city transformation as stunning views unfold and great photo opps become available - travel comfortably by private car with professional guide ensuring this </span><strong style=\"color: rgb(0, 0, 0); background-color: transparent;\">Evening Delhi City Tour 4 Hours</strong><span style=\"color: rgb(0, 0, 0); background-color: transparent;\"> offers unparalleled insight into its beauty, history and local atmosphere!</span></p>', '[\"Explore illuminated India Gate in the evening light\",\"drive past Rashtrapati Bhavan and Parliament House that have been beautifully lit up, \",\"walk through lively Connaught Place to experience Delhi\'s nightlife vibe\",\"Tatse delicious street foods such as chaat, golgappa or local snacks - you won\'t be  disappointed!\",\"Enjoy a comfortable private AC car Pickup & Drop service from your hotel\"]', '[\"Private air-conditioned car for a comfortable 4-hour city tour\",\"Hotel pickup and drop-off in Delhi\",\"Professional English-speaking driver\\/guide\",\"Bottled mineral water during the tour\",\"Street food tasting experience at a popular local market\",\"All fuel charges, parking fees, and taxes\"]', '[\"Monument entrance fees (if any)\",\"Personal expenses such as shopping or extra food\",\"Tips and gratuities for driver or guide\"]', '[{\"title\":\"04:30 PM - Hotel Pickup & Start a Tour \",\"points\":[\"Your driver will pick you up at your hotel \\/ other Location  in Delhi in an priavte AC car  for an enjoyable evening Delhi City Tour \"]},{\"title\":\"05:00 PM \\u2013 Visit India Gate \",\"points\":[\"Arrive at India Gate for a relaxing walk around this iconic war memorial landmarks  and experience its captivating lights in the evening atmosphere. \"]},{\"title\":\"05:45 PM \\u2013 Drive Past Rashtrapati Bhavan & Parliament House\",\"points\":[\"Drive past Rashtrapati Bhavan & Parliament House for some stunning sights to be  seen Have a scenic drive through New Delhi\'s VIP areas, seeing Rashtrapati Bhavan  and Parliament House illuminated in the evening light show.  \"]},{\"title\":\"06:30 PM \\u2013 Connaught Place Market Visit & Food Tasting\",\"points\":[\"Explore the bustling Connaught Place market. Taste delicious street foods such as  chaat or golgappa while taking in local culture. \"]},{\"title\":\"07:45 PM \\u2013 Return Drive to Hotel\",\"points\":[\"After an enjoyable evening experience, relax during your return drive back to your  hotel at 08:30 PM with Hotel Drop Off Service provided for  \"]},{\"title\":\"08:30 PM \\u2013 Hotel Drop-off\",\"points\":[\"Your driver will take you back to your hotel at the conclusion of this 4-hour Delhi  evening tour, marking an end to this unforgettable adventure. \"]}]', 24999.00, '[\"tours-image\\/1775651817_69d64be90f8cc.webp\",\"tours-image\\/1775651817_69d64be90fdb4.webp\",\"tours-image\\/1775651817_69d64be9101d6.webp\",\"tours-image\\/1775651817_69d64be9105cf.webp\"]', NULL, 20, 1, 'Delhi', '4 Hours', 5.00, 157, '2026-01-07 18:12:00', '2026-04-19 08:53:04', 'Evening Delhi City Tour 4 Hours with Pickup & Drop & Guide', 'Enjoy a 4-hour evening Delhi tour with street food tasting. Visit India Gate & \r\nConnaught Place with pickup, drop & guide. Taste local flavors—book now!', '', NULL, 'INDEX,FOLLOW', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"id\":\"0\",\"type\":\"custom\",\"data\":{\"@context\":\"https://schema.org\",\"@type\":\"TouristAttraction\",\"name\":\"Tour Name\",\"description\":\"Tour description\",\"address\":{\"@type\":\"PostalAddress\",\"addressLocality\":\"Location\",\"addressRegion\":\"Uttar Pradesh\",\"addressCountry\":\"IN\"}}}]', NULL, NULL, NULL, '[{\"question\":\"1. What places are covered in the Evening Delhi City Tour?\",\"answer\":\"This tour typically includes major attractions like India Gate, Rashtrapati Bhavan, Gurudwara\\nBangla Sahib, and Connaught Place, beautifully illuminated in the evening\"},{\"question\":\"2. Is hotel pickup and drop included in the tour?\",\"answer\":\"Yes, the tour includes comfortable pickup and drop from your hotel or preferred location\\nanywhere in Delhi, ensuring a hassle-free experience.\"},{\"question\":\"3. Will a professional guide be provided?\",\"answer\":\"Absolutely! A knowledgeable local guide will accompany you, sharing interesting history and\\nstories about Delhi\\u2019s landmarks during the tour.\"},{\"question\":\"4. What is the duration of the evening city tour?\",\"answer\":\"The tour lasts approximately 4 hours, giving you enough time to explore Delhi\\u2019s iconic sights\\nand enjoy the city\\u2019s vibrant evening atmosphere.\"},{\"question\":\"5. Is this tour suitable for first-time visitors to Delhi?\",\"answer\":\"Yes, this tour is perfect for first-time visitors who want a quick yet memorable introduction to\\nDelhi\\u2019s top attractions in a short time.\"}]', 1, '[{\"id\":\"0\",\"type\":\"custom\",\"data\":{\"@context\":\"https://schema.org\",\"@type\":\"TouristAttraction\",\"name\":\"Tour Name\",\"description\":\"Tour description\",\"address\":{\"@type\":\"PostalAddress\",\"addressLocality\":\"Location\",\"addressRegion\":\"Uttar Pradesh\",\"addressCountry\":\"IN\"}}}]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','editor') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'indiadaytrip@gmail.com', '$2y$12$uPSvOYMkw25WmSwznRQep.A/Ca.cASUhxHO6fzjDZdK616M0hYbLi', 'indiadaytrip@gmail.com', 'admin', '2025-12-28 08:46:45', '2026-03-31 07:33:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blog_id` (`blog_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_seo`
--
ALTER TABLE `page_seo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_key` (`page_key`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `page_seo`
--
ALTER TABLE `page_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_blogs_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
