<?php

class DataProvider
{
	protected $dbh;
	protected $data = array();
	protected static $__instance;
	
	public static function getInstance() {
	
		if (!self::$__instance instanceof self) {
		
			self::$__instance = new self;
		}
		
		return self::$__instance;
	}
	
	public function getSoundcloudParams($encrypt = false) {
	
		$params = '&color=0090ff&show_artwork=false';
		if ($encrypt) {
		
			$params = htmlentities($params);
		}
		
		return $params;
	}
	
	public function setDbh(\PDO $dbh) {

		$this->dbh = $dbh;
	}

	public function __construct() {
	
		$this->data['artists'] = array(
			///////////////////////////////////////////////////////////////
			'spekta' => (object)array(
				'key' => 'spekta',
				'name' => 'Spekta',
				'shortInfo' => 'Uplifting, Progressive and Psychedelic Trance Producer, Club DJ & Live Act.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692626%3Fsecret_token%3Ds-dDyJt&auto_play=false',
				'youtube' => array(
					'http://www.youtube.com/embed/vLgCYUcGJsk?wmode=opaque&autoplay=1',
					'http://www.youtube.com/embed/bgCdjNyeQ2M?wmode=opaque',
				),
				'longInfo' => 'Spekta started making music at the age of six years by playing the piano. After about ten years he continued his career with german Rap and HipHop DJing. During 2012 he discovered the world of electronic beats and began to perform live-mixes with royalty free samples. Later he started to produce his own samples and tracks and colaborated with yet rather unknown artists. In the end of 2012 he founded the electronic music label "3886records".',
				'firstname' => 'Andreas',
				'lastname' => 'Linden',
				'location' => 'Bonn, Germany',
				'birthday' => '1985-01-20',
				'links' => array(
					'facebook' => 'http://www.facebook.com/spekta85',
					'soundcloud' => 'http://www.soundcloud.com/spekta85',
					'beatport' => 'http://dj.beatport.com/spekta',
					'youtube' => 'http://www.youtube.com/zolexdx',
				),
			),
			///////////////////////////////////////////////////////////////
			'zwielicht' => (object)array(
				'key' => 'zwielicht',
				'name' => 'Zwielicht',
				'shortInfo' => 'Progressive Morning Trance Producer & Club DJ Duo',
				'longInfo' => 'Zwielicht is a Producer and DJ Duo consisting of Flittchen and Greggel. Greggel‘s musical carer initiated in 1994 when he started listening to electronic music and in 1997 when he first came across psychedelic trance he already started producing the music himself. A few years later he commenced DJ‘ing as well. Flittchen is a few years younger. He played guitar when he was about 14 years old and was just into Punkrock. 2007 he first came in contact with psychedelic trance music. It took him not long to start DJ‘ing and also producing mostly progressive Psytrance but also some Trip Hop. Since December 2011 Flittchen and Greggel combined their talent and founded Zwielicht through the intervention by one of their friends.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F6111209%3Fsecret_token%3Ds-w1OLm&auto_play=true',
				'links' => array(
					'facebook' => 'https://www.facebook.com/pages/Zwielicht/355130044577017',
					'soundcloud' => 'https://soundcloud.com/zwie_licht'
				),
			),			
			///////////////////////////////////////////////////////////////
			'spirtualzune' => (object)array(
				'key' => 'spirtualzune',
				'name' => 'Spirtualzune',
				'shortInfo' => 'Progressive and Psychedelic Trance Producer & DJ',
				'longInfo' => 'Spirtualzune aka Raphael Abu, 32 years old from Tel Aviv in Israel started to make Trance, Psy and Progressive before nine years and already released two albums on beatport. Now he is working on a new project for 3886records in Germany. Spirtualzune plays at events and bounces the people with bits and sounds that gives them an entirely spiritual experience.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7378446%3Fsecret_token%3Ds-bMDqm&auto_play=true',
				'links' => array(
					'facebook' => 'http://www.facebook.com/Spirtualzunemusic',
					'soundcloud' => 'http://soundcloud.com/spirtualzune-music'
				),
			),
			///////////////////////////////////////////////////////////////
			'cannabliss' => (object)array(
				'key' => 'cannabliss',
				'name' => 'CannaBliss',
				'shortInfo' => 'Progressive and Psychedelic Trance Producer',
				'longInfo' => 'Started in 2012, with a lot experimentation, He eventually developed his style and started producing Progressive Psy/ Goa psy and different styles of Psychedelic music.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7449786%3Fsecret_token%3Ds-lMi0a&auto_play=true',
				'links' => array(
					'facebook' => 'https://www.facebook.com/Cannabliss.Psybaba',
					'soundcloud' => 'http://soundcloud.com/cannabliss-3'
				),
			),
			///////////////////////////////////////////////////////////////
			'psybuddy' => (object)array(
				'key' => 'psybuddy',
				'name' => 'PsyBuddy',
				'shortInfo' => 'Progressive Psytrance Producer',
				'longInfo' => 'Miro Moric was involved in the psytrance scene since the mid of the 90\'s. Spending nearly two decades with doing artwork for parties, clubs, festivals and musicians, he finally decided to transform all the great experiences, he had with psytrance music over the time, by starting a solo project and producing by his own. PsyBuddy\'s progressive psytrance is combining all the influences of the so called goa genre into an individual style and interpretation.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F8364715%3Fsecret_token%3Ds-EbXLc&auto_play=true',
				'links' => array(
					'facebook' => 'http://www.facebook.com/PsyBuddy?ref=hl',
					'soundcloud' => 'https://soundcloud.com/psybuddy',
					'youtube' => 'http://www.youtube.com/user/sketchinc'
				),
			),
			///////////////////////////////////////////////////////////////
			'fivemonths' => (object)array(
				'key' => 'fivemonths',
				'name' => 'Five Months',
				'shortInfo' => 'Trance Producer',
				'longInfo' => 'Five Months is the alias of enthousiastic EDM producer Jason Janssens.  Being influenced by the likes of M.I.K.E. and Airwave he started to make his own creations in the mid 90\'s.  Living in Antwerp he discovered the beauty of Progressive Trance made popular by the two already mentioned icons at Bonzaï Records.  After several years and more influence from the Dutchmen Tiësto and Armin, music production became more serious.<br/>In 2012 the first professional releases became reality.  He starred on three different EP\'s by 7fgr label for their "Seducing the night" serie.  Lately he has done some remix work for French Electro prodigy Intox.  They also have a project running called "French Motion & Five Months" with their first single "Bombay Sapphire" soon to be released.<br/>Several projects are in the pipeline for future release.  Check out his soundcloud page for the latest songs and ongoing projects.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7162742%3Fsecret_token%3Ds-aVDUO&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/fivemonths',
				),
			),
			///////////////////////////////////////////////////////////////
			'shinson' => (object)array(
				'key' => 'shinson',
				'name' => 'Shinson',
				'shortInfo' => 'Trance Producer & Radio DJ',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692347%3Fsecret_token%3Ds-xzLq4&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/d-j-j-t',
					'facebook' => 'http://www.facebook.com/pages/Shinson/346606128784107',
				),
			),
			///////////////////////////////////////////////////////////////
			'aljoshakonstanty' => (object)array(
				'key' => 'aljoshakonstanty',
				'name' => 'Aljosha Konstanty',
				'shortInfo' => 'Trance Producer & Radio DJ',
				'longInfo' => 'Aljosha Konstanty, born 1998 in Den Haag, The Netherlands, is a german Uplifting- and Progressive Trance musician and Online Radio DJ. He started making music when he was 14, because he enjoyed listening to the music of Armin van Buuren so much that he wanted to know more about producing electronic music. In 2013 he joined the \'Sunshine Radio\' as their youngest DJ. His musical influences are: Armin van Buuren, Orjan Nilsen, Markus Schulz, Deadmau5, Rank1 and Paul van Dyk.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5793792%3Fsecret_token%3Ds-l2cjU&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/akhtrance',
					'facebook' => 'http://www.facebook.com/aljoshakonstanty',
				),
			),
			///////////////////////////////////////////////////////////////
			'cosmicchuen' => (object)array(
				'key' => 'cosmicchuen',
				'name' => 'Cosmic Chuen',
				'shortInfo' => 'Progressive and Psychedelic Trance Producer',
				'longInfo' => ' Jorge de la Cruz (Cosmic Chuen) is a producer from Guadalajara Jal. Mexico, which began producing electronic music in 2008 with an old PC desktop, wishing to create some quality sounds, now the time and practice have shown results. Cosmic chuen is an independent project that creates progressive Psytrance for the body and soul.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692528%3Fsecret_token%3Ds-HcxIv&auto_play=true"></iframe>',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/de-la-cru5',
				),
			),
			///////////////////////////////////////////////////////////////
			'take' => (object)array(
				'key' => 'take',
				'name' => 'Take',
				'shortInfo' => 'Progressive and Psychedelic Trance Producer',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692388%3Fsecret_token%3Ds-9ObTA&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/takewithuran',
					'facebook' => 'http://www.facebook.com/takepage000',
				),
			),
			///////////////////////////////////////////////////////////////
			'darwinxp' => (object)array(
				'key' => 'darwinxp',
				'name' => 'Darwin Experience',
				'shortInfo' => 'House Producer',
				'longInfo' => 'The Darwin Experience Project was formed in September 2011 by experienced DJ, producer and musician Ross Watson as a bold university assignment. The project is influenced by many styles of electronic and world music, with an emphasis on organic, natural sounds combined with jackin\' rhythms. The initial idea behind the project was to explore the concept of house music\'s evolution as a genre and how new styles could be created through the cross pollination of genres and subgenres. The idea of evolution is expanded not only to the music, but to the artist himself and how he can push forth the limits of his own creativity and artistic expression.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692570%3Fsecret_token%3Ds-jfgyw&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/darwinxp',
					'facebook' => 'http://www.facebook.com/darwinexperience',
				),
			),
			///////////////////////////////////////////////////////////////
			'nopublicity' => (object)array(
				'key' => 'nopublicity',
				'name' => 'No Publicity',
				'shortInfo' => 'Progressive and Psychedelic Trance Producer',
				'longInfo' => 'Born in 80\'s England, grew up listening to every type of music available. Experienced the rave and dance music explosion in Britain and got into DJing at 13. Moved on to producing music in 2000. Enjoying People, Music and Life',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692317%3Fsecret_token%3Ds-CXLsv&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/roscoe-averiss',
				),
			),
			/*
			///////////////////////////////////////////////////////////////
			'euphoristix' => (object)array(
				'key' => 'euphoristix',
				'name' => 'Euphoristix',
				'shortInfo' => 'Chillout / Downtempo & Trance producer',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692688%3Fsecret_token%3Ds-4y3At&auto_play=true',
				'links' => array(
					'facebook' => 'http://www.facebook.com/pages/Euphoristix/548468771838150',
					'youtube' => 'http://www.youtube.com/user/Euphoristix',
				),
				'aka' => array(
					'Willian Sinus',
				),
			),
			*/
			///////////////////////////////////////////////////////////////
			'alfredkwak' => (object)array(
				'key' => 'alfredkwak',
				'name' => 'Alfred Kwak',
				'shortInfo' => 'Electro / Tech / Progressive House Producer',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F9005527%3Fsecret_token%3Ds-oucbz&auto_play=true',
				'links' => array(
					'soundcloud' => 'https://soundcloud.com/alfred-kwak',
				),
			),
			///////////////////////////////////////////////////////////////
			'bernardogigante' => (object)array(
				'key' => 'bernardogigante',
				'name' => 'Bernardo Gigante',
				'longInfo' => 'Cristian Catania, born in 1989 in Nuremberg, Bavaria, is a German trance and chillout producer. He started making music when he was 18 as he was fascinated of ATB\'s music. So he decided to begin with the production of electronic music.',
				'shortInfo' => 'Trance & Chillout Producer',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F6214263%3Fsecret_token%3Ds-FUt7P&auto_play=true',
				'links' => array(
					'soundcloud' => 'http://soundcloud.com/bernardo-gigante',
					'facebook' => 'http://www.facebook.com/bernardo.gigante',
				),
			),
			///////////////////////////////////////////////////////////////
			'mindlab' => (object)array(
				'key' => 'mindlab',
				'name' => 'Mind Lab',
				'shortInfo' => 'Trance Producer',
				'longInfo' => 'Rudi C., aka Mind Lab, is Electro, Ambient, Trance and Dubstep musician from Belgium. He started making music at the age of 16, because he was fascinated of early dance industry. His favorite style of music is Trance. Artists he is inspired by: Dash Berlin, Armin van Buuren and Ferry Corsten',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7163709%3Fsecret_token%3Ds-hN2q7&auto_play=true',
				'links' => array(
					'soundcloud' => 'http://soundcloud.com/astrobot-1',
				),
			),
			///////////////////////////////////////////////////////////////
			'autonomicwonder' => (object)array(
				'key' => 'autonomicwonder',
				'name' => 'Autonomic Wonder',
				'shortInfo' => 'House Producer',
				'longInfo' => 'More than years ago, Johannes Buchholz aka. AutonomicWonder, arranged his first track on his Macintosh. Today, AutonomicWonder, living in Kassel, Germany, makes step after step into the world of producing, recording and arranging music of every kind. However, his main genres are still House, Hardstyle and electronic music in which he already published several albums such as "Fractuered" or the upcoming album "House Session".<br/>AutonomicWonder will keep delivering music from all the different styles and genres from Rock over soundtrack to dance, Trance and and everything danceable.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7163455%3Fsecret_token%3Ds-ik6Gu&auto_play=true',
				'links' => array(
					'soundcloud' => 'http://soundcloud.com/autonomicwonder',
				),
			),			
			
			///////////////////////////////////////////////////////////////
			'commonsen5e' => (object)array(
				'key' => 'commonsen5e',
				'name' => 'CommonSen5e',
				'shortInfo' => 'Chillout Producer',
				'longInfo' => 'I have many reasons why i chose the name CommonSen5e. what i hear what i see and what i believe is that i am. anyone could be, everyone has what i have. and not saying im all of everything that has to do with Common Sense, but i want to be. i want to learn, i want to be fully connected to my other self that i feel within me. My name is Mason Metcalf, born and raised in Oregon, United states. Im a Seventeen year old Electronic Music Artist, that has just recently been signed to 3886Records. finally i have found a home, i make a lot of music, have and always will. i wont stop until the sounds stop, and i dont think we have even come close to making every sound. im here for a reason, i have music to give you but i have messages as well integrated within my music , do i believe i can achieve this goal? with this name? Yes. i do. there is not a doubt in my mind. i love music. i love life, i love the sorrow the pain and the gain from it, its what i live for is life. ive had a hard life like many, but ive put my emotions and the events that occur that i live through in my music, thats what gives it its touch. i am CommonSen5e, there is no one else. everyone has used Common Sense in a misused manner, its not about how the government should run things, i have things to say to them but its more then that, its everything about your common sense. its the rights and wrongs and the obvious answers that are put in front of our face that i see a lot of people ignore these days. im hear to get you to wake up and open your eyes, or to get you back to reality, because this world we live in... once you understand what i understand, you\'ll see how beautifully corrupted we have made this earth, i am here to change that. hope you enjoy the tunes everyone and welcome. im glad to be here officially.',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F7080878%3Fsecret_token%3Ds-O4Ys9&auto_play=true',
				'links' => array(
					'soundcloud' => 'http://soundcloud.com/commonsen5e',
				),
			),
			///////////////////////////////////////////////////////////////
			'stormanimal' => (object)array(
				'key' => 'stormanimal',
				'name' => 'StormAnimal',
				'shortInfo' => 'Experimental style Producer & Radio DJ',
				'longInfo' => 'Even as a young child he was interested in music and learned to play piano for 6 years. StormAnimal started to produce electronic music in june 2010. He already created his own StormAnimal Style & Comedylectro. StormAnimal Style is just he! Already produced over 70 Tracks. Makes his own music videos and track covers. StormAnimal is resident radio host for popsoundradio.de! He´s got over 40000 Downloads at mp3tht.de! He also did collaborations, remixes & dj mixes, but its not his focus. StormAnimal´s biggest dream to give the world his StormAnimal style to forget reality and get into another universe!',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5571626%3Fsecret_token%3Ds-UioyT&auto_play=true',
			),
			///////////////////////////////////////////////////////////////
			'aldrich' => (object)array(
				'key' => 'aldrich',
				'name' => 'Aldrich',
				'shortInfo' => 'Deep House Producer',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5764374%3Fsecret_token%3Ds-Bp6yn&auto_play=true',
			),
		);
		
		$this->data['labels'] = array(
			'main' => (object)array(
				'key' => 'main',
				'name' => 'Thirtyeight Eightysix',
				'shortInfo' => 'The home of various electronic music artists',
				'longInfo' => 'Thirtyeight Eightysix is the main Label of 3886records headquartered in Bonn, Germany. It was founded by Spekta in the end of 2012. It\'s name is an allusion to the high performance audio amplifier LM3886. Talented artists from all over the world were scraped together to promote outstanding music for no charges. The label quickly moved on to professional distribution to reach even more people.',
				'links' => array(
					'facebook' => 'http://www.facebook.com/3886records',
					'twitter' => 'http://twitter.com/3886records',
				),
				'address' => 'Andreas Linden 3886records<br/>Agnesstr. 3a<br/>53225 Bonn<br/>Germany',
				'phone' => '+49 228 2275587',
				'email' => '<i>General purposes:</i> <a href="mailto:info@3886records.de">info@3886records.de</a><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Demo submissions:</i> <a href="mailto:demos@3886records.de">demos@3886records.de</a>',
			),
			'enchanted' => (object)array(
				'key' => 'enchanted',
				'name' => 'Enchanted Recordings',
				'shortInfo' => 'Pure Trance Sublabel',
				'longInfo' => 'Enchanted Recordings is a Trance sub-label of 3886records, which has got its headquarters in The Hague, Netherlands. It was founded by Spekta and Aljosha Konstanty in 2013.<a href="http://soundcloud.com/enchanted-recordings/dropbox" class="soundcloud-dropbox inline-dropbox">Send us your sounds</a>',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5029204&amp;auto_play=true',
				'email' => '<i>General purposes:</i> <a href="mailto:info@enchantedrecordings.com">info@enchantedrecordings.com</a><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>Demo Submissions:</i> <a href="mailto:demo@enchantedrecordings.com">demo@enchantedrecordings.com</a>'
			),
		);
		
		$this->data['genres'] = array(
			'psy' => (object)array(
				'key' => 'psy',
				'name' => 'Psy/Prog (Goa)',
				'shortInfo' => '',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5784479%3Fsecret_token%3Ds-47XgH&auto_play=true',
			),
			'trance' => (object)array(
				'key' => 'trance',
				'name' => 'Trance',
				'shortInfo' => '',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5793641%3Fsecret_token%3Ds-oSCVn&auto_play=true',
			),
			'house' => (object)array(
				'key' => 'house',
				'name' => 'House',
				'shortInfo' => '',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5793665%3Fsecret_token%3Ds-kBbXs&auto_play=true',
			),
			'chillout' => (object)array(
				'key' => 'chillout',
				'name' => 'Chillout',
				'shortInfo' => '',
				'soundcloud' => 'https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5793726%3Fsecret_token%3Ds-p56yD&auto_play=true',
			),
		);
		
		$this->data['releases'] = array(
			'all' => array(
				(object)array(
					'artist' => 'Aljosha Konstanty',
					'title' => 'On the edge',
					'genre' => 'Trance',
					'cover' => 'Aljosha Konstanty - On the edge (200x200).jpg',
					'date' => '2013-06-10',
					'format' => 'Digital',
					'type' => 'Single',
					'links' => array(
						'Beatport' => 'http://www.beatport.com/release/on-the-edge/1100003',
					)
				),
				(object)array(
					'artist' => 'Spekta',
					'title' => 'Delusions',
					'genre' => 'Progressive Psytrance',
					'cover' => 'Spekta - Delusions (200x200).jpg',
					'date' => '2013-07-01',
					'format' => 'Digital',
					'type' => 'Single',
				),
				(object)array(
					'artist' => 'Spekta',
					'title' => 'Addiction',
					'genre' => 'Progressive Psychill',
					'cover' => 'Spekta - Addiction (200x200).jpg',
					'date' => '2013-07-08',
					'format' => 'Digital',
					'type' => 'Single',
				),
				(object)array(
					'artist' => 'Spekta',
					'title' => 'Cobitis Taenia',
					'genre' => 'Progressive Psychill',
					'cover' => 'Spekta - Cobitis-Taenia (200x200).jpg',
					'date' => '2013-07-15',
					'format' => 'Digital',
					'type' => 'Single',
				),
			),
			'upcomming' => array(
				(object)array(
					'artist' => 'Shinson',
					'title' => 'In my mind',
					'genre' => 'Trance',
					'cover' => 'default.jpg',
					'date' => 'tba',
					'format' => 'Digital',
					'type' => 'Single',
				),
				(object)array(
					'artist' => 'Shinson',
					'title' => 'Arctic Warrior',
					'genre' => 'Trance',
					'cover' => 'default.jpg',
					'date' => 'tba',
					'format' => 'Digital',
					'type' => 'Single',
				),
				(object)array(
					'artist' => 'Mind Lab',
					'title' => 'Antarctic Dreams',
					'genre' => 'Trance',
					'cover' => 'antarcticdreams.jpg',
					'date' =>  '2013-08-14',
					'format' => 'Digital',
					'type' => 'Single',
				),
				(object)array(
					'artist' => 'Spirtualzune',
					'title' => 'Psychedelic Spirit EP',
					'genre' => 'Profressive Psytrance',
					'cover' => 'Spirtualzune - Psychedelic Spirit EP (200x200).jpg',
					'date' => '2013-08-20',
					'format' => 'Digital',
					'type' => 'EP',
				),
				(object)array(
					'artist' => 'CannaBliss',
					'title' => 'Spykedelic EP',
					'genre' => 'Profressive Psytrance',
					'cover' => 'CannaBliss - Spykedelic EP (200x200).jpg',
					'date' => '2013-09-06',
					'format' => 'Digital',
					'type' => 'EP',
				),
				(object)array(
					'artist' => 'CommonSen5e',
					'title' => 'Until the end of time',
					'genre' => 'Chillout',
					'cover' => 'CommonSen5e - Until The End Of time - Small.jpg',
					'date' => '2013-09-14',
					'format' => 'Digital',
					'type' => 'Album',
				),
				/*
				(object)array(
					'artist' => 'Bernardo Gigante',
					'title' => 'Beginning of a Friendship',
					'genre' => 'Trance',
					'cover' => 'default.jpg',
					'date' => 'tba',
					'format' => 'Digital',
					'type' => 'Single',
				),
				*/
			),
		);

		$this->data['events'] = array(
			(object)array(
				'name' => 'Organic Jungle',
				'date' => '2013-09-27',
				'time' => '21:00',
				'location' => 'Theatherfabrik München',
				'image' => 'jungle.jpg',
				'artists' => array(
					'psybuddy' => 'Progressive Psytrance Live Act',
				),
				'facebook' => 'http://www.facebook.com/events/100631313461835/',
			),
			(object)array(
				'name' => 'Goa4All',
				'date' => '2013-10-11',
				'time' => '23:00',
				'location' => 'Barbarossaplatz Köln',
				'image' => 'default.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ Set',
				),
				#'facebook' => '',
			),
			(object)array(
				'name' => '3 Tage Wach',
				'date' => '2013-05-24',
				'time' => '22:00',
				'location' => 'N8Lounge Bonn',
				'image' => 'bonngoa2.png',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/282422095224210/',
			),
			(object)array(
				'name' => 'SunSplash',
				'date' => '2013-06-15',
				'time' => '23:00',
				'location' => 'Kunstpark Köln',
				'image' => 'sunsplash.jpg',
				'artists' => array(
					'spekta' => 'Trance Live-Performance',
				),
				'facebook' => 'https://www.facebook.com/events/539297896093739/',
			),
			(object)array(
				'name' => 'Goa N8lounge #3',
				'date' => '2013-06-22',
				'time' => '22:00',
				'location' => 'N8lounge Bonn',
				'image' => 'bonngoa.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
					'zwielicht' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/432130930209903/',
			),
			(object)array(
				'name' => 'I WANT YOU...FOR GOA',
				'date' => '2013-06-29',
				'time' => '20:00',
				'location' => 'Cologne Outdoor',
				'image' => 'iwantyou.png',
				'artists' => array(
					'spekta' => 'Progressive Trance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/456992084396884',
			),
			(object)array(
				'name' => 'PsyBeach',
				'date' => '2013-07-13',
				'time' => '14:00',
				'location' => 'Tortuga Beachbar Alsdorf',
				'image' => 'psybeach.png',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/542119655846311/',
			),
			(object)array(
				'name' => 'PULSE #4',
				'date' => '2013-07-26',
				'time' => '22:00',
				'location' => '4ELEMENTS Paris',
				'image' => 'pulse4.jpg',
				'artists' => array(
					'aldrich' => 'DJ-Set',
				),
			),
			(object)array(
				'name' => 'Goa Goa Bunga Bunga',
				'date' => '2013-07-27',
				'time' => '22:00',
				'location' => 'N8Lounge Bonn',
				'image' => 'bunga.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/331263030310549/',
			),
			(object)array(
				'name' => 'Psychedelic TanzTraum',
				'date' => '2013-08-16',
				'time' => '18:00',
				'location' => 'Cologne Outdoor',
				'image' => 'tanztraum.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/428339923947583/',
			),
			(object)array(
				'name' => 'Psytrance meets Trash',
				'date' => '2013-08-23',
				'time' => '22:00',
				'location' => 'N8lounge Bonn',
				'image' => 'trash.png',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/117054781836249/',
			),
			(object)array(
				'name' => 'GOA Werkstatt',
				'date' => '2013-08-24',
				'time' => '22:00',
				'location' => 'Die Werkstatt Köln',
				'image' => 'werkstatt.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'http://www.facebook.com/events/169374586578849/',
			),
			(object)array(
				'name' => 'Sunsplash',
				'date' => '2013-08-31',
				'time' => '23:00',
				'location' => 'Kunstpark Cologne / Henry Ford Beach',
				'image' => 'sunsplash2.png',
				'artists' => array(
					'spekta' => 'Trance Live-Performance + Progressive DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/241690399288976/',
			),
			(object)array(
				'name' => 'Goa N8Lounge #4',
				'date' => '2013-09-14',
				'time' => '22:00',
				'location' => 'N8Lounge Bonn',
				'image' => 'bonngoa4.png',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/247899552015321',
			),
			(object)array(
				'name' => 'CHILL GOA',
				'date' => '2013-07-21',
				'time' => '08:00',
				'location' => 'Oberkassel Beach',
				'image' => 'oberkassel.jpg',
				'artists' => array(
					'spekta' => 'Progressive Psytrance DJ-Set',
					'zwielicht' => 'Progressive Psytrance DJ-Set',
				),
				'facebook' => 'https://www.facebook.com/events/247899552015321',
			),
		);
	}
	
	// 0 = all
	// 1 = upcomming
	// 2 = past
	public function getEvents($which = 1, $artist = null, $includeArtist = false) {
	
		if ($which === 0 && $artist === null) {

			$events = $this->data['events'];

		} else {

			$events = array();
			$today = strtotime(date("Y-m-d 00:00:00"));
			foreach ($this->data['events'] as $event) {

				if ($which === 1 && $today > strtotime($event->date)) {

					continue;
				}

				if ($which === 2 && $today <= strtotime($event->date)) {

					continue;
				}

				if (null !== $artist && !array_key_exists($artist, $event->artists)) {

					continue;
				}

				$events[] = $event;
			}
		}

		if ($includeArtist) {

			foreach ($events as &$event) {

				$event->acts = array();
				foreach ($event->artists as $artistKey => $actInfo) {

					$artist = $this->getArtist($artistKey);
					$event->acts[] = (object)array(
						'artist' => $artist,
						'info' => $actInfo,
					);
				}
			}
		}

		if ($which == 2) {
		
			usort($events, function($a, $b) {
			
				return $a->date < $b->date;
			});
		
		} else {
		
			usort($events, function($a, $b) {
			
				return $a->date > $b->date;
			});
		}
		
		return $events;
	}
	
	public function getLabels() {
	
		return $this->data['labels'];
	}
	
	public function getLabel($name) {
	
		$key = strtolower($name);
		if (array_key_exists($key, $this->data['labels'])) {
		
			return $this->data['labels'][$key];
		}
		
		return null;
	}	
	
	public function getArtist($name) {
	
		$key = strtolower($name);
		if (array_key_exists($key, $this->data['artists'])) {
		
			return $this->data['artists'][$key];
		}
		
		return null;
	}
	
	public function getArtists() {
	
		return $this->data['artists'];
	}

	public function getGenre($name) {
	
		$key = strtolower($name);
		if (array_key_exists($key, $this->data['genres'])) {
		
			return $this->data['genres'][$key];
		}
		
		return null;
	}
	
	public function getGenres() {
	
		return $this->data['genres'];
	}	
	
	public function getReleases($type) {
	
		$releases = $this->data['releases'][$type];
		usort($releases, function($a, $b) {
			
			return $a->date > $b->date;
		});
	
		return $releases;
	}
}