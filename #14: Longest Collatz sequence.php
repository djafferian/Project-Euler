<?php
$_fp = fopen("php://stdin", "r");
/* Enter your code here. Read input from STDIN. Print output to STDOUT */

/* The problem involves finding the maximum Y such that Y=F(X) for some
 * function F over a domain of X=1..N for some positive integer N.  In this
 * case there seems to be nothing about F which provides any optimization over
 * computing Y for every X=1..N before being able to determine the maximum.
 * So this implementation computes the answer to every possible input before
 * reading the test cases.  PHP arrays and SplFixedArray objects consume too
 * much time and memory to meet the capacity, so a string is used as a byte
 * array to store the data.  The length of any Collatz sequence starting with
 * a number within the input capacity will fit into two bytes.  The length is
 * represented by the number of items in the sequence, including the starting
 * number, rather than the number of operations.  This allows zero to indicate
 * that a Collatz sequence length (CSL) has not yet been computed.
 */
define('CAPACITY', 5000000);    // Specified constraint
// Quickly fill with zeroes a byte array large enough to hold all CSLs.
for ($csl = chr(0), $n = CAPACITY<<5; $n; $csl.=$csl, $n>>=1);
// The length of the Collatz sequence starting with 1 is declared to be 1.
// Doing so provides a termination point for computations.
$csl[1] = 1;
// Loop through every possible input.
for ($n=1;$n<=CAPACITY;$n+=1) {
    // Generate the Collatz sequence to compute its length.  
    $i=$n;
    $sequence = array();
    while (true) {
        // Avoid storing the CSL for a term that is beyond capacity.
        if ($i <= CAPACITY) {
            // Check whether the CSL with the term as
            // its starting number has been stored.
            $j = 2*$i;
            $k = ord($csl[$j-2])<<8|ord($csl[$j-1]);
            if ($k) break;
        }
        // Temporarily store the term.
        $sequence[] = $i;
        // Compute the next term in the Collatz sequence.
        //$i = $i&1 ? 3*$i+1 : $i/2;    // Collatz function
        $i = ($i&1) ? ($i<<2)-($i&~1) : ($i>>1);    // faster Collatz function
    }
    // Use the known length to compute the CSL for every term encountered.
    $c = count($sequence);
    while ($c) {
        $c-=1;
        $k+=1;
        $i = $sequence[$c];
        if (CAPACITY < $i) continue;
        $j = 2*$i;
        $csl[$j-2] = chr(0xff&($k>>8));
        $csl[$j-1] = chr(0xff&$k);
    }
}

// Now that all of the CSLs have been found, compute the maximums.
$mx = 0;
for ($n=1;$n<=CAPACITY;$n+=1) {
    $j = 2*$n;
    $k = ord($csl[$j-2])<<8|ord($csl[$j-1]);
    if ($mx <= $k) {
        $mx = $k;
        $maximums[] = $n;
//echo $n, ' ';
    }
}
//echo "\n";

// Finally, process the input.
fscanf($_fp, "%d", $t);
while (0<=($t-=1)) {
    fscanf($_fp, "%d", $n);
    $i=count($maximums);
    while ($n < $maximums[$i-=1]);
    echo $maximums[$i], "\n";
}
?>
