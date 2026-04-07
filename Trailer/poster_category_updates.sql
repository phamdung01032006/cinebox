START TRANSACTION;

-- Spider-Man: No Way Home
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (100);

-- The Batman
-- TMDB genres: Crime, Mystery, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (101);

-- No Exit
-- TMDB genres: Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (102);

-- Encanto
-- TMDB genres: Animation, Comedy, Family, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (103);

-- The King's Man
-- TMDB genres: Action, Adventure, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (104);

-- The Commando
-- TMDB genres: Action, Crime, Thriller
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (105);

-- Scream
-- TMDB genres: Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (106);

-- Kimi
-- TMDB genres: Thriller, Mystery, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (107);

-- Fistful of Vengeance
-- TMDB genres: Action, Fantasy, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (108);

-- Eternals
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (109);

-- Pursuit
-- TMDB genres: Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (110);

-- My Hero Academia: World Heroes' Mission
-- TMDB genres: Animation, Action, Adventure, Science Fiction
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (111);

-- Restless
-- TMDB genres: Action, Thriller, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (112);

-- Nightmare Alley
-- TMDB genres: Crime, Drama, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (113);

-- The Ice Age Adventures of Buck Wild
-- TMDB genres: Animation, Comedy, Family, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (114);

-- Hotel Transylvania: Transformania
-- TMDB genres: Animation, Comedy, Family, Adventure, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (115);

-- Texas Chainsaw Massacre
-- TMDB genres: Horror, Crime, Drama
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (116);

-- The Requin
-- TMDB genres: Horror, Mystery, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (117);

-- Looop Lapeta
-- TMDB genres: Action, Comedy, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (118);

-- Red Notice
-- TMDB genres: Action, Comedy, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (119);

-- Sing 2
-- TMDB genres: Animation, Music, Comedy, Family
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (120);

-- The Jack in the Box: Awakening
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (121);

-- Venom: Let There Be Carnage
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (122);

-- The Matrix Resurrections
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (123);

-- Resident Evil: Welcome to Raccoon City
-- TMDB genres: Action, Horror, Science Fiction
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (124);

-- Last Man Down
-- TMDB genres: Action, Thriller, Mystery
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (125);

-- American Siege
-- TMDB genres: Action, Adventure, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (126);

-- Uncharted
-- TMDB genres: Documentary, Music
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (127);

-- Demon Slayer -Kimetsu no Yaiba- The Movie: Mugen Train
-- TMDB genres: Animation, Action, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (128);

-- Ghostbusters: Afterlife
-- TMDB genres: Fantasy, Comedy, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (129);

-- The 355
-- TMDB genres: Action, Adventure, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (130);

-- Shang-Chi and the Legend of the Ten Rings
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (131);

-- Marry Me
-- TMDB genres: Romance, Comedy, Music
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (132);

-- The Hunting
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (133);

-- West Side Story
-- TMDB genres: Drama, Romance, Crime
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (134);

-- Through My Window
-- TMDB genres: Romance, Drama, Comedy
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (135);

-- The Seven Deadly Sins: Cursed by Light
-- TMDB genres: Animation, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (136);

-- One Shot
-- TMDB genres: Action, Thriller, Crime, War
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (137);

-- Tom and Jerry: Cowboy Up!
-- TMDB genres: Animation, Comedy, Family, Western
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (138);

-- Chernobyl: Abyss
-- TMDB genres: Drama, History, Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (139);

-- Desperate Riders
-- TMDB genres: Western, Action, Drama
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (140);

-- Clifford the Big Red Dog
-- TMDB genres: Family, Adventure, Comedy, Fantasy
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (141);

-- Tyler Perry's A Madea Homecoming
-- TMDB genres: Comedy, Drama
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (142);

-- The Boss Baby: Family Business
-- TMDB genres: Animation, Comedy, Adventure, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (143);

-- Turning Red
-- TMDB genres: Animation, Family, Comedy, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (144);

-- Brazen
-- TMDB genres: Thriller, Mystery, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (145);

-- The House
-- TMDB genres: Animation, Drama, Comedy, Horror, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (146);

-- Blacklight
-- TMDB genres: Action, Thriller, Adventure
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (147);

-- Ron's Gone Wrong
-- TMDB genres: Animation, Science Fiction, Family, Comedy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (148);

