<html>
    <head>
        <link href="/css/croppic.css" rel="stylesheet">
        <script src="/js/jquery-1.9.1.js"></script>
        <script src="/js/croppic.min.js"></script>
    </head>
    <body>

        <?php 
             $imageDir = '/srv/apache2/3886records.de/www/production/current/public';
             $imageUrl = '/img/artists/'. $_GET['key'] .'.jpg';
             $imagePath = $imageDir . $imageUrl;
             if (!is_readable($imagePath) || !is_file($imagePath)) {

                $imageUrl = '/img/artists/default.jpg';
                $imagePath = $imageDir . $imageUrl;
             }
        ?>

    	<input type="hidden" name="croppic_output_url" id="croppic_output_url">
    	<div id="croppicwrapper" style="width: 200px; height: 200px; position: relative;">
    		<img id="currentImg" src="<?= $imageUrl ?>?t=<?= filemtime($imagePath) ?>">
    	</div>
    	
    	
    	<script>
            $(document).ready(function() {
            
            	var cropperHeader = new Croppic('croppicwrapper', {
            		onBeforeImgUpload: function() {
						$('#currentImg').remove();
            		},
            		outputUrlId: 'croppic_output_url',
            		uploadUrl: '/img_save_to_file.php',
					cropUrl: '/img_crop_to_file.php',
            		cropData:{
        				'type': '<?= $_GET['type'] ?>',
        				'key': '<?= $_GET['key'] ?>'
        			}
            	});
            });
        </script>
    </body>
</html>