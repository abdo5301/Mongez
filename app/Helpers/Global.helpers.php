<?php

use App\Models\Staff;


function area_has_models_ids(){
    $get = \App\Models\Area::select(\DB::raw('GROUP_CONCAT(`id` separator ",") as ids'))->where('has_property_model',1)->first();
    return $get->ids;
}

function notifyStaff(array $to,$title,$description,$url){
    $data = [
        'title'         => $title,
        'description'   => $description,
        'url'           => $url
    ];

   switch ($to['type']){
       case 'staff':
           $staff = Staff::whereIn('id',$to['ids'])->get();
           if($staff->isNotEmpty()){
               foreach ($staff as $key => $value){
                   $value->notify(new \App\Notifications\General($data));
               }
           }
           break;

       case 'group':
           $staff = Staff::whereIn('permission_group_id',$to['ids'])->get();
           if($staff->isNotEmpty()){
               foreach ($staff as $key => $value){
                   $value->notify(new \App\Notifications\General($data));
               }
           }
           break;

   }

   return true;
}

function seeMenu($route, $mainMenu = false){
    $route = explode(',',$route);

    static $canAccess = null;

    if(!$canAccess){
        $canAccess = Staff::StaffPerms(Auth::id())->toArray();
    }

    $numAccess = 0;
    foreach ($route as $key => $value){
        if (in_array($value,$canAccess)){
            $numAccess++;
        }
    }

    if(!$numAccess) return 'hidden-menu';


    if(in_array(request()->route()->getName(),$route)){
        if(!$mainMenu){
            return 'k-menu__item--active';
        }else{
            return 'k-menu__item--open';
        }
    }
}

