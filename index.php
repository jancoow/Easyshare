<?php
	function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return strtolower($ext);
	}
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$randomname = substr(md5(uniqid(mt_rand(), true)), 0, 5);
		$images = array("png", "jpg", "jpeg", "gif");
		$music = array("mp3", "ogg", "flac", "m4a");
		$video = array("mp4", "avi", "mkv", "mpeg");
		
		
		if(isset($_POST['url']) && $_POST['url'] != ""){ 										//URL
			mkdir($randomname);
			$file = fopen($randomname."/index.php", "w");
			fwrite($file, "<?php header('Location: ".$_POST['url']."'); ?>");
			fclose($file);
		}else if(isset($_POST['code']) && $_POST['code'] != ""){								//CODE
			mkdir($randomname);
			$file = fopen($randomname."/index.html", "w");
			$code = $_POST['code'];
			$code = str_replace('<', '&lt;', $code);
			$code = str_replace('>', '&gt;', $code);
			fwrite($file, '<html><head><script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script></head><body><pre class="prettyprint">'.$code.'</pre></body></html>');
			fclose($file);					
		}else if(isset($_FILES['file'])){														//FILE
			mkdir($randomname);
			move_uploaded_file($_FILES['file']['tmp_name'], $randomname."/".$randomname);
			$ex = getExtension($_FILES['file']['name']);
			$file = fopen($randomname."/index.php", "w");

			if(in_array($ex, $images)){															//IMAGES
				fwrite($file, '<img src='.$randomname.' />');
			}else if(in_array($ex, $video)){													//VIDEO
				fwrite($file, ' <video width="320" height="240" controls><source src="'.$randomname.'" type="video/'.$ex.'">Browser ondersteund dit niet.</video>');
			}else if(in_array($ex, $music)){													//MUSIC
				fwrite($file, '<audio controls><source src="'.$randomname.'" type="audio/'.$ex.'">Browser ondersteund dit niet.</audio>');
			}else{
				$name = $randomname.".".$ex;													//other file
				fwrite($file,"<?php header('Content-Type: application/octet-stream'); header(\"Content-Transfer-Encoding: Binary\"); header(\"Content-disposition: attachment; filename=$name\"); readfile(\"$randomname\"); ?>");
			}	
			fclose($file);			
		}
		
		$link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]".$randomname;
		if(isset($_POST['plain'])){
			echo $link;
			die();
		}else{
			echo '<a href="'.$link.'">'.$link.'</a>';			
		}
	}
?>

<html>
	<header>
		<meta name="description" content="A linux/php script to easy share media files, url's or documents.">
		<meta name="author" content="Janco Kock">
	</header>
	<body>
		<form method="post" enctype="multipart/form-data" action="">
			URL: <input placeholder="URL" type="url" name="url" /> <br />
			Or upload file: <input name="file" type="file" /> <br />
			Or code			<textarea name="code" ></textarea>  <br />			
			<input class="btn btn-default" name="Submit" type="submit" value="upload">
		</form>
	</body>
</html>