-- Free Guy
-- TMDB genres: Comedy, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (149);

-- Cruella
-- TMDB genres: Comedy, Crime, Adventure
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (150);

-- The Suicide Squad
-- TMDB genres: Action, Comedy, Adventure
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (151);

-- The Simpsons in Plusaversary
-- TMDB genres: Animation, Comedy, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (152);

-- Forgive Us Our Trespasses
-- TMDB genres: Drama, History, Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (153);

-- Luca
-- TMDB genres: Animation
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (154);

-- Mortal Kombat
-- TMDB genres: Action, Fantasy, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (155);

-- Zack Snyder's Justice League
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (156);

-- Mother/Android
-- TMDB genres: Science Fiction, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (157);

-- Exploits of a Young Don Juan
-- TMDB genres: Comedy, Drama
-- Local category: Teen (12)
-- Mapping reason: Title, overview, or keywords indicate teen/high-school content
UPDATE entities
SET categoryId = 12
WHERE id IN (158);

-- Dune
-- TMDB genres: Science Fiction, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (159);

-- The Last Warrior: Root of Evil
-- TMDB genres: Fantasy, Adventure, Comedy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (160);

-- The Privilege
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (161);

-- Queen of Spades
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (162);

-- Antlers
-- TMDB genres: Drama, Horror, Mystery
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (163);

-- Dangerous
-- TMDB genres: Romance, Crime, Action, Thriller
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (164);

-- After We Fell
-- TMDB genres: Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (165);

-- No Time to Die
-- TMDB genres: Action, Thriller, Adventure
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (166);

-- Sooryavanshi
-- TMDB genres: Action, Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (167);

-- Finch
-- TMDB genres: Science Fiction, Drama, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (168);

-- Two
-- TMDB genres: Comedy, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (169);

-- PAW Patrol: The Movie
-- TMDB genres: Family, Comedy, Adventure, Animation
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (170);

-- Avengers: Infinity War
-- TMDB genres: Adventure, Action, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (171);

-- Batman
-- TMDB genres: Comedy
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (172);

-- Black Widow
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (173);

-- The Wonderful Winter of Mickey Mouse
-- TMDB genres: Animation
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (174);

-- F9
-- TMDB genres: Action, Adventure, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (175);

-- Moonfall
-- TMDB genres: Science Fiction, Adventure, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (176);

-- Sex, Shame and Tears 2
-- TMDB genres: Comedy, Drama
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (177);

-- Diary of a Wimpy Kid
-- TMDB genres: Animation, Comedy, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (178);

-- AI Love You
-- TMDB genres: Romance, Science Fiction, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (179);

-- Sonic the Hedgehog 2
-- TMDB genres: Action, Adventure, Family, Comedy
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (180);

-- Erax
-- TMDB genres: Mystery, Family
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (181);

-- Deathstroke: Knights & Dragons - The Movie
-- TMDB genres: Animation, Action, Adventure, Science Fiction
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (182);

-- The Last Duel
-- TMDB genres: History, Drama, Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (183);

-- The Pirates: The Last Royal Treasure
-- TMDB genres: Action, Adventure, Comedy, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (184);

-- Paranormal Activity: Next of Kin
-- TMDB genres: Horror, Mystery
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (185);

-- House of Gucci
-- TMDB genres: Drama, Crime, History
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (186);

-- Dark Spell
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (187);

-- Jungle Cruise
-- TMDB genres: Fantasy, Adventure, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (188);

-- Godzilla vs. Kong
-- TMDB genres: Action, Science Fiction, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (189);

-- The Amazing Spider-Man
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (190);

-- Heart Shot
-- TMDB genres: Romance, Crime
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (191);

-- Avatar
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (192);

-- Mobile Suit Gundam Hathaway
-- TMDB genres: Animation, Action, Drama, Science Fiction
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (193);

-- Army of Thieves
-- TMDB genres: Action, Crime, Comedy
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (194);

-- UFO
-- TMDB genres: Drama, Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (195);

-- The Weekend Away
-- TMDB genres: Thriller, Mystery
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (196);

-- The Croods: A New Age
-- TMDB genres: Animation, Family, Adventure, Fantasy, Comedy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (197);

