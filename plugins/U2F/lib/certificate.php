<?php

class Certificate {
    static public function wrap_pem($txt)
    {
        $cert_txt = "-----BEGIN CERTIFICATE-----\n";
        $lines = str_split($txt, 64);
        foreach ($lines as $line) {
            $cert_txt .= $line . "\n";
        }
        $cert_txt .= "-----END CERTIFICATE-----\n";
        return $cert_txt;
    }

    static public function parse_certificate($txt)
    {
        if (preg_match('/^-----BEGIN CERTIFICATE-----', $txt)) {
            $cert_txt = $txt;
        } else {
            $cert_txt = self::wrap_pem($txt);
        }
        return openssl_x509_parse($cert_txt);
    }
}
