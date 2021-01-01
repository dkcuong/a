<?php
	App::uses('Component', 'Controller');
	App::uses('Folder', 'Utility');
	App::uses('File', 'Utility');

	class CommonComponent extends Component {
		
		/**
		 * Components
		 *
		 * @var array
		 */
		public $components = array(
			// Enable ExcelReader in PhpExcel plugin
			'PhpExcel.ExcelReader',

			// Enable PhpExcel in PhpExcel plugin
			'PhpExcel.PhpExcel',

			'Session',

			'Redis'
		);


		public function initialize(Controller $controller) {
			parent::initialize($controller);

			if (!class_exists('PHPExcel')){
				// load vendor classes if does not load before
				App::import('Vendor', 'PhpExcel.PHPExcel');
			}
		}

		public function upload_images( $image, $relative_upload_folder, $image_name_suffix = "" ){
			$message = 'File is null';
			$params = array();
			if( isset($image) && !empty($image) ){
				$upload_folder = WWW_ROOT;
				$sub_folder = 'uploads';
				$static_path = 'img' . DS . $sub_folder;

				$upload_folder .= $static_path;
				if( isset($relative_upload_folder) && !empty($relative_upload_folder) ){
					$upload_folder .= DS . $relative_upload_folder;
				} else {
					$upload_folder .= 'img' . DS . 'trash';
				}

				$folder = new Folder($upload_folder, true, 0777);

				if( $folder ){
					try{
						$file = new File( $image['name'] );
						// rename the uploaded file
						$renamed_file = $image_name_suffix . '-' . date('YmdHis') . rand(1,5000) . '.' . $file->ext();
						// set the full path of uploaded file name
                        $renamed_file_full_path = $upload_folder . DS . $renamed_file;

                        list($width, $height, $type, $attr) = getimagesize( $image['tmp_name'] );

						move_uploaded_file($image['tmp_name'], $renamed_file_full_path);
						chmod($renamed_file_full_path, 0777);
						
						return array(
							'status' => true, 
							'params' => array(
								'ori_name' => $image['name'],
								're_name' => $renamed_file,
                                'path' => $sub_folder . DS . $relative_upload_folder . DS . $renamed_file,
                                'type' => $type,
                                'width' => $width,
                                'height' => $height,
                                'size' => $image['size']
							)
						);
					} catch(Exception $e) {
						$message = 'Upload file failed. ' . $e->getMessage();
						$params = array(
							're_name' => $renamed_file,
							'folder_path' => $upload_folder,
							'path' => $sub_folder . DS . $relative_upload_folder . DS . $renamed_file
						);
					}
				} else {
					$message = 'Fail to create folder.';
					$params = array(
						'folder_path' => $upload_folder,
					);
				}
			}

			return array(
				'status' => false, 
				'message' => $message,
				'params' => $params
			);
		}

		public function upload_file( $image, $relative_upload_folder, $image_name_suffix = "" ){
			$message = 'File is null';
			$params = array();
			if( isset($image) && !empty($image) ){
				//$upload_folder = WWW_ROOT . 'file/';
				$upload_folder = WWW_ROOT . 'file' . DS;

				if( isset($relative_upload_folder) && !empty($relative_upload_folder) ){
					$upload_folder .= $relative_upload_folder;
				} else {
					$upload_folder .= '';
				}

				$folder = new Folder($upload_folder, true, 0777);

				if( $folder ){
					try{
						$file = new File( $image['name'] );
						// rename the uploaded file
						$renamed_file = $image_name_suffix . '-' . date('YmdHis') . '.' . $file->ext();
						// set the full path of uploaded file name
						$renamed_file_full_path = $upload_folder . DS . $renamed_file;

						move_uploaded_file($image['tmp_name'], $renamed_file_full_path);
						chmod($renamed_file_full_path, 0777);
						
						return array(
							'status' => true, 
							'params' => array(
								'ori_name' => $image['name'],
								're_name' => $renamed_file,
								'path' => $relative_upload_folder . DS . $renamed_file
							)
						);
					} catch(Exception $e) {
						$message = 'Upload file failed. ' . $e->getMessage();
						$params = array(
							're_name' => $renamed_file,
							'folder_path' => $upload_folder,
							'path' => $relative_upload_folder . DS . $renamed_file
						);
					}
				} else {
					$message = 'Fail to create folder.';
					$params = array(
						'folder_path' => $upload_folder,
					);
				}
			}

			return array(
				'status' => false, 
				'message' => $message,
				'params' => $params
			);
		}

		public function slugify($text) {
			// replace non letter or digits by -
			$text = preg_replace('~[^\pL\d]+~u', '-', $text);
			// transliterate
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
			// remove unwanted characters
			$text = preg_replace('~[^-\w]+~', '', $text);
			// trim
			$text = trim($text, '-');
			// remove duplicate -
			$text = preg_replace('~-+~', '-', $text);
			// lowercase
			$text = strtolower($text);
			
			return $text;
		}

		public function get_available_language_list() {
			$available_lang = Environment::read('site.available_languages');

			$get_long_names = array_map(function($lang) {
				return __($lang.'_language');
			}, $available_lang);

			return array_combine($available_lang, $get_long_names);
		}
        
		public function generate_qrcode( $prefix, $member_number, $file_name){
			$upload_folder = WWW_ROOT . 'img/qrcode';

			$folder = new Folder($upload_folder, true, 0777);

			if( $folder ){
				$file_name = $prefix . '-' . $file_name . '.png';
				$file = $upload_folder . DS . $file_name;

				QRcode::png($member_number, $file, QR_ECLEVEL_L, 8);

				return array(
					'status' => true,
					'path' => 'qrcode' . DS . $file_name,
				);
			} else {
				return array(
					'status' => false, 
					'message' => 'Fail to create folder.',
				);
			}
		}

		function export_csv($data = array(), $headlist = array(), $fileName) {
		
			$fileName = $this->_encode($fileName);	// vilh, change to UTF-8

			header('Content-Encoding: UTF-8'); // vilh, change to UTF-8

			header("Content-type:   application/x-msexcel; charset=utf-8");  // vilh, change to UTF-8
			header('Content-Disposition: attachment; filename="'.$fileName.'.csv"');
			header('Cache-Control: max-age=0');
		  
			// for browser down
			$fp = fopen('php://output', 'a');
			
			fputs ($fp, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!

			//header
			foreach ($headlist as $key => $value) {
				$headlist[$key] = $this->_encode($value);	// vilh, change to UTF-8
			}
		
			fputcsv($fp, $headlist);
			
			//計數器
			$num = 0;
			
			$limit = 100000;
			
			$count = count($data);

			for ($i = 0; $i < $count; $i++) {
				$num++;

				if ($limit == $num) { 
					ob_flush();
					flush();
					$num = 0;
				}
				
				$row = $data[$i];
				
				foreach ($row as $key => $value) {
					$value = isset($value) ? $value : 'NULL';
					$row[$key] = $this->_encode($value); 
				}

				fputcsv($fp, $row);
			}
		}

		public function export_excel ($data, $output_filename, $readable_header, $file_path = '') {	
			/*
			if( empty($data) ){
                return array(
					'status' => false, 
					'message' => 'No data can be exported',
					'params' => array()
				);
            } 
			*/
			
            if( !isset($output_filename) || empty($output_filename) ){
                $output_filename = date('Ymd-Hi') . ".xls";
            }
            else
            {
                $output_filename = $output_filename . ".xls"; //".xlsx";	
            }

            try{
                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);

                $readable_field_keys = array_keys( $readable_header );

                $excel_readable_header  = array();

                foreach ($readable_field_keys as $key => $f_key) {
                    $excel_readable_header[ $key ]['label'] = $readable_header[ $f_key ]['label'];

                    if( isset($readable_header[ $f_key ]['width']) && !empty($readable_header[ $f_key ]['width']) ){
                        $excel_readable_header[ $key ]['width'] = $readable_header[ $f_key ]['width'];
                    }

                    if( isset($readable_header[ $f_key ]['filter']) && ($readable_header[ $f_key ]['filter'] == true) ){
                        $excel_readable_header[ $key ]['filter'] = $readable_header[ $f_key ]['filter'];
                    }
                }

                // add readable heading bold text
                //$this->PhpExcel->addTableHeader($excel_readable_header, array('name' => 'Roboto', 'bold' => true));

                $this->PhpExcel->addTableHeader($excel_readable_header, array('name' => 'Tahoma'));

                // $db_field_keys = array_keys( $readable_header );
                // foreach ($readable_header as $key => $db_f_key) {
                // 	$excel_db_header[ $key ]['label'] = $db_f_key['label'];
                // }

                // add heading for db fields
                // $this->PhpExcel->addTableHeader($excel_db_header);

                foreach ($data as $value) {
                    $_data = array();
                    foreach ($readable_field_keys as $key => $f_key) {
                        if( isset($value[$f_key]) ){

                            if( $f_key === "status" ){
                                $_data[$key] = (int) $value[$f_key];
                            } else  {
                                $_data[$key] = !empty($value[$f_key]) ? $value[$f_key] : ' ';
							}
                        }
                    }

                    $this->PhpExcel->addTableRow($_data);
                }

                // vilh (2019/04/06) 
                // Format Excel file
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();
                
                $fill_style = array(
                                    'fill' => array
                                    (
                                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'FFCC00')	// yellow	
                                    )
                                );

                $border_style = array(
                                    'borders' => array
                                    (
                                        'allborders' => array
                                        (
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('rgb' => '000000'),		// BLACK
                                        )
                                    )
                                );

                $column =  $this->getCellID(count($readable_header) - 1);
                $row = count($data) + 1;
                
                // format header
                $sheet->getStyle("A1:" . $column . "1")->applyFromArray($fill_style);
                $sheet->getStyle("A1:" . $column . "1")->getFont()->setBold(false)
                                                    ->setName('Verdana')
                                                    ->setSize(14)
                                                    ->getColor()->setRGB('FF0000');

                // format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);
                $this->PhpExcel->addTableFooter();
                // close table and output
                if($file_path){
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array( 
                            'status' => true, 
                            'message' => '', 
                            'params' => array() 
                        );
                }else{
                    $this->PhpExcel->output($output_filename);
                    
                    return array( 'status' => true, 'message' => '', 'params' => array() );	
                }
            } catch ( Exception $e ) {
                $export = array(
                    'status' => false, 
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
		}

        public function setup_export_csv($headlist, $model, $data, $conditions, $limit, $file_name, $lang){
            $file_name = $this->_encode($file_name);

			header('Content-Encoding: UTF-8'); 
			header("Content-type: application/x-msexcel; charset=utf-8");
			header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
            header('Cache-Control: max-age=0');
            // for browser down
			$fp = fopen('php://output', 'a');
			
            fputs ($fp, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
		
            fputcsv($fp, $headlist);
            $objModel = ClassRegistry::init($model);
            $num = 0;

            return $this->set_data_csv($objModel, $fp, $data, $conditions, 1, $limit, $num, $lang);
        }

        private function set_data_csv($objModel, $fp, $data, $conditions, $page, $limit, $num, $lang){
            $list_item = $objModel->get_data_export($conditions, $page, $limit, $lang);

            $limit_csv = 100000;
            $skip = $limit * ($page - 1);
            foreach ($list_item as $row) {
                $num++;
                $item = $objModel->format_data_export($data, $row);
                
                if ($limit_csv <= ($num + $skip)) { 
                    ob_flush();
                    flush();
                    $num = 0;
                }

                fputcsv($fp, $item);
            }

            if($limit <= count($list_item)){
                return $this->set_data_csv($objModel, $fp, $data, $conditions, ($page+1), $limit, $num, $lang);
            }else{
                return $skip + count($list_item);
            }
        }

		// Covert charater set to UTF-8
		protected function _encode($str = '') {
			return iconv("UTF-8","UTF-8//TRANSLIT", html_entity_decode($str, ENT_COMPAT, 'utf-8'));
		}

		public function force_logout_affected_user($ids) {
            $myid = $this->Session->id();
			foreach($ids as $id) {
                $objAdministratorsRole = ClassRegistry::init('Administration.AdministratorsRole');
                $user_arr = $objAdministratorsRole->get_user_by_role($id);
				foreach($user_arr as $user) {
                    $session_cache = $this->Redis->get_cache_global('booster_user'.$user.'_sessionid');
					if(!empty($session_cache)) {						
						session_write_close();

						session_id($session_cache);
						session_start();
						session_destroy();

						session_id($myid);
						session_start();
					}
				}
			}
        }

        public function force_logout_affected_user_by_user_ids($user_ids) {
            $myid = $this->Session->id();
            foreach($user_ids as $user) {
                $session_cache = $this->Redis->get_cache_global('booster_user'.$user.'_sessionid');
                if(!empty($session_cache)) {						
                    session_write_close();

                    session_id($session_cache);
                    session_start();
                    session_destroy();

                    session_id($myid);
                    session_start();
                }
            }
        }

        public function get_log_data_admin(){
            $agent_info = $this->get_agent_info();

            $current_user = $this->Session->read('Administrator.current');
    
            return array(
                'remote_ip' => $this->get_client_ip(),
                'agent' => $agent_info["userAgent"],
                'browser' => $agent_info["browser"],
                'version' => $agent_info["version"],
                'platform' => $agent_info["platform"],
            );
        }

        public function get_log_data_cronjob(){
            return array(
                'remote_ip' => null,
                'agent' => null,
                'browser' => null,
                'version' => null,
                'platform' => null,
            );
        }

		public function get_agent_info() {
			$u_agent = $_SERVER['HTTP_USER_AGENT'];
			$temp = strtolower($_SERVER['HTTP_USER_AGENT']);

			$bname    = 'Unknown';
			$platform = 'Unknown';
			$version  = "";

			// Get the platform
			if (preg_match('/linux/i', $temp)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $temp)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $temp)) {
				$platform = 'windows';
			}
        
            $ub = '';
			// Get the name of the useragent
			if(preg_match('/msie/i',$temp) && !preg_match('/opera/i',$temp)) {
				$bname = 'Internet Explorer';
				$ub = "msie";
			}
			elseif(preg_match('/firefox/i',$temp)) {
				$bname = 'Mozilla Firefox';
				$ub = "firefox";
			}
			elseif(preg_match('/chrome/i',$temp)) {
				$bname = 'Google Chrome';
				$ub = "chrome";
			}
			elseif(preg_match('/safari/i',$temp)) {
				$bname = 'Apple Safari';
				$ub = "safari";
			}
			elseif(preg_match('/opera/i',$temp)) {
				$bname = 'Opera';
				$ub = "opera";
			}
			elseif(preg_match('/netscape/i',$temp)) {
				$bname = 'Netscape';
				$ub = "netscape";
			}
		
			$known = array('version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			preg_match_all($pattern, $temp, $matches);

			$i = count($matches['browser']);
			if ($i != 1) {
				if (strripos($temp,"version") < strripos($temp,$ub)) {
					$version = $matches['version'][0];
				}
				else {
					$version = $matches['version'][1];
				}
			}
			else {
				$version = $matches['version'][0];
			}
		
			if ($version == null || $version == "") {
				$version = "?";
			}
		
			return array(
				'userAgent' 	=> $u_agent,
				'browser'      	=> $bname,
				'version'   	=> $version,
				'platform' 		=> $platform,
			);
        }
        
        public function get_client_ip() {
			$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');

			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');

			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');

			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');

			else if(getenv('HTTP_FORWARDED'))
				$ipaddress = getenv('HTTP_FORWARDED');

			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');

			else
				$ipaddress = 'UNKNOWN';
			
			return $ipaddress;
        }

        public function clone_object($from_object, $to_object){
            $not_allow_keys = ['id', 'created', 'created_by', 'updated', 'updated_by'];

            foreach($to_object as $key => $value){
                if(!in_array($key, $not_allow_keys)){
                    $from_object[$key] = $value;
                }
            }

            return $from_object;
        }

        public function get_list_month(){
            return  array(
                '1' => __('jan'),
                '2' => __('feb'),
                '3' => __('mar'),
                '4' => __('apr'),
                '5' => __('may'),
                '6' => __('jun'),
                '7' => __('jul'),
                '8' => __('aug'),
                '9' => __('sep'),
                '10' => __('oct'),
                '11' => __('nov'),
                '12' => __('dec'),
            );
        }

        public function get_day_of_week(){
            return array(
                0 => __('sun'),
                1 => __('mon'),
                2 => __('tue'),
                3 => __('wed'),
                4 => __('thu'),
                5 => __('fri'),
                6 => __('sat'),
            );
        }

        public function get_list_month_names($months){
            $result = array();
            $month_names = $this->get_list_month();
            foreach($months as $value){
                if(isset($month_names[$value])){
                    array_push($result, $month_names[$value]);
                }
            }
            return $result;
        }

        public function generate_code($prefix, $id){
            $length = 4;
            return $prefix . str_pad((string)$id, $length, 0, STR_PAD_LEFT);
		}
		
		public function generate_verification_code() {
			return (substr(rand(1000000,99999999),0,4));
		}

		public function phone_validation($country_code, $phone) {
			$valid = true;

			if (($country_code == '+852') && 
				((strlen($phone) != 8) || (!in_array($phone[0], array(5,6,7,8,9))))) {
				$valid = false;
			} else if (($country_code == '+853') && 
					   ((strlen($phone) != 8) || ($phone[0] != '6'))) {
				$valid = false;
			} else if (($country_code == '+86') && 
					  ((strlen($phone) != 11) || ($phone[0] != '1'))) {
				$valid = false;
			} else if (!in_array($country_code, array('+852', '+853', '+86', '+84'))) {
				$valid = false;
			}

			return $valid;
        }

		public function getCellID($num) {
			$arr = [
			    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
                'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM',
                'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
                'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM',
                'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
            ];
			return $arr[$num];
		}

		public function export_multiple_excel ($parm_data, $parm_readable_header) {
			$export = array(
				'status' => true, 'message' => '', 'params' => array()
			);
			
			if( empty($parm_data) ){
				$export = array(
					'status' => false, 
					'message' => 'No data can be exported',
					'params' => array()
				);
			} else {
				try{
					$filename_arr = array_keys( $parm_readable_header );
					foreach ($filename_arr as $file_index => $filename) {
						// create new empty worksheet and set default font
						$this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
						$this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);

						$sheet_arr = array_keys( $parm_readable_header[$filename] );
						foreach ($sheet_arr as $sheet_index => $sheet_name) {

							if ($sheet_index > 0) {
								$this->PhpExcel->createSheet();
							}

							$this->PhpExcel->setActiveSheetIndex($sheet_index);
							$this->PhpExcel->getActiveSheet()->setTitle($sheet_name);
							$this->PhpExcel->setRow(1);

							$readable_header = $parm_readable_header[$filename][$sheet_name];
							$readable_field_keys = array_keys( $readable_header );

							$excel_readable_header  = array();

							foreach ($readable_field_keys as $key => $f_key) {
								$excel_readable_header[ $key ]['label'] = $readable_header[ $f_key ]['label'];
	
								if( isset($readable_header[ $f_key ]['width']) && !empty($readable_header[ $f_key ]['width']) ){
									$excel_readable_header[ $key ]['width'] = $readable_header[ $f_key ]['width'];
								}
	
								if( isset($readable_header[ $f_key ]['filter']) && ($readable_header[ $f_key ]['filter'] == true) ){
									$excel_readable_header[ $key ]['filter'] = $readable_header[ $f_key ]['filter'];
								}
							}

							$this->PhpExcel->addTableHeader($excel_readable_header, array('name' => 'Tahoma'));

							$data = $parm_data[$filename][$sheet_name];
							foreach ($data as $value) {
								$_data = array();
	
								foreach ($readable_field_keys as $key => $f_key) {
									if( isset($value[$f_key]) ){
										if( $f_key === "status" ){
											$_data[$key] = (int) $value[$f_key];
										} else  {
											$_data[$key] = !empty($value[$f_key]) ? $value[$f_key] : ' ';
										}
	
									}
								}

								$this->PhpExcel->addTableRow($_data);
							}

							$sheet = $this->PhpExcel->getActiveSheet();
							$fill_style = array(
								'fill' => array
								(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'FFCC00')	// yellow	
								)
							);

							$border_style = array(
												'borders' => array
												(
													'allborders' => array
													(
														'style' => PHPExcel_Style_Border::BORDER_THIN,
														'color' => array('rgb' => '000000'),		// BLACK
													)
												)
											);

							$column =  $this->getCellID(count($readable_header) - 1);
							$row = count($data) + 1;

							// format header
							$sheet->getStyle("A1:" . $column . "1")->applyFromArray($fill_style);
							$sheet->getStyle("A1:" . $column . "1")->getFont()->setBold(false)
																->setName('Verdana')
																->setSize(14)
																->getColor()->setRGB('FF0000');

							// format header + data
							$sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);

							// close table
							$this->PhpExcel->addTableFooter();

						} //end for each sheet

						$this->PhpExcel->addTableFooter()->output($filename.'.xls');
					} // end for each file


				} catch ( Exception $e ) {
					$export = array(
						'status' => false, 
						'message' => 'Fail to export the Excel file. Please try again.',
						'params' => array()
					);

					return $export;
				}
			}

			return $export;	
		}	

		public function get_filter_conditions($data_search, $model, $languages_model, $filter) {

			$filter_language_fields = ['name', 'title'];

			$conditions = array();
			foreach($data_search as $key => $value) {		
				if($key != 'button' && trim($value) != ''){
					if (in_array($key, $filter)) {
						if (in_array($key, $filter_language_fields)) {
							$conditions[] = $languages_model.".".$key." like '%".$value."%' ";
						} else {
							$conditions[] = $model.".".$key." like '%".$value."%' ";
						}
					} else {
						$conditions[] = $model.".".$key." = '".$value."'";
					}
				}
			}

			// pr($conditions);
			// exit;

			return $conditions;
		}

		/**
		 * General function for single table/model excel import
		 * 
		 */
		public function upload_and_read_excel($data, $upload_folder) {
			$import = array(
				'status' => false, 'message' => '', 'data' => array()
			);

			if( !isset($upload_folder) || empty($upload_folder) ){
				$upload_folder = WWW_ROOT . 'img' . DS . 'uploads' . DS . 'import';
			}

			$folder = new Folder($upload_folder, true, 0777);
			if( $folder ){
				$file = new File( $data['file']['name'] );

				// rename the uploaded file
				$renamed_file = date('Ymd-Hi') . '.' . $file->ext();

				// set the full path of uploaded file name
				$renamed_file_full_path = $upload_folder . DS . $renamed_file;
				if( move_uploaded_file($data['file']['tmp_name'], $renamed_file_full_path) ){
					try {
						// read the uploaded file through ExcelReader Component
						//$this->ExcelReader->loadExcelFile( $renamed_file_full_path );
						$objPHPExcel = $this->ExcelReader->loadExcelFileMultipleSheet( $renamed_file_full_path );
					} catch (Exception $e) {

						$import = array(
							'status' => false, 
							'message' => 'Fail to read the uploaded file. Please check and try again.',
							'params' => array(
								'File name' => $data['file']['name'],
							)
						);

						return $import;
					}

					// if read successfully, get the result array
					$rows_data = $this->ExcelReader->dataArray;

					$rows = array();

					/**
					 * get the db header
					 * 
					 * row 0 - readable text header
					 */

					/*
					foreach ($rows_data as $sheet_name => $sheet_data) {

						$header = $sheet_data[0];
						foreach ($sheet_data as $row => $data) {
							if( $row >= 1 ){ // skip the headers
								// re-format the resultant array
								foreach ($data as $d_key => $d_field) {
									$rows[$sheet_name][$row][$header[$d_key]] = $d_field;
								}
							}
						}

					}
					*/
					



					$import = array(
						'status' => true, 
						'message' => 'Uploading and Reading Completed Successfully.',
						'data' => $rows_data
					);

				} else {
					$import = array(
						'status' => false, 
						'message' => 'Fail to upload file.',
						'data' => array(
							'File name' => $data['file']['name'],
						)
					);
				}
			} else {
				$import = array(
					'status' => false, 
					'message' => 'Fail to create folder.',
					'data' => array(
						'Folder Path' => $upload_folder,
					)
				);
			}
			

			//pr($import);
			//exit;

			return $import;
		}

		public function check_rules($rules, $spec, $header, $row, $line, $title = ''){
			$valid = true;
			$message = array();
			$data = array();
			foreach($rules as $key => $value) {
				foreach($value as $rule) {
					switch($rule) {
						case 'required' : 
							if (trim($row[$key]) == '' || empty($row[$key])) {
								$valid = false;
								array_push($message, sprintf(__('value_is_required'), $line, $header[$key]));
							}
							break;
						case 'number' : 
							if (!(trim($row[$key]) == '' || is_numeric($row[$key]))) {
								$valid = false;
								array_push($message, sprintf(__('id_must_be_numeric'), $line, $header[$key]));
							}
							break;
						case 'enum' : 
							if (!in_array($row[$key], $spec[$key])) {
								$valid = false;
								array_push($message, sprintf(__('value_is_not_valid'), $line, $header[$key]));
							}
							break;
						case 'multiline_enum' : 
							$tmp_value = explode(',', $row[$key]);
							foreach($tmp_value as $value) {
								if (($valid) && (!in_array(trim($value), $spec[$key]))) {
									$valid = false;
									array_push($message, sprintf(__('value_is_not_valid'), $line, $header[$key]));
								}
							}
							break;
						case 'active' : 
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {

								$objModel = ClassRegistry::init($spec[$key]);
								$model = $objModel->alias;

								$model_use_non_id = array('School');
								$field = (in_array($model, $model_use_non_id)) ? 'school_number' : (($model == 'ChildrenSchoolYear') ? 'invite_code' : 'id') ;

								$options = array(
									'conditions' => array(
										$field => $row[$key],
										'enabled' => 1
									),
									'recursive' => -1
								);

								$value = $objModel->find('first', $options);

								if (!isset($value[$model]) || empty($value[$model])) {
									$valid = false;
									array_push($message, sprintf(__('record_is_not_active'), $line, $header[$key]));
								} else {
									$data = array_merge($data, array($model => $value[$model]['id']));
								}
							}
							break;
						case 'valid_date' :
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {
								$date = $row[$key];
								$date_parts = explode( '-', substr($date,0,10));

								if(!is_numeric($date_parts[0]) || !is_numeric($date_parts[1]) || !is_numeric($date_parts[2]) ||
									!checkdate( $date_parts[1], $date_parts[2], $date_parts[0] )){
									$valid = false;
									array_push($message, sprintf(__('date_is_not_valid'), $line, $header[$key]));
								}
							}
							break;
						case 'valid_month' :
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {
								if (!is_numeric($row[$key]) || !($row[$key] >= 1 && $row[$key] <= 12)) {
									$valid = false;
									array_push($message, sprintf(__('month_is_not_valid'), $line, $header[$key]));
								}
							}
							break;
						case 'valid_time' :
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {
								$time = explode(":",$row[$key]);
								if (!is_numeric($time[0]) || !($time[0] >= 0 && $time[0] <= 23) ||
									!is_numeric($time[1]) || !($time[1] >= 0 && $time[1] <= 59)) {
									$valid = false;
									array_push($message, sprintf(__('time_is_not_valid'), $line, $header[$key]));
								}
							}
							break;
						case 'valid_day' :
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {
								$month = $row[$key-1];
								if (!empty($row[$month]) && trim($row[$month]) != '') { 
									$date = $row[$key];
									if(!is_numeric($row[$key]) || !checkdate( $month, $date, '2016' )){
										$valid = false;
										array_push($message, sprintf(__('date_is_not_valid'), $line, $header[$key]));
									}
								} else {
									$valid = false;
									array_push($message, sprintf(__('month_is_not_valid'), $line, $header[$key-1]));
								}
							}
							break;
						case 'custom' : 
							if (!(empty($row[$key]) || trim($row[$key]) == '')) {
								if ($title == 'Route' && $key == 0) {
									//check if Route has RouteSchedule
									$route_id = $row[$key];
									$objModel = ClassRegistry::init('Route.RouteSchedule');
									$options = array(
										'conditions' => array(
											'route_id' => $route_id
										),
										'recursive' => -1
									);
									$result_count = $objModel->find('count', $options);

									if ($result_count > 0) {
										$valid = false;
										array_push($message, sprintf(__('data_has_been_used_error'), $line, $header[$key], 'Route'));
									}
								} else if ($title == 'ChildrenSchoolYearRoute' && $key == 2) {
									//check if children use the same route twice
									$objModel = ClassRegistry::init('Route.ChildrenSchoolYear');

									$id = 0;
									if ($row[0] > 0) {
										$id = $row[0];
									}
									$invitation_code = $row[1];
									$route_id = $row[$key];
									$options = array(
										'field' => array('ChildrenSchoolYearRoute.id'),
										'joins' => array(
											array(
												'alias' => 'ChildrenSchoolYearRoute',
												'table' => Environment::read('table_prefix') . 'children_school_year_routes',
												'type' => 'left',
												'conditions' => array(
													'ChildrenSchoolYearRoute.children_school_year_id = ChildrenSchoolYear.id' 
												),
											),
										),
										'conditions' => array(
											'invite_code' => $invitation_code,
											'enabled' => 1,
											'route_id' => $route_id,
											'ChildrenSchoolYearRoute.id !=' => $id
										),
										'recursive' => -1
									);
									$result_count = $objModel->find('count', $options);

									if ($result_count > 0) {
										$valid = false;
										array_push($message, sprintf(__('route_is_duplicate'), $line, $header[$key]));
									}
								} else if ($title == 'ChildrenSchoolYearRoute' && $key == 4) {
									//check if the route have this station
									$objModel = ClassRegistry::init('Route.RouteStation');
									$route_id = $row[2];
									$station_id = $row[3];
									$dropoff_station_id = $row[4];

									if ($station_id == $dropoff_station_id) {
										$valid = false;
										array_push($message, sprintf(__('station_pickup_and_dropoff_same'), $line, $header[$key]));
									}

									$station_order = 0;
									$station_dropoff_order = 0;
									if ($valid) {
										$options = array(
											'conditions' => array(
												'route_id' => $route_id,
												'enabled' => 1,
												'station_id' => $station_id
											),
											'recursive' => -1
										);
										$result_station = $objModel->find('first', $options);

										if (isset($result_station['RouteStation']) && !empty($result_station['RouteStation'])) {
											$station_order = $result_station['RouteStation']['sorting'];
										} else {
											$valid = false;
											array_push($message, sprintf(__('route_is_not_going_to_this_station'), $line, 'Station'));
										} 
									}

									if ($valid) {
										$options = array(
											'conditions' => array(
												'route_id' => $route_id,
												'enabled' => 1,
												'station_id' => $dropoff_station_id
											),
											'recursive' => -1
										);
										$result_station_dropoff = $objModel->find('first', $options);

										if (isset($result_station_dropoff['RouteStation']) && !empty($result_station_dropoff['RouteStation'])) {
											$station_dropoff_order = $result_station_dropoff['RouteStation']['sorting'];
										} else {
											$valid = false;
											array_push($message, sprintf(__('route_is_not_going_to_this_station'), $line, $header[$key]));
										}

										if ($valid && $station_dropoff_order <= $station_order) {
											$valid = false;
											array_push($message, sprintf(__('wrong_period_station_error'), $line, $header[$key]));
										}
									}

								} 
							} else if ($title == 'Holiday' && $key == 10) {
								$is_forever = (in_array($row[9], array('Y', 'y'))) ? true: false;
								if (!$is_forever && (trim($row[$key]) == '' || empty($row[$key]))) {
									$valid = false;
									array_push($message, sprintf(__('value_is_required'), $line, $header[$key]));
								} 
							} else if ($title == 'Holiday' && $key == 12) {
								$is_forever = (in_array($row[9], array('Y', 'y'))) ? true: false;
								if ($is_forever && (trim($row[$key]) == '' || empty($row[$key]))) {
									$valid = false;
									array_push($message, sprintf(__('value_is_required'), $line, $header[$key]));
								} 
							} else if ($title == 'Holiday' && $key == 13) {
								$is_forever = (in_array($row[9], array('Y', 'y'))) ? true: false;
								if ($is_forever && (trim($row[$key]) == '' || empty($row[$key]))) {
									$valid = false;
									array_push($message, sprintf(__('value_is_required'), $line, $header[$key]));
								} 
							}
							break;
					}
				}
			}

			return array('status' => $valid, 'message' => $message, 'data' => $data);
		}

		public function generate_random_pass() {
			return (substr(md5(microtime()),rand(0,26),5));
		}		

		public function remove_uploaded_image( $images_model, $images_plugin = '',$image_ids = array()){
			$params = array();
			
			$result = array(
				'status' => false,
				'params' => $params
			);
			
			$removed = array();
			
			$conditions = array(
				'id' => $image_ids
			);
			
			$this->images_model = ClassRegistry::init("$images_plugin.$images_model");
			
			$images = $this->images_model->find('all', array(
				'fields' => array(
				$images_model.'.id', $images_model.'.video_path',
                $images_model.'.id', $images_model.'.poster_path'
				),
				'conditions' => $conditions,
				'recursive' => -1
			));

			if( $images ){
				foreach ($images as $key => $image) {
					if( $this->images_model->delete($image[$images_model]['id']) ){
						$file = new File( 'img/'.$image[$images_model]['video_path'] );
						
						$file->delete();

                        $file = new File( 'img/'.$image[$images_model]['poster_path'] );

                        $file->delete();

                        $removed[] = $image[$images_model]['id'];
						
						$params['removed'][] = $image[$images_model];
					}
				}
			}
			
			if( !empty($removed) ){
				$result = array(
					'status' => true,
					'params' => $params
				);
			}
			
			return $result;
		}

		public function get_checksum($data) {

			$array_element = array();
			$array_element[] = $data['inv_number'];
			$array_element[] = $data['status'];
			$array_element[] = $data['date'];
			$array_element[] = Environment::read('Security.salt');

			$array_string = implode(':', $array_element);
			
			return md5($array_string);
		}

		public function get_qrcode_value($data, $type) {
			$checksum = $this->get_checksum($data);
			$qrcode_array = array();
			$qrcode_array['id'] = $data['id'];
			$qrcode_array['type'] = $type;
			$qrcode_array['status'] = $data['status'];	
			$qrcode_array['checksum'] = $checksum;

			$qrcode_json = json_encode($qrcode_array);
			$enc_json = base64_encode($qrcode_json);

			return $enc_json;
		}

		public function decode_qrcode_value($str) {
			$status = false;
			$message = "";
			$params = (object)array();

			$qrcode_json = base64_decode($str);
			$qrcode_array = json_decode($qrcode_json);

			$result = array();

			if (isset($qrcode_array->id) && !empty($qrcode_array->id)) {
				$type = $qrcode_array->type;
				$model = ($type == 'order') ? 'Order' : 'Purchase';
				$objModel = ClassRegistry::init('Pos.'.$model);

				$option = array(
					'conditions' => array(
                        'id' => $qrcode_array->id,
                        'is_pos' => 0,
						'void' => 0
					)
				);

				$data_trans = $objModel->find('first', $option);

				if (isset($data_trans[$model]) && !empty($data_trans[$model])) {

					$checksum = $this->get_checksum($data_trans[$model]);
					if ($checksum != $qrcode_array->checksum) {	
						$message = __('qrcode_invalid');
						goto return_result;
					} else if (!in_array($data_trans[$model]['status'], array(2,3))) {
						$message = __('trans_status_invalid');
						goto return_result;
					} else {
						$status = true;
						$data_trans[$model]['type'] = $type;
						$params = $data_trans[$model];
						$message = __('trans_found');
					}

				} else {	
					$message = __('decoding_qrcode_failed');
					goto return_result;
				}

			} else {
				$message = __('decoding_qrcode_failed');
				goto return_result;
			}
			return_result :

			return array('status' => $status, 'message' => $message, 'params' => $params);
		}

		public function curl_post($url, $body){
			$curl = curl_init();
			$base_api_url = Environment::read('base_api_url');
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $base_api_url.$url,
			    CURLOPT_POST => 1,
			    CURLOPT_SSL_VERIFYPEER => false, //Bỏ kiểm SSL
			    CURLOPT_POSTFIELDS => $body
			));
			$resp = curl_exec($curl);
			curl_close($curl);
			return $resp;
		}

        public function export_excel_report_1 ($now, $data_result, $output_filename, $readable_header, $file_path = '') {

            try {
                $data = $data_result['result_format'];
                $data_total = $data_result['result_format_total'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $list_row = array();
                // add heading for db fields
                $excel_db_header[]['label'] = 'Report';
                $this->PhpExcel->addTableHeader($excel_db_header);
                $list_row[] =array();

                $sheet->mergeCells("A2:B3");


                //$now = '2020-11-30';
                $begining_of_month = date("Y-m-01", strtotime($now));

                // Get list day

                $list_day = array();

                // Use strtotime function
                $Variable1 = strtotime($begining_of_month);
                $Variable2 = strtotime($now);

                // Use for loop to store dates into array
                // 86400 sec = 24 hrs = 60*60*24 = 1 day
                for ($currentDate = $Variable2; $currentDate >= $Variable1;
                     $currentDate -= (86400)) {

                    $Store = date('Y-m-d', $currentDate);
                    $list_day[] = $Store;
                }

                foreach ($data as $k => $v) {
                    $list_movie[$v['key_movie']]['key_movie'] = $v['key_movie'];
                    $list_movie[$v['key_movie']]['movie_name'] = $v['movie_name'];
                }

                array_multisort(array_column($list_movie, "movie_name"), SORT_ASC, $list_movie);


                //$list_movie = array_unique(array_column($data, 'movie_name'));
                //sort($list_movie);

                $list_hall_by_movie = array();
                foreach ($data as $k => $v) {
                    $list_hall_by_movie[$v['key_movie']][$v['hall']['id']] = $v['hall']['code'];
                }
                foreach ($list_hall_by_movie as $k => $v) {
                    asort($list_hall_by_movie[$k]);
                    $list_hall_by_movie[$k][] = '';
                }


                // Get list movie

                $flag_merge = array();

                // Row Transacation Text
                $temp_row = array();
                $temp_row[] = '';
                $temp_row[] = '';

                $temp_row[] = 'Transaction Date';
                foreach ($list_day as $k => $v) {
                    $temp_row[] = "";
                    $temp_row[] = "";
                }
                $temp_row[] = "";
                $temp_row[] = "";
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                // Row list day
                $temp_row = array('', '');
                $count_flag = 2;
                foreach ($list_day as $k => $v) {
                    $temp = array($v, '');
                    $temp_row = array_merge($temp_row, $temp);

                    $flag_merge[count($list_row)+1][] = array('from' => $count_flag, 'next' => 1);
                    $count_flag += 2;
                }
                $temp_row[] = "Sum of Ticket Total";
                $temp_row[] = "Sum of Net Sales";
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                foreach ($flag_merge as $k => $v) {
                    foreach ($v as $i => $j) {
                        $column_from = $this->getCellID($j['from']);
                        $column_to = $this->getCellID($j['from'] + $j['next']);


                        $sheet->mergeCells($column_from . $k . ':' . $column_to . $k);

                        $style = array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            )
                        );
                        $sheet->getStyle($column_from . $k . ':' . $column_to . $k)->applyFromArray($style);

                    }

                }
                $temp_index = count($list_row);
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    )
                );
                $column_from = $this->getCellID(count($temp_row)-1);
                $sheet->mergeCells($column_from . $temp_index . ':' . $column_from . ($temp_index+1));
                $sheet->getStyle($column_from . $temp_index . ':' . $column_from . ($temp_index+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                $column_from = $this->getCellID(count($temp_row)-2);
                $sheet->mergeCells($column_from . $temp_index . ':' . $column_from . ($temp_index+1));
                $sheet->getStyle($column_from . $temp_index . ':' . $column_from . ($temp_index+1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);



                // Row Title
                $temp_row = array();
                $temp_row[] = 'Movie Name';
                $temp_row[] = 'House';

                foreach ($list_day as $k => $v) {
                    $temp_row[] = "Sum of Ticket Sold";
                    $temp_row[] = "Sum of Paid Amount";
                }


                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                $list_row_movie = array();

                $index_movie = count($list_row);
                foreach ($list_movie as $kMovie => $vMovie) {
                    $flag_merge_list_movie[$kMovie]['from'] = $index_movie;
                    $from_movie = $index_movie + 1;
                    $temp_row_movie = [$vMovie['movie_name']];
                    foreach ($list_hall_by_movie[$kMovie] as $kHall => $vHall) {
                        if (empty($vHall)) {
                            $temp_row_movie = [$vMovie['movie_name'] . " Total"];
                        } else {
                            $temp_row_movie = [$vMovie['movie_name']];
                        }
                        $temp_row_hall = [$vHall];
                        $temp_row_movie = array_merge($temp_row_movie, $temp_row_hall);
                        $total_sales_by_day = 0;
                        $total_ticket_by_day = 0;

                        foreach ($list_day as $kDay => $vDay) {
                            // $data[]
                            if (!empty($vHall)) {
                                $key = $vDay . '_' . $kMovie . '_' . $kHall;
                                if (isset($data[$key])) {
                                    $temp_row_movie = array_merge($temp_row_movie, [$data[$key]['total_ticket']]);
                                    $temp_row_movie = array_merge($temp_row_movie, [$data[$key]['total_sale']]);

                                    $total_ticket_by_day += $data[$key]['total_ticket'];
                                    $total_sales_by_day += $data[$key]['total_sale'];

                                } else {
                                    $temp_row_movie[] = '';
                                    $temp_row_movie[] = '';
                                }
                            } else {
                                $key = $vDay . '_' . $kMovie;
                                if (isset($data_total[$key])) {
                                    $temp_row_movie = array_merge($temp_row_movie, [$data_total[$key]['total_ticket']]);
                                    $temp_row_movie = array_merge($temp_row_movie, [$data_total[$key]['total_sale']]);

                                    $total_ticket_by_day += $data_total[$key]['total_ticket'];
                                    $total_sales_by_day += $data_total[$key]['total_sale'];

                                } else {
                                    $temp_row_movie[] = '';
                                    $temp_row_movie[] = '';
                                }
                            }

                        }
                        $temp_row_movie[] = $total_ticket_by_day;
                        $temp_row_movie[] = $total_sales_by_day;

                        $list_row_movie[] = $temp_row_movie;
                        $index_movie++;
                    }
                    $to_movie = $index_movie;
                    $sheet->mergeCells('A' . $from_movie . ':A' . ($to_movie-1));
                    $sheet->getStyle('A' . $from_movie . ':A' . ($to_movie-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    $sheet->mergeCells('A' . $index_movie . ':B' . $index_movie);
                }

                $list_row = array_merge($list_row, $list_row_movie);

                foreach ($list_row_movie as $k => $v) {
                    $this->PhpExcel->addTableRow($v);
                }

                // final row
                $final_row = array();
                $final_row[] = 'Grand Total';
                $final_row[] = '';

                $total_ticket = $total_sale = 0;
                foreach ($list_day as $kDay => $vDay) {
                    $key = $vDay;

                    if (isset($data_total_final[$key])) {
                        $final_row = array_merge($final_row, [$data_total_final[$key]['total_ticket']]);
                        $final_row = array_merge($final_row, [$data_total_final[$key]['total_sale']]);

                        $total_ticket += $data_total_final[$key]['total_ticket'];
                        $total_sale += $data_total_final[$key]['total_sale'];
                    } else {
                        $final_row[] = '';
                        $final_row[] = '';
                    }
                }
                $final_row[] = $total_ticket;
                $final_row[] = $total_sale;
                $this->PhpExcel->addTableRow($final_row);


                $list_row[] = $final_row;
                $temp_index = count($list_row);
                $sheet->mergeCells('A' . $temp_index . ':B' . $temp_index);

                if (!isset($output_filename) || empty($output_filename)) {
                    $output_filename = date('Ymd-Hi') . ".xls";
                } else {
                    $output_filename = $output_filename . ".xls"; //".xlsx";
                }



                $fill_style = array(
                    'fill' => array
                    (
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFCC00')    // yellow
                    )
                );

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                $column = $this->getCellID(count($list_row[2])-1);
                $row = count($list_row);

                //format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);



                $this->PhpExcel->addTableFooter();


                // close table and output
                if ($file_path) {
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array(
                        'status' => true,
                        'message' => '',
                        'params' => array()
                    );
                } else {
                    $this->PhpExcel->output($output_filename);

                    return array('status' => true, 'message' => '', 'params' => array());
                }
            } catch (Exception $e) {
                $export = array(
                    'status' => false,
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
        }

        public function export_excel_report_2 ($now, $data_result, $output_filename, $readable_header, $file_path = '')
        {

            try {
                $data = $data_result['result_format'];
                $data_total = $data_result['result_format_total'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                // Get list movie
                $list_row = array();

                $excel_db_header[]['label'] = 'Report';

                // add heading for db fields
                $this->PhpExcel->addTableHeader($excel_db_header);
                $list_row[] = array();

                // Row show date
                $temp_row = array();
                $temp_row[] = 'Show Date';
                $temp_row[] = $now;
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                // Row blank
                $temp_row = array('');
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Data
                $temp_row = array('', '', '', 'Data');
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Title
                $temp_row = array();
                $temp_row[] = 'Movie Name';
                $temp_row[] = 'House';
                $temp_row[] = 'Show Time';
                $temp_row[] = 'Staff';
                $temp_row[] = 'Payment Method';
                $temp_row[] = 'Sum of Total Amount';
                $temp_row[] = 'Sum of Ticket Total';
                $temp_row[] = 'Correct Amount';
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                $list_row_movie = array();
                $index_movie = count($list_row);
                $count = 0;
                foreach ($data as $k => $v) {
                    $from_movie = $index_movie + 1;

                    $flag_new_movie = 1;
                    foreach ($v['list_hall'] as $kHall => $vHall) {
                        $flag_new_hall = 1;
                        foreach ($vHall['list_time'] as $kTime => $vTime) {
                            $flag_new_time = 1;

                            foreach ($vTime['list_staff'] as $kStaff => $vStaff) {
                                $flag_new_staff = 1;
                                foreach ($vStaff['list_combination_method'] as $kCombinationMethod => $vCombinationMethod) {

                                    $temp_row = array();
                                    if ($flag_new_movie == 1) {
                                        $temp_row[] = $v['movie_name'];
                                        $flag_new_movie = 0;
                                    } else {
                                        $temp_row[] = '';
                                    }

                                    if ($flag_new_hall == 1) {
                                        $temp_row[] = $vHall['hall_code'];
                                        $from_hall = $index_movie + 1;
                                        $flag_new_hall = 0;
                                    } else {
                                        $temp_row[] = '';
                                    }

                                    if ($flag_new_time == 1) {
                                        $temp_row[] = $vTime['time_display'];
                                        $from_time = $index_movie + 1;
                                        $flag_new_time = 0;
                                    } else {
                                        $temp_row[] = '';
                                    }

                                    if ($flag_new_staff == 1) {
                                        $temp_row[] = $vStaff['staff_name'];
                                        $from_staff = $index_movie + 1;
                                        $flag_new_staff = 0;
                                    } else {
                                        $temp_row[] = '';
                                    }
                                    $temp_row[] = $vCombinationMethod['combination_payment_method_display'];
                                    $temp_row[] = $vCombinationMethod['total_sale'];
                                    $temp_row[] = $vCombinationMethod['total_ticket'];
                                    $temp_row[] = $vCombinationMethod['total_sale_custom'];

                                    $list_row_movie[] = $temp_row;
                                    $index_movie++;
                                }
                                $to_movie = $index_movie;
                                $sheet->mergeCells('D' . $from_staff . ':D' . $to_movie);
                                $sheet->getStyle('D' . $from_staff . ':D' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            }
                            $to_movie = $index_movie;
                            $sheet->mergeCells('C' . $from_time . ':C' . $to_movie);
                            $sheet->getStyle('C' . $from_time . ':C' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        }
                        $to_movie = $index_movie;
                        $sheet->mergeCells('B' . $from_hall . ':B' . $to_movie);
                        $sheet->getStyle('B' . $from_hall . ':B' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    }
                    $to_movie = $index_movie;
                    $sheet->mergeCells('A' . $from_movie . ':A' . $to_movie);
                    $sheet->getStyle('A' . $from_movie . ':A' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    // add final per each movie
                    $temp_row = array();
                    $temp_row[] = $v['movie_name'] . ' Total';
                    $temp_row[] = '';
                    $temp_row[] = '';
                    $temp_row[] = '';
                    $temp_row[] = '';
                    $temp_row[] = $data_total[$k]['total_sale'];
                    $temp_row[] = $data_total[$k]['total_ticket'];
                    $temp_row[] = $data_total[$k]['total_sale_custom'];


                    $list_row_movie[] = $temp_row;
                    $index_movie++;
                    $sheet->mergeCells('A' . ($index_movie) . ':E' . ($index_movie));
                }

                    $list_row = array_merge($list_row, $list_row_movie);



                    foreach ($list_row_movie as $k => $v) {
                        $this->PhpExcel->addTableRow($v);
                    }

                    // add final row
                    $temp_row = array('Total Amount', '', '' ,'', '');
                    $temp_row[] = $data_total_final['total_sale'];
                    $temp_row[] = $data_total_final['total_ticket'];
                    $temp_row[] = $data_total_final['total_sale_custom'];
                    $list_row[] = $temp_row;
                    $this->PhpExcel->addTableRow($temp_row);
                    $temp_index = count($list_row);
                    $sheet->mergeCells('A' . $temp_index . ':E' . $temp_index);


                    if (!isset($output_filename) || empty($output_filename)) {
                        $output_filename = date('Ymd-Hi') . ".xls";
                    } else {
                        $output_filename = $output_filename . ".xls"; //".xlsx";
                    }


                    $column = $this->getCellID(count($list_row[4]) - 1);
                    $row = count($list_row);

                    //format header + data
                    $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);
                    $sheet->getStyle("A1:" . $column . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    $this->PhpExcel->addTableFooter();

                    foreach ($list_row as $k => $v) {
                        //$this->PhpExcel->addTableRow($v);
                    }

                    // close table and output
                    if ($file_path) {
                        $this->PhpExcel->save(WWW_ROOT . $file_path);
                        return array(
                            'status' => true,
                            'message' => '',
                            'params' => array()
                        );
                    } else {
                        $this->PhpExcel->output($output_filename);

                        return array('status' => true, 'message' => '', 'params' => array());
                    }
            }
            catch (Exception $e) {
                    $export = array(
                        'status' => false,
                        'message' => 'Fail to export the Excel file. Please try again.',
                        'params' => array()
                    );

                    return $export;
                    }
        }

        public function export_excel_report_3 ($now, $data_result, $output_filename, $readable_header, $file_path = '') {
            try {
                $data = $data_result['result_format'];
                $data_total = $data_result['result_format_total'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                // Get list movie
                $list_row = array();


                $excel_db_header[]['label'] = 'Report';
                // add heading for db fields
                $this->PhpExcel->addTableHeader($excel_db_header);


                // Row show date
                $temp_row = array();
                $temp_row[] = 'Show Date';
                $temp_row[] = $now;

                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                // Row blank
                $temp_row = array('');

                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Data
                $temp_row = array('', '', '', 'Data');

                $list_row[] = $temp_row;
                // Row Title
                $temp_row = array();
                $temp_row[] = 'Movie Name';
                $temp_row[] = 'House';
                $temp_row[] = 'Sum of Ticket Sold';
                $temp_row[] = 'Sum of Paid Amound';


                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                $list_row_movie = array();
                $index_movie = count($list_row);

                foreach ($data as $k => $v) {
                    $from_movie = $index_movie+1;

                    $flag_new_movie = 1;

                    foreach ($v['list_hall'] as $kHall => $vHall) {
                        $temp_row = array();
                        if ($flag_new_movie == 1) {
                            $temp_row[] = $v['movie_name'];
                            $flag_new_movie = 0;
                        } else {
                            $temp_row[] = '';
                        }

                        $temp_row[] = $vHall['hall_code'];
                        $temp_row[] = $vHall['total_ticket'];
                        $temp_row[] = $vHall['total_sale'];
                        $list_row_movie[] = $temp_row;
                        $index_movie++;
                    }
                    $to_movie = $index_movie;
                    $sheet->mergeCells('A' . $from_movie . ':A' . $to_movie);
                    $sheet->getStyle('A' . $from_movie . ':A' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    // add final per each movie
                    $temp_row = array();
                    $temp_row[] = $v['movie_name'] . ' Total';
                    $temp_row[] = '';
                    $temp_row[] = $data_total[$k]['total_ticket'];
                    $temp_row[] = $data_total[$k]['total_sale'];
                    $list_row_movie[] = $temp_row;
                    $index_movie++;
                    $sheet->mergeCells('A' . ($index_movie) . ':B' . ($index_movie));
                }

                $list_row = array_merge($list_row, $list_row_movie);

                foreach ($list_row_movie as $k => $v) {
                    $this->PhpExcel->addTableRow($v);
                }

                // add final row
                $temp_row = array('Grand Total', '');
                $temp_row[] = $data_total_final['total_ticket'];
                $temp_row[] = $data_total_final['total_sale'];
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);
                $temp_index = count($list_row);
                $sheet->mergeCells('A' . $temp_index . ':B' . $temp_index);

                if (!isset($output_filename) || empty($output_filename)) {
                    $output_filename = date('Ymd-Hi') . ".xls";
                } else {
                    $output_filename = $output_filename . ".xls"; //".xlsx";
                }

                $column = $this->getCellID(count($list_row[4])-1);
                $row = count($list_row);

                //format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);

                $this->PhpExcel->addTableFooter();

                foreach ($list_row as $k => $v) {
                    //$this->PhpExcel->addTableRow($v);
                }

                // close table and output
                if ($file_path) {
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array(
                        'status' => true,
                        'message' => '',
                        'params' => array()
                    );
                } else {
                    $this->PhpExcel->output($output_filename);

                    return array('status' => true, 'message' => '', 'params' => array());
                }
            } catch (Exception $e) {
                $export = array(
                    'status' => false,
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
        }

        public function export_excel_report_4 ($now, $data_result, $output_filename, $readable_header, $file_path = '') {
            try {
                $data = $data_result['result_format'];
                $data_total_movie = $data_result['result_format_total_movie'];
                $data_total_hall = $data_result['result_format_total_hall'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                // Get list movie
                $list_row = array();


                $excel_db_header[]['label'] = 'Report';
                // add heading for db fields
                $this->PhpExcel->addTableHeader($excel_db_header);
                $list_row[] = array();


                // Row show date
                $temp_row = array();
                $temp_row[] = 'Show Date';
                $temp_row[] = $now;
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                // Row blank
                $temp_row = array('');
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Data
                $temp_row = array('', '', '', 'Data');
                $this->PhpExcel->addTableRow($temp_row);

                $list_row[] = $temp_row;
                // Row Title
                $temp_row = array();
                $temp_row[] = 'House';
                $temp_row[] = 'Movie';
                $temp_row[] = 'Show Time';
                $temp_row[] = 'Sum of Paid Amount';
                $temp_row[] = 'Sum of Ticket Sold';

                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // List Row Data
                $list_row_movie = array();
                $index_movie = count($list_row);

                foreach ($data as $k => $v) {
                    $from_hall = $index_movie + 1;

                    $flag_new_hall = 1;
                    foreach ($v['list_movie'] as $kMovie => $vMovie) {
                        $from_movie = $index_movie + 1;

                        $flag_new_movie = 1;
                        foreach ($vMovie['list_time'] as $kTime => $vTime) {
                            $temp_row = array();
                            if ($flag_new_hall == 1) {
                                $temp_row[] = $v['hall_code'];
                                $flag_new_hall = 0;
                            } else {
                                $temp_row[] = '';
                            }

                            if ($flag_new_movie == 1) {
                                $temp_row[] = $vMovie['movie_name'];
                                $flag_new_movie = 0;
                            } else {
                                $temp_row[] = '';
                            }

                            $temp_row[] = $vTime['schedule_date_time_display'];
                            $temp_row[] = $vTime['total_sale'];
                            $temp_row[] = $vTime['total_ticket'];
                            $list_row_movie[] = $temp_row;
                            $index_movie++;
                        }
                        $to_movie = $index_movie;
                        $sheet->mergeCells('B' . $from_movie . ':B' . $to_movie);
                        $sheet->getStyle('B' . $from_movie . ':B' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                        // add final per each movie
                        $temp_row = array();
                        $temp_row[] = '';
                        $temp_row[] = $vMovie['movie_name'] . ' Total';
                        $temp_row[] = '';
                        $temp_row[] = $data_total_movie[$kMovie]['total_sale'];
                        $temp_row[] = $data_total_movie[$kMovie]['total_ticket'];

                        $list_row_movie[] = $temp_row;
                        $index_movie++;
                        $sheet->mergeCells('B' . $index_movie . ':C' . $index_movie);
                    }
                    $to_hall = $index_movie;
                    $sheet->mergeCells('A' . $from_hall . ':A' . $to_hall);
                    $sheet->getStyle('A' . $from_hall . ':A' . $to_hall)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    // add final per each hall
                    $temp_row = array();
                    $temp_row[] = $v['hall_code'] . ' Total';
                    $temp_row[] = '';
                    $temp_row[] = '';
                    $temp_row[] = $data_total_hall[$k]['total_sale'];
                    $temp_row[] = $data_total_hall[$k]['total_ticket'];

                    $list_row_movie[] = $temp_row;
                    $index_movie++;
                    $sheet->mergeCells('A' . $index_movie . ':C' . $index_movie);
                }

                $list_row = array_merge($list_row, $list_row_movie);
                foreach ($list_row_movie as $k => $v) {
                    $this->PhpExcel->addTableRow($v);
                }

                // add final row
                $temp_row = array('Grand Total', '', '');
                $temp_row[] = $data_total_final['total_sale'];
                $temp_row[] = $data_total_final['total_ticket'];
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);
                $temp_index = count($list_row);
                $sheet->mergeCells('A' . $temp_index . ':C' . $temp_index);


                if (!isset($output_filename) || empty($output_filename)) {
                    $output_filename = date('Ymd-Hi') . ".xls";
                } else {
                    $output_filename = $output_filename . ".xls"; //".xlsx";
                }

                $column = $this->getCellID(count($list_row[4])-1);
                $row = count($list_row);

                //format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);

                $this->PhpExcel->addTableFooter();
                foreach ($list_row as $k => $v) {
                    //$this->PhpExcel->addTableRow($v);
                }

                // close table and output
                if ($file_path) {
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array(
                        'status' => true,
                        'message' => '',
                        'params' => array()
                    );
                } else {
                    $this->PhpExcel->output($output_filename);

                    return array('status' => true, 'message' => '', 'params' => array());
                }
            } catch (Exception $e) {
                $export = array(
                    'status' => false,
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
        }

        public function export_excel_report_5 ($now, $data_result, $output_filename, $readable_header, $file_path = '') {
            try {
                $data = $data_result['result_format'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                // Get list movie
                $list_row = array();


                $excel_db_header[]['label'] = 'Report';
                // add heading for db fields
                $this->PhpExcel->addTableHeader($excel_db_header);
                $list_row[] = array();


                // Row show date
                $temp_row = array();
                $temp_row[] = 'Show Date';
                $temp_row[] = $now;
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);


                // Row blank
                $temp_row = array('');
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Data
                $temp_row = array('', '', 'Data');
                $this->PhpExcel->addTableRow($temp_row);

                $list_row[] = $temp_row;
                // Row Title
                $temp_row = array();
                $temp_row[] = 'Staff Name';
                $temp_row[] = 'Payment Method';
                $temp_row[] = 'Sum of Ticket Sold';
                $temp_row[] = 'Sum of Paid Amount';
                $temp_row[] = 'Number of Transaction';

                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // List Row Data
                $list_row_data = array();

                foreach ($data as $k => $v) {
                    $temp_row = array();
                    $temp_row[] = implode(', ', $v['staff_list']);
                    $temp_row[] = $v['group_method_name'];
                    $temp_row[] = $v['total_ticket'];
                    $temp_row[] = $v['total_sale'];
                    $temp_row[] = $v['total_transaction'];
                    $list_row_data[] = $temp_row;
                }

                $list_row = array_merge($list_row, $list_row_data);
                foreach ($list_row_data as $k => $v) {
                    $this->PhpExcel->addTableRow($v);
                }

                // add final row
                $temp_row = array('Grand Total', '');
                $temp_row[] = $data_total_final['total_ticket'];
                $temp_row[] = $data_total_final['total_sale'];
                $temp_row[] = $data_total_final['total_transaction'];
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);
                $temp_index = count($list_row);
                $sheet->mergeCells('A' . $temp_index . ':B' . $temp_index);


                if (!isset($output_filename) || empty($output_filename)) {
                    $output_filename = date('Ymd-Hi') . ".xls";
                } else {
                    $output_filename = $output_filename . ".xls"; //".xlsx";
                }

                $column = $this->getCellID(count($list_row[4])-1);
                $row = count($list_row);

                //format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);

                $this->PhpExcel->addTableFooter();
                foreach ($list_row as $k => $v) {
                    //$this->PhpExcel->addTableRow($v);
                }

                // close table and output
                if ($file_path) {
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array(
                        'status' => true,
                        'message' => '',
                        'params' => array()
                    );
                } else {
                    $this->PhpExcel->output($output_filename);

                    return array('status' => true, 'message' => '', 'params' => array());
                }
            } catch (Exception $e) {
                $export = array(
                    'status' => false,
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
        }

        public function export_excel_report_6 ($now, $data_result, $output_filename, $readable_header, $file_path = '') {
            try {
                $data = $data_result['result_format'];
                $data_total = $data_result['result_format_total'];
                $data_total_final = $data_result['result_format_total_final'];

                // create new empty worksheet and set default font
                $this->PhpExcel->createWorksheet()->setDefaultFont('Tahoma', 12);
                $this->PhpExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
                $sheet = $this->PhpExcel->getActiveSheet();

                $border_style = array(
                    'borders' => array
                    (
                        'allborders' => array
                        (
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000'),        // BLACK
                        )
                    )
                );

                // Get list movie
                $list_row = array();


                $excel_db_header[]['label'] = 'Report';
                // add heading for db fields
                $this->PhpExcel->addTableHeader($excel_db_header);
                $list_row[] = array();


                // Row transaction date
                $temp_row = array();
                $temp_row[] = 'Transaction Date';
                $temp_row[] = $now;
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row show date
                $temp_row = array();
                $temp_row[] = 'Show Date';
                $temp_row[] = "(All)";
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row blank
                $temp_row = array('');
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                // Row Data
                $temp_row = array('', '', 'Data');
                $this->PhpExcel->addTableRow($temp_row);

                $list_row[] = $temp_row;

                // Row Title
                $temp_row = array();
                $temp_row[] = 'Staff';
                $temp_row[] = 'Payment Method';
                $temp_row[] = 'Sum of Ticket Total';
                $temp_row[] = 'Sum of Net Sales';

                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);

                 //List Row Data
                $list_row_temp = array();
                $index_temp = count($list_row);
                foreach ($data as $k => $v) {
                    $from_movie = $index_temp+1;

                    $flag_new_movie = 1;

                    foreach ($v['list_payment'] as $kPayment => $vPayment) {
                        $temp_row = array();
                        if ($flag_new_movie == 1) {
                            $temp_row[] = $v['staff_name'];
                            $flag_new_movie = 0;
                        } else {
                            $temp_row[] = '';
                        }

                        $temp_row[] = $vPayment['group_method_name'];
                        $temp_row[] = $vPayment['total_ticket'];
                        $temp_row[] = $vPayment['total_sale'];
                        $list_row_temp[] = $temp_row;
                        $index_temp++;
                    }
                    $to_movie = $index_temp;
                    $sheet->mergeCells('A' . $from_movie . ':A' . $to_movie);
                    $sheet->getStyle('A' . $from_movie . ':A' . $to_movie)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                    // add final per each movie
                    $temp_row = array();
                    $temp_row[] = $v['staff_name'] . ' Total';
                    $temp_row[] = '';
                    $temp_row[] = $data_total[$k]['total_ticket'];
                    $temp_row[] = $data_total[$k]['total_sale'];
                    $list_row_temp[] = $temp_row;
                    $index_temp++;
                    $sheet->mergeCells('A' . ($index_temp) . ':B' . ($index_temp));
                }

                $list_row = array_merge($list_row, $list_row_temp);
                foreach ($list_row_temp as $k => $v) {
                    $this->PhpExcel->addTableRow($v);
                }

                // add final row
                $temp_row = array('Grand Total', '');
                $temp_row[] = $data_total_final['total_ticket'];
                $temp_row[] = $data_total_final['total_sale'];
                $list_row[] = $temp_row;
                $this->PhpExcel->addTableRow($temp_row);
                $temp_index = count($list_row);
                $sheet->mergeCells('A' . $temp_index . ':B' . $temp_index);


                if (!isset($output_filename) || empty($output_filename)) {
                    $output_filename = date('Ymd-Hi') . ".xls";
                } else {
                    $output_filename = $output_filename . ".xls"; //".xlsx";
                }

                $column = $this->getCellID(count($list_row[5])-1);
                $row = count($list_row);

                //format header + data
                $sheet->getStyle("A1:" . $column . $row)->applyFromArray($border_style);

                $this->PhpExcel->addTableFooter();
                foreach ($list_row as $k => $v) {
                    //$this->PhpExcel->addTableRow($v);
                }

                // close table and output
                if ($file_path) {
                    $this->PhpExcel->save(WWW_ROOT . $file_path);
                    return array(
                        'status' => true,
                        'message' => '',
                        'params' => array()
                    );
                } else {
                    $this->PhpExcel->output($output_filename);

                    return array('status' => true, 'message' => '', 'params' => array());
                }
            } catch (Exception $e) {
                $export = array(
                    'status' => false,
                    'message' => 'Fail to export the Excel file. Please try again.',
                    'params' => array()
                );

                return $export;
            }
        }

	}
?>