function debug_query($query) {
    $query = vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());

    $double_linebreak_words = ['(', ')'];
    $double_linebreak_words_replace = array_map(function($str){ return PHP_EOL . $str . PHP_EOL; }, $double_linebreak_words);
    $query = str_replace($double_linebreak_words, $double_linebreak_words_replace, $query);

    $mysql_keywords = ['ADD', 'ALL', 'ALTER', 'ANALYZE', 'AND', 'AS', 'ASC', 'AUTO_INCREMENT', 'BDB', 'BERKELEYDB', 'BETWEEN', 'BIGINT', 'BINARY', 'BLOB', 'BOTH', 'BTREE', 'BY', 'CASCADE', 'CASE', 'CHANGE', 'CHAR', 'CHARACTER', 'CHECK', 'COLLATE', 'COLUMN', 'COLUMNS', 'CONSTRAINT', 'CREATE', 'CROSS', 'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'DATABASE', 'DATABASES', 'DAY_HOUR', 'DAY_MINUTE', 'DAY_SECOND', 'DEC', 'DECIMAL', 'DEFAULT', 'DELAYED', 'DELETE', 'DESC', 'DESCRIBE', 'DISTINCT', 'DISTINCTROW', 'DIV', 'DOUBLE', 'DROP', 'ELSE', 'ENCLOSED', 'ERRORS', 'ESCAPED', 'EXISTS', 'EXPLAIN', 'FALSE', 'FIELDS', 'FLOAT', 'FOR', 'FORCE', 'FOREIGN', 'FROM', 'FULLTEXT', 'FUNCTION', 'GEOMETRY', 'GRANT', 'GROUP', 'HASH', 'HAVING', 'HELP', 'HIGH_PRIORITY', 'HOUR_MINUTE', 'HOUR_SECOND', 'IF', 'IGNORE', 'INDEX', 'INFILE', 'INNER', 'INNODB', 'INSERT', 'INTEGER', 'INTERVAL', 'INTO', 'JOIN', 'KEY', 'KEYS', 'KILL', 'LEADING', 'LEFT', 'LIKE', 'LIMIT', 'LINES', 'LOAD', 'LOCALTIME', 'LOCALTIMESTAMP', 'LOCK', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOW_PRIORITY', 'MASTER_SERVER_ID', 'MATCH', 'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MINUTE_SECOND', 'MOD', 'MRG_MYISAM', 'NATURAL', 'NOT', 'NULL', 'NUMERIC', 'ON', 'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'ORDER', 'OUTER', 'OUTFILE', 'PRECISION', 'PRIMARY', 'PRIVILEGES', 'PROCEDURE', 'PURGE', 'READ', 'REAL', 'REFERENCES', 'REGEXP', 'RENAME', 'REPLACE', 'REQUIRE', 'RESTRICT', 'RETURNS', 'REVOKE', 'RIGHT', 'RLIKE', 'RTREE', 'SELECT', 'SET', 'SHOW', 'SMALLINT', 'SOME', 'SONAME', 'SPATIAL', 'SQL_BIG_RESULT', 'SQL_CALC_FOUND_ROWS', 'SQL_SMALL_RESULT', 'SSL', 'STARTING', 'STRAIGHT_JOIN', 'STRIPED', 'TABLE', 'TABLES', 'TERMINATED', 'THEN', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO', 'TRAILING', 'TRUE', 'TYPES', 'UNION', 'UNIQUE', 'UNLOCK', 'UNSIGNED', 'UPDATE', 'USAGE', 'USE', 'USER_RESOURCES', 'USING', 'VALUES', 'VARBINARY', 'VARCHAR', 'VARCHARACTER', 'VARYING', 'WARNINGS', 'WHEN', 'WHERE', 'WITH', 'WRITE', 'XOR', 'YEAR_MONTH', 'ZEROFILL', 'INT', 'OR', 'IS', 'IN'];
    $mysql_keywords = array_map(function($str){ return " $str "; }, $mysql_keywords);
    $mysql_keywords_lc = array_map(function($str){ return strtolower($str); }, $mysql_keywords);
    $query = str_replace($mysql_keywords_lc, $mysql_keywords, $query);

    $linebreak_before_words = ['INNER JOIN', 'LEFT JOIN', 'OUTER JOIN', 'RIGHT JOIN', 'WHERE', 'FROM', 'GROUP BY', 'SELECT'];
    $linebreak_before_words_replace = array_map(function($str){ return PHP_EOL . $str; }, $linebreak_before_words);
    $query = str_replace($linebreak_before_words, $linebreak_before_words_replace, $query);

    $linebreak_after_words = [','];
    $linebreak_after_words_replace = array_map(function($str){ return $str . PHP_EOL; }, $linebreak_after_words);
    $query = str_replace($linebreak_after_words, $linebreak_after_words_replace, $query);

    $query = str_replace('select ', 'SELECT ', $query);

    return $query;
}

function getPropertyType(){
    return \App\Models\PropertyType::get(['id','name_'.\App::getLocale().' as name']);
}

function getPurpose(){
    return \App\Models\Purpose::get(['id','name_'.\App::getLocale().' as name']);
}

function getPropertyStatus(){
    return \App\Models\PropertyStatus::get(['id','name_'.\App::getLocale().' as name']);
}


function getFirstArea(){
    $areaType = \App\Models\AreaType::orderBy('id','ASC')->first();
    return \App\Models\Area::where('area_type_id',$areaType->id)->orderBy('id','ASC')->first([
        'id',
        'name_'.\App::getLocale().' as name'
    ]);
}

function getSales(){
    return Staff::whereIn('permission_group_id',explode(',',setting('sales_group')))
        ->where('status','active')
        ->get([
            'id',
            \DB::raw('CONCAT(firstname,\' \',lastname) as name')
        ]);
}

function getStaff(){
    return Staff::where('status','active')
        ->get([
            'id',
            \DB::raw('CONCAT(firstname,\' \',lastname) as name')
        ]);
}


function getClientByMobile($mobile){
    $client = \App\Models\Client::where('mobile1',$mobile)->orWhere('mobile2',$mobile)->first();

    if(!$client) return null;

    return '<a target="_blank" href="'.route('system.client.show',$client->id).'">( '.$client->name.' )</a>';

}

