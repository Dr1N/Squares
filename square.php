<?php
	session_start();

	require_once("config.php");
		
	$xClick = isset($_GET['x']) ? $_GET['x'] : "";
	$yClick = isset($_GET['y']) ? $_GET['y'] : "";
	$squares = isset($_SESSION['squares']) ? $_SESSION['squares'] : "";

	//Создание изображения

	$image = imagecreate(IMAGE_WIDTH, IMAGE_HEIGHT);
	$background = imagecolorallocate($image, 127, 127, 127);
	$textColor = imagecolorallocate($image, 0, 0, 0);

	//Генерация изображения после клика пользователя

	if($xClick != "" && $yClick != "" && $squares != "")
	{
		$squreInCircle = 0;
		for ($sqr = 0; $sqr < count($squares); $sqr++) 
		{ 
			$r = $squares[$sqr]['r'];
			$g = $squares[$sqr]['g'];
			$b = $squares[$sqr]['b'];
			$color = imagecolorallocate($image, $r, $g, $b);
			$x1 = $squares[$sqr]['x1'];
			$y1 = $squares[$sqr]['y1'];
			$x2 = $squares[$sqr]['x2'];
			$y2 = $squares[$sqr]['y2'];
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);

			//Окружность
						
			$ellipseColor = imagecolorallocate($image, 255, 255, 255);
			imageellipse($image, $xClick, $yClick, 2 * RADIUS, 2 * RADIUS, $ellipseColor);
			imageellipse($image, $xClick, $yClick, 3, 3, $ellipseColor);

			//Количество квадратов в окружности

			$distance_1 = sqrt(pow(($xClick - $x1), 2) + pow(($yClick - $y1), 2)); 
			$distance_2 = sqrt(pow(($xClick - $x2), 2) + pow(($yClick - $y2), 2));
			$distance_3 = sqrt(pow(($xClick - $x1), 2) + pow(($yClick - $y2), 2)); 
			$distance_4 = sqrt(pow(($xClick - $x2), 2) + pow(($yClick - $y1), 2));

			if( $distance_1 < RADIUS && 
				$distance_2 < RADIUS && 
				$distance_3 < RADIUS && 
				$distance_4 < RADIUS)
			{
				$white = imagecolorallocate($image, 255, 255, 255);
				imagefilledrectangle($image, $x1, $y1, $x2, $y2, $white);
				$squreInCircle++;
			};
		}

		//Текст

		imagestring($image, 5, 0, 0, "SQUARES IN CIRCLE: " . $squreInCircle . " (" . count($squares) . ")", $textColor);
	}
	else
	{
		session_destroy();
		session_start();

		//Создание случайного изображения (нового)

		$squareCnt = mt_rand(MIN_SQUARE_CNT, MAX_SQUARE_CNT);
		$squares = array();
		for ($sqr = 0; $sqr < $squareCnt ; $sqr++) 
		{ 
			$r = mt_rand(0, 255);
			$g = mt_rand(0, 255);
			$b =  mt_rand(0, 255);
			$color = imagecolorallocate($image, $r, $g, $b);
			$x1 = mt_rand(1, IMAGE_WIDTH - SQUART_SIDE);
			$y1 = mt_rand(1, IMAGE_HEIGHT - SQUART_SIDE);
			$x2 = ($x1 + SQUART_SIDE < IMAGE_WIDTH) ? $x1 + SQUART_SIDE : IMAGE_WIDTH;
			$y2 = ($y1 + SQUART_SIDE < IMAGE_HEIGHT) ? $y1 + SQUART_SIDE : IMAGE_HEIGHT;
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $color);
			$squares[] = array(
				"x1" => $x1, 
				"y1" => $y1, 
				"x2" => $x2, 
				"y2" => $y2, 
				"r" => $r,
				"g" => $g,
				"b" => $b
			);
		}
		$_SESSION['squares'] = $squares;
	}

	//Вывод изображения

	header("Content-Type: image/jpeg");

	imagejpeg($image);
	imagedestroy($image);
?>