<?php
/**
 * Create a tar archive of most of the files and provide it as download.
 *
 * This is used in CI/CD to build up a cache mirroring the application root.
 * A TOTP token is used as a primitve authorization.
 */

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

// The application root is the parent directory.
$dirname = realpath(__DIR__ . '/..');
// Location to store the download archive.
$filename = $dirname . '/private/archive.tar';

// TOTP access check.
$totp = $_POST['totp'] ?? null;
if ($totp != totp(base64_decode('81GUnotStNFiPc4wjr0dq/Sg3cg='), floor(time() / 30))) {
  http_response_code(404);
  die();
}

// Create the archive.
$cmd = 'tar --owner 0 --group 0 --exclude-from ' . escapeshellarg($dirname . '/.ftpignore') . ' -cf ' . escapeshellarg($filename) . ' --directory ' . escapeshellarg($dirname) . ' ./';
exec($cmd, $output, $code);
if (0 !== $code) {
  http_response_code(500);
  foreach ($output as $line) {
    echo $line . PHP_EOL;
  }
  die("failed to create archive ({$code})");
}

// Clean output buffer and write new set of response headers.
ob_clean();
header('Content-Type: "application/x-tar"');
header('Content-Disposition: attachment; filename="archive.tar"');
header("Content-Transfer-Encoding: binary");
header('Expires: 0');
header('Pragma: no-cache');
header("Content-Length: " . filesize(trim($filename)));

// Send the file.
readfile($filename);
