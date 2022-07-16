<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class HammingDistance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hammingdistance {x} {y}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will get the Hamming distance between two integers is the number of positions at which the corresponding
    bits are different.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Validates if values are being pased
        $validator = Validator::make($this->arguments(), [
            'x' => 'required|integer|min:0|max:2147483648',
            'y' => 'required|integer|min:0|max:2147483648'
        ]);
        // Checks if validation fails and shows specific errors
        if ($validator->fails()) {
            $error = $validator->errors();
            echo PHP_EOL;
            foreach($error->messages() as $key => $messages) {
                foreach($messages as $message) {
                    print $message;
                }
            }
            echo PHP_EOL;
        } else {
            // Convert the integers to string binary and reverse the string.
            $xString = strrev(decbin($this->arguments()['x']));
            $yString = strrev(decbin($this->arguments()['y']));
            
            // Check which string is longer
            if(strlen($xString) > strlen($yString)) {
                $loop = $xString;
                $compare = $yString;
            } else {
                $loop = $yString;
                $compare = $xString;
            }
            // Set count to 0
            $count = 0;

            // Loop the string with most length
            for($i = 0; $i < strlen($loop); $i++ ) {
                // Get the character by position
                $a = $loop[$i];
                // If the shorter strings length is greater than the current index
                // set the character to compare to the character at current index value
                if($i<strlen($compare)) {
                    $b = $compare[$i];
                } else {
                    // if index is greater then value is automatically "0"
                    $b = "0";
                }

                // Compare the characters and if it differes then increment count
                if($a != $b) {
                    $count++;
                }
            }
            // Output the count.
            echo $count;
        }
        
        echo PHP_EOL;
        return 0;
    }
}
