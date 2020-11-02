<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Affine Cipher
 * 
 * Affine Cipher is a type of monoalphabetic substitution, where 
 * each leter in an alphabet is mapped to its numeric equivalent using 
 * a simple mathmatical function, and converted back to letter.
 * 
 * @author Ichwanul Fadhli, Tangguh D. Pramono
 * @copyright Copyright (c) 2020, Ichwanul Fadhli, Tangguh D. Pramono
 */
class Affine {
    // The alphabet lookup variable.
	private static $alphabet = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 
		'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 
		'u', 'v', 'w', 'x', 'y', 'z'
    );

    public function encrypt($a, $b, $message){
        if(preg_match('/[0-9]/', $a) === false || preg_match('/[0-9]/', $b) === false){
            throw new Error('The key \'a\' or \'b\' must be a number.');
        }
        elseif($a < 1){
            throw new Error('The key \'a\' cant\'t be zero.');
        }
        elseif($b < 0){
            throw new Error('The key \'b\' cant\'t be a negative number.');
        }
        elseif((int)gmp_gcd($a, 26) !== 1){
            throw new Error('The key \'a\' is not prime with 26.');
        }
        elseif(is_numeric($a) === false || is_numeric($b) === false){
            throw new Error('The key \'a\' or \'b\' cant\'t be a decimal number.');
        }
        else{
            $output = '';
            $encrypted = '';
            $messageSize = strlen($message);
    
            if(preg_match('/[a-z0-9\W\s_]/', $message)){
                $editedMessage = strtolower(preg_replace("/[0-9\W\s_]/", '', $message));
                $editedmessageSize = strlen($editedMessage);

                for($i = 0; $i < $editedmessageSize; $i++){
                    $x = array_search($editedMessage[$i], self::$alphabet);
                    $y = fmod((($a * $x) + $b), 26);

                    $encrypted .= self::$alphabet[$y];
                }
            }

            $k = 0;

            for($j = 0; $j < $messageSize; $j++){
                if(ctype_alpha($message[$j])){
                    if(ctype_upper($message[$j])){
                        $output .= strtoupper($encrypted[$k]);
        
                        $k += 1;
                    }
                    else{
                        $output .= $encrypted[$k];
        
                        $k += 1;
                    }
                }
                else{
                    $output .= $message[$j];
                }
            }
            return $output;
        }
    }

    public function decrypt($key, $message){

    }
}