-- Back to the Outback
-- TMDB genres: Family, Animation, Adventure, Comedy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (198);

-- The Tomorrow War
-- TMDB genres: Action, Science Fiction, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (199);

-- Home Team
-- TMDB genres: Family, Comedy, Drama
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (200);

-- The Fallout
-- TMDB genres: Drama
-- Local category: Teen (12)
-- Mapping reason: Title, overview, or keywords indicate teen/high-school content
UPDATE entities
SET categoryId = 12
WHERE id IN (201);

-- Raya and the Last Dragon
-- TMDB genres: Animation, Family, Fantasy, Adventure, Action
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (202);

-- Infinite
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (203);

-- Wrath of Man
-- TMDB genres: Thriller, Crime, Drama
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (204);

-- Fortress
-- TMDB genres: Action, Thriller, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (205);

-- Space Jam: A New Legacy
-- TMDB genres: Family, Comedy, Adventure, Animation, Science Fiction
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (206);

-- Laura y el misterio del asesino inesperado
-- TMDB genres: Comedy, Crime, Mystery
-- Local category: Thrillers (9)
-- Mapping reason: Manual web override from IMDb genres Comedy/Crime/Mystery
UPDATE entities
SET categoryId = 9
WHERE id IN (207);

-- The Conjuring: The Devil Made Me Do It
-- TMDB genres: Horror, Mystery, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (208);

-- Batman Begins
-- TMDB genres: Drama, Crime, Action
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (209);

-- The Royal Treatment
-- TMDB genres: Romance, Comedy
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (210);

-- Ciao Alberto
-- TMDB genres: Animation, Comedy, Family, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (211);

-- Gone Mom: The Disappearance of Jennifer Dulos
-- TMDB genres: Thriller, Mystery, Crime, TV Movie
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (212);

-- Ripper Untold
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (213);

-- Meander
-- TMDB genres: Horror, Science Fiction, Drama, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (214);

-- Zeros and Ones
-- TMDB genres: War, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (215);

-- Snake Eyes: G.I. Joe Origins
-- TMDB genres: Action, Adventure
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (216);

-- Survive the Game
-- TMDB genres: Action, Thriller, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (217);

-- Riverdance: The Animated Adventure
-- TMDB genres: Animation, Fantasy, Music, Adventure, Comedy, Family
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (218);

-- Apex
-- TMDB genres: Action, Thriller, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (219);

-- The Tinder Swindler
-- TMDB genres: Documentary, Crime
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (220);

-- Love Tactics
-- TMDB genres: Romance, Comedy
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (221);

-- The Addams Family 2
-- TMDB genres: Animation, Family, Comedy, Fantasy, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (222);

-- Dynasty Warriors
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (223);

-- Miraculous World: Shanghai – The Legend of Ladydragon
-- TMDB genres: Animation, Fantasy, Action, TV Movie
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (224);

-- Miraculous World: New York, United HeroeZ
-- TMDB genres: Animation, Family, TV Movie, Action, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (225);

-- Surviving Paradise: A Family Tale
-- TMDB genres: Documentary
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (226);

-- My Hero Academia: Heroes Rising
-- TMDB genres: Animation, Action, Fantasy, Adventure
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (227);

-- Amina
-- TMDB genres: War, History, Drama
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (228);

-- Encounter
-- TMDB genres: Science Fiction, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (229);

-- Son
-- TMDB genres: Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (230);

-- Wonder Woman 1984
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (231);

-- The Amazing Spider-Man 2
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (232);

-- The Whole Truth
-- TMDB genres: Horror, Mystery, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (233);

-- Catwoman: Hunted
-- TMDB genres: Animation, Action, Crime
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (234);

-- Hacksaw Ridge
-- TMDB genres: Drama, History, War
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (235);

-- Crazy Fist
-- TMDB genres: Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (236);

-- Harry Potter and the Philosopher's Stone
-- TMDB genres: Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (237);

-- The Avengers
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (238);

-- Dragon Fury
-- TMDB genres: Horror, Fantasy, Mystery
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (239);

-- Tarumama
-- TMDB genres: Horror, Drama
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (240);

-- Doctor Strange
-- TMDB genres: Fantasy, Adventure, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (241);

-- Halloween Kills
-- TMDB genres: Horror
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (242);

