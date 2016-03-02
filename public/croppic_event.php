<html>
    <head>
        <link href="/css/croppic.css" rel="stylesheet">
        <script src="/js/jquery-1.9.1.js"></script>
        <script src="/js/croppic.min.js"></script>
    </head>
    <body>
    	<input type="hidden" name="croppic_output_url" id="croppic_output_url">
    	<div id="croppicwrapper" style="width: 200px; height: 200px; position: relative;">
    		<img id="currentImg" src="/img/events/<?= $_GET['flyer'] ?>">
    	</div>
    	
    	
    	<script>
            $(document).ready(function() {
            
            	var cropperHeader = new Croppic('croppicwrapper', {
            		onBeforeImgUpload: function() {
						$('#currentImg').remove();
            		},
            		onAfterImgCrop: function() {
						$(parent.document).find('#croppic_output_url').val($('#croppic_output_url').val());
            		},
            		outputUrlId: 'croppic_output_url',
            		uploadUrl: '/event_save_to_file.php',
					cropUrl: '/event_crop_to_file.php',
            		cropData:{
        				'key': '<?= $_GET['flyer'] ?>'
        			}
            	});
            });
        </script>
    </body>
</html>