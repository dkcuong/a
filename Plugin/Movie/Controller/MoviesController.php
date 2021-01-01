<?php
App::uses('MovieAppController', 'Movie.Controller');

class MoviesController extends MovieAppController {

	public $components = array('Paginator');
	private $model = 'Movie';
    private $model_movie_type = 'MovieType';
	private $model_lang = 'MovieLanguage';
	private $model_movie = 'MovieTrailer';
	
	private $language_input_fields = array(
		'id',
		'movie_id',
		'language',
		'name',
		//'short_storyline',
		'storyline',
        'lang_movie',
        'lang_info',
        'director',
        'genre',
        'subtitle'
	);
	private $language_display_fields = array('name'); //used to display name in view
	private $filter = array(
		'code',
		'name',
	);
	private $rule = array(
		1 => array('required'),
		2 => array('required'),
		3 => array('required'),
		4 => array('required','enum'),
		6 => array('required'),
		7 => array('required'),
		9 => array('required'),
		10 => array('number'),
		11 => array('number'),
		12 => array('required'),
		14 => array('required'),
		15 => array('required','enum'),
	);
	private $rule_spec = array(
		4 => array('N', 'Y', 'y', 'n'),
		15 => array('N', 'Y', 'y', 'n'),
	);
	private $api_rules = array(
		// 'token' => array(
		// 	'required' => true,
		// 	'type' => 'string',
		// ),
		'language' => array(
			'required' => true,
			'type' => 'string',
			'in' => ['zho', 'eng', 'chi'],
		),
		// 'is_latest' => array(
		// 	'required' => false,
		// 	'type' => 'boolean',
		// ),
	);

	private $upload_path = 'movies';
	private $poster_prefix = 'poster';
	private $video_prefix = 'video';

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('movie', 'item_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$languages_model = $this->model_lang;

		if (isset($data_search) && !empty($data_search['movie_type']))
		{
			$movieType = $data_search['movie_type'];
			unset($data_search['movie_type']);
		}
		else
		{
			$movieType = null;
		}

		$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);

		if (!is_null($movieType))
		{
			$data_search['movie_type'] = $movieType;
		}

        $contain = array(
            'MovieType' => array( 'fields' => array('name') ),
        );