-- The Exorcism of God
-- TMDB genres: Horror, Crime, Drama, Fantasy
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (243);

-- Spider-Man: Far From Home
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (244);

-- Harry Potter 20th Anniversary: Return to Hogwarts
-- TMDB genres: Documentary
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (245);

-- After We Collided
-- TMDB genres: Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (246);

-- The Desperate Hour
-- TMDB genres: Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (247);

-- Dragon Ball Z: Resurrection 'F'
-- TMDB genres: Action, Animation, Science Fiction
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (248);

-- Seal Team
-- TMDB genres: Animation, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (249);

-- Knowing
-- TMDB genres: Music
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (250);

-- Witch Hunt
-- TMDB genres: Horror, Fantasy
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (251);

-- The Maze Runner
-- TMDB genres: Action, Mystery, Science Fiction, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (252);

-- The Unholy
-- TMDB genres: Action, Drama, Fantasy, Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (253);

-- Ashfall
-- TMDB genres: Action, Adventure, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (254);

-- The Sky Is Everywhere
-- TMDB genres: Drama, Romance, Music
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (255);

-- Time Is Up
-- TMDB genres: Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (256);

-- The Deep House
-- TMDB genres: Horror, Mystery
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (257);

-- Boruto: Naruto the Movie
-- TMDB genres: Adventure, Action, Animation, Comedy, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (258);

-- Harry Potter and the Chamber of Secrets
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (259);

-- Sonic the Hedgehog
-- TMDB genres: Action, Science Fiction, Comedy, Family
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (260);

-- Coco
-- TMDB genres: Documentary
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (261);

-- Ava
-- TMDB genres: Drama
-- Local category: Foreign (16)
-- Mapping reason: Country of origin is outside the default English-speaking set
UPDATE entities
SET categoryId = 16
WHERE id IN (262);

-- Fury
-- TMDB genres: War, Drama, Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (263);

-- Don't Breathe 2
-- TMDB genres: Thriller, Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (264);

-- Matando Cabos 2: La Máscara del Máscara
-- TMDB genres: Action, Adventure, Comedy
-- Local category: Action & adventure (1)
-- Mapping reason: Manual web override from Rotten Tomatoes/other web sources
UPDATE entities
SET categoryId = 1
WHERE id IN (265);

-- Monster Hunter
-- TMDB genres: Action, Fantasy, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (266);

-- The Last: Naruto the Movie
-- TMDB genres: Action, Romance, Animation
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (267);

-- Spider-Man: Homecoming
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (268);

-- Bigbug
-- TMDB genres: Science Fiction, Comedy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (269);

-- Spider-Man
-- TMDB genres: Action, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (270);

-- Avengers: Endgame
-- TMDB genres: Adventure, Science Fiction, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (271);

-- Old
-- TMDB genres: Thriller, Mystery, Horror
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (272);

-- Real Steel
-- TMDB genres: Action, Science Fiction, Drama
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (273);

-- Love and Leashes
-- TMDB genres: Romance, Comedy
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (274);

-- After
-- TMDB genres: Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (275);

-- The Forever Purge
-- TMDB genres: Action, Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (276);

-- Tall Girl 2
-- TMDB genres: Comedy, Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (277);

-- Dog
-- TMDB genres: Drama
-- Local category: Foreign (16)
-- Mapping reason: Country of origin is outside the default English-speaking set
UPDATE entities
SET categoryId = 16
WHERE id IN (278);

-- I Am Legend
-- TMDB genres: Drama, Science Fiction, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (279);

-- Naruto Shippuden the Movie
-- TMDB genres: Animation, Action, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (280);

-- Tom & Jerry
-- TMDB genres: Comedy, Family, Animation
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (281);

-- Against the Ice
-- TMDB genres: Drama, Adventure, History
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (282);

-- An Egg Rescue
-- TMDB genres: Animation, Comedy, Adventure, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (283);

-- Harry Potter and the Goblet of Fire
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (284);

-- Pretty Guardian Sailor Moon Eternal The Movie Part 2
-- TMDB genres: Animation, Action, Fantasy, Drama
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (285);

-- Demonic
-- TMDB genres: Drama, Horror, Science Fiction
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (286);

