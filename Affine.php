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

    /** Greatest Common Divisor 
     * 
     * The function which later will be used to determine 
     * wether or not value of 'a' is prime with value of 'b'.
     * 
     * The process to figure out value 'a' is prime with value 'b' 
     * could've easily been done with PHP built-in function called 
     * 'gmp_gcd()', but in order to use the function, you have to 
     * change a setting on php.ini, and we don't want that.
     * 
     * @param integer $a 
     * The first value.
     * 
     * @param integer $b 
     * The second value.
     * 
     * @return integer
     */
    function gcd($a,$b){
        return (int)($a % $b) ? $this->gcd($b,$a % $b) : $b;
    }

    /** Modulo
     * 
     * The function is created due to the built-in function 
     * fmod() has an issue if the divisor holds a negative value.
     * 
     * @param integer $a 
     * The first value.
     * 
     * @param integer $b 
     * The second value.
     * 
     * @return integer
     */
    function mod($a, $b){
        $c = $a % $b;
        return (int)($c < 0) ? $c + $b : $c;
    }

    /** Affine Encrypt
     * 
     * The encrypt function, turns a plaintext to a cryptic message.
     * 
     * Notice, the first and second key must be an integers,
     * otherwise the result will be different from as it 
     * supposed to be.
     * 
     * @param integer $a
     * The first key.
     * 
     * @param integer $b
     * The second key.
     * 
     * @param string $message
     * The plaintext message.
     * 
     * @return string
     * The final process is returning a plaintext message 
     * into an encrypted message.
     * 
     */
    public function encrypt($a, $b, $message){
        
        // Error handlers
        //
        // If key 'a' or 'b' is not a number, then throw an error.
        if(preg_match('/[0-9]/', $a) === false || preg_match('/[0-9]/', $b) === false){
            throw new Error('The key \'a\' or \'b\' must be a number.');
        }
        // If key 'a' is less than 1, then throw an error.
        elseif($a < 1){
            throw new Error('The key \'a\' cant\'t be zero.');
        }
        // If key 'b' is less than 0 or a negative number, then throw an error.
        elseif($b < 0){
            throw new Error('The key \'b\' cant\'t be a negative number.');
        }
        // If key 'a' is not prime to 26, then throw an error.
        elseif($this->gcd($a, 26) !== 1){
            throw new Error('The key \'a\' is not prime with 26.');
        }
        // If key 'a' or 'b' is not an integers, then throw an error.
        elseif(is_numeric($a) === false || is_numeric($b) === false){
            throw new Error('The key \'a\' or \'b\' cant\'t be a decimal number.');
        }
        //
        // End of error handlers
        else{
            // Defining all the necessary variables.
            $output = ''; # output varable.
            $encrypted = ''; # encrypted varable.
            $messageSize = strlen($message); # message length variable.
    
            // Check if the message consist of a non-aplhabetic characters.
            if(preg_match('/[a-z0-9\W\s_]/', $message)){
                // Edit the original message, removing  all the non-alphabetic
                // characters and convert it to lower case.
                $editedMessage = strtolower(preg_replace("/[0-9\W\s_]/", '', $message));
                // Edited message length.
                $editedmessageSize = strlen($editedMessage);

                // The calculation process
                for($i = 0; $i < $editedmessageSize; $i++){
                    // Get the letter number from the alphabet lookup.
                    $x = array_search($editedMessage[$i], self::$alphabet);
                    // After obtaining the letter number, apply the 
                    // affine cipher encryption formula which is
                    // f(x) = (ax + b) mod 26
                    $y = $this->mod((($a * $x) + $b), 26);

                    // Appending the calculation number and get the letter from
                    // the alphabet lookup based on the result of $y.
                    $encrypted .= self::$alphabet[$y];
                }
            }

            // Variable $k for later purpose.
            $k = 0;

            // Reconstructing the output.
            for($j = 0; $j < $messageSize; $j++){
                // Check if the letter on index $j is an alphabetic.
                if(ctype_alpha($message[$j])){
                    // Check of the letter on index $j is an uppercase letter.
                    if(ctype_upper($message[$j])){
                        // Appending the encrypted message to output.
                        $output .= strtoupper($encrypted[$k]);
        
                        // Increment
                        $k += 1;
                    }
                    // Otherwise, it's a lowercase letter
                    else{
                        // Appending the encrypted message to output.
                        $output .= $encrypted[$k];
        
                        // Increment
                        $k += 1;
                    }
                }
                // Otherwise, just return the original message.
                else{
                    // Appending the encrypted message to output.
                    $output .= $message[$j];
                }
            }

            // Returning the output
            return $output;
        }
    }

    /** Affine Decrypt
     * 
     * The decrypt function, turns an encrypted message to a plaintext.
     * 
     * Notice, the first and second key must be an integers,
     * otherwise the result will be different from as it 
     * supposed to be.
     * 
     * @param integer $a
     * The first key.
     * 
     * @param integer $b
     * The second key.
     * 
     * @param string $message
     * The encrypted message.
     * 
     * @return string
     * The final process is returning an encrypted message 
     * into a plaintext.
     * 
     */
    public function decrypt($a, $b, $message){
        // Error handlers
        //
        // If key 'a' or 'b' is not a number, then throw an error.
        if(preg_match('/[0-9]/', $a) === false || preg_match('/[0-9]/', $b) === false){
            throw new Error('The key \'a\' or \'b\' must be a number.');
        }
        // If key 'a' is less than 1, then throw an error.
        elseif($a < 1){
            throw new Error('The key \'a\' cant\'t be zero.');
        }
        // If key 'b' is less than 0 or a negative number, then throw an error.
        elseif($b < 0){
            throw new Error('The key \'b\' cant\'t be a negative number.');
        }
        // If key 'a' is not prime to 26, then throw an error.
        elseif($this->gcd($a, 26) !== 1){
            throw new Error('The key \'a\' is not prime with 26.');
        }
        // If key 'a' or 'b' is not an integers, then throw an error.
        elseif(is_numeric($a) === false || is_numeric($b) === false){
            throw new Error('The key \'a\' or \'b\' cant\'t be a decimal number.');
        }
        //
        // End of error handlers
        else{
            // Defining all the necessary variables.
            $output = ''; # output varable.
            $decrypted = ''; # decrypted varable.
            $messageSize = strlen($message); # message length variable.
    
            // Check if the message consist of a non-aplhabetic characters.
            if(preg_match('/[a-z0-9\W\s_]/', $message)){
                // Edit the original message, removing  all the non-alphabetic
                // characters and convert it to lower case.
                $editedMessage = strtolower(preg_replace("/[0-9\W\s_]/", '', $message));
                // Edited message length.
                $editedmessageSize = strlen($editedMessage);

                // The calculation process
                $i = 1;
                $x = 0;
                // The purpose of this loop is to figure out 
                // modular multiplicative inverse of $a modulo 26.
                while($x != 1){
                    $x = $this->mod(($a * $i), 26);

                    // If the result of $x is 1, then the calculation 
                    // is finished and then extract the value of $i.
                    if($x == 1){
                        break;
                    }
                    // Otherwise continue the calculation.
                    else{
                        $i++;
                    }
                }

                for($j = 0; $j < $editedmessageSize; $j++){
                    // Get the letter number from the alphabet lookup.
                    $z = array_search($editedMessage[$j], self::$alphabet);
                    // After obtaining the letter number, apply the 
                    // affine cipher encryption formula which is
                    // f(x) = a * (x - y) mod 26
                    $y = $this->mod(($i * ($z - $b)), 26);

                    // Appending the calculation number and get the letter from
                    // the alphabet lookup based on the result of $y.
                    $decrypted .= self::$alphabet[$y];
                }
            }

            // Variable $k for later purpose.
            $k = 0;

            // Reconstructing the output.
            for($j = 0; $j < $messageSize; $j++){
                // Check if the letter on index $j is an alphabetic.
                if(ctype_alpha($message[$j])){
                    // Check of the letter on index $j is an uppercase letter.
                    if(ctype_upper($message[$j])){
                        // Appending the decrypted message to output.
                        $output .= strtoupper($decrypted[$k]);
        
                        // Increment
                        $k += 1;
                    }
                    // Otherwise, it's a lowercase letter
                    else{
                        // Appending the decrypted message to output.
                        $output .= $decrypted[$k];
        
                        // Increment
                        $k += 1;
                    }
                }
                // Otherwise, just return the original message.
                else{
                    // Appending the plaintext message to output.
                    $output .= $message[$j];
                }
            }

            // Returning the output
            return $output;
        }
    }
}