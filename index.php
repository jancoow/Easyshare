<?php
require_once("settings.php");
header('Content-type: application/json');

if (empty($_SERVER['HTTP_API_KEY']) && !in_array($_SERVER['HTTP_API_KEY'], Settings::API_KEYS)) {
    echo json_encode(
        array(
            "success" => false,
            "error" => "forbidden"
        )
    );
}

$random_name = substr(md5(uniqid(mt_rand(), true)), 0, 5);

//Upload image
if (!empty($_FILES['file']) && exif_imagetype($_FILES['file']['tmp_name'])) {
    $random_name = "i" . $random_name;
    mkdir($random_name);
    move_uploaded_file($_FILES['file']['tmp_name'], $random_name . "/" . $random_name);
    $file = fopen($random_name . "/index.php", "w");
    fwrite($file, '
        <html>
            <body>
                <img src=' . $random_name . ' />
            </body>
        </html>
    ');
    fclose($file);
    die(json_encode(array("success" => true, "url" => "https://tvw.me/" . $random_name)));
}
//Upload file
if (!empty($_FILES['file']) && !exif_imagetype($_FILES['file']['tmp_name'])) {
    $random_name = "f" . $random_name;
    mkdir($random_name);
    move_uploaded_file($_FILES['file']['tmp_name'], $random_name . "/" . $random_name);
    $file = fopen($random_name . "/index.php", "w");
    $name = $_FILES['file']['name'];
    fwrite($file,"
        <?php
            header('Content-Type: application/octet-stream'); 
            header('Content-Transfer-Encoding: Binary'); 
            header('Content-disposition: attachment; filename=\"$name\"'); 
            readfile(\"$random_name\"); 
        ?>
    ");
    fclose($file);
    die(json_encode(array("success" => true, "url" => "https://tvw.me/" . $random_name)));
}
//Upload url
if (!empty($_POST['input']) && filter_var($_POST['input'], FILTER_VALIDATE_URL)) {
    $random_name = "u" . $random_name;
    mkdir($random_name);
    $file = fopen($random_name . "/index.php", "w");
    fwrite($file, "
        <?php 
            header('Location: " . $_POST['input'] . "'); 
        ?>
    ");
    fclose($file);
    die(json_encode(array("success" => true, "url" => "https://tvw.me/" . $random_name)));
}
//Upload text
if (!empty($_POST['input']) && filter_var($_POST['input'], FILTER_VALIDATE_URL) === false) {
    $random_name = "t" . $random_name;
    mkdir($random_name);
    $file = fopen($random_name . "/index.html", "w");
    $input = $_POST['input'];
    $input = str_replace('<', '&lt;', $input);
    $input = str_replace('>', '&gt;', $input);
    fwrite($file, '
        <html>
            <head>
			<link rel="stylesheet"
				href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/styles/darcula.min.css">
			<script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.17.1/build/highlight.min.js"></script>
            </head>
            <body style="background: #2b2b2b;">
                <pre><code>' . $input . '</code></pre>
				<script>hljs.initHighlightingOnLoad();</script>
            </body>
        </html>');
    fclose($file);
    die(json_encode(array("success" => true, "url" => "https://tvw.me/" . $random_name)));
}