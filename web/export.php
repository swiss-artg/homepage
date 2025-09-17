<?php

/**
 * This function implements the algorithm outlined
 * in RFC 6238 for Time-Based One-Time Passwords
 *
 * @link http://tools.ietf.org/html/rfc6238
 * @param string $key    the string to use for the HMAC key
 * @param mixed  $time   a value that reflects a time (unix time in the example)
 * @param int    $digits the desired length of the OTP
 * @param string $algo   the desired HMAC hashing algorithm
 * @return string the generated OTP
 */
function totp($key, $time, $digits=6, $algo='sha1') {
    $digits = intval($digits);
    $data = pack('NNC*', $time >> 32, $time & 0xFFFFFFFF);
    $data = str_pad($data, 8, chr(0), STR_PAD_LEFT);
    $hash = hash_hmac($algo, $data, $key);
    $offset = 2 * hexdec(substr($hash, strlen($hash) - 1, 1));
    $binary = hexdec(substr($hash, $offset, 8)) & 0x7fffffff;
    $result = $binary % pow(10, $digits);
    $result = str_pad($result, $digits, "0", STR_PAD_LEFT);

    return $result;
}

$dirname = realpath(__DIR__ . '/..');
$filename = $dirname . '/private/archive.tar';

try {
  $totp = $_POST['totp'] ?? null;
  if ($totp != totp(base64_decode('81GUnotStNFiPc4wjr0dq/Sg3cg='), floor(time() / 30))) {
    http_response_code(404);
    die();
  }

  if (file_exists($filename)) {
    unlink($filename);
  }

  $excluded = file($dirname . '/.ftpignore', FILE_IGNORE_NEW_LINES);

  $dir = new RecursiveDirectoryIterator($dirname);
  $files = new RecursiveCallbackFilterIterator($dir, function($file, $key, $iterator) use ($dirname, $excluded){
    $relname = substr($file->getRealPath(), strlen($dirname) + 1);

    if ($relname == "") {
      return false;
    }

    foreach ($excluded as $ex) {
      if (fnmatch($ex, $relname)) {
        return false;
      }
    }

    return $iterator->hasChildren() || $file->isFile();
  });

  $tar = new PharData($filename);
  $tar->buildFromIterator(new RecursiveIteratorIterator($files), $dirname);

  ob_clean();

  header('Content-Type: "application/x-tar"');
  header('Content-Disposition: attachment; filename="archive.tar"');
  header("Content-Transfer-Encoding: binary");
  header('Expires: 0');
  header('Pragma: no-cache');
  header("Content-Length: " . filesize(trim($filename)));

  $fp = fopen($filename, "r");
  fpassthru($fp);
  fclose($fp);
}
catch (Exception $e) {
  http_response_code(500);
	die($e);
}