		if ($data_search)
		{
			if( isset($data_search['movie_type']) && !empty($data_search['movie_type']) )
			{
				$objXrefs = ClassRegistry::init('Movie.MoviesMovieType');
				$option = array(
					'fields' => array('movie_id'),
					'recursive' => -1,
					'conditions' => array(
						'movie_type_id =' => $data_search['movie_type']
					)
				);
				$xrefs = $objXrefs->find('all', $option);

				$movie_ids = Hash::extract( $xrefs, "{n}.MoviesMovieType.movie_id" );

				if (!empty($movie_ids))
				{
					$conditions["Movie.id IN"] = $movie_ids;
				}
			}

			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'movie',
                    'controller' => 'movies',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'csv',
                ));
            }

            // button export Excel
            if( isset($data_search['button']['exportExcel']) && !empty($data_search['button']['exportExcel']) ) {
                $this->requestAction(array(
                    'plugin' => 'movie',
                    'controller' => 'movies',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'xls',
                ));
		    }			
		}

		$this->Paginator->settings = array(
			'contain' => $contain,
			'fields' => array($model.".*", $languages_model.".*"),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
			'joins' => array(
				array(
					'alias' => $languages_model,
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						$model.'.id = '.$languages_model.'.movie_id',
						$languages_model.'.language' => $this->lang18
					),
				),
			),
		);

		$movie_types =  $this->Movie->MovieType->get_list_movie_types();

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'languages_model', 'data_search', 'movie_types'));	
	}

	public function admin_export(){
		$model = $this->model;

		$results = array(
		   'status' => false, 
		   'message' => __('missing_parameter'),
		   'params' => array(),
	   );

	   $this->disableCache();

	   if( $this->request->is('get') ) {
		   $result = $this->$model->get_data_export($this->request->conditions, 1, 2000, $this->lang18);

		   if ($result) {

				$cvs_data = array();

				foreach ($result as $row) {
					$temp = $this->$model->format_data_export(array(), $row);

					array_push($cvs_data, $temp);
				}

			   try{
				   $file_name = 'movies_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('code')),
							array('label' => __('name_zho')),
							array('label' => __('name_eng')),
							array('label' => __('enabled')),
							array('label' => __d('movie', 'slug')),
							array('label' => __d('movie', 'director')),
							array('label' => __d('movie', 'writer')),
							array('label' => __d('movie', 'star')),
							array('label' => __d('movie', 'language')),
							array('label' => __d('movie', 'rating')),
							array('label' => __d('movie', 'duration')),
							array('label' => __d('movie', 'poster')),
							array('label' => __d('movie', 'video')),
							array('label' => __d('movie', 'movie_type')),
							array('label' => __d('movie', 'is_feature')),
							array('label' => __d('movie', 'genre')),
							array('label' => __d('movie', 'subtitle')),
                            array('label' => __d('movie', 'lang_info')),
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __('code'),
							'label' => __('name_zho'),
							'label' => __('name_eng'),
							'label' => __('enabled'),
							'label' => __d('movie', 'slug'),
							'label' => __d('movie', 'director'),
							'label' => __d('movie', 'writer'),
							'label' => __d('movie', 'star'),
							'label' => __d('movie', 'language'),
							'label' => __d('movie', 'rating'),
							'label' => __d('movie', 'duration'),
							'label' => __d('movie', 'poster'),
							'label' => __d('movie', 'video'),
							'label' => __d('movie', 'movie_type'),
							'label' => __d('movie', 'is_feature'),
							'label' => __d('movie', 'genre'),
							'label' => __d('movie', 'subtitle'),
                            'label' => __d('movie', 'lang_info'),
						);
						$this->Common->export_csv(
							$cvs_data,
							$header,
							$file_name
						);
					}
			   	} catch ( Exception $e ) {
					$this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
					$results = array(
						'status' => false, 
						'message' => __('export_csv_fail'),
						'params' => array()
					);
			   	}
			}else{
				$results['message'] = __('no_record');
			}
	   }

	   $this->set(array(
		   'results' => $results,
		   '_serialize' => array('results')
	   ));
	}

	public function admin_import($id = null) {
		$model = $this->model;
		$languages_model = $this->model_lang;
		$class_hidden = 'hidden';
		$message = array();
		$is_valid_all = true;

		if ($this->request->is('post')) {
			$data = $this->request->data;

			$objresult = $this->Common->upload_and_read_excel($data['Movie'], '');

			if (!isset($objresult['status']) || !$objresult['status']) {
				throw new NotFoundException(__('invalid_data'));
			}

			$sheet_list = array_keys($objresult['data']);

			$data_upload = $objresult['data'][$sheet_list[0]];

			$line = -1;
			foreach($data_upload as $obj) {
				$line++;
				if ($line > 0) {
					$check_result = $this->Common->check_rules($this->rule, $this->rule_spec, $data_upload[0], $obj, $line);
					$valid = $check_result['status'];
					$tmp_msg = $check_result['message'];

					if ($valid) {
						$dbo = $this->$model->getDataSource();
						$dbo->begin();

						$data_insert = array();

						$option = array(
							'conditions' => array(
								'id' => $obj[0]
							),
						);
						$data_old = $this->$model->find('first', $option);

						$data_id = 0;
						if (isset($data_old[$model]['id']) && !empty($data_old[$model]['id'])) {
							$data_insert[$model]['id'] = $data_old[$model]['id'];
							$data_id = $data_old[$model]['id'];
							$data_lang = $this->$model->$languages_model->find('all', array('conditions' => array('movie_id' => $data_old[$model]['id'])));

							foreach($data_lang as &$lang) {
								switch($lang[$languages_model]['language']) {
									case 'zho' :
											$lang[$languages_model]['name'] = $obj[2];
										break;
									case 'eng' :
											$lang[$languages_model]['name'] = $obj[3];
										break;
								}
								$data_insert[$languages_model][] = $lang[$languages_model];
							}
						} else {
							$data_insert[$model]['id'] = null;

							$data_insert[$languages_model][0]['language'] = 'zho';
							$data_insert[$languages_model][0]['name'] = $obj[2];

							$data_insert[$languages_model][1]['language'] = 'eng';
							$data_insert[$languages_model][1]['name'] = $obj[3];
						}

						$data_insert[$model]['code'] = $obj[1];
						$data_insert[$model]['enabled'] = (in_array($obj[4], array('Y', 'y'))) ? 1: 0;
						
						$data_insert[$model]['slug']     = $obj[5];
						$data_insert[$model]['director'] = $obj[6];
						$data_insert[$model]['writer']   = $obj[7];
						$data_insert[$model]['language'] = $obj[9];
						$data_insert[$model]['rating']   = $obj[10];
						$data_insert[$model]['duration'] = $obj[11];
						$data_insert[$model]['poster']   = $obj[12];
						$data_insert[$model]['video']    = $obj[13];

						$movie_type_names = array_map('trim', explode(',', $obj[14]));
						$data_insert[$model]['is_feature'] = (in_array($obj[15], array('Y', 'y'))) ? 1: 0;
						$data_insert[$model]['genre']    = $obj[16];
						$data_insert[$model]['subtitle']    = $obj[17];
                        $data_insert[$model]['lang_info']    = $obj[18];

						$objMovieType = ClassRegistry::init('Movie.MovieType');
						$option = array(
							'fields' => array('id'),
							'recursive' => -1,
							'conditions' => array(
								'enabled' => 1,
								'name IN' => $movie_type_names
							)
						);
						$active_movie_types = $objMovieType->find('all', $option);
						$active_movie_type_ids = Hash::extract( $active_movie_types, "{n}.MovieType.id" );

						$data_insert['MovieType'] = $active_movie_type_ids;
						
						if ($this->$model->saveAll($data_insert)) {
							$dbo->commit();
						} else {
							$valid = false;
							$dbo->rollback();
							$validationErrors = $this->$model->validationErrors;

							$tmp_msg = array();
							foreach($validationErrors as $key => $value) {
								foreach($value as $error_msg) {
									array_push($tmp_msg, 'Error at line' . $line . ', ' . $error_msg);
								}
							}
						}
					}

					if (!$valid) {
						$message = array_merge($message, $tmp_msg);
						$is_valid_all = false;
					}
				}
			}

			if ($is_valid_all) {
				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$class_hidden = '';
			}

		}

		display:
		$this->set(compact('model', 'class_hidden', 'message'));
	}

	public function admin_view($id) {
		$model = $this->model;
		$languages_model = $this->model_lang;

		$options = array(
			'fields' => array($model.'.*'),
			'contain' => array(
				$languages_model,
				'MovieType',
				'Star',
				'MovieTrailer',
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
		);
		$model_data = $this->$model->find('first', $options);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

        //languages fields
        $language_input_fields = $this->language_display_fields;

        $languages = isset($model_data[$languages_model]) ? $model_data[$languages_model] : array();

		$this->set('dbdata', $model_data);

        $this->set(compact('model', 'language_input_fields','languages', 'languages_model'));
	}

	public function admin_add() {
		$model = $this->model;
		$languages_model = $this->model_lang;
		$movie_model = $this->model_movie;

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$data              = $this->request->data;
			//$data['MovieType'] = $data[$model]['movie_type_id'];
			$data['Star']      = $data[$model]['stars_id'];

			foreach ($data['MovieLanguage'] as $kMovieLanguage => $vMovieLanguage) {
			    $data['MovieLanguage'][$kMovieLanguage]['short_storyline'] = $vMovieLanguage['storyline'];
            }

			foreach($data['MovieType'] as &$movie_type) {
				if (isset($movie_type['publish_date']) && !empty($movie_type['publish_date'])) {
					$movie_type['publish_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['publish_date'])));
				}

				if (isset($movie_type['start_date']) && !empty($movie_type['start_date'])) {
					$movie_type['start_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['start_date'])));
				}
	
				if (isset($movie_type['end_date']) && !empty($movie_type['end_date'])) {
					$movie_type['end_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['end_date'])));
				}
			}

			$valid        = true;
			$extra_errors = '';

			//$data[$model]['slug'] = $this->make_slug($data[$languages_model][0]['name'], empty($data[$model]['slug']) ? null : $data[$model]['slug']);

			$this->upload_poster($data, $valid, $extra_errors);
			$data[$this->model_movie] = $this->upload_video($data, $valid, $extra_errors);

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();

				if ($this->$model->saveAll($data)) {
					$dbo->commit();
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');

				if (!empty($extra_errors)) {
					$this->Session->setFlash($extra_errors, 'flash/error');
				}
			}
			
		}

		//languages fields
		$language_input_fields = $this->language_input_fields;

		$languages_list = (array)Environment::read('site.available_languages');

		$movie_types =  $this->Movie->MovieType->get_list_movie_types();	
		$stars =  $this->Movie->Star->get_list_stars();	

		$add_movie_trailer_url = Router::url(array('plugin'=>'movie', 'controller'=>'movies', 'action'=>'admin_add_new_image_new'),true);

		$detail_model = 'MovieType';
		$add_new_movie_type_url = Router::url(array('plugin'=>'movie', 'controller'=>'movies', 'action'=>'admin_add_movie_type_new'),true);

		$this->set(compact('model', 'detail_model', 'add_new_movie_type_url', 'language_input_fields', 'languages_model', 'languages_list', 'movie_types', 'stars', 'add_movie_trailer_url', 'movie_model'));
	}

	private function make_slug($name, $slug)
	{
		if (is_null($slug))
		{
			$slugified = $this->Common->slugify($name);
		}
		else
		{
			$slugified = $this->Common->slugify($slug);
		}

		return $slugified;
	}

	private function upload_poster(&$data, &$valid, &$extra_errors, $old_poster = null)
	{
		$uploaded_poster = $data[$this->model]['poster'];

		if (!empty($uploaded_poster) && $uploaded_poster['tmp_name'])
		{
			if (!preg_match('/image\/*/', $uploaded_poster['type']))
			{
				$valid = false;
				$extra_errors .= 'Wrong image type. ';
			}
			else
			{
				$uploaded = $this->Common->upload_images( $uploaded_poster, $this->upload_path, $this->poster_prefix );

				if( isset($uploaded['status']) && ($uploaded['status'] == true) )
				{
					$data[$this->model]['poster'] = $uploaded['params']['path'];
					$data[$this->model]['poster'] = str_replace("\\",'/',$data[$this->model]['poster']);
					
					if (!empty($old_poster)) {
						$file = new File( 'img/'. $old_poster );						
						$file->delete();
					}
				}
				else
				{
					$valid = false;
				}
			}
		}
		else
		{
			if (is_null($old_poster))
			{
				unset($data[$this->model]['poster']);
			}
			else
			{
				$data[$this->model]['poster'] = $old_poster;
			}
		}
	}

	private function upload_video(&$data, &$valid, &$extra_errors, $old_video = null)
	{
		$uploaded_videos = isset($data[$this->model_movie]) ? $data[$this->model_movie] : [];

		if (!empty($uploaded_videos))
		{
			foreach($uploaded_videos as $k=>$uploaded_video) {

					if (!preg_match('/video\/*/', $uploaded_video['Movie']['type']))
					{
						$valid = false;
						$extra_errors .= 'Wrong video type. ';
					}
					else
					{
						$uploaded = $this->Common->upload_images( $uploaded_video['Movie'], $this->upload_path, $this->video_prefix );

						if( isset($uploaded['status']) && ($uploaded['status'] == true) )
						{
							$path_video = $uploaded['params']['path'];
							$total_video[$k]['video_path'] = str_replace("\\",'/',$path_video);
						}
						else
						{
							$valid = false;
						}
					}

                    if (!preg_match('/image\/*/', $uploaded_video['Poster']['type']))
                    {
                        $valid = false;
                        $extra_errors .= 'Wrong image type. ';
                    }
                    else
                    {
                        $uploaded = $this->Common->upload_images( $uploaded_video['Poster'], $this->upload_path, $this->poster_prefix );

                        if( isset($uploaded['status']) && ($uploaded['status'] == true) )
                        {
                            $path_poster = $uploaded['params']['path'];
                            $total_video[$k]['poster_path'] = str_replace("\\",'/',$path_poster);
                        }
                        else
                        {
                            $valid = false;
                        }
                    }
				}

				if ($valid) {
					return $total_video;
				}
		}
		else
		{
		// 	if (is_null($old_video))
		// 	{
		// 		unset($data['MovieTrailer']);
		// 	}
		// 	else
		// 	{
		// 		$data['MovieTrailer'] = $old_video;
		// 		//$data[$this->model]['video'] = $old_video;
		// 	}
		}

	}

	public function admin_edit($id = null) {
		$model = $this->model;
		$languages_model = $this->model_lang;
		$movie_model = $this->model_movie;

		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1,
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) {
			throw new NotFoundException(__('invalid_data'));
		}

		$objOrder = ClassRegistry::init('Pos.Order');
		$ticket_sold = $objOrder->is_ticket_sold($id);

		$movie_type_list = Hash::extract($old_item['MovieType'],'{n}.id');
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$data              = $this->request->data;
			// $data['MovieType'] = $data[$model]['movie_type_id'];
			$data['Star']      = $data[$model]['stars_id'];

            foreach ($data['MovieLanguage'] as $kMovieLanguage => $vMovieLanguage) {
                $data['MovieLanguage'][$kMovieLanguage]['short_storyline'] = $vMovieLanguage['storyline'];
            }

			$valid = true;

			if ($ticket_sold) {
				foreach($data['MovieType'] as $movie_types) {
					$index = array_search($movie_types['movie_type_id'], $movie_type_list);
					if($valid && ($index !== false)) {
						$new_end_date = date('Y-m-d', strtotime(str_replace('/', '-', $movie_types['end_date'])));
						$old_end_date = $old_item['MovieType'][$index]['MoviesMovieType']['end_date'];
						if (strtotime($new_end_date) < strtotime($old_end_date)) {
							$valid = false;
							$this->request->data = $old_item;
							$this->Session->setFlash(__('end_date_cant_be_smaller_than_old_date_because_ticket_has_been_sold'), 'flash/success');
						}
					}
				}
			}

			if ($valid) {
				foreach($data['MovieType'] as &$movie_type) {
					if (isset($movie_type['publish_date']) && !empty($movie_type['publish_date'])) {
						$movie_type['publish_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['publish_date'])));
					}

					if (isset($movie_type['start_date']) && !empty($movie_type['start_date'])) {
						$movie_type['start_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['start_date'])));
					}
		
					if (isset($movie_type['end_date']) && !empty($movie_type['end_date'])) {
						$movie_type['end_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $movie_type['end_date'])));
					}
				}

				$extra_errors = '';


				//$data[$model]['slug'] = $this->make_slug($data[$languages_model][0]['name'], empty($data[$model]['slug']) ? null : $data[$model]['slug']);
				$this->upload_poster($data, $valid, $extra_errors, $old_item[$model]['poster']);

				if (isset($data['MovieTrailer']) && count($data['MovieTrailer']) >0) {
					$new_movie = $this->upload_video($data, $valid, $extra_errors, $old_item[$movie_model]);
					$data['MovieTrailer'] = $new_movie;
				}

				if (isset($data['remove_image']) && count($data['remove_image']) >0) {
					$this->Common->remove_uploaded_image('MovieTrailer', 'Movie', $data['remove_image']);
				}
			}
			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				
				try 
				{
					if ($this->$model->saveAll($data))
					{
						$dbo->commit();
						$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					} 
					else 
					{
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					}
				} 
				catch (Exception $ex) 
				{
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');

				if (!empty($extra_errors))
				{
					$this->Session->setFlash($extra_errors, 'flash/error');
				}
			}
		} 
		else {
			$this->request->data = $old_item;
		}

		$movie_types =  $this->Movie->MovieType->get_list_movie_types();	
        $current_movie_types = isset($this->request->data['MovieType']) ? Hash::extract($this->request->data['MovieType'],'{n}.id') : $data[$model]['movie_type_id'];

		$stars =  $this->Movie->Star->get_list_stars();	
        $current_stars = isset($this->request->data['Star']) ? Hash::extract($this->request->data['Star'],'{n}.id') : $data[$model]['stars_id'];

		//languages fields
		$language_input_fields = $this->language_input_fields;
		$languages_list = (array)Environment::read('site.available_languages');

		$add_movie_trailer_url = Router::url(array('plugin'=>'movie', 'controller'=>'movies', 'action'=>'admin_add_new_image_new'),true);

		$detail_model = 'MovieType';
		$add_new_movie_type_url = Router::url(array('plugin'=>'movie', 'controller'=>'movies', 'action'=>'admin_add_movie_type_new'),true);

		$this->set(compact('model', 'ticket_sold', 'detail_model', 'add_new_movie_type_url', 'language_input_fields', 'languages_model', 'languages_list', 'movie_types', 'current_movie_types', 'stars', 'current_stars', 'movie_model', 'add_movie_trailer_url'));
	}

	public function admin_delete($id = null) {
        $model = $this->model;
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->$model->id = $id;
		if (!$this->$model->exists()) {
			throw new NotFoundException(__('invalid_data'));
		}
		if ($this->$model->delete()) {
			$this->Session->setFlash(__('data_is_deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('data_is_not_deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_add_movie_type_new(){
		$data = $this->request->data;

		if ($data) {
			$detail_model = $data['detail_model'];
            $count = $data['count'];

			$movie_types =  $this->Movie->MovieType->get_list_movie_types();

			$this->set(compact('detail_model','count', 'movie_types'));

			$this->render('Pages/add_new_movie_type');
		}else{
			return 'NULL';
		}
	}


	public function api_get_list() 
	{	
		$this->Api->init_result();


		if ($this->request->is('post')) 
		{
			$this->disableCache();
			$data   = $this->request->data;

            $status = false;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

			if( ! isset($data['language']) || empty($data['language']) ){
			 	$message = __('missing_parameter') .  __('language');
			} else if( ! isset($data['type']) || empty($data['type']) ) {
                $message = __('missing_parameter') . __('type');
            } else if (! in_array($data['type'], array('now_playing', 'coming_soon', 'both')) ) {
                $message = __('invalid_data') . __('type');
            } else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->Movie->get_list($data);

                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }

			$this->Api->set_result(true, $message, $params);
		}

		$this->Api->output();		
	}

	
	public function api_get_detail() {
		$this->Api->init_result();
        $model = $this->model;

		if ($this->request->is('post')) 
		{
			$this->disableCache();
			$data   = $this->request->data;

            $status = false;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['language']) || empty($data['language']) ){
                $message = __('missing_parameter') .  __('language');
            } else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->get_detail($data);

                $status = $result['status'];
                $message = $result['message'];

                if ($status) {
                    $params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                } else {
                    if (isset($result['log_data']) && $result['log_data']) {
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }
            $this->Api->set_result(true, $message, reset($params));
		}

		$this->Api->output();
	}

	public function api_get_schedule_detail_layout() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = false;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['schedule_detail_id']) || empty($data['schedule_detail_id']) ){
                $message = __('missing_parameter') .  __('schedule_detail_id');
            } else {
//                $url_params = $this->request->params;
//                $this->Api->set_post_params($url_params, $data);
//                $this->Api->set_save_log(true);

                $result = $this->$model->get_schedule_detail_layout($data);

                $status = $result['status'];
                $message = $result['message'];

                if ($status) {
                    $params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                } else {
                    if (isset($result['log_data']) && $result['log_data']) {
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }
            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_movie_by_schedule_date_time() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = false;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['movie_id']) || empty($data['movie_id']) ) {
                $message = __('missing_parameter') . __('movie_id');

            } else {
//                $url_params = $this->request->params;
//                $this->Api->set_post_params($url_params, $data);
//                $this->Api->set_save_log(true);

                $result = $this->$model->get_movie_by_schedule_date_time($data);

                $status = $result['status'];
                $message = $result['message'];

                if ($status) {
                    $params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                } else {
                    if (isset($result['log_data']) && $result['log_data']) {
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }
            $this->Api->set_result(true, $message, $params);
        }

        $this->Api->output();
    }

	public function admin_add_movie_trailer_url(){
		$data = $this->request->data;

		if ($data) {
			$detail_ticket_type_model = $data['detail_ticket_type_model'];
			$detail_model = $data['detail_model'];
            $count = $data['count'];

			$option_ticket_type = array(
				'fields' => array("TicketType.id", 'TicketTypeLanguage.name'),
				'joins' => array(
					array(
						'alias' => 'TicketTypeLanguage',
						'table' => Environment::read('table_prefix') . 'ticket_type_languages',
						'type' => 'left',
						'conditions' => array(
							'TicketTypeLanguage.ticket_type_id = TicketType.id',
							'TicketTypeLanguage.language' => $this->lang18
						),

					),
				),
				'conditions' => array(
					'enabled' => 1
				)
			);

			$objTicketType = ClassRegistry::init('Pos.TicketType');
			$data_ticket_type = $objTicketType->find('all', $option_ticket_type);

			$this->set(compact('detail_model', 'detail_ticket_type_model', 'data_ticket_type', 'count'));

			$this->render('Pages/add_new_time_schedule');
		}else{
			return 'NULL';
		}
	}

	public function api_get_list_feature_movie() {
        $this->Api->init_result();

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = false;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['language']) || empty($data['language']) ){
                $message = __('missing_parameter') .  __('language');
            } else {
                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                //$this->Api->set_save_log(true);

                $result = $this->Movie->get_list_feature_movie($data);

                $status = $result['status'];
                $message = $result['message'];

                if ($status) {
                    $params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                } else {
                    if (isset($result['log_data']) && $result['log_data']) {
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }
            $this->Api->set_result(true, $message, $params);
        }

        $this->Api->output();
    }

	public function admin_add_new_image_new(){
		$data = $this->request->data;

		if ($data) {
			$images_model = $data['images_model'];
            $count = $data['count'];

			$this->set(compact('images_model','count'));


			$this->render('Pages/add_new_image_only');
		}else{
			return 'NULL';
		}
	}

    public function api_get_movie_type_list() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['language']) || empty($data['language']) ){
                $message = __('missing_parameter') .  __('language');
            } else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $data['is_api'] = true;
                $params = $this->Movie->MovieType->get_list_movie_types($data);
            }

            if (!$params) {
                $params = (object)array();
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_ticket_type_schedule_list() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            if( ! isset($data['language']) || empty($data['language']) ){
                $message = __('missing_parameter') .  __('language');
            } else if( ! isset($data['schedule_detail_id']) || empty($data['schedule_detail_id']) ){
                $message = __('missing_parameter') .  __('schedule_detail_id');
            }
            else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $params = $this->Movie->get_ticket_type_schedule_list($data);

            }

            if (!$params) {
                $params = (object)array();
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_list_day_has_schedule() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

/*            if( ! isset($data['language']) || empty($data['language']) ){
                $message = __('missing_parameter') .  __('language');
            } else if( ! isset($data['schedule_detail_id']) || empty($data['schedule_detail_id']) ){
                $message = __('missing_parameter') .  __('schedule_detail_id');
            }
            else {*/
            //$this->Api->set_language($data['language']);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $params = $this->$model->get_list_day_has_schedule($data);

            /*}*/

            if (!$params) {
                $params = (object)array();
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_trailer_by_id() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            if( ! isset($data['trailer_id']) || empty($data['trailer_id']) ){
                $message = __('missing_parameter') .  __('trailer_id');
            } else {
                $params = $this->$model->get_trailer_by_id($data);
            }

            if (!$params) {
                $params = (object)array();
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }
}

