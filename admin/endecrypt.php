<?php
class Encryption {


    protected $cipher = '';


    protected $mode = '';


    protected $rounds = 100;


    public function __construct($cipher, $mode, $rounds = 100) {

        $this->cipher = $cipher;

        $this->mode = $mode;

        $this->rounds = (int) $rounds;

    }

    public function decrypt($data, $key) {

        $c = base64_decode($data);
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
		{
			return $original_plaintext;
		}

        

    }


    public function encrypt($data, $key) {
		
		$ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		return $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

    }


}
?>