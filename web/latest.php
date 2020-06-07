<?php
    require_once("authentication.php");

    $types = array();

		chdir("files");
    $files = scandir(".", SCANDIR_SORT_NONE);
    foreach($files as $dir){
        if(is_dir($dir) && $dir != "." && $dir != "..") {
            // Search type metadata
            $dir_files = scandir($dir);
            $dir_types = preg_grep("/^.type.*/", $dir_files);


            if(count($dir_types) == 1){
                $dir_type = reset($dir_types);
               $file_type = substr($dir_type, 6, strlen($dir_type));
            }else{
               $file_type = 'unknown';
            }

            if(!array_key_exists($file_type, $types)){
                $types[$file_type] = array();
            }

            array_push($types[$file_type], array(
                "url" => "<a target=\"_blank\" href=\"".BASE_URL.$dir."/\">".$dir."</a>",
                "time" => filemtime($dir)
            ));
        }
    }


    function cmp($a, $b)
    {
        if ($a["time"] == $b["time"]) {
            return 0;
        }
        return ($a["time"] > $b["time"]) ? -1 : 1;
    }

    foreach ($types as $key => $value) {
        usort($types[$key], "cmp");
        $types[$key] =  array_slice($types[$key], 0, 10, true);
    }

?>

<html>
<header>
<style>
	table {
		font-family: arial, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}

	td, th {
		border: 1px solid #bfbfbf;
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even) {
		background-color: #616161;
	}
	
	a {
		display: block;
		color: #fff;
		padding: 8px 16px;
		text-decoration: none;
	}
</style>
</header>
<body style="background: #2b2b2b; color:#fff; margin-right:50px;">
<h1>Latest</h1>

<?php
	foreach ($types as $key => $value) {
		print '
			<h2>Latest '.$key.':</h2>
			<table style="margin-left:50px; margin-right: 50px; width: 80%">
				<tr><th>URl</th><th style="width: 20%">Modified</th></tr>';
					foreach ($types[$key] as $file) {
						echo "<tr><td >".$file["url"]."</td><td >".date("Y-m-d H:i:s",$file["time"])."</td></tr>";
					}
		print '
			</table>
		';
	}
?>
</body>
</html>
