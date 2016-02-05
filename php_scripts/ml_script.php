<?php

	$execPath = "/home/jon/.CLion12/system/cmake/generated/82e833ec/82e833ec/Debug/ASE-WS2015-16";

	$folder = "/home/jon/Desktop/training_images";

	$mserDeltas = array(45, 40, 30, 25);
	$mserMinAreaFactors = array(0.00005, 0.00002, 0.000005);
	$mserMaxAreaFactors = array(0.1, 0.05, 0.02);
	$mserMaxVariations = array(10, 5, 3, 1);
	$minConfidences1 = array(0.5, 0.3);
	$minConfidences2 = array(0.5, 0.3);

	$indices = array(0, 0, 0, 0, 0, 0);

	for($i = 0; $indices[0] < count($mserDeltas); $indices[0]++) {
		$mserDelta = $mserDeltas[$indices[0]];
		for($j = 0; $indices[1] < count($mserMinAreaFactors); $indices[1]++) {
			$mserMinAreaFactor = $mserMinAreaFactors[$indices[1]];
			for($k = 0; $indices[2] < count($mserMaxAreaFactors); $indices[2]++) {
				$mserMaxAreaFactor = $mserMaxAreaFactors[$indices[2]];
				for($l = 0; $indices[3] < count($mserMaxVariations); $indices[3]++) {
					$mserMaxVariation = $mserMaxVariations[$indices[3]];
					for($m = 0; $indices[4] < count($minConfidences1); $indices[4]++) {
						$minConfidence1 = $minConfidences1[$indices[4]];
						for($n = 0; $indices[5] < count($minConfidences2); $indices[5]++) {
							$minConfidence2  = $minConfidences1[$indices[5]];
							$jsonOutputPath = "/home/jon/Desktop/training_results/$indices[0]_$indices[1]_$indices[2]_$indices[3]_$indices[4]_$indices[5].json";
							echo $jsonOutputPath . "<br>";
							// echo "$execPath $folder $jsonOutputPath $mserDelta $mserMinAreaFactor $mserMaxAreaFactor $mserMaxVariation $minConfidence1 $minConfidence2";
							exec("$execPath $folder $jsonOutputPath $mserDelta $mserMinAreaFactor $mserMaxAreaFactor $mserMaxVariation $minConfidence1 $minConfidence2");
						}
						$indices[5] = 0;
					}
					$indices[4] = 0;
				}
				$indices[3] = 0;
			}
			$indices[2] = 0;
		}
		$indices[1] = 0;
	}
