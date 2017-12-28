<?php

class ExcelTools {
	
	public static $STYLE_COMBINE = array(
		'BOLD' => array('font'=>array('bold'=>true)),
		'ITALIC' => array('font'=>array('italic'=>true)),
		'ALIGN_LEFT' => array('alignment'=>array('horizontal' => 'left')),
		'ALIGN_RIGHT' => array('alignment'=>array('horizontal' => 'right')),
		'ALIGN_CENTER' => array('alignment'=>array('horizontal' => 'center')),
		'VALIGN_TOP' => array('alignment'=>array('vertical' => 'top')),
		'VALIGN_BOTTOM' => array('alignment'=>array('vertical' => 'bottom')),
		'VALIGN_CENTER' => array('alignment'=>array('vertical' => 'center')),
		'ALL_BORDER_BOLD' => array(
			'borders' => array(
				'top' => array('style' => 'medium'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'medium')
			)
		),
		'ALL_BORDER_NORMAL' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'thin'),
				'right' => array('style' => 'thin'),
				'bottom' => array('style' => 'thin')
			)
		),
		'TOP_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'medium'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'thin')
			)
		),
		
		'TOPBOLD' => array('borders' => array('top' => array('style' => 'medium'))),
		'LEFTBOLD' => array('borders' => array('left' => array('style' => 'medium'))),
		'RIGHTBOLD' => array('borders' => array('right' => array('style' => 'medium'))),
		'BOTTOMBOLD' => array('borders' => array('bottom' => array('style' => 'medium'))),
		
		'TOPNORMAL' => array('borders' => array('top' => array('style' => 'thin'))),
		'LEFTNORMAL' => array('borders' => array('left' => array('style' => 'thin'))),
		'RIGHTNORMAL' => array('borders' => array('right' => array('style' => 'thin'))),
		'BOTTOMNORMAL' => array('borders' => array('bottom' => array('style' => 'thin'))),
		
		'BOTTOM_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'medium')
			)
		),
		'LEFTTOP_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'medium'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'thin'),
				'bottom' => array('style' => 'thin')
			)
		),
		'RIGHTTOP_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'medium'),
				'left' => array('style' => 'thin'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'thin')
			)
		),
		'RIGHTBOTTOM_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'thin'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'medium')
			)
		),
		'LEFTBOTTOM_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'thin'),
				'bottom' => array('style' => 'medium')
			)
		),
		'MIDLEFT_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'medium'),
				'right' => array('style' => 'thin'),
				'bottom' => array('style' => 'thin')
			)
		),
		'MIDRIGHT_BLOCK' => array(
			'borders' => array(
				'top' => array('style' => 'thin'),
				'left' => array('style' => 'thin'),
				'right' => array('style' => 'medium'),
				'bottom' => array('style' => 'thin')
			)
		),
		'FILL_GREEN' => array(
			'fill' => array(
				'type' => 'linear',
				'rotation' => 0,
				'startcolor' => array(
					'rgb' => '92D050'
				),
				'endcolor' => array(
					'argb' => 'FF92D050'
				)
			)
		),//92D050
		'FILL_GRAY' => array(
			'fill' => array(
				'type' => 'linear',
				'rotation' => 0,
				'startcolor' => array(
					'rgb' => 'D9D9D9'
				),
				'endcolor' => array(
					'argb' => 'FFD9D9D9'
				)
			)
		),//D9D9D9
		
		'CURRENCY_RUB' => array(
			'numberformat' => array(
				'code' => '#,##0_-р'
			)
		)
	);
	
	public static $CHARS = array(
		'','A','B','C','D','E','F','G','H','I','J','K',
		'L','M','N','O','P','Q','R','S','T','U','V',
		'W','X','Y','Z'
	);
	
	public static function getCellName($x, $y){
		$y = intval($y);
		$name = '';
		$name .= self::getColumnName($x);
		$name .= $y;
		return $name;
	}
	
	public static function getColumnName($x){
		$name = '';
		$x = intval($x);
		if($x <= 26) $name .= self::$CHARS[$x];
		else{
			$countCeil = ceil($x/26);
			$countFloor = floor($x/26);
			
			$ceilNumber = $countFloor * 26;
			$charNumber = $x - $ceilNumber;
			
			$name .= self::$CHARS[$countFloor] . self::$CHARS[$charNumber];
			
		}
		return $name;
	}
	
	public static function setColumnWidth(&$activeSheet,$columnNumber,$width){
		$activeSheet->getColumnDimension(self::getColumnName($columnNumber))->setWidth($width);
	}
	
	public static function setCell(&$activeSheet,$x,$y,$value,array $styles = array()){
		$activeSheet->setCellValue(self::getCellName($x,$y),$value);
		$activeSheet->getStyle(ExcelTools::getCellName($x,$y))->applyFromArray($styles);
	}
	
	public static function setCellCombine(&$activeSheet,$x,$y,$value,array $stylesCombine = array()){
		
		$styles = array();
		
		if(count($stylesCombine)) 
			foreach($stylesCombine as $nameStyle) 
				if(array_key_exists($nameStyle,self::$STYLE_COMBINE))
					$styles = self::array_merge_recursive_distinct($styles, self::$STYLE_COMBINE[$nameStyle]);
		
		return self::setCell($activeSheet,$x,$y,$value,$styles);
	}
	
	public static function array_merge_recursive_distinct( array &$array1, array &$array2 ){
		$merged = $array1;

		foreach ( $array2 as $key => &$value ){
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ){
				$merged [$key] = self::array_merge_recursive_distinct ( $merged [$key], $value );
			}else{
				$merged [$key] = $value;
			}
		}

		return $merged;
	}
	
	public static function valueAdditionalText($value, $addValue, $color = 'GREEN'){
	
		$objRichText = new PHPExcel_RichText();
	
		$objRichText->createText($value . ' ');
	
		if( $color == 'RED' ) $colorCode = PHPExcel_Style_Color::COLOR_RED;
		//if( $color == 'GREEN' ) $colorCode = PHPExcel_Style_Color::COLOR_GREEN;
		if( $color == 'GREEN' ) $colorCode = 'FF6FAA2F';
		if( $color == 'BLACK' ) $colorCode = 'FF000000';
	
		$colorObject = new PHPExcel_Style_Color($colorCode);

		$objBold = $objRichText->createTextRun($addValue);
		//$objBold->getFont()->setBold(true);
		$objBold->getFont()->setColor($colorObject);
		
		return $objRichText;
	}
	
}

//echo ExcelTools::getCellName(87, 4);
