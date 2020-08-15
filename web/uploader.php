<?php
require_once("authentication.php");
header('Content-type: application/json');

function getExtension($str) {
    return pathinfo($str, PATHINFO_EXTENSION);
}

function scaleImage($x,$y,$cx,$cy) {
    list($nx,$ny)=array($x,$y);

    if ($x>$cx || $y>$cx) {
        if ($x>0) $rx=$cx/$x;
        if ($y>0) $ry=$cy/$y;

        if ($rx>$ry) {
            $r=$ry;
        } else {
            $r=$rx;
        }

        $nx=intval($x*$r);
        $ny=intval($y*$r);
    }

    return array($nx,$ny);
}

function saveType($dir, $type){
   touch($dir.'/.type.'.$type);
}

function returnUrl($dir_name, $base_url){
	if(isset($_GET["plain"])){
		die($base_url . $dir_name. "/");
	}else{
		die(json_encode(array("success" => true, "url" => $base_url . $dir_name. "/")));
	}
}

$base_url = BASE_URL;
chdir("files");
$dir_name = substr(md5(uniqid(mt_rand(), true)), 0, 5);

if($_SERVER['REQUEST_METHOD'] == "POST"){
    // Create unique directory where the files will be placed
    mkdir($dir_name);

    if(!empty($_POST['url']) ){
        $_POST['snippet'] = $_POST['url'];
    }
    

    if (!empty($_FILES['file']) && file_exists($_FILES['file']["tmp_name"]) && is_uploaded_file($_FILES['file']['tmp_name']) ) {
    // Upload file

        if($_POST["randomName"]){
            $file_name = $dir_name . "." . getExtension($_FILES['file']['name']);
        }else{
            $file_name = $_FILES['file']['name'];
        }

        if(exif_imagetype($_FILES['file']['tmp_name'])){
		      // Upload image
		      saveType($dir_name, 'img');

		      if($_POST["exifdata"] || $_POST["compressImage"]){
		          $img = new Imagick($_FILES['file']['tmp_name']);

		          if($_POST["exifdata"]){
					  
					$exif = exif_read_data($_FILES['file']['tmp_name']);
					$orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;


					if (!empty($orientation)) {
							switch ($orientation) {
								case 3:
									$img->rotateImage('#000000', 180);
									break;

								case 6:
									$img->rotateImage('#000000', 90);
									break;

								case 8:
									$img->rotateImage('#000000', -90);
									break;
							}
					 }
					 
		             $img->stripImage();                          
		          }
		          if($_POST["compressImage"]){
		              # Only scale if larger then full HD
		              if($img->getImageWidth() > 1920 || $img->getImageHeight() > 1920){
		                  //Work out new dimensions
		                  list($newX,$newY)=scaleImage(
		                      $img->getImageWidth(),
		                      $img->getImageHeight(),
		                      1920,
		                      1920);

		                  //Scale the image
		                  $img->scaleImage($newX,$newY);
		              }
		          }

		          $img->writeImage($_FILES['file']['tmp_name']);
		      }


            move_uploaded_file($_FILES['file']['tmp_name'], $dir_name . "/" . $file_name);

            $file = fopen($dir_name . "/index.php", "w");
            fwrite($file, '
                <html>
                    <header>
                        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
                        <script src="../iv-viewer.min.js"></script>
                        <link rel="stylesheet" href="../iv-viewer.min.css">
                    </header>
                    <body style="margin: 0; background: #2b2b2b; color: #fff; overflow: hidden;">
                    </body>
                    <footer>
                        <script>
													const viewer = new ImageViewer.FullScreenViewer();
													viewer.show("' . $file_name . '");
                        </script>
                    </footer>
                </html>
            ');
            fclose($file);
						returnUrl($dir_name, $base_url);
        }else if(strstr(mime_content_type($_FILES['file']['tmp_name']), "video/")){
                   saveType($dir_name, 'video');
           move_uploaded_file($_FILES['file']['tmp_name'], $dir_name . "/" . $file_name);
        		$file = fopen($dir_name . "/index.php", "w");
            fwrite($file, '
                <html>
                    <header>
                        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
                    </header>
                    <body style="margin: 0; background: #2b2b2b; color: #fff; overflow: hidden;">
                        <div style="display: block; max-width: 100%; max-height: 100%;">
                        	<video width="720"  controls>
                        		<source src="'. $file_name.'" type="video/'.getExtension($_FILES["file"]["name"]).'">Browser does not support videos.</video>
                        </div>
                    </body>
                    <footer>
                    </footer>
                </html>
            ');
            fclose($file);
						returnUrl($dir_name, $base_url);
        }else{
            // Upload other file
           saveType($dir_name, 'file');

            move_uploaded_file($_FILES['file']['tmp_name'], $dir_name . "/" . $file_name);
            $file = fopen($dir_name . "/index.php", "w");
            $name = $_FILES['file']['name'];
            fwrite($file,"<?php
                    header('Content-Type: application/octet-stream');
                    header('Content-Transfer-Encoding: Binary');
                    header('Content-disposition: attachment; filename=\"$file_name\"');
                    readfile(\"$file_name\"); ?>");
            fclose($file);
						returnUrl($dir_name, $base_url);
        }
    }elseif (!empty($_POST['snippet']) && filter_var($_POST['snippet'], FILTER_VALIDATE_URL)) {
        //Upload redirect url
        saveType($dir_name, 'url');

        $file = fopen($dir_name . "/index.php", "w");

        fwrite($file, "
            <?php
                header('Location: " . $_POST['snippet'] . "');
            ?>
        ");
        fclose($file);
				returnUrl($dir_name, $base_url);
    }else if (!empty($_POST['snippet'])) {
        //Upload snippet
        saveType($dir_name, 'snippet');

        $file = fopen($dir_name . "/index.html", "w");
        $input = $_POST['snippet'];
        $input = str_replace('<', '&lt;', $input);
        $input = str_replace('>', '&gt;', $input);

        if($_POST['raw'] == 0){
            fwrite($file, '
                <html>
                    <head>
		                  <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/styles/darcula.min.css">
		                  <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/highlight.min.js"></script>
                    </head>
                    <body style="background: #2b2b2b;">
                        <pre><code id="code">' . $input . '</code></pre>
                        <script>hljs.initHighlightingOnLoad();</script>
                    </body>
                </html>');
        }else{
            fwrite($file, '
            <html>
                <head></head>
                <body style="background: #2b2b2b; color: #fff">
                    <pre>' . $input . '</pre>
                </body>
            </html>');
        }

        fclose($file);
        returnUrl($dir_name, $base_url);
    }else{
        rmdir($dir_name);
        die(json_encode(array("success" => false, "error" => "Could not determine type")));
    }
}