function propertyToText($data){

    $return = [];
    $return[]= __('ID').": ".$data->id;
    $return[]= __('Type').": ".$data->property_type->{'name_'.\App::getLocale()};
    $return[]= __('Purpose').": ".$data->purpose->{'name_'.\App::getLocale()};
    $return[]= __('Data Source').": ".$data->data_source->{'name_'.\App::getLocale()};
    $return[]= __('Area').": ".implode(' -> ',\App\Libs\AreasData::getAreasUp($data->area_id,true));
    $return[]= __('Description').": ".$data->description;

    if($data->payment_type == 'cash'){
        $return[]= __('Payment Type').": ".__('Cash');
        $return[]= __('Price').": ".amount($data->price,true);
    }elseif($data->payment_type == 'installment'){
        $return[]= __('Payment Type').": ".__('Installment');
        $return[]= __('Deposit').": ".amount($data->deposit,true);
        $return[]= __('Price').": ".amount($data->price,true);
    }elseif($data->payment_type == 'cash_installment'){
        $return[]= __('Payment Type').": ".__('Cash & Installment');
        $return[]= __('Deposit').": ".amount($data->deposit,true);
        $return[]= __('Price').": ".amount($data->price,true);
    }

    $return[]= __('Currency').": ".$data->currency;
    $return[]= __('Space').": ".number_format($data->space);

    foreach($data->main_paramaters as $key => $value){
        if($data->paramaters->{$value->column_name}){
            $return[]= $value->{'name_'.\App::getLocale()} .": ".$data->paramaters->{$value->column_name};
        }
    }

    return $return;
}


function requestToText($data)
{

    $return = [];
    $return[] = __('ID') . ": " . $data->id;
    $return[] = __('Type') . ": " . $data->property_type->{'name_' . \App::getLocale()};
    $return[] = __('Purpose') . ": " . $data->purpose->{'name_' . \App::getLocale()};
    foreach (explode(',', $data->area_ids) as $key => $value){
        $return[] = __('Area') . ": " . implode(' -> ',\App\Libs\AreasData::getAreasUp($value,true));
    }

    $return[] = __('Space') . ": " . number_format($data->space_from).' '.__(ucfirst($data->space_type)).' : '.number_format($data->space_to).' '.__(ucfirst($data->space_type));

    if($data->payment_type == 'cash'){
        $return[] = __('Payment Type') . ": " . __('Installment');
        $return[] = __('Price') . ": " . amount($data->price_from,true).' : '.amount($data->price_to,true);
    }elseif($data->payment_type == 'installment'){
        $return[] = __('Payment Type') . ": " . __('Cash');
        $return[] = __('Deposit') . ": " . amount($data->deposit_from,true).' : '.amount($data->deposit_to,true);
        $return[] = __('Price') . ": " . amount($data->price_from,true).' : '.amount($data->price_to,true);
    }elseif($data->payment_type == 'cash_installment'){
        $return[] = __('Payment Type') . ": " . __('Cash & Installment');
        $return[] = __('Payment Type') . ": " . __('Cash');
        $return[] = __('Deposit') . ": " . amount($data->deposit_from,true).' : '.amount($data->deposit_to,true);
        $return[] = __('Price') . ": " . amount($data->price_from,true).' : '.amount($data->price_to,true);
    }

    $return[] = __('Description') . ": " . $data->description;


    return $return;
}


function numModelRows($model ,$where= []){ // start numModelRows
    $model = trim('\App\Models\ ').$model;
    if(!empty($where)){
        return $model::where($where)->count();
    }else{
        return $model::count();
    }
} // end numModelRows



function numProperties($year,$month){
    return \App\Models\Property::whereRaw('YEAR(created_at) = ?',[$year])
        ->whereRaw('MONTH(created_at) = ?',[$month])
        ->count();
}


function numRequests($year,$month){
    return \App\Models\Request::whereRaw('YEAR(created_at) = ?',[$year])
        ->whereRaw('MONTH(created_at) = ?',[$month])
        ->count();
}

