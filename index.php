<?php

require_once 'Cacher.php';

class MyClass
{

    private $cacher;

    public function __construct()
    {
        $this->cacher = new Cacher();
    }

    private function validate(array $integers)
    {
        foreach ($integers as $integer) {
            if (!is_string($integer) || !preg_match('/^\d+(\.\d+)?$/', $integer)) {
                throw new Exception("$integer не число");
            }
        }
    }

    private function to_equal_symbols(string &$int1, string &$int2): int
    {
        $lengthInt1   = mb_strlen($int1);
        $lengthInt2   = mb_strlen($int2);
        $minLength    = min([$lengthInt1, $lengthInt2]);
        $maxLength    = max([$lengthInt1, $lengthInt2]);
        $addingString = '';

        while (mb_strlen($addingString) < ($maxLength - $minLength)) {
            $addingString .= '0';
        }

        if ($lengthInt1 < $lengthInt2) {
            $int1 = $addingString . $int1;
        } elseif ($lengthInt1 > $lengthInt2) {
            $int2 = $addingString . $int2;
        }

        return $maxLength;
    }

    private function sum_2_long_integers(string $int1, string $int2): string
    {
        $this->validate([$int1, $int2]);

        $stringsLength = $this->to_equal_symbols($int1, $int2);
        $result        = '';
        $residue       = 0;

        for ($i = $stringsLength - 1; $i >= 0; $i--) {
            $sum     = $int1[$i] + $int2[$i] + $residue;
            $sum     .= '';
            $residue = 0;

            if (mb_strlen($sum) > 1) {
                $residue = 1;
                $sum     = $sum[1];
            }

            $result = $sum . $result;
        }

        if ($residue) {
            $result = $residue . $result;
        }
        
        return $result;
    }

    private function sum_array(): string
    {
        $result      = '0';
        $resultArray = [];
        $fileRows    = file('file');

        foreach ($fileRows as $row) {
            $row           = preg_replace('/[^\d\.]/', '', $row);
            $resultArray[] = $row;
            $result        = $this->sum_2_long_integers($result, $row);
        }
        $resultArray[]=$result;

        print_r($resultArray);

        return $result;
    }

    public function func()
    {
        $this->sum_array();
    }

}

echo '<pre>';
print_r((new MyClass())->func());
