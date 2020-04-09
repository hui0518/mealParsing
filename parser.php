<?php 
include('simple_html_dom.php');

$schulCode = 'B100000570'; //학교 코드 (기본값: 한성과학고)
$countryCode = 'stu.sen.go.kr'; //지역 교육청 나이스 url
$schulCrseScCode = '4'; //학교 분류
$schulKndScCode = '04'; //학교 종류

// i달 후 학사력을 html table로 리턴
function calendar($i) {
    $year = date("Y", mktime(0,0,0,date("m")+$i,date("d"),date("Y")));
    $month = date("m", mktime(0,0,0,date("m")+$i,date("d"),date("Y")));
    $url = 'https://'.$countryCode.'/sts_sci_sf01_001.do?schulCode='.$schoolCode.'&schulCrseScCode='.$schulCrseScCode.'&schulKndScCode='.$schulKndScCode.'&ay='.$year.'&mm=' . $month;

    while (strlen($text)==0) {
      $text=@file_get_contents($url);
    }

    $text = @explode('<table', $text);
    $text = @explode('table>', $text[1]);
    $text = '<table' . $text[0] . 'table>';

    $text = str_replace('<caption>월간학사일정 달력</caption>', '', $text);
    $text = preg_replace('/<a href=.*>/', '', $text);
    $text = str_replace('<strong>', '', $text);
    $text = str_replace('</strong></a>', '', $text);

    echo $text;
}

// i일 후 급식 파싱
function meal($i, $mealcode){
    
    //parameter의 값을 숫자로 변환한다.
    switch ($mealcode) {
        case '1' : $meal = '조식';
            break;
        case '2' : $meal = '중식';
            break;
        case '3' : $meal = '석식';
            break;
    }
    
    $schYmd = date("Y.m.d", mktime(0,0,0,date("m")  , date("d")+$i, date("Y"))); //오늘 날짜
    $food_url = 'http://stu.sen.go.kr/sts_sci_md01_001.do?schulCode=B100000570&schulCrseScCode=4&schMmealScCode='.$mealcode.'&schYmd='.$schYmd;
    
    while (strlen($text)==0) {
      $text = @file_get_html($food_url);
    }
    
    $sun =$text->find('th', 1)->innertext;
    $mon=$text->find('th', 2)->innertext;
    $tus=$text->find('th', 3)->innertext;
    $wed=$text->find('th', 4)->innertext;
    $thu=$text->find('th', 5)->innertext;
    $fri=$text->find('th', 6)->innertext;
    $sat=$text->find('th', 7)->innertext;
    
    
    if ($sun==$schYmd . '(일)'){
        $index = 7;
        $day = '일';
    }else if($mon==$schYmd . '(월)'){
        $index = 8;
        $day = '월';
    }else if($tus==$schYmd . '(화)'){
        $index = 9;
        $day = '화';
    }else if($wed==$schYmd . '(수)'){
        $index = 10;
        $day = '수';
    }else if($thu==$schYmd . '(목)'){
        $index = 11;
        $day = '목';
    }else if($fri==$schYmd . '(금)'){
        $index = 12;
        $day = '금';
    }else if($sat==$schYmd . '(토)'){
        $index = 13;
        $day = '토';
    }
    
    $str =$text->find('td', $index)->innertext;
    $str = str_replace('<br />', '
                       ', $str);
    
    if (strlen($str) == 0) {
        $array1 = array("text" => $schYmd . ' (' . $day.') '. $meal);
        $array3 = array("text" => '급식이 없습니다.');
        $array4 = array($array1, $array3);
        $array5 = array("messages" => $array4);
        $json = @json_encode($array5);
        echo $json;
    }else{
        $array1 = array("text" => $schYmd . ' (' . $day.') '. $meal);
        $array3 = array("text" => $str);
        $array4 = array($array1, $array3);
        $array5 = array("messages" => $array4);
        $json = @json_encode($array5);
        $json = preg_replace("/\s+/", "", $json);
        echo $json;
    };
}
?>