-- Far From the Tree
-- TMDB genres: Drama
-- Local category: Foreign (16)
-- Mapping reason: Country of origin is outside the default English-speaking set
UPDATE entities
SET categoryId = 16
WHERE id IN (287);

-- Black Water: Abyss
-- TMDB genres: Horror, Action, Adventure
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (288);

-- Yes, No, or Maybe Half?
-- TMDB genres: Animation, Drama, Romance
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (289);

-- The SpongeBob Movie: Sponge on the Run
-- TMDB genres: Family, Comedy, Adventure, Animation
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (290);

-- Spider-Man: Into the Spider-Verse
-- TMDB genres: Animation, Action, Adventure, Science Fiction
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (291);

-- Harry Potter and the Prisoner of Azkaban
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (292);

-- The Vault
-- TMDB genres: Drama, Action, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (293);

-- The Adam Project
-- TMDB genres: Adventure, Science Fiction, Comedy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (294);

-- Deadpool
-- TMDB genres: Action, Adventure, Comedy
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (295);

-- The Harder They Fall
-- TMDB genres: Western
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (296);

-- Monsters, Inc.
-- TMDB genres: Animation, Comedy, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (297);

-- Ainbo: Spirit of the Amazon
-- TMDB genres: Adventure, Animation, Family, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (298);

-- Don't Look Up
-- TMDB genres: Comedy, Science Fiction, Drama
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (299);

-- Cars 3
-- TMDB genres: Animation, Family, Drama
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (300);

-- Spider-Man 3
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (301);

-- Last Shoot Out
-- TMDB genres: Western, Action, Thriller, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (302);

-- Sator
-- TMDB genres: Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (303);

-- Iron Man 2
-- TMDB genres: Adventure, Action, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (304);

-- Soul
-- TMDB genres: Drama
-- Local category: Dramas (4)
-- Mapping reason: TMDB genre Drama
UPDATE entities
SET categoryId = 4
WHERE id IN (305);

-- Pirates of the Caribbean: On Stranger Tides
-- TMDB genres: Adventure, Action, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (306);

-- Phobias
-- TMDB genres: Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (307);

-- A Quiet Place Part II
-- TMDB genres: Science Fiction, Thriller, Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (308);

-- What the Peeper Saw
-- TMDB genres: Horror, Drama, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (309);

-- Fast & Furious 10
-- TMDB genres: Action, Thriller, Crime
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (310);

-- The Dark Knight Rises
-- TMDB genres: Action, Crime, Drama, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (311);

-- Trolls Holiday in Harmony
-- TMDB genres: Animation, Fantasy, Family, Comedy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (312);

-- Batman v Superman: Dawn of Justice
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (313);

-- Motherly
-- TMDB genres: Thriller, Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (314);

-- Nobody
-- TMDB genres: Action, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (315);

-- My Hero Academia: Two Heroes
-- TMDB genres: Animation, Action, Adventure, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (316);

-- Harry Potter and the Half-Blood Prince
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (317);

-- Book of Love
-- TMDB genres: Comedy, Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (318);

-- Jurassic Hunt
-- TMDB genres: Action, Science Fiction, Thriller, Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (319);

-- American Underdog
-- TMDB genres: Drama, Family
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (320);

-- The Dark Knight
-- TMDB genres: Action, Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (321);

-- Jujutsu Kaisen 0
-- TMDB genres: Animation, Action, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (322);

-- Ava
-- TMDB genres: Action, Thriller, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (323);

-- Three Steps Above Heaven
-- TMDB genres: Romance, Drama
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (324);

-- Sinkhole
-- TMDB genres: Comedy, Drama
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (325);

-- Undisputed III: Redemption
-- TMDB genres: Action, Thriller, Crime, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (326);

-- Toy Story
-- TMDB genres: Family, Comedy, Animation, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (327);

-- Roald Dahl's The Witches
-- TMDB genres: Comedy, Fantasy, Family, Horror
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (328);

-- El Paseo 6
-- TMDB genres: Comedy
-- Local category: Comedies (3)
-- Mapping reason: Manual web override from TMDB/Rotten Tomatoes
UPDATE entities
SET categoryId = 3
WHERE id IN (329);

-- Separation
-- TMDB genres: Drama
-- Local category: Foreign (16)
-- Mapping reason: Country of origin is outside the default English-speaking set
UPDATE entities
SET categoryId = 16
WHERE id IN (330);

