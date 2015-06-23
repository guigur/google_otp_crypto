<head>
    <meta charset="UTF-8"> <!--Encode toute la page en Utf-8 pour les accents -->
</head>
Validitée: <label id="seconds"></label> Secondes
<br /><br />

<script type="text/javascript">
    var message = Math.round(new Date().getTime() / 1000.0);
    var countDown = 30 - (message % 30);

    var secondsLabel = document.getElementById("seconds");
    setInterval(setTime, 1000);

    function setTime()
    {
        secondsLabel.innerHTML = countDown;
        if (countDown == 0)
            location.reload();
        countDown--;
    }

</script>
<?php
/**
 * Created by PhpStorm.
 * User: guigur
 * Date: 23/06/15
 * Time: 09:50
 */

include_once('convBase.php');
/*
** CONFIG
*/
$TO = 0; //Représente le temps a partir duquel on commence a compter, de base 0 (le temps unix)
$X = 30; // Représente les intervalles de temps ou on recréer un mot de passe, de base toutes les 30 secondes
$key = "***REMOVED***"; // clé 32 bits générée par google

echo "Clé secrète: <b>".$key.'</b><br />';

$keyHex=convBase(strtoupper($key), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', '0123456789ABCDEF'); // On convertis la base 32 en hexadécimal et on oublie pas de mettre strtoupper histoir que la clé match avec les caractère
echo "Clé secrète en HEXA: <b>".$keyHex.'</b><br />';

$message = floor((time('now') - $TO) / $X);             //Création de l'époque Unix
echo "Epoque Unix : <b><span id='epoque'>".$message."</span></b><br />";

$message = dechex($message);                            //On convertis L'époque Unix en hexa
$message = str_pad($message, 16, '0', STR_PAD_LEFT);    //On met des zeros devant histoire de le passer sur 16 bits
echo "Epoque Unix en HEXA : <b>".$message."</b><br />";

$hmac = hash_hmac("sha1",  pack("H*", $message), pack("H*", $keyHex)); //On hache le tout en mettant bien les chaines en hexa avec le bit de poids fort en premier
echo "HMAC en HEXA : <b>".$hmac."</b><br />";

$offset = hexdec(substr($hmac, strlen($hmac) - 1, strlen($hmac))); //On récupère la dernière lettre de la chaine que l'on viens de hacher et on la convertie en décimal et on multiplie par 2
echo "Valeur de l'offset : <b>".$offset."</b><br />";

$tokenHach = substr($hmac, $offset * 2, 8);
echo "Token Haché : <b>".$tokenHach."</b><br />";

$otp = hexdec($tokenHach) & hexdec('7fffffff'); //On enleve le bit le plus significatif comme indiqué dans la rfc
$otp = str_pad($otp % 1000000, 6, '0', STR_PAD_LEFT); //On divise le résulta par 10^6 comme indiqué dans la rfc et on rajoute des 0 pour arriver a 6 chiffres

echo "One Time Password : <b>".$otp."</b><br />";