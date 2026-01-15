<?php
declare(strict_types=1);

/**
 * Creates a record for a given URL
 *
 * @param string $longURL
 * @return string The URL ID
 */
function shortenURL(string $longURL): string {
    $data = fopen("src/data.txt", "a+") or die("Unable to open file!");
    $urlid = uniqid();
    fwrite($data, $urlid . " | " . $longURL . " | " . strval(time()) . " | " . "0\n");
    fclose($data);
    return $urlid;
}

/**
 * Gets all the data associated with a given URL
 *
 * @param string $urlid
 * @param bool $is_visiting
 * @return array|string[] The URL ID, the long URL, the time of creation, and the visit count
 */
function getURL(string $urlid, bool $is_visiting = false): array {
    $data = fopen("src/data.txt", "r+") or die("Unable to open file!");
    $text = fread($data, filesize("src/data.txt"));
    $lines = explode("\n", $text);
    for ($pos = 0; $pos < count($lines); $pos++) {
        $line = explode(" | ", $lines[$pos]);
        if ($line[0] == $urlid) {
            if ($is_visiting) {
                $visit_count = intval($line[3]) + 1;
                updateLine($data, $pos, $line[0], $line[1], $line[2], strval($visit_count));
            }
            fclose($data);
            return [$line[0], $line[1], $line[2], $line[3]];

        }
    }
    fclose($data);
    return ["non_existent_url"];
}

function updateLine($file, int $line_number, string $urlid, string $url, string $time, string $visits): void
{
    fseek($file, 0);
    $lines = explode("\n", fread($file, filesize("src/data.txt")));
    $lines[$line_number] = $urlid . " | " . $url . " | " . $time . " | " . $visits;
    ftruncate($file, 0);
    fseek($file, 0);
    fwrite($file, implode("\n", $lines));
}
