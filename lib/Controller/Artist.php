<?php

namespace Controller;

class Artist
{
	public function overview($request) {
	
		return array(
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Artists',
				),
			),
		);
	}

	public function booking($request) {
	
		if (null === $artist = $this->getArtist($request->getParam('artist'))) {
		
			return false;
		}
		
		$finished = false;
		$errors = array();
		if ($request->isPost()) {
			
			if (!$firstname = $request->getPost('firstname')) {
			
				$errors['firstname'] = 'Please enter your firstname.';
			}
			
			if (!$lastname = $request->getPost('lastname')) {
			
				$errors['lastname'] = 'Please enter your lastname.';
			}
			
			if (!$email = $request->getPost('email')) {
			
				$errors['email'] = 'Please enter your email address.';
			}			
			if (!$location = $request->getPost('location')) {
			
				$errors['location'] = 'Please enter the desired location.';
			}			
			if (!$location = $request->getPost('date')) {
			
				$errors['date'] = 'Please enter the desired date.';
			}	

			if (!$message = $request->getPost('message')) {
			
				$errors['message'] = 'Please add some information to let '. $artist->name .' know what you want.';
			}
			
			if (!count($errors)) {
			
				$finished = true;
			}
		}
		
		return array(
			'metaTitle' => $artist->name . ' Booking',
			'artist' => $artist,
			'errors' => $errors,
			'values' => $request->getPost(),
			'finished' => $finished,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/artists',
					'title' => 'Artists',
				),
				(object)array(
					'url' => '/artist/'. $artist->key,
					'title' => $artist->name,
				),(object)array(
					'active' => true,
					'title' => 'Booking',
				),
			),
		);
	}
	
	public function details($request) {
            
		if (null === $artist = $this->getArtist($request->getParam('artist'))) {
		
			return false;
		}
			
		return array(
			'metaTitle' => $artist->name,
			'artist' => $artist,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/artists',
					'title' => 'Artists',
				),
				(object)array(
					'active' => true,
					'title' => $artist->name,
				),
			),
		);
	}
	
	protected function getArtist($name) {
	
		switch (strtolower($name)) {
			case 'spekta':
				return (object)array(
					'key' => 'spekta',
					'name' => 'Spekta',
					'shortInfo' => 'Progressive Trance & Psytrance Producer, Club DJ & Live Act.',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692626%3Fsecret_token%3Ds-dDyJt&color=0090ff&auto_play=false&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F25218273&amp;color=0090ff&amp;auto_play=false&amp;show_artwork=true"></iframe>',
					'youtube' => array(
						'<iframe style="margin-left: auto; margin-right: auto;" width="100%" height="360" src="http://www.youtube.com/embed/bgCdjNyeQ2M?wmode=opaque&autoplay=1" frameborder="0" allowfullscreen></iframe>',
						'<iframe style="margin-left: auto; margin-right: auto;" width="100%" height="360" src="http://www.youtube.com/embed/wbBdAP91Mvs?wmode=opaque" frameborder="0" allowfullscreen></iframe>',
					),
					'longInfo' => 'Spekta started making music at the age of six years by playing the piano. After about ten years he continued his career with german Rap and HipHop DJing. During 2012 he discovered the world of electronic beats and began to perform live-mixes with royalty free samples. Later he began to produce his own samples and tracks and started to colaborate with yet rather unknown artists. In the end of 2012 he founded the electronic music label "3886records" and brought a few talented artists together.',
					'location' => 'Bonn, Germany',
					'born' => '1985-01-20',
					'links' => array(
						'facebook' => 'http://www.facebook.com/spekta85',
						'soundcloud' => 'http://www.soundcloud.com/spekta85',
						'beatport' => 'http://dj.beatport.com/spekta',
					),
					'events' => array(
						
					),
				);
				
			case 'shinson':
				return (object)array(
					'key' => 'shinson',
					'name' => 'Shinson',
					'shortInfo' => 'Trance Producer & Radio DJ',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692347%3Fsecret_token%3Ds-xzLq4&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F16017898&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'euphoristix':
				return (object)array(
					'key' => 'euphoristix',
					'name' => 'Euphoristix',
					'shortInfo' => 'Chillout / Downtempo & Trance producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692688&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F34894782&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'stormanimal':
				return (object)array(
					'key' => 'stormanimal',
					'name' => 'StormAnimal',
					'shortInfo' => 'StormAnimal Style Producer & Radio DJ',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5571626%3Fsecret_token%3Ds-UioyT&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F36670015&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'aljoshakonstanty':
				return (object)array(
					'key' => 'aljoshakonstanty',
					'name' => 'Aljosha Konstanty',
					'shortInfo' => 'Trance Producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F19697305&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'sherwee':
				return (object)array(
					'key' => 'sherwee',
					'name' => 'Sherwee',
					'shortInfo' => 'Electro / Tech / Progressive House Producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692717%3Fsecret_token%3Ds-EnYHR&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F855292&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'darwinxp':
				return (object)array(
					'key' => 'darwinxp',
					'name' => 'Darwin Experience',
					'shortInfo' => 'House Producer',
					'longInfo' => 'The Darwin Experience Project was formed in September 2011 by experienced DJ, producer and musician Ross Watson as a bold university assignment. The project is influenced by many styles of electronic and world music, with an emphasis on organic, natural sounds combined with jackin\' rhythms. The initial idea behind the project was to explore the concept of house music\'s evolution as a genre and how new styles could be created through the cross pollination of genres and subgenres. The idea of evolution is expanded not only to the music, but to the artist himself and how he can push forth the limits of his own creativity and artistic expression.',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692570%3Fsecret_token%3Ds-jfgyw&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F446510&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);
				
			case 'cosmicchuen':
				return (object)array(
					'key' => 'cosmicchuen',
					'name' => 'Cosmic Chuen',
					'shortInfo' => 'Progressive and Psychadelic Trance Producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692528%3Fsecret_token%3Ds-HcxIv&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F19757603&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);	
				
			case 'take':
				return (object)array(
					'key' => 'take',
					'name' => 'Take',
					'shortInfo' => 'Progressive and Psychadelic Trance Producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692388%3Fsecret_token%3Ds-9ObTA&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F5273198&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);
				
			case 'nopublicity':
				return (object)array(
					'key' => 'nopublicity',
					'name' => 'No Publicity',
					'shortInfo' => 'Progressive and Psychadelic Trance Producer',
					'longInfo' => 'Born in 80\'s England, grew up listening to every type of music available. Experienced the rave and dance music explosion in Britain and got into DJing at 13. Moved on to producing music in 2000. Enjoying People, Music and Life',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F5692317%3Fsecret_token%3Ds-CXLsv&color=0090ff&auto_play=true&show_artwork=false"></iframe>',
					'soundcloud-profile' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F33247796&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			default:
				return null;
		}
	}
}