function numCalls($year,$month){
    return \App\Models\Call::whereRaw('YEAR(created_at) = ?',[$year])
        ->whereRaw('MONTH(created_at) = ?',[$month])
        ->count();
}

function object2select($object,$callback){
    $data = $returnData = [];

    foreach ($object as $key => $value){
        $data[] = $callback($value);
    }
    foreach ($data as $key => $value){
        $returnData[$value['key']] = $value['value'];
    }

    return $returnData;
}

function getUserAgent(){
    return request()->server('HTTP_USER_AGENT');
}

function getDeviceInfo($userAgent){
    $agent = new \Jenssegers\Agent\Agent();
    $agent->setUserAgent($userAgent);
    return [
        'platform'=> $agent->platform(),
        'browser'=> $agent->browser()
    ];
}

function walletTransactionModal($transaction,$myWalletID,$lang){
    if(is_null($transaction->modal)){
        if($transaction->from_id == $myWalletID){
            return __('Transfer amount to wallet #ID: :walletID',['walletID'=> $transaction->toWallet->id]);
        }else{
            return __('Transfer amount from wallet #ID: :walletID',['walletID'=> $transaction->toWallet->id]);
        }
    }elseif($transaction->modal instanceof \App\Models\PaymentInvoice){
        $serviceName = $transaction->modal->payment_transaction->payment_services->{'name_'.$lang};
        $serviceName.= ' '.$transaction->modal->payment_transaction->payment_services->payment_service_provider->{'name_'.$lang};
        if(!empty($transaction->modal->payment_transaction->client_number)){
            $serviceName.= ' : '.$transaction->modal->payment_transaction->client_number;
        }
        return $serviceName;
    }

    return '--';

}

function handleLoop($data,$callback){
    if(is_object($data)){
        if($data->isEmpty()){
            return [];
        }
    }elseif (is_array($data)){
        if(empty($data)){
            return [];
        }
    }else{
        return [];
    }

    $newData = [];
    foreach ($data as $key => $value){
        $newData[$key] = $callback($key,$value);
    }

    return $newData;
}

function uploadBase64(){
    return 'test';
}

function getPaymentServicesParametersInfo($parameters,$language){

    if(empty($parameters) && !is_array($parameters)){
        return [];
    }

    $keys = array_keys($parameters);

    $data = \App\Models\PaymentServiceAPIParameters::whereIn('var_name',$keys)
        ->get([
            'var_name',
            'name_'.$language.' as name'
        ]);

    if($data->isEmpty()){
        $data = [];
    }else{
        $data = array_column($data->toArray(),'name','var_name');
    }

    $newData = [];
    foreach ($parameters as $key => $value){
        $newData[] = [
            'var_name'=> $keys,
            'name'=> (isset($data[$key])) ? $data[$key] : null,
            'value'=> $value
        ];
    }

    return $newData;

}

function mobileValidation($mobile,$country){
    return true;

    static $static;
    if($static == NULL){
        $static = true;
        include __DIR__."/phoneNumberVendor/autoload.php";
    }
    $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
    try {
        $numberProto = $phoneUtil->parse($mobile, strtoupper($country));
        $isValid = $phoneUtil->isValidNumberForRegion($numberProto, strtoupper($country));
        if($isValid && $phoneUtil->getNumberType($numberProto) == 1){
            return TRUE;
        }else{
            return FALSE;
        }
    } catch (\libphonenumber\NumberParseException $e) {
        return FALSE;
    }
}

