<?php
	/* Libchart - PHP chart library
	 * Copyright (C) 2005-2008 Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
	 * 
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 * 
	 */
	
	/**
	 * Pie chart.
	 *
	 * @author Jean-Marc Trémeaux (jm.tremeaux at gmail.com)
	 */
	class PieChart extends Chart {
		protected $pieCenterX;
		protected $pieCenterY;
		public $pieCenterW;
	
		/**
		 * Constructor of a pie chart.
		 *
		 * @param integer width of the image
		 * @param integer height of the image
		 */
		public function PieChart($width = 500, $height = 300) {
			
			parent::Chart($width, $height);
			//$this->plot->setGraphPadding(new Padding(15, 10, 30, 30));
			$this->plot->setGraphPadding(new Padding(0, 0, 10, 10));
			
						
			if(count($width) == 0)
			$pieCenterW = 600;
			else
			$pieCenterW = $this->width;

			
		}
		


		/**
		 * Computes the layout.
		 */
		protected function computeLayout() {
			$this->plot->setHasCaption(true);
			$this->plot->computeLayout();
			
			// Get the graph area
			$graphArea = $this->plot->getGraphArea();

			// Compute the coordinates of the pie
			
			$this->pieCenterX = $graphArea->x1 + ($graphArea->x2 - $graphArea->x1) / 2;
			$this->pieCenterY = $graphArea->y1 + ($graphArea->y2 - $graphArea->y1) / 2;
			$this->pieWidth = round(($graphArea->x2 - $graphArea->x1) * 4 / 5);
			$this->pieHeight = round(($graphArea->y2 - $graphArea->y1) * 3.7 / 5);
			$this->pieDepth = round($this->pieWidth * 0.05);

		}
		
		/**
		 * Compare two sampling point values, order from biggest to lowest value.
		 *
		 * @param double first value
		 * @param double second value
		 * @return integer result of the comparison
		 */
		protected function sortPie($v1, $v2) {
			return $v1[0] == $v2[0] ? 0 :
				$v1[0] > $v2[0] ? -1 :
				1;
		}
		
		/**
		 * Compute pie values in percentage and sort them.
		 */
		protected function computePercent() {
			$this->total = 0;
			$this->percent = array();

			$pointList = $this->dataSet->getPointList();
			foreach ($pointList as $point) {
				$this->total += $point->getY();
			}

			foreach ($pointList as $point) {
				$percent = $this->total == 0 ? 0 : 100 * $point->getY() / $this->total;

				array_push($this->percent, array($percent, $point));
			}

			usort($this->percent, array("PieChart", "sortPie"));
		}

		/**
		 * Creates the pie chart image.
		 */
		protected function createImage() {
			parent::createImage();

			// Get graphical obects
			$img = $this->plot->getImg();
			$palette = $this->plot->getPalette();
			$primitive = $this->plot->getPrimitive();
			
			// Get the graph area
			$graphArea = $this->plot->getGraphArea();

			// Legend box
			$primitive->outlinedBox($graphArea->x1, $graphArea->y1, $graphArea->x2, $graphArea->y2, $palette->axisColor[0], $palette->axisColor[1]);

			// Aqua-like background
			for ($i = $graphArea->y1 + 2; $i < $graphArea->y2 - 1; $i++) {
				$color = $palette->aquaColor[($i + 3) % 4];
				$primitive->line($graphArea->x1 + 2, $i, $graphArea->x2 - 2, $i, $color);
			}
		}

		/**
		 * Renders the caption.
		 */
		protected function printCaption() {
			// Create a list of labels
			$labelList = array();
			foreach($this->percent as $percent) 
			{
				list($percent, $point) = $percent;
				$label = $point->getX();

				
				array_push($labelList, $label);
			}
			
			// Create the caption
			$caption = new Caption();
			$caption->setPlot($this->plot);
			$caption->setLabelList($labelList);
			
			$palette = $this->plot->getPalette();
			$pieColorSet = $palette->pieColorSet;
			$caption->setColorSet($pieColorSet);

			// Render the caption
			$caption->render();
		}

		/**
		 * Draw a 2D disc.
		 *
		 * @param integer Center coordinate (y)
		 * @param array Colors for each portion
		 * @param bitfield Drawing mode
		 */
		protected function drawDisc($cy, $colorArray, $mode) {
			// Get graphical obects
			$img = $this->plot->getImg();

			$i = 0;
			$angle1 = 0;
			$percentTotal = 0;

			foreach ($this->percent as $a) {
				list ($percent, $point) = $a;

				// If value is null, don't draw this arc
				if ($percent <= 0) {
					continue;
				}
				
				$color = $colorArray[$i % count($colorArray)];

				$percentTotal += $percent;
				$angle2 = $percentTotal * 360 / 100;

				imagefilledarc($img, $this->pieCenterX, $cy, $this->pieWidth, $this->pieHeight, $angle1, $angle2, $color->getColor($img), $mode);

				$angle1 = $angle2;

				$i++;
			}
		}

		/**
		 * Print the percentage text.
		 */
		protected function drawPercent() {
			// Get graphical obects
			$img = $this->plot->getImg();
			$palette = $this->plot->getPalette();
			$text = $this->plot->getText();
			$primitive = $this->plot->getPrimitive();
			
			$angle1 = 0;
			$percentTotal = 0;

			foreach ($this->percent as $a) {
				list ($percent, $point) = $a;

				// If value is null, don't print percentage
				if ($percent <= 0) {
					continue;
				}

				$percentTotal += $percent;
				$angle2 = $percentTotal * 2 * M_PI / 100;

				$angle = $angle1 + ($angle2 - $angle1) / 2;
				$label = number_format($percent) . "%";

				$x = cos($angle) * ($this->pieWidth + 35) / 2 + $this->pieCenterX;
				$y = sin($angle) * ($this->pieHeight + 35) / 2 + $this->pieCenterY;

				$text->printText($img, $x, $y, $this->plot->getTextColor(), $label, $text->fontCondensed, $text->HORIZONTAL_CENTER_ALIGN | $text->VERTICAL_CENTER_ALIGN);
				


				$angle1 = $angle2;
			}
		}

		/**
		 * Print the pie chart.
		 */
		protected function printPie() {
			// Get graphical obects
			$img = $this->plot->getImg();
			$palette = $this->plot->getPalette();
			$text = $this->plot->getText();
			$primitive = $this->plot->getPrimitive();

			// Get the pie color set
			$pieColorSet = $palette->pieColorSet;
			$pieColorSet->reset();

			// Silhouette
			for ($cy = $this->pieCenterY + $this->pieDepth / 2; $cy >= $this->pieCenterY - $this->pieDepth / 2; $cy--) {
				$this->drawDisc($cy, $palette->pieColorSet->shadowColorList, IMG_ARC_EDGED);
			}

			// Top
			$this->drawDisc($this->pieCenterY - $this->pieDepth / 2, $palette->pieColorSet->colorList, IMG_ARC_PIE);

			// Top Outline
			$this->drawPercent();
		}

		/**
		 * Render the chart image.
		 *
		 * @param string name of the file to render the image to (optional)
		 */
		public function render($fileName = null) {
			$this->computePercent();
			$this->computeLayout();
			$this->createImage();
			$this->plot->printLogo();
			$this->plot->printTitle();
			$this->printPie();
			$this->printCaption();
			$this->plot->render($fileName);
		}
		
		public function printCaptionHtml() 
		{
			
				$color = array(
				
				// AZULES
				'003366',
				'006699',
				'0099CC',
				'00CCFF',
				'00FFFF',
				
				// Verdes
				'003300',
				'006600',
				'009900',
				'00CC00',
				'00FF00',
				
				// Rojos
				'FF0000',
				'FF3300',
				'FF6600',
				'FF9900',
				'FFCC00',
				
				//Grises
				
				'000000',
				'333333',
				'666666',
				'999999',
				'CCCCCC'
				
				
				);
			
			// Create a list of label
			// $point->getX()  -> Etiquetas
			// $point->getY()  -> Valores
			
			$ind = 0;
			
			// Get the graph area
			$graphArea = $this->plot->getGraphArea();
			$this->pieWidth = round(($graphArea->x2 - $graphArea->x1) * 4 / 5);
	
			$width  = $this->pieWidth + 110;
			$cwidth = ($width - 40) / 2; 
			 
			echo "<table border='0' style='font-size:11px; width:".$width."px;'>";
			foreach($this->percent as $percent) 
			{
				list($percent, $point) = $percent;
				$label = $point->getX();
			
				if($ind % 2 == 0)
				{
					echo "<tr><td width='20'>";
					echo "<table width='20' height='20'><tr><td width='18' bgcolor='#".$color[$ind]."'></td></tr></table>";
					echo "</td><td width='".$cwidth."'>&nbsp;".$label."</td>";
				}	
				else
				{
					//echo "<td width='20' bgcolor='#".$color[$ind]."'></td><td width='".$cwidth."'>&nbsp;".$label."</td>";
					//echo "</tr>";
					
					echo "<td width='20'>";
					echo "<table width='20' height='20'><tr><td width='18' bgcolor='#".$color[$ind]."'></td></tr></table>";
					echo "</td><td width='".$cwidth."'>&nbsp;".$label."</td></tr>";
					
				}	

	
				
				$ind = $ind + 1;
				//echo $label." ".$point->getY();
			}
			
			if($ind%2!= 0)
			echo "<td width='20'>&nbsp;</td><td width='".$cwidth."'>&nbsp;</td></tr>";
			
			echo "</table><br />";


			
		}
	}
?>