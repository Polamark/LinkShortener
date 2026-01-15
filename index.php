<?php

require_once 'src/shortener.php';
date_default_timezone_set("UTC");

;

$url_pattern = "/^https?:\\/\\/(?:www\\.)?[-a-zA-Z0-9@:%._\\+~#=]{1,256}\\.[a-zA-Z0-9()]{1,6}\\b(?:[-a-zA-Z0-9()@:%_\\+.~#?&\\/=]*)$/";
$url_shortened_pattern = $pattern = '/^' . (empty($_SERVER['HTTPS']) ? 'http' : 'https') . ':\/\/'. explode(".", $_SERVER['HTTP_HOST'])[0] .'\.' . explode(".", $_SERVER['HTTP_HOST'])[1] .'\/index\.php\?url=[a-zA-Z0-9]*$/';
$shortened_url = "";
$analytics_text = "";

if (isset($_POST["url"])) {
    if (preg_match($url_pattern, $_POST["url"])) {
        if (preg_match($url_shortened_pattern, $_POST["url"])) {
            $analytics_text = "The url has been created on " . date('l jS \of F Y h:i:s A', getURL(explode("=", $_POST["url"])[1])[2] ?? 0) . " UTC and in that time it has gotten " . (getURL(explode("=", $_POST["url"])[1])[3] ?? 0) . " visits";
        } else {
            $shortened_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://". $_SERVER['HTTP_HOST'] . "/index.php?url=" . shortenURL($_POST["url"]);
        }
    }
}

//Redirect to the long URL
if (isset($_GET["url"])) {

    if (getURL($_GET["url"])[0] != "non_existent_url") {
        header("location: " . getURL($_GET["url"], true)[1]);;
    } else {
        header("location: index.php");
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="src/output.css">
    <title>Shortener</title>
</head>
<body class="w-screen min-h-screen flex flex-col justify-center items-center gap-5 bg-gray-950">
<h1 class="text-white text-5xl">Link Shortener</h1>
<form action="index.php" method="post" class="flex gap-3 bg-gray-950">
    <label for="url" class="hidden">URL/analytics accessor</label>
    <input id="url" type="text" name="url" value="<?php if (isset($_POST['url'])) { echo $_POST['url'];}?>" placeholder="Enter URL or paste shortened URL to access statistics"
           class="text-lg w-150  text-white border-white px-3">
    <button type="submit" class="bg-orange-600 font-medium text-white w-50 text-3xl rounded-2xl py-1.5">Shorten</button>
</form>
<?php if ($shortened_url != ""): ?>
    <a href="<?php echo $shortened_url; ?>" target="_blank"
       class="text-white text-xl mt-5"><?php echo $shortened_url; ?></a>
<?php endif; ?>
<?php if ($analytics_text != ""): ?>
    <p class="text-white text-xl mt-5"><?php echo $analytics_text; ?></p>
<?php endif; ?>
</body>
</html>
