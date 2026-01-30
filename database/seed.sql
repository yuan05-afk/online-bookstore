-- Online Bookstore System - Seed Data
-- Initial data population

-- Insert categories
INSERT INTO categories (name, slug, description) VALUES
('Fiction', 'fiction', 'Fictional novels and stories'),
('Non-Fiction', 'non-fiction', 'Non-fictional books and biographies'),
('Science', 'science', 'Scientific books and research'),
('Technology', 'technology', 'Technology and computer science books'),
('History', 'history', 'Historical books and references'),
('Business', 'business', 'Business and economics books'),
('Self-Help', 'self-help', 'Self-improvement and motivational books'),
('Children', 'children', 'Children and young adult books'),
('Mystery', 'mystery', 'Mystery and thriller novels'),
('Romance', 'romance', 'Romance novels');

-- Insert admin user (password: admin123)
-- Password hash generated with: password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('admin@bookstore.com', '$2y$12$aBhTIGuTM65R4MIMvciIPONqMItOPMVdI0nohLmIWHie4zo3H3SeOS', 'Admin', 'User', 'admin', '555-0100', '123 Admin St', 'New York', 'NY', '10001');

-- Insert test user (password: user123)
-- Password hash generated with: password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO users (email, password_hash, first_name, last_name, role, phone, address, city, state, zip_code) VALUES
('user@bookstore.com', '$2y$12$VZL5zI6Pq5REgT1.Y71Zu.NqMItOPMVdI0nohLmIWHie4zo3H3SeOS', 'Test', 'User', 'user', '555-0200', '456 User Ave', 'Los Angeles', 'CA', '90001');

