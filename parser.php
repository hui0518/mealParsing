<?php 
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
function meal($i, $mealcode) {
    $schYmd = date("Y.m.d", mktime(0,0,0,date("m"),date("d")+$i,date("Y")));
    switch ($mealcode) {
        case '1': $meal = '조식';break;
        case '2': $meal = '중식';break;
        case '3': $meal = '석식';break;
    }

    $food_url='http://'.$countryCode.'/sts_sci_md01_001.do?schulCode='.$schulCode.'&schulCrseScCode='.$schulCrseScCode.'&schMmealScCode='.$mealcode.'&schYmd='.$schYmd;
    
    while (strlen($text)==0) {
      $text=@file_get_contents($food_url);
    }

    $temp=@explode('<table',$text);
    $a=@explode('<thead>',$temp[1]);
    $b=@explode('<tr>',$a[1]);
    $r=@explode('</th>',$b[1]);
    $sun=@explode('<th scope="col" class="point2">',$r[1]);
    $mon=@explode('<th scope="col">',$r[2]);
    $tus=@explode('<th scope="col">',$r[3]);
    $wed=@explode('<th scope="col">',$r[4]);
    $thu=@explode('<th scope="col">',$r[5]);
    $fri=@explode('<th scope="col">',$r[6]);
    $sat=@explode('<th scope="col" class="last point1">',$r[7]);
    $index=0;
    if ($sun[1]==$schYmd . '(일)') {$index=0;$day='일';}
    elseif ($mon[1]==$schYmd.'(월)') {$index=1;$day='월';}
    elseif ($tus[1]==$schYmd.'(화)') {$index=2;$day='화';}
    elseif ($wed[1]==$schYmd.'(수)') {$index=3;$day='수';}
    elseif ($thu[1]==$schYmd.'(목)') {$index=4;$day='목';}
    elseif ($fri[1]==$schYmd.'(금)') {$index=5;$day='금';}
    elseif ($sat[1]==$schYmd.'(토)') {$index=6;$day='토';}
    else{echo'오류가 발생했습니다.';}

    $temp=@explode('<table',$text);
    $a=@explode('<tbody>',$temp[1]);
    $b=@explode('<tr>',$a[1]);
    $r=@explode('</td>',$b[2]);
    $c=@explode('<td class="textC">',$r[$index]);
    if (strlen($c[1]) < 2) {
      echo'급식이 없습니다.';
    }else{
      echo $c[1];
    }
}
?>