function countries(){
    return array(
        "AF" => "Afghanistan",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "American Samoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AG" => "Antigua and Barbuda",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "Bosnia and Herzegovina",
        "BW" => "Botswana",
        "BV" => "Bouvet Island",
        "BR" => "Brazil",
        "IO" => "British Indian Ocean Territory",
        "BN" => "Brunei Darussalam",
        "BG" => "Bulgaria",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CV" => "Cape Verde",
        "KY" => "Cayman Islands",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "Christmas Island",
        "CC" => "Cocos (Keeling) Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo",
        "CD" => "Congo, the Democratic Republic of the",
        "CK" => "Cook Islands",
        "CR" => "Costa Rica",
        "CI" => "Cote D'Ivoire",
        "HR" => "Croatia",
        "CU" => "Cuba",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "El Salvador",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "Falkland Islands (Malvinas)",
        "FO" => "Faroe Islands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "French Guiana",
        "PF" => "French Polynesia",
        "TF" => "French Southern Territories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GN" => "Guinea",
        "GW" => "Guinea-Bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "Heard Island and Mcdonald Islands",
        "VA" => "Holy See (Vatican City State)",
        "HN" => "Honduras",
        "HK" => "Hong Kong",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran, Islamic Republic of",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KP" => "Korea, Democratic People's Republic of",
        "KR" => "Korea, Republic of",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "Lao People's Democratic Republic",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libyan Arab Jamahiriya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macao",
        "MK" => "Macedonia, the Former Yugoslav Republic of",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "Marshall Islands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "MX" => "Mexico",
        "FM" => "Micronesia, Federated States of",
        "MD" => "Moldova, Republic of",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "Netherlands Antilles",
        "NC" => "New Caledonia",
        "NZ" => "New Zealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "Norfolk Island",
        "MP" => "Northern Mariana Islands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PS" => "Palestinian Territory, Occupied",
        "PA" => "Panama",
        "PG" => "Papua New Guinea",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "Puerto Rico",
        "QA" => "Qatar",
        "RE" => "Reunion",
        "RO" => "Romania",
        "RU" => "Russian Federation",
        "RW" => "Rwanda",
        "SH" => "Saint Helena",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "PM" => "Saint Pierre and Miquelon",
        "VC" => "Saint Vincent and the Grenadines",
        "WS" => "Samoa",
        "SM" => "San Marino",
        "ST" => "Sao Tome and Principe",
        "SA" => "Saudi Arabia",
        "SN" => "Senegal",
        "CS" => "Serbia and Montenegro",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapore",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "SB" => "Solomon Islands",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "GS" => "South Georgia and the South Sandwich Islands",
        "ES" => "Spain",
        "LK" => "Sri Lanka",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "Svalbard and Jan Mayen",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "Syrian Arab Republic",
        "TW" => "Taiwan, Province of China",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania, United Republic of",
        "TH" => "Thailand",
        "TL" => "Timor-Leste",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "Trinidad and Tobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "Turks and Caicos Islands",
        "TV" => "Tuvalu",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "AE" => "United Arab Emirates",
        "GB" => "United Kingdom",
        "US" => "United States",
        "UM" => "United States Minor Outlying Islands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "Viet Nam",
        "VG" => "Virgin Islands, British",
        "VI" => "Virgin Islands, U.s.",
        "WF" => "Wallis and Futuna",
        "EH" => "Western Sahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe"
    );
}

function passwordValidation($password){
    preg_match_all('#([a-z]+)|([A-Z]+)|([0-9]+)#',$password,$data);
    if(empty($data[0])){
        if(mb_strlen($password) >= 6){
            return true;
        }

        return false;
    }

    $chars        = $data[1];
    $upperChars   = $data[2];
    $numbers      = $data[3];

    // Chars
    foreach ($chars as $char){
        if(!empty($char)){
            $strLen = mb_strlen($char);
            if($strLen > 1 && $char == implode(range($char[0],$char[$strLen-1]))){
                return false;
            }
        }
    }

    // Upper Chars
    foreach ($upperChars as $char){
        if(!empty($char)){
            $strLen = mb_strlen($char);
            if($strLen > 1 && $char == implode(range($char[0],$char[$strLen-1]))){
                return false;
            }
        }
    }

    // Numbers
    foreach ($numbers as $number){
        if(!empty($number)){
            $nulLen = mb_strlen($number);
            if($nulLen > 1 && $number == implode(range($number[0],$number[$nulLen-1]))){
                return false;
            }
        }
    }

    return true;

}


