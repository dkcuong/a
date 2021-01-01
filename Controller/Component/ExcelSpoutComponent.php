<?php
	use Box\Spout\Common\Type;
	use Box\Spout\Common\Entity\Style\Border;
	use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
	use Box\Spout\Common\Entity\Style\Color;
	use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
	use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

	App::uses('Component', 'Controller');
	App::uses('Folder', 'Utility');
	App::uses('File', 'Utility');

	class ExcelSpoutComponent extends Component {
		/**
		 * Components
		 *
		 * @var array
		 */
		public $components = array();
        
		public function initialize(Controller $controller) {
			parent::initialize($controller);
		}
	
        public function setup_export_excel($readable_header, $model, $data, $conditions, $limit, $output_filename, $lang){
			$border = (new BorderBuilder())
				->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->build();

			$style_header = (new StyleBuilder())
				->setBorder($border)
				->setFontColor(Color::RED)
				->setShouldWrapText()
				->setFontSize(14)
				->setBackgroundColor(Color::YELLOW)
				->build();

			$style = (new StyleBuilder())
				->setBorder($border)
				->build();

			$writer = WriterEntityFactory::createWriter(Type::XLSX);
			$writer->setTempFolder('/tmp');
			$writer->setShouldUseInlineStrings(false);
			$type_file = '.xlsx';
			$file_path = $output_filename . $type_file;
			$writer->openToBrowser($file_path);

			// $file_path = APP . 'webroot/download/' . $output_filename . $type_file;
			// $writer->openToFile($file_path);

			$row = WriterEntityFactory::createRowFromArray($readable_header, $style_header);
			$writer->addRow($row);

			$objModel = ClassRegistry::init($model);
			$total = $this->set_data_excel($writer, $style, $objModel, $data, $conditions, 1, $limit, $lang);
			
			$writer->close();
            return $total;
        }

        private function set_data_excel($writer, $style, $objModel, $data, $conditions, $page, $limit, $lang){
            $list_item = $objModel->get_data_export($conditions, $page, $limit, $lang);
            foreach ($list_item as $item) {
				$arr_item = $objModel->format_data_export($data, $item);
				
                $row = WriterEntityFactory::createRowFromArray($arr_item, $style);
				$writer->addRow($row);
			}

            if($limit <= count($list_item)){
                return $this->set_data_excel($writer, $style, $objModel, $data, $conditions, ($page+1), $limit, $lang);
            }else{
                return ($limit * ($page - 1)) + count($list_item);
            }
		}
	
        public function export_excel_small_data($readable_header, $data, $output_filename, $lang){
			$border = (new BorderBuilder())
				->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->build();

			$style_header = (new StyleBuilder())
				->setBorder($border)
				->setFontColor(Color::RED)
				->setShouldWrapText()
				->setFontSize(14)
				->setBackgroundColor(Color::YELLOW)
				->build();

			$style = (new StyleBuilder())
				->setBorder($border)
				->build();

			$writer = WriterEntityFactory::createWriter(Type::XLSX);
			$writer->setTempFolder('/tmp');
			$writer->setShouldUseInlineStrings(false);
			$type_file = '.xlsx';
			$file_path = $output_filename . $type_file;
			$writer->openToBrowser($file_path);

			$row = WriterEntityFactory::createRowFromArray($readable_header, $style_header);
			$writer->addRow($row);

			foreach ($data as $item) {
                $row = WriterEntityFactory::createRowFromArray($item, $style);
				$writer->addRow($row);
			}
			
			$writer->close();
            return count($data);
		}
		
		public function setup_export_excel_component($readable_header, $component, $data, $data_search, $max, $limit, $output_filename, $lang){
			$border = (new BorderBuilder())
				->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
				->build();

			$style_header = (new StyleBuilder())
				->setBorder($border)
				->setFontColor(Color::RED)
				->setShouldWrapText()
				->setFontSize(14)
				->setBackgroundColor(Color::YELLOW)
				->build();

			$style = (new StyleBuilder())
				->setBorder($border)
				->build();

			$writer = WriterEntityFactory::createWriter(Type::XLSX);
			$writer->setTempFolder('/tmp');
			$writer->setShouldUseInlineStrings(false);
			$type_file = '.xlsx';
			$file_path = $output_filename . $type_file;
			$writer->openToBrowser($file_path);

			$row = WriterEntityFactory::createRowFromArray($readable_header, $style_header);
			$writer->addRow($row);
			
			$collection = new ComponentCollection();
			$this->ComponentInit = $collection->load($component);

			$total = $this->set_data_excel_component($writer, $style, $data, $data_search, 1, $limit, $max, $lang);
			
			$writer->close();
            return $total;
        }

        private function set_data_excel_component($writer, $style, $data, $data_search, $page, $limit, $max, $lang){
			$list_item = $this->ComponentInit->get_data_export($data_search, $page, $limit, $lang);
            foreach ($list_item as $item) {
				$arr_item = $this->ComponentInit->format_data_export($data, $item);
				
                $row = WriterEntityFactory::createRowFromArray($arr_item, $style);
				$writer->addRow($row);
			}

            if($limit <= count($list_item) && (count($list_item) < $max || $max == 0)){
				if($max != 0){
					$max -= $limit;
				}
                return $this->set_data_excel_component($writer, $style, $data, $data_search, ($page++), $limit, $max, $lang);
            }else{
                return ($limit * ($page - 1)) + count($list_item);
            }
		}
    }
?>