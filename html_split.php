<?php
/**
 * Recursive detect DOMText fit $len
 * Find text and check if that text appending still fit the required len. 
 * @param DOMNode $dom
 * @param int $len
 * @param type $buffer
 */
function getNext($dom, $len, &$buffer=''){
	
	// fulled -> by pass all followed tag
	if (mb_strlen( $buffer) >= $len){
		return null;
	}else if (get_class($dom)=='DOMText'){ // if tag is text -> try to use this text
		// still shorter than required $len
		if (mb_strlen( $buffer.$dom->wholeText) < $len){
			$buffer.= $dom->wholeText;
			return $dom;
		}else{	// Get part of this node text fitting the required $len
			//return null;
			// try to get part of
			$part = $len - mb_strlen($buffer);
			$st = mb_substr ($dom->wholeText, 0, $part);
			$buffer.= $st;
			return $st;
		}
	// node have childs -> check each child
	}elseif (isset($dom->childNodes) && $dom->childNodes->length>0){
		$i = 0;
		while ($i < $dom->childNodes->length){
			$child = $dom->childNodes->item($i);
			$ret = getNext($child, $len, $buffer);
			if ($ret===null || (is_string($ret))){
					
				while ($i < $dom->childNodes->length){
					$dom->removeChild ($dom->childNodes->item($i));
				}
				if (is_string($ret)) {
					$dom->appendChild(new DOMText($ret.'...'));
					$i++;
				};
				
			}else{
				$i++;
			}
			
		}
		// if all child deleted -> return null node
		if ($dom->childNodes->length == 0)
			return null;
		else
			return $dom;
	// other leaf node (br, img)
	}else{
		echo $dom->nodeName;
		return $dom;
	}
	//TODO image balancing -> 1 image equivalent how many charaters? 
}

$html = <<<COD
<div class="entry-content">
    <p>こんにちは柴田ですー！
        <br> 最近夜が涼しくて夏の終わりを感じております。
        <br> なんだか物足りなくて遊び足りなくてちょっと切ないこの感じ。
        <br> 何か夏っぽい事がしたくて、海に行ってきました。
        <br> 京都の北の方。丹後の方です。
        <br> あいにくの曇と雨でしたが、晴れ間もちょこちょこあって、楽しめました。ちょっとだけ泳ぎましたよ！あと岩牡蠣食べました！水平線！波の音！ほんのちょこっとだけ焼けました。
    </p>
    <p>私の日記はほどほどにして、新作紹介に移ります♪</p>
    <p>
        <a href="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237399712.jpg"><img src="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237399712-576x1024.jpg" alt="新作" width="576" height="1024" class="alignnone size-large wp-image-33248"></a>
    </p>
    <p><a href="http://www.wargo.jp/products/detail2213.html" target="_blank">天の川とんぼ玉簪　￥3,400(税抜)</a>
        <br>
        <a href="http://www.wargo.jp/products/detail3411.html" target="_blank">小百合簪-ピンク　￥3,900(税抜)</a>　<strong><span style="color: #ff6600">NEW!!</span></strong>
        <br>
        <a href="http://www.wargo.jp/products/detail919.html" target="_blank">ドロシー-黄　￥4,200(税抜)</a></p>
    <p>浴衣、振り袖、是非着けて欲しい！</p>
    <p>
        <a href="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237404428.jpg"><img src="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237404428-576x1024.jpg" alt="新作" width="576" height="1024" class="alignnone size-large wp-image-33249"></a>
    </p>
    <p><a href="http://www.wargo.jp/products/detail2995.html" target="_blank">彩艶玉　大-紫　￥2,400(税抜)</a>
        <br>
        <a href="http://www.wargo.jp/products/detail3410.html" target="_blank">巻物一本簪-赤　￥5,400(税抜)</a>　<strong><span style="color: #ff6600">NEW!!</span></strong>
        <br>
        <a href="http://www.wargo.jp/products/detail2778.html" target="_blank">蝶唐草透かし簪-赤　￥2,900(税抜)</a>　<strong><span style="color: #ff6600">再入荷！</span></strong></p>
    <p>カッコイイの来たよ！</p>
    <p>
        <a href="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237401969.jpg"><img src="http://www.wargo.jp/blog/wp-content/uploads/2015/08/1440237401969-576x1024.jpg" alt="新作" width="576" height="1024" class="alignnone size-large wp-image-33250"></a>
    </p>
    <p><a href="http://www.wargo.jp/products/detail2730.html" target="_blank">輪島塗一本簪-夜地球　￥11,900(税抜)</a>
        <br>
        <a href="http://www.wargo.jp/products/detail3412.html" target="_blank">ランタン一本簪　￥7,200(税抜)</a>　<strong><span style="color: #ff6600">NEW!!</span></strong></p>
    <p>しぶい！合わせやすい！</p>
    <p>柴田は巻物一本簪ほしいです。赤と緑と紫があるんですよ♪
        <br> 天然石付きです。結構ボリュームあります。
    </p>
    <p>ぜひともお店に見に来てくださいな(O∀O)♪
        <br> 岩津、柴田がお待ちしております～。
    </p>
</div>
COD;

header('Content-Type: text/html; charset=utf-8');
?>
<html>
<body>
<?php
	// http://.../html_split.php?max=250
	$len = empty($_GET['max'])?100:(int)$_GET['max'];
	
	// load html
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->formatOutput       = true;
	$doc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$html);
	
	// select by class
	$finder = new DomXPath($doc);
	$classname="entry-content";
	$content = $finder->query("//*[contains(@class, '$classname')]");
	//$content = $doc->getElementsByTagName('body');

	// Get first DIV
	$content = $content->item(0);
	// Go through and process DOM tree
	$newContent = getNext($content, $len);
	
	// GENERATE NEW HTML
	echo $doc->saveHTML($newContent);

?>
</body>
</html>