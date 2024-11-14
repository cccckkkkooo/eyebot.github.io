<?
$f=fopen('../../telegram.txt', 'r');
$list=[];
while (($row=fgets($f))!==false)
{
	$list[]=str_replace('http:','https:',trim($row));
}
fclose($f);

// ---------------------------------------------

function getPage($url)
{
$curl=curl_init();

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_AUTOREFERER,true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

$data=curl_exec($curl);

curl_close($curl);

return $data;
}

// ---------------------------------------------

$original=$list;

$list=array_unique($list);

$redirectUrl='';

foreach ($list as $k=>$v)
{
	$text=getPage($list[$k]);
	
	if (strlen($text)<1000)
	{
		sleep(3);
		continue;
	}

	if (!strpos($text,'<meta name="robots" content="noindex, nofollow">'))
	{
		$redirectUrl=$list[$k];
		break;
	}
	else unset($list[$k]);
}

if (count($original)>count($list)) file_put_contents('../../telegram.txt',implode("\r\n",$list));

if ($redirectUrl)
{
	$html=file_get_contents('yes.html');
	$html=str_replace('{URL}',strrev($redirectUrl),$html);
}
else
{
	$html=file_get_contents('no.html');
}

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

echo $html;
?>