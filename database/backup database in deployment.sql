-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: sql12.freesqldatabase.com
-- Generation Time: Feb 04, 2026 at 02:02 PM
-- Server version: 5.5.62-0ubuntu0.14.04.1
-- PHP Version: 7.0.33-0ubuntu0.16.04.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql12815923`
--
CREATE DATABASE IF NOT EXISTS `sql12815923` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sql12815923`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `cover_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `price`, `description`, `cover_image`, `category_id`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, '9780515123443', 'Killing Floor', 'Lee Child', '9.99', 'Drifter Jack Reacher is arrested for murder in Margrave, Georgia, less than an hour after arriving. He must navigate a town where he stands no chance of convincing anyone of his innocence to find the real killer.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1399313258i/78129.jpg', 9, 45, '2026-02-01 10:21:57', NULL),
(2, '9780060740226', 'Shakespeare: The World as Stage', 'Bill Bryson', '14.95', 'Bill Bryson brings his curiosity and wit to the life of William Shakespeare, offering a refreshing take on the elusive literary master. This biography explores the man behind the plays with the same freedom of spirit found in Bryson\'s travelogues.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1434075816i/135611.jpg', 5, 30, '2026-02-01 10:21:57', NULL),
(3, '9781571782522', 'Behind the Bell', 'Dustin Diamond', '16.99', 'Dustin Diamond, known as Screech, reveals the dark, behind-the-scenes story of the Saved by the Bell cast\'s extreme lifestyle. He shares his personal struggles with typecasting and his attempts to reinvent himself after childhood stardom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1349057755i/6684320.jpg', 2, 25, '2026-02-01 10:21:57', NULL),
(4, '9781908476693', 'Victory or Violence - The Story of the AWB of South Africa', 'Arthur Kemp', '19.50', 'This book details the history of the Afrikaner Weerstandsbeweging (AWB) and its leader Eugene Terre\'Blanche during the end of Apartheid. It covers the organization\'s campaign of violence and bombing in a largely eyewitness account.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347633268i/3661838.jpg', 5, 20, '2026-02-01 10:21:57', NULL),
(5, '9781933929690', 'Mother Puncher', 'Gina Ranalli', '11.95', 'In an overpopulated world where procreation is discouraged, Ed Means works for the government as a licensed \"Mother Puncher.\" Though he doesn\'t relish the job, he serves his country by enforcing its strict population control methods.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1348980396i/3928180.jpg', 1, 15, '2026-02-01 10:21:57', NULL),
(6, '9780132126953', 'Computer Networks', 'Andrew S. Tanenbaum', '64.99', 'This classic textbook explains the inner workings of computer networks, from physical hardware to popular applications. It covers modern technologies like wireless broadband, Bluetooth, and quantum cryptography using real-world examples.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347462821i/166190.jpg', 4, 34, '2026-02-01 10:21:57', NULL),
(7, '9780826412768', 'Pedagogy of the Oppressed', 'Paulo Freire', '18.95', 'Paulo Freire\'s seminal work outlines a methodology to empower the impoverished and illiterate through education. This anniversary edition includes an introduction on the book\'s lasting impact on educators and social justice movements.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388180018i/72657.jpg', 2, 40, '2026-02-01 10:21:57', NULL),
(8, '9780743243773', 'Teacher Man', 'Frank McCourt', '15.00', 'Frank McCourt reflects on his thirty-year teaching career in New York City high schools, which shaped his second act as a writer. He recounts the trials and triumphs of engaging unruly adolescents using unconventional methods.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1441149612i/4909.jpg', 2, 30, '2026-02-01 10:21:57', NULL),
(9, '9780853300137', 'Discipleship in the New Age II', 'Alice A. Bailey', '22.00', 'This volume continues the personal and group instructions given to aspirants on meditation, initiation, and the six stages of discipleship. It emphasizes the need for group consciousness and the \"anchoring\" of new civilization principles for the Aquarian era.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1189842181i/1890070.jpg', 7, 17, '2026-02-01 10:21:57', NULL),
(10, '9788416327340', 'El día que dejó de nevar en Alaska', 'Alice Kellen', '12.50', 'Heather flees to a small town in Alaska to start over, where she meets Nilak, a cold and reserved restaurant owner. As she discovers the memories hiding behind his distance, both find a second chance at life and love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1501600291i/33846385.jpg', 10, 34, '2026-02-01 10:21:57', NULL),
(11, '9780571068876', 'Pincher Martin', 'William Golding', '13.99', 'Stranded on a grotesque rock in the North Atlantic, Christopher Hadley Martin struggles to survive with only his own mind for company. He must piece together the terrifying truth of his fate in this journey into one man\'s psyche.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1458386642i/845721.jpg', 1, 28, '2026-02-01 10:21:57', NULL),
(12, '9781913076130', 'The Grim Reaper', 'William Hanna', '14.99', 'Freelance correspondent Mike Walker exposes the crimes of Private Military and Security Companies operating in war zones. When he attracts the attention of a British PMSC and MI5, he must fight to survive and keep reporting the truth.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1586297372i/52445828.jpg', 9, 32, '2026-02-01 10:21:57', NULL),
(13, '9780593296376', 'Upgrade', 'Blake Crouch', '28.00', 'Logan Ramsay\'s genome is hacked, giving him enhanced mental and physical abilities that are part of a dangerous plan for human evolution. He must use his new powers to stop a terrifying transformation that threatens humanity at large.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1643994317i/59838811.jpg', 1, 40, '2026-02-01 10:21:57', NULL),
(14, '9780060773750', 'A Quick Bite', 'Lynsay Sands', '7.99', 'Vampire Lissianna Argeneau finds a handsome man tied to her bed, but her tendency to faint at the sight of blood complicates things. Dr. Gregory Hewitt must decide if he can find true love with a vampire vixen or if he is just a meal.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1410129015i/38568.jpg', 10, 48, '2026-02-01 10:21:57', NULL),
(15, '9780007162494', 'The Summer Garden', 'Paullina Simons', '16.95', 'Reunited in America after years of war, Tatiana and Alexander must build a new life with their son while facing the rising Cold War. They struggle to heal the lingering pain of the past to prevent their history from destroying their future.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1411993309i/608216.jpg', 10, 38, '2026-02-01 10:21:57', NULL),
(16, '9780199537396', 'The Italian', 'Ann Radcliffe', '11.95', 'Vincentio di Vivaldi falls for the mysterious Ellena, but his mother and a demonic monk named Schedoni plot to separate them. This Gothic romance features abduction, torture, and supernatural horror amidst sublime landscapes.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1668768515i/93136.jpg', 1, 22, '2026-02-01 10:21:57', NULL),
(17, '9780060853983', 'Good Omens', 'Terry Pratchett & Neil Gaiman', '10.99', 'As the apocalypse approaches, a somewhat fussy angel and a fast-living demon team up to prevent the Rapture because they enjoy life on Earth too much. Complicating matters, they seem to have misplaced the Antichrist.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1615552073i/12067.jpg', 1, 55, '2026-02-01 10:21:57', NULL),
(18, '9780439598514', 'The Haunting of Alaizabel Cray', 'Chris Wooding', '9.99', 'Wych-hunter Thaniel and his mentor Cathaline track creatures in London\'s Old Quarter when they encounter the possessed Alaizabel Cray. They must discover what evil entity has turned her into a magnet for horrors before it destroys them all.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1328867929i/140260.jpg', 1, 26, '2026-02-01 10:21:57', NULL),
(19, '9780375703768', 'House of Leaves', 'Mark Z. Danielewski', '21.00', 'A family moves into a home that is impossibly bigger on the inside than the outside, leading to a terrifying exploration of a dark abyss. As they investigate, an unholy growl threatens to consume their sanity and dreams.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1403889034i/24800.jpg', 1, 30, '2026-02-01 10:21:57', NULL),
(20, '9780763653309', 'Finnikin of the Rock', 'Melina Marchetta', '12.99', 'Finnikin is summoned by a young woman named Evanjalin who claims the heir to the cursed kingdom of Lumatere is still alive. As they journey to reunite their people, Finnikin must test his faith in Evanjalin and the truth she hides.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1346007613i/4932435.jpg', 1, 42, '2026-02-01 10:21:57', NULL),
(21, '9780062654199', 'The Alice Network', 'Kate Quinn', '16.99', 'In 1947, a pregnant American socialite searching for her cousin teams up with a former WWI spy haunted by the betrayal of her network. Together, they launch a mission to uncover the truth about the \"Alice Network\" and what happened in occupied France.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1492238040i/32051912.jpg', 1, 48, '2026-02-01 10:21:57', NULL),
(22, '9781912555629', 'Tooth for Tooth', 'J.K. Franko', '13.50', 'Susie and Roy thought they committed the perfect murder, but a loose end threatens to destroy their flawless crime. As enemies multiply and the hunters become the hunted, their world begins to crumble.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1583417175i/52117056.jpg', 9, 34, '2026-02-01 10:21:57', NULL),
(23, '9780802808684', 'God in the Dock', 'C.S. Lewis', '14.99', 'This collection of essays by C.S. Lewis covers topics ranging from \"Myth Become Fact\" to \"The Grand Miracle.\" It offers theological insights and arguments on Christianity, ethics, and the modern world.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1437542039i/22051585.jpg', 2, 35, '2026-02-01 10:21:57', NULL),
(24, '9781609809627', 'I Who Have Never Known Men', 'Jacqueline Harpman', '16.95', 'A young woman raised in an underground cage with thirty-nine others is suddenly freed into a ravaged, desolate world. Having never known men or her own history, she must reinvent herself while facing the terrifying challenge of freedom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1636235968i/11996.jpg', 1, 24, '2026-02-01 10:21:57', NULL),
(25, '9780312577995', 'Foretold', 'Jana Oliver', '10.99', 'Riley and Beck face a final battle for humanity as a dark secret in Beck\'s hometown threatens to drive them apart. Meanwhile, a brutal war with the prince of Hell looms, and they must defeat their old enemy or lose everything.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1333737125i/13487029.jpg', 1, 29, '2026-02-01 10:21:57', NULL),
(26, '9781444951387', 'Heartstopper: Volume 1', 'Alice Oseman', '14.99', 'Charlie and Nick meet at a British all-boys grammar school and quickly become friends, though Charlie wonders if there could be something more. This graphic novel explores their blooming relationship as they navigate school and young love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1652212196i/58027863.jpg', 10, 59, '2026-02-01 10:21:57', NULL),
(27, '9780445003668', 'Joanne, The Unpredictable', 'Katheryn Kimbrough', '6.50', 'Joanne arrives at Merrihew Manor and uses her beauty to manipulate the men around her, treating life like a romance. However, she soon discovers a satanic force ruling the estate that her guile cannot deceive.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1341850363i/11076322.jpg', 10, 18, '2026-02-01 10:21:57', NULL),
(28, '9780195663365', 'Godaan', 'Munshi Premchand', '12.95', 'In this classic Hindi novel, a poor farmer named Hori struggles to own a cow, a symbol of wealth and salvation. The story serves as a moving document of peasant life and social conflict in pre-independence India.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1205467570i/694226.jpg', 1, 25, '2026-02-01 10:21:57', NULL),
(29, '9780452267459', 'Baghdad without a Map', 'Tony Horwitz', '16.00', 'Journalist Tony Horwitz travels through the Middle East, offering a witty and insightful look at the region beyond the headlines. From Yemeni wilds to Saddam\'s Iraq, he recounts misadventures where the ancient and modern collide.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1168030064i/29446.jpg', 2, 31, '2026-02-01 10:21:57', NULL),
(30, '9780140442354', 'Either/Or: A Fragment of Life', 'Søren Kierkegaard', '18.00', 'Kierkegaard explores the conflict between the aesthetic and ethical ways of life through two distinct voices. This philosophical masterpiece contemplates subjects like boredom, seduction, and the search for a meaningful existence.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1393892756i/24970.jpg', 2, 25, '2026-02-01 10:21:57', NULL),
(31, '9780156605175', 'The Misanthrope', 'Molière', '10.95', 'Molière\'s comedy satirizes the hypocrisies of French aristocratic society through the character of Alceste, who criticizes others\' faults while ignoring his own. The play focuses on character nuances and the universal nature of misanthropy.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1416873266i/752994.jpg', 1, 33, '2026-02-01 10:21:57', NULL),
(32, '9780786868711', 'The Five People You Meet in Heaven', 'Mitch Albom', '15.99', 'After dying in a tragic accident, Eddie awakes in the afterlife to find that heaven is a place where five people explain his life to him. These encounters reveal the unexpected connections and meaning behind his seemingly uninspired existence.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388200541i/3431.jpg', 1, 52, '2026-02-01 10:21:57', NULL),
(33, '9780451531063', 'Main Street', 'Sinclair Lewis', '9.95', 'This novel tells the story of an idealistic young woman who attempts to reform her small town, only to face suffocation and spiritual discomfort. It is a critical look at American small-town life and the struggle for intellectual freedom.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1308953459i/11376.jpg', 1, 28, '2026-02-01 10:21:57', NULL),
(34, '9780020446507', 'Z for Zachariah', 'Robert C. O\'Brien', '8.99', 'Sixteen-year-old Ann Burden believes she is the last survivor of a nuclear war until a stranger arrives in her valley. She must decide if this man is a companion to be trusted or a threat worse than solitude.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1420324231i/69477.jpg', 1, 37, '2026-02-01 10:21:57', NULL),
(35, '9781602602331', 'Santa\'s Angels', 'Janet Kaderli', '12.99', 'Celebrity Nick Klaus hides out as a department store Santa, where he falls for photographer Janie Langston. As the Santa suit changes his priorities, he must keep his identity secret while hoping for a chance at love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1356111065i/4748437.jpg', 10, 44, '2026-02-01 10:21:57', NULL),
(36, '9780553106633', 'A Storm of Swords', 'George R.R. Martin', '30.00', 'The War for the Iron Throne rages as the Seven Kingdoms face revolt, blood feuds, and a savage invasion from the north. While Robb Stark defends his crown, dark forces gather beyond the Wall, threatening everyone.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1353235205i/768889.jpg', 1, 62, '2026-02-01 10:21:57', NULL),
(37, '9781101947135', 'Homegoing', 'Yaa Gyasi', '16.00', 'This novel traces the descendants of two half-sisters born in eighteenth-century Ghana: one marries an Englishman, while the other is sold into slavery. The story follows their lineages through centuries of history, from African warfare to the American jazz age.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1448108591i/27071490.jpg', 1, 46, '2026-02-01 10:21:57', NULL),
(38, '9780553381900', 'My Losing Season', 'Pat Conroy', '17.00', 'Pat Conroy recounts his senior year of basketball at The Citadel, realizing that his losing season was the most consequential of his life. He explores his relationship with his harsh father and how the trials of sport shaped him as a writer.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1333578751i/119216.jpg', 2, 29, '2026-02-01 10:21:57', NULL),
(39, '9781523775831', 'RoomHate', 'Penelope Ward', '13.99', 'A woman inherits half a summer house, only to find the other half belongs to Justin, the man who broke her heart years ago. Forced to live together, they realize their connection is still alive, blurring the line between love and hate.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1453991440i/27083865.jpg', 10, 41, '2026-02-01 10:21:57', NULL),
(40, '9780307356222', 'The Golden Mean', 'Annabel Lyon', '15.95', 'Aristotle is summoned to tutor King Philip\'s son, the future Alexander the Great, and struggles to instill the \"golden mean\" of balance in the young conqueror. The novel explores the relationship between the philosopher and the warrior prince in earthy, muscular prose.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320496914i/6396579.jpg', 1, 23, '2026-02-01 10:21:57', NULL),
(41, '9781501142970', 'It', 'Stephen King', '19.99', 'In Derry, Maine, a shapeshifting entity preys on children by exploiting their deepest fears. Years later, the survivors are called back to confront the horror of their past that has returned to kill again.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1309376909i/18342.jpg', 1, 58, '2026-02-01 10:21:57', NULL),
(42, '9780307277190', 'Traffic', 'Tom Vanderbilt', '16.95', 'Tom Vanderbilt explores the physical, psychological, and technical factors that explain how traffic works and what it says about human nature. The book reveals surprising dynamics, such as why sunny days see more crashes and how road rage can be interpreted.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320428955i/2776527.jpg', 2, 32, '2026-02-01 10:21:57', NULL),
(43, '9780340794500', 'The Cosmic Ordering Service', 'Barbel Mohr', '11.99', 'Barbel Mohr guides readers on how to fulfill their wishes by \"placing an order\" with the universe. The book teaches how to listen to one\'s inner voice and decide what is truly wanted to achieve a more fulfilling life.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1347955270i/737825.jpg', 7, 27, '2026-02-01 10:21:57', NULL),
(44, '9781601090560', 'The Journey Home', 'Radhanath Swami', '18.99', 'Radhanath Swami shares his transformation from a young seeker in Chicago to a renowned spiritual guide in India. The memoir details his travels, apprenticeships with yogis, and the discovery of inner harmony and divine love.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1328744836i/7013431.jpg', 2, 35, '2026-02-01 10:21:57', NULL),
(45, '9780140233907', 'Paddy Clarke Ha Ha Ha', 'Roddy Doyle', '14.00', 'Set in 1960s Dublin, ten-year-old Paddy Clarke navigates the rough streets of Barrytown while his parents\' marriage crumbles at home. As his family life deteriorates, the once mischievous boy becomes isolated and vulnerable.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1168077668i/30512.jpg', 1, 26, '2026-02-01 10:21:57', NULL),
(46, '9780679722670', 'A Pale View of Hills', 'Kazuo Ishiguro', '15.95', 'Etsuko, a Japanese woman living in England, relives a summer in post-war Nagasaki following her daughter\'s suicide. Her memories of a strange friendship with a woman named Sachiko take on a disturbing and haunting cast.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1348339374i/28920.jpg', 1, 26, '2026-02-01 10:21:57', NULL),
(47, '9780062118462', 'Sweet Reckoning', 'Wendy Higgins', '10.99', 'Anna Whitt and her Nephilim allies must rid the earth of demons while facing the ultimate evil. As the stakes rise, Anna has to decide how much she is willing to risk, including her love for Kaidan Rowe.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1381492410i/16007855.jpg', 1, 39, '2026-02-01 10:21:57', NULL),
(48, '9780375703423', 'Family Matters', 'Rohinton Mistry', '17.00', 'In Bombay, an elderly man with Parkinson\'s is forced to move in with his daughter\'s crowded family, testing their resources and compassion. The novel portrays the domestic drama and corruption of the city through the lens of one family\'s struggle.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1388279746i/19661.jpg', 1, 27, '2026-02-01 10:21:57', NULL),
(49, '9781250223180', 'What Happened To You?', 'Bruce D. Perry', '28.99', 'Oprah Winfrey and Dr. Bruce Perry discuss the impact of trauma and how shifting the question from \"What\'s wrong with you?\" to \"What happened to you?\" can lead to healing. They offer scientific insights and personal stories to help reshape our understanding of behavior.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1653552093i/53238858.jpg', 7, 49, '2026-02-01 10:21:57', NULL),
(50, '9780307353467', 'Guilty', 'Ann Coulter', '27.95', 'Ann Coulter argues that liberals victimize their opponents while claiming to be victims themselves. The book presents various cases to support her thesis that the political Left engages in bullying under the guise of compassion.', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1320400867i/6076107.jpg', 2, 21, '2026-02-01 10:21:57', NULL),
(51, '9780393330123', 'Test Book', 'Yuan Mariano', '1000.00', 'This is a test book', 'https://png.pngtree.com/png-clipart/20230804/original/pngtree-test-orange-icon-sign-test-testing-vector-picture-image_9551097.png', 8, 5, '2026-02-01 13:10:05', NULL),
(52, '9780385537999', 'Magical Journey Test Book 123', 'Tester Admin Yuan', '99.99', 'This is not a real book, this book is just for testing.', 'https://play-lh.googleusercontent.com/VCO9uJT7uUkjy4ei73ch-4h7IS5vsy96A7QYePp4L5FURGPWsvw8Wss6WHgiMta5wETQ', 3, 4, '2026-02-04 13:51:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `session_id`, `book_id`, `quantity`, `created_at`) VALUES
(14, 9, NULL, 14, 1, '2026-02-01 12:43:00'),
(17, 8, NULL, 46, 1, '2026-02-04 01:16:42');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Fiction', 'fiction', 'Fictional novels and stories', '2026-02-01 10:21:53'),
(2, 'Non-Fiction', 'non-fiction', 'Non-fictional books and biographies', '2026-02-01 10:21:53'),
(3, 'Science', 'science', 'Scientific books and research', '2026-02-01 10:21:53'),
(4, 'Technology', 'technology', 'Technology and computer science books', '2026-02-01 10:21:53'),
(5, 'History', 'history', 'Historical books and references', '2026-02-01 10:21:53'),
(6, 'Business', 'business', 'Business and economics books', '2026-02-01 10:21:53'),
(7, 'Self-Help', 'self-help', 'Self-improvement and motivational books', '2026-02-01 10:21:53'),
(8, 'Children', 'children', 'Children and young adult books', '2026-02-01 10:21:53'),
(9, 'Mystery', 'mystery', 'Mystery and thriller novels', '2026-02-01 10:21:53'),
(10, 'Romance', 'romance', 'Romance novels', '2026-02-01 10:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL,
  `status` enum('Processing','Shipped','Delivered','Cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Processing',
  `shipping_address` text COLLATE utf8_unicode_ci NOT NULL,
  `shipping_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_zip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `tax_amount`, `grand_total`, `status`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_zip`, `shipping_country`, `payment_method`, `transaction_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 9, 'ORD-697F39BFD62D2-3940', '56.94', '4.84', '61.78', 'Processing', 'Dampalit, Malabon, Metro Manila, 1480, Philippines', 'Malabon', 'Metro Manila', '1480', 'Philippines', 'Visa ending in 0366', 'TXN-697F39BF77BE4-55070', NULL, '2026-02-01 11:33:53', NULL),
