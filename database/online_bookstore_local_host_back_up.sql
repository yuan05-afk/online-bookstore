-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 03:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_bookstore`
--
CREATE DATABASE IF NOT EXISTS `online_bookstore` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `online_bookstore`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_isbn` (`isbn`),
  KEY `idx_title` (`title`),
  KEY `idx_author` (`author`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `price`, `description`, `cover_image`, `category_id`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, '9780515123443', 'Killing Floor', 'Lee Child', 9.99, 'Drifter Jack Reacher is arrested for murder in Margrave, Georgia, less than an hour after arriving. He must navigate a town where he stands no chance of convincing anyone of his innocence to find the real killer.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1399313258i/78129.jpg', 9, 45, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(2, '9780060740226', 'Shakespeare: The World as Stage', 'Bill Bryson', 14.95, 'Bill Bryson brings his curiosity and wit to the life of William Shakespeare, offering a refreshing take on the elusive literary master. This biography explores the man behind the plays with the same freedom of spirit found in Bryson\'s travelogues.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1434075816i/135611.jpg', 5, 30, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(3, '9781571782522', 'Behind the Bell', 'Dustin Diamond', 16.99, 'Dustin Diamond, known as Screech, reveals the dark, behind-the-scenes story of the Saved by the Bell cast\'s extreme lifestyle. He shares his personal struggles with typecasting and his attempts to reinvent himself after childhood stardom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1349057755i/6684320.jpg', 2, 24, '2026-01-30 20:49:04', '2026-01-30 22:46:06'),
(4, '9781908476693', 'Victory or Violence - The Story of the AWB of South Africa', 'Arthur Kemp', 19.50, 'This book details the history of the Afrikaner Weerstandsbeweging (AWB) and its leader Eugene Terre\'Blanche during the end of Apartheid. It covers the organization\'s campaign of violence and bombing in a largely eyewitness account.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347633268i/3661838.jpg', 5, 19, '2026-01-30 20:49:04', '2026-01-30 22:47:31'),
(5, '9781933929690', 'Mother Puncher', 'Gina Ranalli', 11.95, 'In an overpopulated world where procreation is discouraged, Ed Means works for the government as a licensed \"Mother Puncher.\" Though he doesn\'t relish the job, he serves his country by enforcing its strict population control methods.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1348980396i/3928180.jpg', 1, 15, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(6, '9780132126953', 'Computer Networks', 'Andrew S. Tanenbaum', 64.99, 'This classic textbook explains the inner workings of computer networks, from physical hardware to popular applications. It covers modern technologies like wireless broadband, Bluetooth, and quantum cryptography using real-world examples.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347462821i/166190.jpg', 4, 34, '2026-01-30 20:49:04', '2026-02-01 09:20:06'),
(7, '9780826412768', 'Pedagogy of the Oppressed', 'Paulo Freire', 18.95, 'Paulo Freire\'s seminal work outlines a methodology to empower the impoverished and illiterate through education. This anniversary edition includes an introduction on the book\'s lasting impact on educators and social justice movements.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388180018i/72657.jpg', 2, 40, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(8, '9780743243773', 'Teacher Man', 'Frank McCourt', 15.00, 'Frank McCourt reflects on his thirty-year teaching career in New York City high schools, which shaped his second act as a writer. He recounts the trials and triumphs of engaging unruly adolescents using unconventional methods.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1441149612i/4909.jpg', 2, 30, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(9, '9780853300137', 'Discipleship in the New Age II', 'Alice A. Bailey', 22.00, 'This volume continues the personal and group instructions given to aspirants on meditation, initiation, and the six stages of discipleship. It emphasizes the need for group consciousness and the \"anchoring\" of new civilization principles for the Aquarian era.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1189842181i/1890070.jpg', 7, 17, '2026-01-30 20:49:04', '2026-02-01 09:20:06'),
(10, '9788416327340', 'El día que dejó de nevar en Alaska', 'Alice Kellen', 12.50, 'Heather flees to a small town in Alaska to start over, where she meets Nilak, a cold and reserved restaurant owner. As she discovers the memories hiding behind his distance, both find a second chance at life and love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1501600291i/33846385.jpg', 10, 34, '2026-01-30 20:49:04', '2026-02-01 09:20:06'),
(11, '9780571068876', 'Pincher Martin', 'William Golding', 13.99, 'Stranded on a grotesque rock in the North Atlantic, Christopher Hadley Martin struggles to survive with only his own mind for company. He must piece together the terrifying truth of his fate in this journey into one man\'s psyche.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1458386642i/845721.jpg', 1, 28, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(12, '9781913076130', 'The Grim Reaper', 'William Hanna', 14.99, 'Freelance correspondent Mike Walker exposes the crimes of Private Military and Security Companies operating in war zones. When he attracts the attention of a British PMSC and MI5, he must fight to survive and keep reporting the truth.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1586297372i/52445828.jpg', 9, 31, '2026-01-30 20:49:04', '2026-01-30 22:40:30'),
(13, '9780593296376', 'Upgrade', 'Blake Crouch', 28.00, 'Logan Ramsay\'s genome is hacked, giving him enhanced mental and physical abilities that are part of a dangerous plan for human evolution. He must use his new powers to stop a terrifying transformation that threatens humanity at large.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1643994317i/59838811.jpg', 1, 40, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(14, '9780060773750', 'A Quick Bite', 'Lynsay Sands', 7.99, 'Vampire Lissianna Argeneau finds a handsome man tied to her bed, but her tendency to faint at the sight of blood complicates things. Dr. Gregory Hewitt must decide if he can find true love with a vampire vixen or if he is just a meal.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1410129015i/38568.jpg', 10, 47, '2026-01-30 20:49:04', '2026-02-01 09:41:56'),
(15, '9780007162494', 'The Summer Garden', 'Paullina Simons', 16.95, 'Reunited in America after years of war, Tatiana and Alexander must build a new life with their son while facing the rising Cold War. They struggle to heal the lingering pain of the past to prevent their history from destroying their future.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1411993309i/608216.jpg', 10, 38, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(16, '9780199537396', 'The Italian', 'Ann Radcliffe', 11.95, 'Vincentio di Vivaldi falls for the mysterious Ellena, but his mother and a demonic monk named Schedoni plot to separate them. This Gothic romance features abduction, torture, and supernatural horror amidst sublime landscapes.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1668768515i/93136.jpg', 1, 22, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(17, '9780060853983', 'Good Omens', 'Terry Pratchett & Neil Gaiman', 10.99, 'As the apocalypse approaches, a somewhat fussy angel and a fast-living demon team up to prevent the Rapture because they enjoy life on Earth too much. Complicating matters, they seem to have misplaced the Antichrist.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1615552073i/12067.jpg', 1, 55, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(18, '9780439598514', 'The Haunting of Alaizabel Cray', 'Chris Wooding', 9.99, 'Wych-hunter Thaniel and his mentor Cathaline track creatures in London\'s Old Quarter when they encounter the possessed Alaizabel Cray. They must discover what evil entity has turned her into a magnet for horrors before it destroys them all.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1328867929i/140260.jpg', 1, 26, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(19, '9780375703768', 'House of Leaves', 'Mark Z. Danielewski', 21.00, 'A family moves into a home that is impossibly bigger on the inside than the outside, leading to a terrifying exploration of a dark abyss. As they investigate, an unholy growl threatens to consume their sanity and dreams.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1403889034i/24800.jpg', 1, 30, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(20, '9780763653309', 'Finnikin of the Rock', 'Melina Marchetta', 12.99, 'Finnikin is summoned by a young woman named Evanjalin who claims the heir to the cursed kingdom of Lumatere is still alive. As they journey to reunite their people, Finnikin must test his faith in Evanjalin and the truth she hides.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1346007613i/4932435.jpg', 1, 41, '2026-01-30 20:49:04', '2026-02-01 09:20:06'),
(21, '9780062654199', 'The Alice Network', 'Kate Quinn', 16.99, 'In 1947, a pregnant American socialite searching for her cousin teams up with a former WWI spy haunted by the betrayal of her network. Together, they launch a mission to uncover the truth about the \"Alice Network\" and what happened in occupied France.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1492238040i/32051912.jpg', 1, 48, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(22, '9781912555629', 'Tooth for Tooth', 'J.K. Franko', 13.50, 'Susie and Roy thought they committed the perfect murder, but a loose end threatens to destroy their flawless crime. As enemies multiply and the hunters become the hunted, their world begins to crumble.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1583417175i/52117056.jpg', 9, 34, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(23, '9780802808684', 'God in the Dock', 'C.S. Lewis', 14.99, 'This collection of essays by C.S. Lewis covers topics ranging from \"Myth Become Fact\" to \"The Grand Miracle.\" It offers theological insights and arguments on Christianity, ethics, and the modern world.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1437542039i/22051585.jpg', 2, 35, '2026-01-30 20:49:04', '2026-02-01 08:00:31'),
(24, '9781609809627', 'I Who Have Never Known Men', 'Jacqueline Harpman', 16.95, 'A young woman raised in an underground cage with thirty-nine others is suddenly freed into a ravaged, desolate world. Having never known men or her own history, she must reinvent herself while facing the terrifying challenge of freedom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1636235968i/11996.jpg', 1, 24, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(25, '9780312577995', 'Foretold', 'Jana Oliver', 10.99, 'Riley and Beck face a final battle for humanity as a dark secret in Beck\'s hometown threatens to drive them apart. Meanwhile, a brutal war with the prince of Hell looms, and they must defeat their old enemy or lose everything.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1333737125i/13487029.jpg', 1, 29, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(26, '9781444951387', 'Heartstopper: Volume 1', 'Alice Oseman', 14.99, 'Charlie and Nick meet at a British all-boys grammar school and quickly become friends, though Charlie wonders if there could be something more. This graphic novel explores their blooming relationship as they navigate school and young love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1652212196i/58027863.jpg', 10, 60, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(27, '9780445003668', 'Joanne, The Unpredictable', 'Katheryn Kimbrough', 6.50, 'Joanne arrives at Merrihew Manor and uses her beauty to manipulate the men around her, treating life like a romance. However, she soon discovers a satanic force ruling the estate that her guile cannot deceive.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1341850363i/11076322.jpg', 10, 17, '2026-01-30 20:49:04', '2026-02-01 09:22:55'),
(28, '9780195663365', 'Godaan', 'Munshi Premchand', 12.95, 'In this classic Hindi novel, a poor farmer named Hori struggles to own a cow, a symbol of wealth and salvation. The story serves as a moving document of peasant life and social conflict in pre-independence India.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1205467570i/694226.jpg', 1, 27, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(29, '9780452267459', 'Baghdad without a Map', 'Tony Horwitz', 16.00, 'Journalist Tony Horwitz travels through the Middle East, offering a witty and insightful look at the region beyond the headlines. From Yemeni wilds to Saddam\'s Iraq, he recounts misadventures where the ancient and modern collide.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1168030064i/29446.jpg', 2, 30, '2026-01-30 20:49:04', '2026-02-01 09:22:55'),
(30, '9780140442354', 'Either/Or: A Fragment of Life', 'Søren Kierkegaard', 18.00, 'Kierkegaard explores the conflict between the aesthetic and ethical ways of life through two distinct voices. This philosophical masterpiece contemplates subjects like boredom, seduction, and the search for a meaningful existence.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1393892756i/24970.jpg', 2, 25, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(31, '9780156605175', 'The Misanthrope', 'Molière', 10.95, 'Molière\'s comedy satirizes the hypocrisies of French aristocratic society through the character of Alceste, who criticizes others\' faults while ignoring his own. The play focuses on character nuances and the universal nature of misanthropy.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1416873266i/752994.jpg', 1, 33, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(32, '9780786868711', 'The Five People You Meet in Heaven', 'Mitch Albom', 15.99, 'After dying in a tragic accident, Eddie awakes in the afterlife to find that heaven is a place where five people explain his life to him. These encounters reveal the unexpected connections and meaning behind his seemingly uninspired existence.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388200541i/3431.jpg', 1, 52, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(33, '9780451531063', 'Main Street', 'Sinclair Lewis', 9.95, 'This novel tells the story of an idealistic young woman who attempts to reform her small town, only to face suffocation and spiritual discomfort. It is a critical look at American small-town life and the struggle for intellectual freedom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1308953459i/11376.jpg', 1, 28, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(34, '9780020446507', 'Z for Zachariah', 'Robert C. O\'Brien', 8.99, 'Sixteen-year-old Ann Burden believes she is the last survivor of a nuclear war until a stranger arrives in her valley. She must decide if this man is a companion to be trusted or a threat worse than solitude.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1420324231i/69477.jpg', 1, 37, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(35, '9781602602331', 'Santa\'s Angels', 'Janet Kaderli', 12.99, 'Celebrity Nick Klaus hides out as a department store Santa, where he falls for photographer Janie Langston. As the Santa suit changes his priorities, he must keep his identity secret while hoping for a chance at love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1356111065i/4748437.jpg', 10, 43, '2026-01-30 20:49:04', '2026-02-01 09:22:55'),
(36, '9780553106633', 'A Storm of Swords', 'George R.R. Martin', 30.00, 'The War for the Iron Throne rages as the Seven Kingdoms face revolt, blood feuds, and a savage invasion from the north. While Robb Stark defends his crown, dark forces gather beyond the Wall, threatening everyone.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1353235205i/768889.jpg', 1, 56, '2026-01-30 20:49:04', '2026-02-01 09:41:56'),
(37, '9781101947135', 'Homegoing', 'Yaa Gyasi', 16.00, 'This novel traces the descendants of two half-sisters born in eighteenth-century Ghana: one marries an Englishman, while the other is sold into slavery. The story follows their lineages through centuries of history, from African warfare to the American jazz age.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1448108591i/27071490.jpg', 1, 46, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(38, '9780553381900', 'My Losing Season', 'Pat Conroy', 17.00, 'Pat Conroy recounts his senior year of basketball at The Citadel, realizing that his losing season was the most consequential of his life. He explores his relationship with his harsh father and how the trials of sport shaped him as a writer.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1333578751i/119216.jpg', 2, 29, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(39, '9781523775831', 'RoomHate', 'Penelope Ward', 13.99, 'A woman inherits half a summer house, only to find the other half belongs to Justin, the man who broke her heart years ago. Forced to live together, they realize their connection is still alive, blurring the line between love and hate.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1453991440i/27083865.jpg', 10, 40, '2026-01-30 20:49:04', '2026-02-01 09:22:55'),
(40, '9780307356222', 'The Golden Mean', 'Annabel Lyon', 15.95, 'Aristotle is summoned to tutor King Philip\'s son, the future Alexander the Great, and struggles to instill the \"golden mean\" of balance in the young conqueror. The novel explores the relationship between the philosopher and the warrior prince in earthy, muscular prose.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320496914i/6396579.jpg', 1, 23, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(41, '9781501142970', 'It', 'Stephen King', 19.99, 'In Derry, Maine, a shapeshifting entity preys on children by exploiting their deepest fears. Years later, the survivors are called back to confront the horror of their past that has returned to kill again.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1309376909i/18342.jpg', 1, 58, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(42, '9780307277190', 'Traffic', 'Tom Vanderbilt', 16.95, 'Tom Vanderbilt explores the physical, psychological, and technical factors that explain how traffic works and what it says about human nature. The book reveals surprising dynamics, such as why sunny days see more crashes and how road rage can be interpreted.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320428955i/2776527.jpg', 2, 32, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(43, '9780340794500', 'The Cosmic Ordering Service', 'Barbel Mohr', 11.99, 'Barbel Mohr guides readers on how to fulfill their wishes by \"placing an order\" with the universe. The book teaches how to listen to one\'s inner voice and decide what is truly wanted to achieve a more fulfilling life.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347955270i/737825.jpg', 7, 27, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(44, '9781601090560', 'The Journey Home', 'Radhanath Swami', 18.99, 'Radhanath Swami shares his transformation from a young seeker in Chicago to a renowned spiritual guide in India. The memoir details his travels, apprenticeships with yogis, and the discovery of inner harmony and divine love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1328744836i/7013431.jpg', 2, 35, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(45, '9780140233907', 'Paddy Clarke Ha Ha Ha', 'Roddy Doyle', 14.00, 'Set in 1960s Dublin, ten-year-old Paddy Clarke navigates the rough streets of Barrytown while his parents\' marriage crumbles at home. As his family life deteriorates, the once mischievous boy becomes isolated and vulnerable.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1168077668i/30512.jpg', 1, 26, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(46, '9780679722670', 'A Pale View of Hills', 'Kazuo Ishiguro', 15.95, 'Etsuko, a Japanese woman living in England, relives a summer in post-war Nagasaki following her daughter\'s suicide. Her memories of a strange friendship with a woman named Sachiko take on a disturbing and haunting cast.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1348339374i/28920.jpg', 1, 19, '2026-01-30 20:49:04', '2026-02-01 12:14:49'),
(47, '9780062118462', 'Sweet Reckoning', 'Wendy Higgins', 10.99, 'Anna Whitt and her Nephilim allies must rid the earth of demons while facing the ultimate evil. As the stakes rise, Anna has to decide how much she is willing to risk, including her love for Kaidan Rowe.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1381492410i/16007855.jpg', 1, 39, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(48, '9780375703423', 'Family Matters', 'Rohinton Mistry', 17.00, 'In Bombay, an elderly man with Parkinson\'s is forced to move in with his daughter\'s crowded family, testing their resources and compassion. The novel portrays the domestic drama and corruption of the city through the lens of one family\'s struggle.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388279746i/19661.jpg', 1, 28, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(49, '9781250223180', 'What Happened To You?', 'Bruce D. Perry', 28.99, 'Oprah Winfrey and Dr. Bruce Perry discuss the impact of trauma and how shifting the question from \"What\'s wrong with you?\" to \"What happened to you?\" can lead to healing. They offer scientific insights and personal stories to help reshape our understanding of behavior.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1653552093i/53238858.jpg', 7, 50, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(50, '9780307353467', 'Guilty', 'Ann Coulter', 27.95, 'Ann Coulter argues that liberals victimize their opponents while claiming to be victims themselves. The book presents various cases to support her thesis that the political Left engages in bullying under the guise of compassion.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320400867i/6076107.jpg', 2, 22, '2026-01-30 20:49:04', '2026-01-30 20:49:04'),
(51, '9780385537079', 'test', 'test', 0.02, 'test', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8aionaoO0kfbBz0rFjedvtwAftZdJ6I-EGQ&s', 6, 2, '2026-01-30 21:20:26', '2026-01-30 21:20:26'),
(52, '9780393330401', 'test low stock', 'Yuan', 99.00, 'test', 'https://cdn2.shopclues.com/images/thumbnails/79835/320/320/104787525124666394ID1006929615021796911502242942.jpg', 9, 3, '2026-01-30 21:29:28', '2026-01-30 21:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`,`book_id`),
  KEY `book_id` (`book_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Fiction', 'fiction', 'Fictional novels and stories', '2026-01-30 20:47:28'),
(2, 'Non-Fiction', 'non-fiction', 'Non-fictional books and biographies', '2026-01-30 20:47:28'),
(3, 'Science', 'science', 'Scientific books and research', '2026-01-30 20:47:28'),
(4, 'Technology', 'technology', 'Technology and computer science books', '2026-01-30 20:47:28'),
(5, 'History', 'history', 'Historical books and references', '2026-01-30 20:47:28'),
(6, 'Business', 'business', 'Business and economics books', '2026-01-30 20:47:28'),
(7, 'Self-Help', 'self-help', 'Self-improvement and motivational books', '2026-01-30 20:47:28'),
(8, 'Children', 'children', 'Children and young adult books', '2026-01-30 20:47:28'),
(9, 'Mystery', 'mystery', 'Mystery and thriller novels', '2026-01-30 20:47:28'),
(10, 'Romance', 'romance', 'Romance novels', '2026-01-30 20:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `status` enum('Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Processing',
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_zip` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `tax_amount`, `grand_total`, `status`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_zip`, `shipping_country`, `payment_method`, `transaction_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'ORD-697D2BB59CADF-1144', 47.85, 4.07, 51.92, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697D2BB59C347-37490', NULL, '2026-01-30 22:07:49', '2026-01-30 22:07:49'),
(2, 2, 'ORD-697D2C409304C-7497', 7.99, 0.68, 8.67, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697D2C4092C29-36474', NULL, '2026-01-30 22:10:08', '2026-01-30 22:10:08'),
(3, 5, 'ORD-697D335EB8B3D-3707', 14.99, 1.27, 16.26, 'Processing', '123, 123, 123, 123, USA', '123', '123', '123', 'USA', 'Visa ending in 0366', 'TXN-697D335EB8625-45057', NULL, '2026-01-30 22:40:30', '2026-01-30 22:40:30'),
(4, 7, 'ORD-697D34AEC0C2A-6864', 16.99, 1.44, 18.43, 'Processing', 'test12345, 123, 123, 123, USA', '123', '123', '123', 'USA', 'Visa ending in 0366', 'TXN-697D34AEC0997-53226', NULL, '2026-01-30 22:46:06', '2026-01-30 22:46:06'),
(5, 2, 'ORD-697D35039FD09-2774', 19.50, 1.66, 21.16, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697D35039F704-93995', NULL, '2026-01-30 22:47:31', '2026-01-30 22:47:31'),
(6, 2, 'ORD-697F081F3EB30-1971', 14.99, 1.27, 16.26, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697F081F3E856-33609', NULL, '2026-02-01 08:00:31', '2026-02-01 08:00:31'),
(8, 9, 'ORD-697F1B6F5256D-2460', 57.47, 4.88, 62.35, 'Shipped', 'Tonsuya, Malabon City, Metro Manila, 1234, USA', 'Malabon City', 'Metro Manila', '1234', 'USA', 'Visa ending in 0366', 'TXN-697F1B6F522E2-59790', '', '2026-02-01 09:22:55', '2026-02-01 09:33:34'),
(9, 9, 'ORD-697F1FE43337E-8291', 277.99, 23.63, 301.62, 'Processing', 'Tonsuya, Malabon City, Metro Manila, 1234, USA', 'Malabon City', 'Metro Manila', '1234', 'USA', 'Visa ending in 0366', 'TXN-697F1FE432F8D-92450', NULL, '2026-02-01 09:41:56', '2026-02-01 09:41:56'),
(10, 2, 'ORD-697F3A293E8E9-4283', 15.95, 1.36, 17.31, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697F3A293E566-78143', NULL, '2026-02-01 11:34:01', '2026-02-01 11:34:01'),
(11, 2, 'ORD-697F3AF34FFD9-9347', 15.95, 1.36, 17.31, 'Processing', '456 User Ave, Los Angeles, CA, 90001, USA', 'Los Angeles', 'CA', '90001', 'USA', 'Visa ending in 0366', 'TXN-697F3AF34FCDE-62450', NULL, '2026-02-01 11:37:23', '2026-02-01 11:37:23'),
(13, 10, 'ORD-697F43B976D52-4148', 63.80, 5.42, 69.22, 'Processing', 'Tonsuya, Malabon, Metro Manila, 23123, USA', 'Malabon', 'Metro Manila', '23123', 'USA', 'Visa ending in 0366', 'TXN-697F43B976AC0-42450', NULL, '2026-02-01 12:14:49', '2026-02-01 12:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 46, 3, 15.95),
(2, 2, 14, 1, 7.99),
(3, 3, 12, 1, 14.99),
(4, 4, 3, 1, 16.99),
(5, 5, 4, 1, 19.50),
(6, 6, 23, 1, 14.99),
(11, 8, 29, 1, 16.00),
(12, 8, 14, 1, 7.99),
(13, 8, 39, 1, 13.99),
(14, 8, 35, 1, 12.99),
(15, 8, 27, 1, 6.50),
(16, 9, 14, 1, 7.99),
(17, 9, 36, 9, 30.00),
(18, 10, 46, 1, 15.95),
(19, 11, 46, 1, 15.95),
(21, 13, 46, 4, 15.95);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'USA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `phone`, `address`, `city`, `state`, `zip_code`, `country`, `created_at`, `updated_at`) VALUES
(1, 'admin@bookstore.com', '$2y$12$x0WRDYb8rDAJU5uHUlaeDu.cTLuPfivogkPTFZKOs3WXq5xKGk062', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001', 'USA', '2026-01-30 17:23:01', '2026-01-30 17:25:12'),
(2, 'user@bookstore.com', '$2y$12$FcJDfAw.k0wr1lsU25yTwO4nVPYZtNaav0YKpu18j93X4MpSW5c4O', 'User Yuan', 'User Mariano', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001', 'USA', '2026-01-30 17:23:01', '2026-01-30 21:30:08'),
(3, 'test@gmail.com', '$2y$12$FxbtNuvhLwHQfbNio4c0LeIBnd2Rx5lBriVAV4jcUFhBIqyjrSTP2', 'test1231', 'test123', 'user', '123', '123', '123', '123', '123', 'JAPAN', '2026-01-30 18:03:34', '2026-01-30 21:37:24'),
(5, 'test123@gmail.com', '$2y$12$fbGUVIipWryxitcfunRH4OCqgiDjpkNrmZZaiWfleh8eabEqeNvla', 'test123', 'test123', 'user', '12312313', '', '', '', '', 'USA', '2026-01-30 22:39:44', '2026-01-30 22:39:44'),
(6, 'newaccount@gmail.com', '$2y$12$dJ9jDHgoYBVYDb7Ekgl8dO8F45Q9GVAyUI6M8pyK8HzsrO7PvfvHm', 'newaccount', 'newaccount', 'user', '1234', '', '', '', '', 'USA', '2026-01-30 22:43:03', '2026-01-30 22:43:03'),
(7, 'test12345@gmail.com', '$2y$12$/QvFx9UiDOF3g4yY8vK.6uYBaAsKKinq4XyOs/2C970dxwDxXbiue', 'test12345', 'test12345', 'user', '1234', '', '', '', '', 'USA', '2026-01-30 22:43:43', '2026-01-30 22:43:43'),
(9, 'ycmariano@fit.edu.ph', '$2y$12$VrPjthlZcp3vnDMjtmig1.MJEhylEDy2.UgrkZ6AFctZ3jWB/La.K', 'Yuan Andrei', 'Mariano', 'user', '', '', '', '', '', 'USA', '2026-02-01 09:22:13', '2026-02-01 09:22:13'),
(10, 'yuanandreim09@gmail.com', '$2y$12$/0QuaqfHZUJcmvNw4EkMJupcae2g0jUmcpA4gTsPkcB3QKLEA91VO', 'User Yuan', 'Mariano', 'user', '09761660123', '', '', '', '', 'USA', '2026-02-01 12:14:21', '2026-02-01 12:14:21');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
