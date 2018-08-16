<?php
	$outputCsv = "";
	$xml = simplexml_load_file('img2.xml');
	if ($xml === false) {
		echo "Failed loading XML: ";
		foreach(libxml_get_errors() as $error) {
			echo "<br>", $error->message;
		}
	} else {
		$object_index = 0;
		foreach($xml as $key1 => $xml_each){
			if($key1=="object"){
				foreach($xml_each as $key2 => $xml_element){
					if($key2=="polygon"){
						$index = 0;
						foreach($xml_element as $key3 => $xml_part){
							if($key3=="pt"){
								if($index==0){
									$x1 = $xml_part->x;
									$y1 = $xml_part->y;
								}else if($index==2){
									$x2 = $xml_part->x;
									$y2 = $xml_part->y;
								}
								$index++;
							}
						}
						//echo "x1: $x1 x2: $x2  y1: $y1 y2: $y2 <br>";
						$boundingWidth = $x2-$x1;
						$boundingHeight = $y2-$y1;
						$centerX = $x1+$boundingWidth/2;
						$centerY = $y1+$boundingHeight/2;
						echo "4 features: $centerX $centerY $boundingWidth $boundingHeight<br>";
						echo $object_index."<br>";
						$feature_csv[$object_index] = array($centerX,$centerY,$boundingWidth,$boundingHeight);
						$trainIndex = $object_index*20;
						for($i=$trainIndex;$i<$trainIndex+20;$i++){
							$train_csv[$i] = array($centerX+gntDecimal(),$centerY+gntDecimal(),$boundingWidth+gntDecimal(),$boundingHeight+gntDecimal());
						}
						$object_index++;
					}
				}
			}
		}
	}
	
	
	$fp = fopen('input.csv', 'w');
	if(isset($_REQUEST["trainingData"])){
		foreach ($train_csv as $line) {
			fputcsv($fp, $line, ',');
		}
	}else{
		foreach ($feature_csv as $line) {
			fputcsv($fp, $line, ',');
		}
		fclose($fp);
	}

	function gntDecimal(){
		return rand(-100,100)/10000;
	}
?>