-- Dragon Ball Super: Super Hero
-- TMDB genres: Animation, Science Fiction, Action
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (331);

-- After Ever Happy
-- TMDB genres: Romance, Drama
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (332);

-- Brothers
-- TMDB genres: Action, Comedy, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (333);

-- Shrek
-- TMDB genres: Animation, Comedy, Fantasy, Adventure, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (334);

-- Jackass Forever
-- TMDB genres: Action, Comedy, Documentary
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (335);

-- Big Hero 6
-- TMDB genres: Adventure, Family, Animation, Action, Comedy
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (336);

-- Monster Pets: A Hotel Transylvania Short
-- TMDB genres: Animation, Comedy, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (337);

-- Off Course
-- TMDB genres: Romance, Comedy
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (338);

-- The Hobbit: The Battle of the Five Armies
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (339);

-- Major Grom: Plague Doctor
-- TMDB genres: Action, Adventure
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (340);

-- Monster Family 2
-- TMDB genres: Animation, Family, Comedy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (341);

-- Gunpowder Milkshake
-- TMDB genres: Action, Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (342);

-- Beautiful Sisters: Flesh Slave
-- TMDB genres: Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (343);

-- Narco Sub
-- TMDB genres: Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (344);

-- Harry Potter and the Order of the Phoenix
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (345);

-- Legend
-- TMDB genres: Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (346);

-- Straight Outta Nowhere: Scooby-Doo! Meets Courage the Cowardly Dog
-- TMDB genres: Animation, Mystery
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (347);

-- Descendants: The Royal Wedding
-- TMDB genres: Animation, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (348);

-- Frozen II
-- TMDB genres: Family, Animation, Adventure, Comedy, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (349);

-- Swim
-- TMDB genres: Horror, Action
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (350);

-- Mutation on Mars
-- TMDB genres: Drama, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (351);

-- Zone 414
-- TMDB genres: Science Fiction, Thriller, Mystery
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (352);

-- Harry Potter and the Deathly Hallows: Part 2
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (353);

-- Avengers: Age of Ultron
-- TMDB genres: Action, Adventure, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (354);

-- Thor: Ragnarok
-- TMDB genres: Action, Science Fiction, Comedy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (355);

-- It Chapter Two
-- TMDB genres: Horror, Thriller, Drama
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (356);

-- The Lion King
-- TMDB genres: Animation, Family, Drama, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (357);

-- King Richard
-- TMDB genres: Drama, History
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (358);

-- Last Night in Soho
-- TMDB genres: Horror, Mystery
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (359);

-- Batman Returns
-- TMDB genres: Action, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (360);

-- The Twilight Saga: Eclipse
-- TMDB genres: Adventure, Fantasy, Drama, Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (361);

-- Wild Indian
-- TMDB genres: Crime, Drama, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (362);

-- Project X
-- TMDB genres: Comedy
-- Local category: Teen (12)
-- Mapping reason: Title, overview, or keywords indicate teen/high-school content
UPDATE entities
SET categoryId = 12
WHERE id IN (363);

-- The Protégé
-- TMDB genres: Action, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (364);

-- Ida Red
-- TMDB genres: Crime, Thriller, Drama, Action
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (365);

-- The Haunting of Helena
-- TMDB genres: Horror, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (366);

-- Vanguard
-- TMDB genres: Action, Adventure, Comedy, Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (367);

-- Pirates of the Caribbean: The Curse of the Black Pearl
-- TMDB genres: Adventure, Fantasy, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (368);

-- The Simpsons: The Good, the Bart, and the Loki
-- TMDB genres: Animation, Comedy, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (369);

-- The Marksman
-- TMDB genres: Action, Drama, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (370);

-- Vicky and Her Mystery
-- TMDB genres: Adventure, Family, Drama
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (371);

-- KonoSuba: God's Blessing on this Wonderful World! Legend of Crimson
-- TMDB genres: Animation, Adventure, Comedy, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (372);

-- 7 Prisoners
-- TMDB genres: Drama, Crime
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (373);

-- 11M
-- TMDB genres: Drama, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (374);

-- Seobok: Project Clone
-- TMDB genres: Science Fiction, Action, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: Manual web override from Wikipedia summary
UPDATE entities
SET categoryId = 7
WHERE id IN (375);