-- Insert 50 books from Kaggle dataset
-- Note: These are sample books. Replace with actual dataset when available.
INSERT INTO books (isbn, title, author, price, description, cover_image, category_id, stock_quantity) VALUES
('9780141439518', 'Pride and Prejudice', 'Jane Austen', 12.99, 'A classic novel of manners and romance in Regency England.', 'pride-prejudice.jpg', 1, 50),
('9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 14.99, 'A gripping tale of racial injustice and childhood innocence.', 'mockingbird.jpg', 1, 45),
('9780451524935', '1984', 'George Orwell', 13.99, 'A dystopian social science fiction novel and cautionary tale.', '1984.jpg', 1, 60),
('9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 11.99, 'A novel about the American dream in the Roaring Twenties.', 'gatsby.jpg', 1, 40),
('9780316769488', 'The Catcher in the Rye', 'J.D. Salinger', 13.49, 'A story about teenage rebellion and alienation.', 'catcher.jpg', 1, 35),
('9780060935467', 'To the Lighthouse', 'Virginia Woolf', 12.49, 'A landmark novel of high modernism.', 'lighthouse.jpg', 1, 30),
('9780140449136', 'The Odyssey', 'Homer', 15.99, 'An ancient Greek epic poem attributed to Homer.', 'odyssey.jpg', 1, 25),
('9780307277671', 'The Road', 'Cormac McCarthy', 14.49, 'A post-apocalyptic novel of a father and son.', 'road.jpg', 1, 40),
('9780062315007', 'The Alchemist', 'Paulo Coelho', 13.99, 'A philosophical book about following your dreams.', 'alchemist.jpg', 1, 55),
('9780544003415', 'The Lord of the Rings', 'J.R.R. Tolkien', 29.99, 'An epic high-fantasy novel trilogy.', 'lotr.jpg', 1, 50),

('9780307887894', 'Sapiens', 'Yuval Noah Harari', 18.99, 'A brief history of humankind from the Stone Age to modern times.', 'sapiens.jpg', 2, 60),
('9780062316097', 'Educated', 'Tara Westover', 16.99, 'A memoir about a woman who grows up in a survivalist family.', 'educated.jpg', 2, 45),
('9781501110368', 'When Breath Becomes Air', 'Paul Kalanithi', 15.49, 'A memoir by a neurosurgeon facing terminal cancer.', 'breath.jpg', 2, 35),
('9780385537858', 'Becoming', 'Michelle Obama', 19.99, 'A memoir by the former First Lady of the United States.', 'becoming.jpg', 2, 70),
('9780735211292', 'Atomic Habits', 'James Clear', 16.99, 'An easy and proven way to build good habits and break bad ones.', 'atomic-habits.jpg', 7, 80),
('9781501156847', 'Born a Crime', 'Trevor Noah', 17.49, 'Stories from a South African childhood.', 'born-crime.jpg', 2, 40),
('9780062457714', 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', 15.99, 'A counterintuitive approach to living a good life.', 'subtle-art.jpg', 7, 65),
('9780143127741', 'Thinking, Fast and Slow', 'Daniel Kahneman', 18.49, 'A book about the two systems that drive the way we think.', 'thinking.jpg', 2, 50),
('9780062820235', 'Dare to Lead', 'Brené Brown', 16.49, 'Brave work, tough conversations, whole hearts.', 'dare-lead.jpg', 6, 45),
('9780812981605', 'Quiet', 'Susan Cain', 15.99, 'The power of introverts in a world that cant stop talking.', 'quiet.jpg', 7, 40),

('9780393356687', 'A Brief History of Time', 'Stephen Hawking', 17.99, 'From the Big Bang to black holes.', 'brief-time.jpg', 3, 35),
('9780385537209', 'The Gene', 'Siddhartha Mukherjee', 19.49, 'An intimate history of genetics and genomics.', 'gene.jpg', 3, 30),
('9780385509862', 'The Elegant Universe', 'Brian Greene', 18.99, 'Superstrings, hidden dimensions, and the quest for ultimate theory.', 'elegant.jpg', 3, 25),
('9780393339918', 'The Origin of Species', 'Charles Darwin', 16.99, 'The foundation of evolutionary biology.', 'origin.jpg', 3, 30),
('9780393330403', 'Cosmos', 'Carl Sagan', 17.49, 'A personal voyage through the universe.', 'cosmos.jpg', 3, 40),
('9780393347777', 'Astrophysics for People in a Hurry', 'Neil deGrasse Tyson', 14.99, 'The universe explained in clear and accessible language.', 'astrophysics.jpg', 3, 55),
('9780062316110', 'The Immortal Life of Henrietta Lacks', 'Rebecca Skloot', 16.99, 'The story of HeLa cells and medical ethics.', 'henrietta.jpg', 3, 35),
('9780393609394', 'The Body', 'Bill Bryson', 18.99, 'A guide for occupants of the human body.', 'body.jpg', 3, 40),
('9780393354324', 'Sapiens: A Graphic History', 'Yuval Noah Harari', 22.99, 'The birth of humankind in graphic novel form.', 'sapiens-graphic.jpg', 3, 30),
('9780062464347', 'Lab Girl', 'Hope Jahren', 15.99, 'A memoir by a geobiologist about life and science.', 'lab-girl.jpg', 3, 25),

('9780134685991', 'Effective Java', 'Joshua Bloch', 49.99, 'Best practices for the Java platform.', 'effective-java.jpg', 4, 40),
('9780135957059', 'The Pragmatic Programmer', 'David Thomas', 44.99, 'Your journey to mastery in software development.', 'pragmatic.jpg', 4, 50),
('9780132350884', 'Clean Code', 'Robert C. Martin', 42.99, 'A handbook of agile software craftsmanship.', 'clean-code.jpg', 4, 60),
('9780201633610', 'Design Patterns', 'Gang of Four', 54.99, 'Elements of reusable object-oriented software.', 'design-patterns.jpg', 4, 35),
('9780137081073', 'The Clean Coder', 'Robert C. Martin', 39.99, 'A code of conduct for professional programmers.', 'clean-coder.jpg', 4, 45),
('9781449355739', 'Designing Data-Intensive Applications', 'Martin Kleppmann', 59.99, 'The big ideas behind reliable, scalable systems.', 'data-intensive.jpg', 4, 40),
('9780596517748', 'JavaScript: The Good Parts', 'Douglas Crockford', 29.99, 'Unearthing the excellence in JavaScript.', 'js-good-parts.jpg', 4, 50),
('9781491950296', 'Building Microservices', 'Sam Newman', 49.99, 'Designing fine-grained systems.', 'microservices.jpg', 4, 35),
('9780134494166', 'Clean Architecture', 'Robert C. Martin', 44.99, 'A craftsmans guide to software structure.', 'clean-architecture.jpg', 4, 40),
('9781617294136', 'Grokking Algorithms', 'Aditya Bhargava', 34.99, 'An illustrated guide for programmers.', 'grokking.jpg', 4, 55),

('9780743264730', 'Team of Rivals', 'Doris Kearns Goodwin', 21.99, 'The political genius of Abraham Lincoln.', 'team-rivals.jpg', 5, 30),
('9780679783268', 'The Guns of August', 'Barbara W. Tuchman', 19.99, 'A history of the first month of World War I.', 'guns-august.jpg', 5, 25),
('9780385495226', 'Unbroken', 'Laura Hillenbrand', 17.99, 'A World War II story of survival and resilience.', 'unbroken.jpg', 5, 40),
('9780385537865', 'The Wright Brothers', 'David McCullough', 18.99, 'The dramatic story of the pioneers of flight.', 'wright.jpg', 5, 30),
('9780743273558', 'John Adams', 'David McCullough', 20.99, 'A biography of the second U.S. president.', 'john-adams.jpg', 5, 25),
('9780812974492', 'Alexander Hamilton', 'Ron Chernow', 22.99, 'The biography that inspired the musical.', 'hamilton.jpg', 5, 50),
('9780679764830', 'A People\'s History of the United States', 'Howard Zinn', 19.49, 'American history from the perspective of ordinary people.', 'peoples-history.jpg', 5, 35),
('9780385537070', '1776', 'David McCullough', 18.49, 'The year of American independence.', 'seventeen76.jpg', 5, 30),
('9780812980660', 'The Warmth of Other Suns', 'Isabel Wilkerson', 19.99, 'The epic story of Americas great migration.', 'warmth-suns.jpg', 5, 28),
('9780385348713', 'The Splendid and the Vile', 'Erik Larson', 21.49, 'Churchill, family, and defiance during the Blitz.', 'splendid-vile.jpg', 5, 32);
