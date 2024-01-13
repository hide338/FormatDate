<?php
class FormatDate 
{
  private $date_time;
  private $year;
  private $era_lists = [
    // 令和(2019年5月1日〜)
    [
      'jp' => '令和', 
      'jp_abbr' => '令',
      'en' => 'r',
      'en_abbr' => 'R',
      'time' => '20190501',
      'year' => '2019'
    ],
    // 平成(1989年1月8日〜)
    [
      'jp' => '平成',
      'jp_abbr' => '平',
      'en' => 'h',
      'en_abbr' => 'H',
      'time' => '19890108',
      'year' => '1989'
    ],
    // 昭和(1926年12月25日〜)
    [
      'jp' => '昭和',
      'jp_abbr' => '昭',
      'en' => 's',
      'en_abbr' => 'S',
      'time' => '19261225',
      'year' => '1926'
    ],
    // 大正(1912年7月30日〜)
    [
      'jp' => '大正',
      'jp_abbr' => '大',
      'en' => 't',
      'en_abbr' => 'T',
      'time' => '19120730',
      'year' => '1912'
    ],
    // 明治(1873年1月1日〜)
    // ※明治5年以前は旧暦を使用していたため、明治6年以降から対応
    [
      'jp' => '明治',
      'jp_abbr' => '明',
      'en' => 'm',
      'en_abbr' => 'M',
      'time' => '18730101',
      'year' => '1873'
    ],
  ];

  public function __construct($time = 'now', $timezone = null) {
    $time = preg_replace("/\s|　/", "", mb_convert_kana($time, 'n'));
    if(preg_match('/^(令|令和|r|R|平|平成|h|H|昭|昭和|s|S|大|大正|t|T|明|明治|m|M)?(元|0?[1-9])?(年|-|\/?)?(0?[1-9]|1[0-2])?(月|-|\/?)?(0?[1-9]|[12][0-9]|3[01])?(日?)?$/', $time, $matches)) {

      $era = $matches[1];
      $year = intval(str_replace('元', '1',$matches[2]));
      $this->year = sprintf('%02d', $year);
      $month = !empty($matches[4]) ? intval($matches[4]) : 1;
      $day = !empty($matches[6]) ? intval($matches[6]) : 1;

      foreach ($this->era_lists as $era_list) {
        if($era_list['jp'] === $era || $era_list['jp_abbr'] === $era || $era_list['en'] === $era || $era_list['en_abbr'] === $era){
          $seireki_year = $era_list['year'] + $year - 1;
          $seireki = sprintf('%04d-%02d-%02d', $seireki_year, $month, $day);
          $this->date_time = new DateTime($seireki, $timezone);
          break;
        }
      }
    } else {
      $this->date_time = new DateTime($time, $timezone);
    }
  }

  public function format($format) {
    /*
    西暦→和暦変換
    
    @param string $format 'K':元号
                          'k':元号略称
                          'Q':元号(英語表記)
                          'q':元号略称(英語表記)
                          'X':和暦年(前ゼロ表記)
                          'x':和暦年
                          'R':曜日
                          'V':曜日略称
                          'f':元(元年表記)
    @param string $this->date_time 変換対象となる日付(西暦)
    
    @return string $result 変換後の日付(和暦)
   */

    $week = ['日','月','火','水','木','金','土'];
    $week_long = ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'];
    
    $format_K = '';
    $format_k = '';
    $format_Q = '';
    $format_q = '';
    $format_X = $this->date_time->format('Y');
    $format_x = $this->date_time->format('y');
    $format_R = $week_long[$this->date_time->format('w')];
    $format_V = $week[$this->date_time->format('w')];
    $format_f = '元';
    
    foreach ($this->era_lists as $era_list) {
      $date_era = new DateTime($era_list['time']);
      if ($this->date_time->format('Ymd') >= $date_era->format('Ymd')) {
        $format_K = $era_list['jp'];
        $format_k = $era_list['jp_abbr'];
        $format_Q = $era_list['en'];
        $format_q = $era_list['en_abbr'];
        $format_x = $this->date_time->format('Y') - $date_era->format('Y') + 1;
        $format_X = sprintf('%02d', $format_x);
        break;
      }
    }

    $result = '';

    foreach (str_split($format) as $value) {
      // フォーマットが指定されていれば置換する
      if (isset(${"format_{$value}"}) && $value === 'f' && $this->year === '01'){
        $result .= ${"format_{$value}"};
      } elseif(isset(${"format_{$value}"}) && $value === 'f' && $this->year !== '01'){
        $year_value = 'X';
        $result .= ${"format_{$year_value}"};
      } elseif (isset(${"format_{$value}"}) && $value !== 'f') {
        $result .= ${"format_{$value}"};
      } else {
        $result .= $this->date_time->format($value);
      }
    }

    return $result;
  }

}