-- Shrek 2
-- TMDB genres: Animation, Family, Comedy, Fantasy, Romance
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (376);

-- Percy Jackson & the Olympians: The Lightning Thief
-- TMDB genres: Adventure, Fantasy, Family
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (377);

-- Your Name.
-- TMDB genres: Animation, Romance, Drama
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (378);

-- Chief Daddy 2: Going for Broke
-- TMDB genres: Comedy
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (379);

-- Captain America: Civil War
-- TMDB genres: Adventure, Action, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (380);

-- Joker
-- TMDB genres: Crime, Thriller, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (381);

-- Jack Reacher
-- TMDB genres: Crime, Drama, Thriller, Action
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (382);

-- The Invisible Thread
-- TMDB genres: Family, Drama, Comedy
-- Local category: Children & family (13)
-- Mapping reason: TMDB genre Family
UPDATE entities
SET categoryId = 13
WHERE id IN (383);

-- SAS: Red Notice
-- TMDB genres: Drama, Action, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (384);

-- Mulan
-- TMDB genres: Adventure, Fantasy, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (385);

-- I Want You Back
-- TMDB genres: Comedy, Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (386);

-- Pirates of the Caribbean: Dead Man's Chest
-- TMDB genres: Adventure, Fantasy, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (387);

-- Ninja Assassin
-- TMDB genres: Action, Adventure, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (388);

-- The Wolf of Wall Street
-- TMDB genres: Crime, Drama, Comedy
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (389);

-- Hilda and the Mountain King
-- TMDB genres: Animation, Adventure, Fantasy, Family, TV Movie
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (390);

-- Rurouni Kenshin: The Beginning
-- TMDB genres: Action, Adventure, Drama, Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (391);

-- Minnal Murali
-- TMDB genres: Action, Comedy, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (392);

-- Pokémon: Mewtwo Strikes Back - Evolution
-- TMDB genres: Animation, Adventure, Fantasy, Action, Family
-- Local category: Anime (14)
-- Mapping reason: Manual web override from TMDB movie page
UPDATE entities
SET categoryId = 14
WHERE id IN (393);

-- Fate/Grand Order Final Singularity – Grand Temple of Time: Solomon
-- TMDB genres: Animation, Action, Adventure, Fantasy
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (394);

-- Teenage Mutant Ninja Turtles
-- TMDB genres: Science Fiction, Action, Adventure, Comedy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (395);

-- Fifty Shades of Grey
-- TMDB genres: Drama, Romance, Thriller
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (396);

-- Never Back Down: Revolt
-- TMDB genres: Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (397);

-- Wish Dragon
-- TMDB genres: Animation, Family, Comedy, Fantasy, Romance, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (398);

-- Ice Age
-- TMDB genres: Animation, Comedy, Family, Adventure
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (399);

-- Fast & Furious Presents: Hobbs & Shaw
-- TMDB genres: Action, Adventure, Comedy
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (400);

-- Toy Story 2
-- TMDB genres: Animation, Comedy, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (401);

-- Sweet Girl
-- TMDB genres: Action, Thriller, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (402);

-- The Stronghold
-- TMDB genres: Thriller, Action, Crime
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (403);

-- Spirit Untamed
-- TMDB genres: Animation, Adventure, Family, Western
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (404);

-- Tom Clancy's Without Remorse
-- TMDB genres: Action, Thriller, War
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (405);

-- Harry Potter and the Deathly Hallows: Part 1
-- TMDB genres: Adventure, Fantasy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (406);

-- Injustice
-- TMDB genres: Animation, Science Fiction, Fantasy, Action
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (407);

-- Nobody Sleeps in the Woods Tonight 2
-- TMDB genres: Horror, Thriller, Comedy
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (408);

-- American Badger
-- TMDB genres: Action
-- Local category: Action & adventure (1)
-- Mapping reason: TMDB genre Action/Adventure/War/Western
UPDATE entities
SET categoryId = 1
WHERE id IN (409);

-- Evangelion: 3.0+1.0 Thrice Upon a Time
-- TMDB genres: Animation, Action, Science Fiction, Drama
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (410);

-- LOL Surprise: The Movie
-- TMDB genres: Animation, Family
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (411);

