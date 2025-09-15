<?php
namespace App;

class Helpers {
    public static function num2Text($number) {
        $remainderStr = '';
        $wholeNumberStr = '';

        $remainder = strrev(round(($number - (int)($number))*100));
        $wholeNumber = strrev((int)$number);

        if(!empty($wholeNumber[0])) {
            if($wholeNumber[0] != '0') {
                $wholeNumberStr = Constants::NUMBERS[$wholeNumber[0]];
            }
        }

        if(!empty($remainder[0])) {
            if($remainder[0] != '0') {
                $remainderStr = Constants::NUMBERS[$remainder[0]];
            }
        }

        if(!empty($wholeNumber[1])) {
            if($wholeNumber[1] != '0') {
                if($wholeNumber[1] == '1') {
                    $str = Constants::NUMBERS[$wholeNumber[0]] ." даҳ";
                    if($wholeNumberStr != "") {
                        $str .= "у " .$wholeNumberStr;
                    }
                    $wholeNumberStr = $str;
                } else {
                    $str = Constants::TENTH[$wholeNumber[1]];
                    if($wholeNumberStr != "") {
                        if($wholeNumber[1] == "3") {
                            $str .= "ву " . $wholeNumberStr;
                        } else {
                            $str .= "у " . $wholeNumberStr;
                        }
                    }
                    $wholeNumberStr = $str;
                }
            }
        }

        if(!empty($remainder[1])) {
            if($remainder[1] != '0') {
                if($remainder[1] == '1') {
                    $str = Constants::NUMBERS[$remainder[0]] ." даҳ";
                    if($remainderStr != "") {
                        $str .= "у " .$remainderStr;
                    }
                    $remainderStr = $str;
                } else {
                    $str = Constants::TENTH[$remainder[1]];
                    if($remainderStr != "") {
                        if($remainder[1] == "3") {
                            $str .= "ву " . $remainderStr;
                        } else {
                            $str .= "у " . $remainderStr;
                        }
                    }
                    $remainderStr = $str;
                }
            }
        }

        if(!empty($wholeNumber[2])) {
            if($wholeNumber[2] != '0') {
                $str = Constants::NUMBERS[$wholeNumber[2]] . " сад";
                if($wholeNumberStr != "") {
                    $str .= "у " . $wholeNumberStr;
                }
                $wholeNumberStr = $str;
            }
        }

        if(!empty($wholeNumber[3])) {
            if($wholeNumber[3] != '0') {
                $str = Constants::NUMBERS[$wholeNumber[3]] . " ҳазор";
                if($wholeNumberStr != "") {
                    $str .= "у " . $wholeNumberStr;
                }
                $wholeNumberStr = $str;
            }
        }

        if(!empty($wholeNumber[4])) {
            if($wholeNumber[4] != '0') {
                $str = Constants::TENTH[$wholeNumber[4]] ;
                if($wholeNumberStr != "") {
                    if($wholeNumber[4] == "3") {
                        $str .= "ву " . $wholeNumberStr;
                    } else {
                        $str .= "у " . $wholeNumberStr;
                    }
                }
                $wholeNumberStr = $str;
            }
        }

        return $wholeNumberStr . ' сомони ' . ($remainderStr != '' ? $remainderStr . ' дирам' : '00 дирам');
    }

    public static function normalizePassportId($id) {
        $id = (string)$id;
        $id = trim($id);
        $id = str_replace([" ","-","_","/","\\"], '', $id);
        $id = mb_strtoupper($id, 'UTF-8');

        $map = [
            'А' => 'A','В' => 'B','Е' => 'E','К' => 'K','М' => 'M','Н' => 'H','О' => 'O','Р' => 'P','С' => 'C','Т' => 'T','У' => 'Y','Х' => 'X',
            'І' => 'I','Ї' => 'I','Й' => 'I','Ґ' => 'G','Ё' => 'E','Й' => 'I','Щ' => 'W','З' => '3'
        ];
        $normalized = '';
        $len = mb_strlen($id, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($id, $i, 1, 'UTF-8');
            $normalized .= $map[$ch] ?? $ch;
        }
        return $normalized;
    }
}

