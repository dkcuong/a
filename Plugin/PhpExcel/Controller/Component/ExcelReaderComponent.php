<?php
	App::uses('Component', 'Controller');

	class ExcelReaderComponent extends Component {
		protected $PHPExcelReader;
		protected $PHPExcelLoaded = false;
		public $dataArray;

		public function initialize(Controller $controller) {
			parent::initialize($controller);

			App::import(
				'Vendor',
				'PhpExcel.IOFactory',
				array('file' => 'PHPExcel' . DS . 'IOFactory.php')
			);

			if (!class_exists('PHPExcel')){
				throw new CakeException('Vendor class PHPExcel not found!');
			}

			$this->dataArray = array();
		}

		public function loadUpFile($filename) {
			$this->PHPExcelReader = PHPExcel_IOFactory::createReaderForFile($filename);

			$this->PHPExcelLoaded = true;
			
			$this->PHPExcelReader->setReadDataOnly(true);

			return $this->PHPExcelReader->load($filename);
		}

		public function loadExcelFile($filename) {
			$excel = $this->loadUpFile($filename);
			$this->dataArray = $excel->getSheet(0)->toArray();
		}

		public function loadExcelFileMultipleSheet($filename) {
			$excel = $this->loadUpFile($filename);

			$mydata = array();
			foreach ($excel->getWorksheetIterator() as $worksheet) {
				$index = $excel->getIndex($worksheet);
				//echo('sheet-index : ' . $index . ', name : ' . $excel->getSheet($index)->getTitle());
				//echo('<br/>');
				$mydata[$excel->getSheet($index)->getTitle()] = $excel->getSheet($index)->toArray();
			}
			$this->dataArray = $mydata;
		}		
	}
?>