function exportXLS($title ,$heads, $exData,$callback){


    $return = "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />

    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Sheet 1</x:Name>
                    <x:WorksheetOptions>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                        </x:Print>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
</head>";

    $return .= '<table><thead><tr><th colspan="'.count($heads).'">'.$title.'</th></tr><tr>';

    foreach ($heads as $key => $value){
        $return.= '<th>'.$value.'</th>';
    }
    $return.= '</thead><tbody>';
    foreach ($exData as $key => $value){
        $return.= '<tr>';
        foreach ($callback as $k => $v){
            if(is_string($v))
                $return.= '<td>'.$value[$v].'</td>' ;
            else
                $return.= '<td>'.$v($value).'</td>';
        }
        $return.= '</tr>';
    }
    $return.= '</tbody></table>';


    header('Content-Disposition: attachment; filename='.$title.'-'.date('Y-m-d H i').'.xls');

    echo $return;

    return;


}
/*
function  exportXLS($title ,$heads, $exData,$callback){

    $return = '<table><thead><tr><th colspan="'.count($heads).'">'.$title.'</th></tr><tr>';

    foreach ($heads as $key => $value){
        $return.= '<th>'.$value.'</th>';
    }
    $return.= '</thead><tbody>';
    foreach ($exData as $key => $value){
        $return.= '<tr>';
        foreach ($callback as $k => $v){
            if(is_string($v))
                $return.= '<td>'.$value[$v].'</td>' ;
            else
                $return.= '<td>'.$v($value).'</td>';
        }
        $return.= '</tr>';
    }
    $return.= '</tbody></table>';

    \Excel::download(function($excel) use ($return) {
        $excel->sheet('Excel sheet', function($sheet) use ($return) {
            $sheet->loadView('system.export-to-excel')->with('return',$return);
        });

    },$title)->export('xls');

}
*/

function  makeTable($title ,$heads, $exData,$callback){

    $return = '<table><thead><tr><th colspan="'.count($heads).'">'.$title.'</th></tr><tr>';

    foreach ($heads as $key => $value){
        $return.= '<th>'.$value.'</th>';
    }
    $return.= '</thead><tbody>';
    foreach ($exData as $key => $value){
        $return.= '<tr>';
        foreach ($callback as $k => $v){
            if(is_string($v))
                $return.= '<td>'.$value[$v].'</td>' ;
            else
                $return.= '<td>'.$v($value).'</td>';
        }
        $return.= '</tr>';
    }
    $return.= '</tbody></table>';

 return $return;

}

function exportTable($title,$sheets){

    \Excel::create($title, function($excel) use ($sheets) {
        foreach ($sheets as $key=> $row) {
            $excel->sheet($row['title'], function ($sheet) use ($row) {
                
                $sheet->loadView('system.export-to-excel')->with('return', $row['table']
                );
            });
        }
    })->export('xls');
}

function exportOneTable($title,$table){

    \Excel::create($title, function($excel) use($table) {

            $excel->sheet('Excel sheet', function ($sheet) use ($table) {

                $sheet->loadView('system.export-to-excel')->with('return', $table);

            });

    })->export('xls');
}


function pda($ob)
{
    print_r($ob->toArray());
    die;
}

function pd($ob)
{
    print_r($ob);
    die;
}

function getLang(){
   return App::getLocale();
}


function json($status,$data=[]){
    return ['status'=>$status,'data'=>$data];
}

function getRealIP(){
    return env('HTTP_CF_CONNECTING_IP') ?? env('REMOTE_ADDR');
}

function databaseAmount($amount){
    $pos = strpos($amount,'.');
    if($pos === false){
        return $amount;
    }

    return substr($amount,0,$pos).substr($amount,$pos,3);
}

function distance($lat1, $lon1, $lat2, $lon2, $unit,$round) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return round(($miles * 1.609344),$round);
    } else if ($unit == "N") {
        return round(($miles * 0.8684),$round);
    } else { // defult kilometer
        return round(($miles * 1.609344),$round);
    }
}


