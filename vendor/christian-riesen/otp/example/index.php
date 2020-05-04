<?php

session_start(); // using it as storage temporary

require_once __DIR__ . '/../vendor/autoload.php';

use Otp\Otp;
use Otp\GoogleAuthenticator;
use Base32\Base32;

// Getting a secret, either by generating or from storage
// DON'T use sessions as storage for this in production!!!
$secret = 0;

if (isset($_SESSION['otpsecret'])) {
	$secret = $_SESSION['otpsecret'];
}

if (strlen($secret) != 16) {
	$secret = GoogleAuthenticator::generateRandom();
	$_SESSION['otpsecret'] = $secret;
}

// The secret is now an easy stored Base32 string.
// To use it in totp though we need to decode it into the original
$otp = new Otp();

$currentTotp = $otp->totp(Base32::decode($secret));

$qrCode = GoogleAuthenticator::getQrCodeUrl('totp', 'otpsample@cr', $secret);
$keyUri = GoogleAuthenticator::getKeyUri('totp', 'otpsample@cr', $secret);

?><html>
<head>
<title>One Time Passwords Example</title>
</head>
<body>

<h1>One Time Passwords Example</h1>

Secret is <?php echo $secret; ?>. This is saved with the users credentials.
<br />
<br />
<hr />

QR Code for totp:<br />
<img src="<?php echo $qrCode; ?>" />
<br />
This QR Code contains the Key URI: <?php echo $keyUri; ?>
<br />
<hr />

Current totp would be <?php echo $currentTotp; ?><br />
<br />
<hr />

Because of timedrift, you could technically enter a code before or after it
would actually be used. This form uses the checkTotp function. To test this,
open this page, wait until the key changes once or twice (not more) on your
Google Authenticator, then hit submit. Even though the key is "wrong" because of
small time differences, you can still use it.
<form action="" method="post">
<input type="text" name="otpkey" value="<?php echo $currentTotp; ?>" /><br />
<input type="submit">
</form>

<br />
Output:<br />
<br />


<?php

if (isset($_POST['otpkey'])) {
	// Sanatizing, this should take care of it
	$key = preg_replace('/[^0-9]/', '', $_POST['otpkey']);
	
	// Standard is 6 for keys, but can be changed with setDigits on $otp
	if (strlen($key) == 6) {
		// Remember that the secret is a base32 string that needs decoding
		// to use it here!
		if ($otp->checkTotp(Base32::decode($secret), $key)) {
			echo 'Key correct!';
			// Add here something that makes note of this key and will not allow
			// the use of it, for this user for the next 2 minutes. This way you
			// prevent a replay attack. Otherwise your OTP is missing one of the
			// key features it can bring in security to your application!
		} else {
			echo 'Wrong key!';
		}
		
	} else {
		echo 'Key not the correct size';
	}
}

?>

</body>
</html>
