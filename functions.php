<?php
/**
 * functions.php
 *
 * user defined functions
 *
 * @package			infoblast-sms-follower
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @license			LGPL2, see included license file
 * @link			http://www.hamizi.net/
 * @since			Version 1.0.0
 */
 
/**
 * getCountryCodeFromIP
 *
 * get country code from ip
 *
 * @access public
 * @params string $ip
 * @return string
 */
if ( ! function_exists('getCountryCodeFromIP') ) 
{
	function getCountryCodeFromIP($ip) 
	{
		$countries = array("AF"=>"93","AL"=>"355","DZ"=>"213","AD"=>"376","AO"=>"244","AI"=>"809","AG"=>"268","AR"=>"54","AM"=>"374","AW"=>"297","AU"=>"61","AT"=>"43","AZ"=>"994","BH"=>"973","BD"=>"880","BB"=>"246","BY"=>"375","BE"=>"32","BZ"=>"501","BJ"=>"229","BM"=>"809","BT"=>"975","BO"=>"591","BA"=>"387","BW"=>"267","BR"=>"55","IO"=>"246","BN"=>"673","BG"=>"359","BF"=>"226","BI"=>"257","KH"=>"855","CM"=>"237","CA"=>"1","CV"=>"238","KY"=>"345","TD"=>"235","CL"=>"56","CN"=>"86","CI"=>"225","CO"=>"57","CG"=>"242","CD"=>"243","HR"=>"385","CU"=>"53","CY"=>"357","CZ"=>"420","DK"=>"45","DJ"=>"253","DM"=>"767","DO"=>"809","TL"=>"670","EC"=>"593","EG"=>"20","SV"=>"503","GQ"=>"240","EE"=>"372","FK"=>"500","FO"=>"298","FJ"=>"679","FI"=>"358","FR"=>"33","GF"=>"594","PF"=>"689","GA"=>"241","GM"=>"220","GP"=>"590","GE"=>"995","DE"=>"49","GH"=>"233","GI"=>"350","GR"=>"30","GL"=>"299","GD"=>"473","GT"=>"502","GG"=>"32767","GY"=>"592","HT"=>"509","HN"=>"504","HK"=>"852","HU"=>"36","IS"=>"354","IN"=>"91","ID"=>"62","IR"=>"98","IQ"=>"964","IE"=>"353","IM"=>"44","IL"=>"972","IT"=>"39","JM"=>"876","JP"=>"81","JE"=>"44","JO"=>"962","KZ"=>"7","KE"=>"254","KR"=>"82","KW"=>"965","KG"=>"996","LV"=>"371","LB"=>"961","LS"=>"266","LR"=>"231","LY"=>"218","LI"=>"423","LT"=>"370","LU"=>"352","MO"=>"853","MK"=>"389","MG"=>"261","MW"=>"265","MY"=>"60","MV"=>"960","ML"=>"223","MT"=>"356","MQ"=>"596","MR"=>"222","MU"=>"230","MX"=>"52","MD"=>"373","MC"=>"33","MN"=>"976","ME"=>"382","MS"=>"473","MA"=>"212","MZ"=>"258","MM"=>"95","NA"=>"264","NP"=>"977","NL"=>"31","AN"=>"599","NC"=>"687","NZ"=>"64","NI"=>"505","NE"=>"227","NG"=>"234","MP"=>"1670","NO"=>"47","OM"=>"968","PK"=>"92","PS"=>"970","PA"=>"507","PY"=>"595","PE"=>"51","PH"=>"63","PL"=>"48","PT"=>"351","PR"=>"1787","QA"=>"974","RE"=>"262","RO"=>"40","RU"=>"7","RW"=>"250","KN"=>"1869","WL"=>"1758","WV"=>"1784","SA"=>"966","SN"=>"221","RS"=>"381","SC"=>"248","SL"=>"232","SG"=>"65","SK"=>"421","SI"=>"386","SO"=>"252","ZA"=>"27","ES"=>"34","LK"=>"94","SD"=>"249","SR"=>"597","SZ"=>"268","SE"=>"46","CH"=>"41","SY"=>"963","TW"=>"886","TJ"=>"7","TZ"=>"255","TH"=>"66","TG"=>"228","TO"=>"676","TT"=>"1868","TN"=>"216","TR"=>"90","TM"=>"993","UG"=>"256","UA"=>"380","AE"=>"971","GB"=>"44","US"=>"1","UY"=>"598","UZ"=>"7","VU"=>"678","VE"=>"58","VN"=>"84","VG"=>"1284","WF"=>"681","YE"=>"381","ZM"=>"260","ZW"=>"263","RD"=>"00");
		$url = 'http://api.ipinfodb.com/v2/ip_query_country.php?key=c9bfcc9cc480f85cd4829e21c00478a5507e4c3990d29323cf68e19ccdeeff24&ip='.$ip;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$cdata = curl_exec($ch);
		curl_close($ch);
		$carr = simplexml_load_string($cdata);
		$return = $countries["{$carr->CountryCode}"];
		return $return;
	}
}

/**
 * get_ip
 *
 * get ip address from user
 *
 * @access public
 * @return string
 */
if (!function_exists('get_ip')) 
{
	function get_ip () 
	{		
		if (isset($_SERVER)) 
		{
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) 
				return $_SERVER["HTTP_X_FORWARDED_FOR"];
				
			if (isset($_SERVER["HTTP_CLIENT_IP"])) 
				return $_SERVER["HTTP_CLIENT_IP"];
			
			return $_SERVER["REMOTE_ADDR"];
		}
		
		if (getenv('HTTP_X_FORWARDED_FOR')) 
		{
			if (strstr(getenv('HTTP_X_FORWARDED_FOR'), ',')) 
				return trim(substr(getenv('HTTP_X_FORWARDED_FOR'), 0, strpos(getenv('HTTP_X_FORWARDED_FOR'), ',')));
			else
				return getenv('HTTP_X_FORWARDED_FOR');
		}
		
		if (getenv('HTTP_CLIENT_IP')) 
			return getenv('HTTP_CLIENT_IP');
		
		return getenv('REMOTE_ADDR');
	}
}

?>