function setError($data,$model_type,$model_id,$msg = null,$type = 'error'){
    $create = \App\Models\ErrorLog::create([
        'model_type'=> $model_type,
        'model_id'=> $model_id,
        'type'=> $type,
        'data'=> $data,
        'msg'=> $msg
    ]);

    if($create){
        return true;
    }else{
        return false;
    }

}


function amount($amount,$format = false){
    if($format){
        return number_format($amount,2).' '.__('LE');
    }
    return $amount.' '.__('LE');
}

function humanStr($value){
    return __(ucwords(str_replace('_', ' ', $value)));
}

// Arrays Helpers

function arrayGetOnly(array $array,$only){
    if(empty($array)){
        return [];
    }else{
        $newData = [];
        if(is_array($only)){
            foreach ($only as $key => $value) {
                if(isset($array[$value])){
                    $newData[$value] = $array[$value];
                }
            }
        }elseif(is_string($only)){
            if(isset($array[$only])){
                $newData[$only] = $array[$only];
            }
        }else{
            return [];
        }

        return $newData;
    }
}

// Arrays Helpers




function listLangCodes(){
    return [
        'ar'=> 'العربية',
        'en'=> 'English'
    ];
}

function iif($conditions,$true = null,$false = null){
    if($conditions){
        if(is_object($true) && ($true instanceof Closure)){
            return $true();
        }else{
            return $true;
        }
    }else{
        if(is_object($false) && ($false instanceof Closure)){
            return $false();
        }else{
            return $false;
        }
    }
}


function whereBetween( &$eloquent,$columnName,$form,$to){
    if(!empty($form) && empty($to)){
        $eloquent->whereRaw("$columnName >= ?",[$form]);
    }elseif(empty($form) && !empty($to)){
        $eloquent->whereRaw("$columnName <= ?",[$to]);
    }elseif(!empty($form) && !empty($to)){
        $eloquent->where(function($query) use($columnName,$form,$to) {
            $query->whereRaw("$columnName BETWEEN ? AND ?",[$form,$to]);
        });
    }
}