-- Coraline
-- TMDB genres: Animation, Family, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (412);

-- How I Became a Superhero
-- TMDB genres: Science Fiction, Adventure, Action, Comedy, Thriller
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (413);

-- The Bad Guys
-- TMDB genres: Family, Comedy, Crime, Animation, Action
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (414);

-- Venom
-- TMDB genres: Science Fiction, Action
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (415);

-- The Last Mercenary
-- TMDB genres: Action, Comedy
-- Local category: Comedies (3)
-- Mapping reason: TMDB genre Comedy
UPDATE entities
SET categoryId = 3
WHERE id IN (416);

-- Cars 2
-- TMDB genres: Animation, Family, Adventure, Comedy
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (417);

-- Downfall: The Case Against Boeing
-- TMDB genres: Documentary
-- Local category: Documentaries (10)
-- Mapping reason: TMDB genre Documentary
UPDATE entities
SET categoryId = 10
WHERE id IN (418);

-- Ratatouille
-- TMDB genres: Animation, Comedy, Family, Fantasy
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (419);

-- Mickey and Minnie Wish Upon a Christmas
-- TMDB genres: Animation, TV Movie, Comedy, Family
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (420);

-- Dragon Ball Z: Broly – The Legendary Super Saiyan
-- TMDB genres: Animation, Science Fiction, Action
-- Local category: Anime (14)
-- Mapping reason: Animated title with Japanese indicators
UPDATE entities
SET categoryId = 14
WHERE id IN (421);

-- Doctor Strange in the Multiverse of Madness
-- TMDB genres: Fantasy, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (422);

-- Death on the Nile
-- TMDB genres: Mystery, Crime, Thriller
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (423);

-- Army of the Dead
-- TMDB genres: Crime, Action, Horror
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (424);

-- Home Sweet Home Alone
-- TMDB genres: Family, Comedy
-- Local category: Christmas (18)
-- Mapping reason: Title, overview, or keywords indicate Christmas/holiday content
UPDATE entities
SET categoryId = 18
WHERE id IN (425);

-- Carriers
-- TMDB genres: Action, Drama, Horror, Science Fiction, Thriller
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (426);

-- Boyka: Undisputed IV
-- TMDB genres: Action, Drama, Thriller, Crime
-- Local category: Sports (8)
-- Mapping reason: Title, overview, or keywords indicate sports content
UPDATE entities
SET categoryId = 8
WHERE id IN (427);

-- 2012
-- TMDB genres: Animation, Drama, Mystery, Romance, Horror
-- Local category: Cartoon (20)
-- Mapping reason: TMDB genre Animation
UPDATE entities
SET categoryId = 20
WHERE id IN (428);

-- Why Women Cheat
-- TMDB genres: Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (429);

-- Sing
-- TMDB genres: Animation, Music, Comedy, Family
-- Local category: Music (17)
-- Mapping reason: TMDB genre Music
UPDATE entities
SET categoryId = 17
WHERE id IN (430);

-- Green Lantern
-- TMDB genres: Adventure, Action, Thriller, Science Fiction
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (431);

-- Peninsula
-- TMDB genres: Horror, Action, Thriller, Adventure
-- Local category: Horror (5)
-- Mapping reason: TMDB genre Horror
UPDATE entities
SET categoryId = 5
WHERE id IN (432);

-- Ted 2
-- TMDB genres: Comedy, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (433);

-- High School Teacher: Maturing
-- TMDB genres: Drama, Romance
-- Local category: Romantic (6)
-- Mapping reason: TMDB genre Romance
UPDATE entities
SET categoryId = 6
WHERE id IN (434);

-- Wonder Woman
-- TMDB genres: Action, Adventure, Fantasy
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (435);

-- A Day to Die
-- TMDB genres: Action, Thriller, Crime, Drama
-- Local category: Thrillers (9)
-- Mapping reason: TMDB genre Thriller/Mystery/Crime
UPDATE entities
SET categoryId = 9
WHERE id IN (436);

-- Cosmic Sin
-- TMDB genres: Science Fiction, Action, Adventure
-- Local category: Sci - Fi & Fantasy (7)
-- Mapping reason: TMDB genre Science Fiction/Fantasy
UPDATE entities
SET categoryId = 7
WHERE id IN (437);

COMMIT;