(2, 8, 'ORD-697F39F784AD5-9451', '31.90', '2.71', '34.61', 'Processing', '123123, 123123, 123123, 123123, USA', '123123', '123123', '123123', 'USA', 'Visa ending in 0366', 'TXN-697F39F78479E-60575', NULL, '2026-02-01 11:34:49', NULL),
(5, 9, 'ORD-697F470E3AD3E-1060', '86.99', '7.39', '94.38', 'Processing', 'Dampalit, Malabon, Metro Manila, 1234, Philippines', 'Malabon', 'Metro Manila', '1234', 'Philippines', 'Visa ending in 0366', 'TXN-697F470E3A9F2-81397', NULL, '2026-02-01 12:30:39', NULL),
(6, 9, 'ORD-697F489296654-8995', '57.49', '4.89', '62.38', 'Delivered', 'Dampalit, Malabon, Metro Manila, 1357, Philippines', 'Malabon', 'Metro Manila', '1357', 'Philippines', 'Visa ending in 0366', 'TXN-697F48929624C-47909', '', '2026-02-01 12:37:08', NULL),
(7, 8, 'ORD-697F6232AB2A7-9478', '45.95', '3.91', '49.86', 'Processing', '123123, 123123, 123123, 123123, USA', '123123', '123123', '123123', 'USA', 'Visa ending in 0366', 'TXN-697F6232A890A-31061', NULL, '2026-02-01 14:26:28', NULL),
(8, 12, 'ORD-697F62E1223C0-5389', '7.99', '0.68', '8.67', 'Processing', 'abc 123, abc 123, abc 123, 1123, USA', 'abc 123', 'abc 123', '1123', 'USA', 'Visa ending in 0366', 'TXN-697F62E122097-91831', NULL, '2026-02-01 14:29:22', NULL),
(9, 14, 'ORD-69835013963C8-3417', '57.89', '4.92', '62.81', 'Processing', '456 User Ave, Los Angeles, Metro Manila, 1234, Philippines', 'Los Angeles', 'Metro Manila', '1234', 'Philippines', 'Visa ending in 0366', 'TXN-6983501393454-15588', NULL, '2026-02-04 13:58:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 49, 1, '28.99'),
(2, 1, 50, 1, '27.95'),
(3, 2, 46, 2, '15.95'),
(8, 5, 6, 1, '64.99'),
(9, 5, 9, 1, '22.00'),
(10, 6, 36, 1, '30.00'),
(11, 6, 10, 1, '12.50'),
(12, 6, 23, 1, '14.99'),
(13, 7, 36, 1, '30.00'),
(14, 7, 46, 1, '15.95'),
(15, 8, 14, 1, '7.99'),
(16, 9, 48, 1, '17.00'),
(17, 9, 28, 2, '12.95'),
(18, 9, 26, 1, '14.99');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'USA',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `phone`, `address`, `city`, `state`, `zip_code`, `country`, `created_at`, `updated_at`) VALUES
(7, 'admin@bookstore.com', '$2y$12$BR06wo/ta4CO6Ng756XwRumJKcNEZ.wqyS2/UKXdKI95mLar9GQO2', 'Admin', 'User', 'admin', NULL, NULL, NULL, NULL, NULL, 'USA', '2026-02-01 11:13:11', NULL),
(8, 'user@bookstore.com', '$2y$12$GhrIMjlbv4gl5mAB6/.Gjef43Zu9V2mh6etHvhgO12xcVvWMu1hEO', 'Test', 'User', 'user', '123123', '123123', '123123', '123123', '123123', 'USA', '2026-02-01 11:13:12', NULL),
(9, 'aliciavilale@gmail.com', '$2y$12$pnGQU2TdH6jjI9lem9iOMO2ysZPinxWfiVCTQ3/VGVxiWu5rXSQwO', 'Alicia Jhyle', 'Vilale', 'user', '09982323352', 'Dampalit', 'Malabon', 'Metro Manila', '1357', 'Philippines', '2026-02-01 11:23:08', NULL),
(12, 'ravisij345@okexbit.com', '$2y$12$6X.bEdfVVTt/tRDK7p8bcOuWQY9F8LJu4HSz0.N/9pSVnByQz9jFC', 'Abc', 'Def', 'user', '11245678', '', '', '', '', 'USA', '2026-02-01 14:28:32', NULL),
(14, 'yuanandreim09@gmail.com', '$2y$12$DJ/bkxBCKuEeOtQvMzOl1.tAPrdnc5m8kUnq43ga0Poi0Na/G8bmS', 'Yuan', 'Mariano', 'user', '09761660123', '', '', '', '', 'USA', '2026-02-04 13:55:07', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_isbn` (`isbn`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_author` (`author`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