function whereBetween2Column( &$eloquent,$columnName,$from,$to){
    if(!empty($from) && empty($to)){
        $eloquent->whereRaw("{$columnName}_from >= ?",[$from]);
    }elseif(empty($from) && !empty($to)){
        $eloquent->whereRaw("{$columnName}_to <= ?",[$to]);
    }elseif(!empty($from) && !empty($to)){
        $eloquent->whereRaw("
            (
                {$columnName}_from >= ? AND
                {$columnName}_to <= ? 
            )
        ",[$from,$to]);
    }
}


function orWhereByLang(&$eloquent,$columnName,$value,$operator = 'like'){
    $eloquent->where(function($query) use($columnName,$value,$operator){
        $count = 0;
        foreach (listLangCodes() as $key => $langName) {

            if($count == 0){
                if($operator == 'like'){
                    $query->where("$columnName".'_'."$key",'LIKE','%'.$value.'%');
                }else{
                    $query->where("$columnName".'_'."$key",$operator,$value);
                }
            }else{
                if($operator == 'like'){
                    $query->orWhere("$columnName".'_'."$key",'LIKE','%'.$value.'%');
                }else{
                    $query->orWhere("$columnName".'_'."$key",$operator,$value);
                }
            }
            $count++;
        }
    });
}

function imageResize($imagePath,$width,$height){
    $vImagePath = $imagePath;
    $imagePath = storage_path('app/public/'.$imagePath);

    if(File::exists($imagePath) && explode('/',File::mimeType($imagePath))[0] == 'image' ){
        $resizedFileName = File::dirname($imagePath).'/'.File::name($imagePath).'_'.$width.'X'.$height.'.'.File::extension($imagePath);

        if(!Storage::exists($resizedFileName)){
            Image::make($imagePath)
                ->resize($width,$height)
                ->save($resizedFileName);
        }

        return File::dirname($vImagePath).'/'.File::name($imagePath).'_'.$width.'X'.$height.'.'.File::extension($imagePath);

//        return $resizedFileName;
    }


    return false;
}


function image($imagePath,$width,$height){
    return imageResize($imagePath,$width,$height);
}




/*
 * @ $areaID : array or int
 */

function getLastNotEmptyItem($array){
    if(empty($array) || !is_array($array)){
        return false;
    }

    $last = end($array);
    if(empty($last)){
        $last = prev($array);
    }
    return $last;
}

function contactType($row){
    return __(ucfirst(str_replace('_',' ',$row->type)));
}


function contactValue($row){
    if($row->type == 'email'){
        return '<a href="mailto:'.$row->value.'">'.$row->value.'</a>';
    }else{
        return '<a href="tel:'.$row->value.'">'.$row->value.'</a>';
    }
}

function UniqueId(){
    return md5(str_random(20).uniqid().str_random(50).(time()*rand()));
}

function Base64PngQR($var,$size=false){
    $height = ((isset($size['0']))? $size['0']:'256');
    $width = ((isset($size['1']))? $size['1']:'256');
    $renderer = new \BaconQrCode\Renderer\Image\Png();
    $renderer->setHeight($height);
    $renderer->setWidth($width);
    $writer = new \BaconQrCode\Writer($renderer);
    return $writer->outputContent($var);
}


function setting($name,$returnAll = false){
    if($name == 'sales_group_id') return '1,2';

    static $data;
    if($data == null){
        $getData = App\Models\Setting::get(['name','value'])->toArray();
        $data = array_column($getData,'value','name');
    }
    if($returnAll){
        return $data;
    }elseif(isset($data[$name])){
        $unserialize = @unserialize($data[$name]);
        if(is_array($unserialize)){
            return $unserialize;
        }
        return $data[$name];
    }

    return null;
}

function recursiveFind(array $array, $needle)
{
    $response = [];
    $iterator  = new RecursiveArrayIterator($array);
    $recursive = new RecursiveIteratorIterator(
        $iterator,
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($recursive as $key => $value) {
        if ($key === $needle) {
            $response[] = $value;
        }
    }
    return ((count($response)=='1')?$response:$response);
}

function response_to_object($array) {
    $obj = new stdClass;
    foreach($array as $k => $v) {
        if(strlen($k)) {
            if((is_array($v)) && count($v)) {
                $obj->{$k} = response_to_object($v); //RECURSION
            } elseif(($k == 'info') && (is_array($v))) {
                    $obj->{$k} = implode("\n",$v);
            } else {
                $obj->{$k} = $v;
            }
        }
    }
    return $obj;
}

function calcDim($width,$height,$maxwidth,$maxheight) {
    if($width != $height){
        if($width > $height){
            $t_width = $maxwidth;
            $t_height = (($t_width * $height)/$width);
            //fix height
            if($t_height > $maxheight)
            {
                $t_height = $maxheight;
                $t_width = (($width * $t_height)/$height);
            }
        } else {
            $t_height = $maxheight;
            $t_width = (($width * $t_height)/$height);
            //fix width
            if($t_width > $maxwidth){
                $t_width = $maxwidth;
                $t_height = (($t_width * $height)/$width);
            }
        }
    } else
        $t_width = $t_height = min($maxheight,$maxwidth);
    return array('height'=>(int)$t_height,'width'=>(int)$t_width);
}

function PaymentParamName($param,$lang){
    $paramData = \App\Models\PaymentServiceAPIParameters::where('external_system_id','=',explode('_',$param)['1'])->first();
    return $paramData->{'name_'.$lang};
}




function PaymentParamNameNew($param,$lang){
    $paramData = \App\Models\PaymentServiceAPIParameters::where('id','=',$param)->first();
    if(!empty($paramData))
        return $paramData->{'name_'.$lang};
    else
        return '--';
}


function monitorNotification($title,$description,$url){
    if(!empty(setting('monitor_staff'))){
        $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
            ->get();

        foreach ($monitorStaff as $key => $value){
            $value->notify(
                (new UserNotification([
                    'title'         => $title,
                    'description'   => $description,
                    'url'           => $url
                ]))
                    ->delay(5)
            );
        }
    }
}