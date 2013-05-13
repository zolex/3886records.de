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
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F25218273&amp;color=0090ff&amp;auto_play=false&amp;show_artwork=true"></iframe>',
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
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F16017898&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'euphoristix':
				return (object)array(
					'key' => 'euphoristix',
					'name' => 'Euphoristix',
					'shortInfo' => 'Chillout / Downtempo & Trance producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F34894782&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'stormanimal':
				return (object)array(
					'key' => 'stormanimal',
					'name' => 'StormAnimal',
					'shortInfo' => 'StormAnimal Style Producer & Radio DJ',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F36670015&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
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
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F855292&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			case 'darwinxp':
				return (object)array(
					'key' => 'darwinxp',
					'name' => 'Darwin Experience',
					'shortInfo' => 'House Producer',
					'soundcloud' => '<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fusers%2F446510&amp;color=0090ff&amp;auto_play=true&amp;show_artwork=true"></iframe>',
				);

			default:
				return null;
		}
	}
}
