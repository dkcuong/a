<?php
App::uses('MovieAppModel', 'Movie.Model');

class Movie extends MovieAppModel {

	public $actsAs = array('Containable');


	public $validate = array(
	);

	public $movie_type_model = "MovieType";
	public $movie_language_model = "MovieLanguage";
    public $movie_trailer_model = "MovieTrailer";
    public $star_model = "Star";

	public $belongsTo = array(
		'CreatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
		'UpdatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'updated_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
	);

	public $hasMany = array(
		'MovieLanguage' => array(
			'className' => 'Movie.MovieLanguage',
			'foreignKey' => 'movie_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Schedule' => array(
			'className' => 'Movie.Schedule',
			'foreignKey' => 'movie_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MovieTrailer' => array(
			'className' => 'Movie.MovieTrailer',
			'foreignKey' => 'movie_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $hasAndBelongsToMany = array(
		'MovieType' => array(
			'className' => 'Movie.MovieType',
			'joinTable' => 'movies_movie_types',
			'foreignKey' => 'movie_id',
			'associationForeignKey' => 'movie_type_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Star' => array(
			'className' => 'Movie.Star',
			'joinTable' => 'movies_stars',
			'foreignKey' => 'movie_id',
			'associationForeignKey' => 'star_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	public function get_data_export($conditions, $page, $limit, $lang){
		$prefix = Environment::read('database.prefix');
		$this->query('drop temporary table if exists ' . $prefix . 'tmp_names');
		$strsql = "set @sql = (
			select group_concat(distinct 
				concat(
					\"max(case when `language`='\", language, \"' then `name` end) as `\", 'name_',`language`, \"`\"
				)
			) 
			from " . $prefix . "movie_languages
		)";
		$this->query($strsql);

		$strsql = "set @sql = concat('create temporary table " . $prefix . "tmp_names (select movie_id, ', coalesce(@sql, '1'), ' from " . $prefix . "movie_languages  group by `movie_id`)')";
		$this->query($strsql);

		$strsql = "prepare stmt from @sql";
		$this->query($strsql);

		$strsql = "execute stmt";
		$this->query($strsql);

		$strsql = "deallocate prepare stmt";
		$this->query($strsql);

        $all_settings = array(
			'fields' => array(
				'Movie.*',
				'TmpName.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy',
                'MovieType'
			),
			'joins' => array(
				array(
					'alias' => 'TmpName',
					'table' => Environment::read('table_prefix') . 'tmp_names',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = TmpName.movie_id',
					),
				),
			),
            'conditions' => $conditions,
            'order' => array( 'Movie.code' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
	}
	
	public function format_data_export($data, $row){
		$model = $this->alias;
        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["code"]) ?  $row[$model]["code"] : ' ',
			!empty($row['TmpName']["name_zho"]) ?  $row['TmpName']["name_zho"] : ' ',
			!empty($row['TmpName']["name_eng"]) ?  $row['TmpName']["name_eng"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
			!empty($row[$model]["slug"]) ?  $row[$model]["slug"] : ' ',
			!empty($row[$model]["director"]) ?  $row[$model]["director"] : ' ',
			!empty($row[$model]["writer"]) ?  $row[$model]["writer"] : ' ',
			' ',
			!empty($row[$model]["language"]) ?  $row[$model]["language"] : ' ',
			!empty($row[$model]["rating"]) ?  $row[$model]["rating"] : ' ',
			!empty($row[$model]["duration"]) ?  $row[$model]["duration"] : ' ',
			!empty($row[$model]["poster"]) ?  $row[$model]["poster"] : ' ',
			!empty($row[$model]["video"]) ?  $row[$model]["video"] : ' ',
			!empty($row['MovieType']) ?  implode(', ', Hash::extract($row['MovieType'],'{n}.name')) : ' ',
			$row[$model]['is_feature'] == 1 ? 'Y' : 'N',
			!empty($row[$model]["genre"]) ?  $row[$model]["genre"] : ' ',
			!empty($row[$model]["subtitle"]) ?  $row[$model]["subtitle"] : ' ',
            !empty($row[$model]["lang_info"]) ?  $row[$model]["lang_info"] : ' ',
        );
    }

	public function get_list($data){
		$model = $this->alias;
        $movie_trailer_model = $this->movie_trailer_model;
		$movie_language_model = $this->movie_language_model;
		$movie_type_model = $this->movie_type_model;
		$webroot = Environment::read('web.url_img');

        /*
		$coming_soon = array(
			'MoviesMovieType.publish_date < ' => date('Y-m-d H:i:s'),
			'MoviesMovieType.start_date > ' => date('Y-m-d H:i:s'),
		);

		$now_playing = array(
			'MoviesMovieType.start_date < ' => date('Y-m-d H:i:s'),
			'MoviesMovieType.end_date > ' => date('Y-m-d H:i:s'),
		);
        */

        $filter_month = date('m');

        $coming_soon = array(
            'MoviesMovieType.publish_date <= ' => date('Y-m-d'),
            'MoviesMovieType.start_date > ' => date('Y-m-d'),
        );

        if (isset($data['start_date_month']) && !empty($data['start_date_month'])) {
            $filter_month = $data['start_date_month'];
            $coming_soon['MONTH(MoviesMovieType.start_date)'] = $filter_month;
        } else {
            $coming_soon['MONTH(MoviesMovieType.start_date) >= '] = $filter_month;
        }

        $date_filter = date( 'Y-m-d' );
        if (isset($data['date']) && !empty($data['date'])) {
            $date_filter = $data['date'];
        }

		$now_playing = array(
			'MoviesMovieType.start_date <= ' => $date_filter,
			'MoviesMovieType.end_date >= ' => $date_filter,
		);

        /*if (isset($data['movie_id']) && !empty($data['movie_id'])) {
            $coming_soon['Movie.id'] = $data['movie_id'];
            $now_playing['Movie.id'] = $data['movie_id'];
        }*/

        $coming_soon_result = $now_playing_result = array();

		$option = array(
			'fields' => array(
				$this->alias.".*",
                'MoviesMovieType.*',
                'MovieType.name'
			),
			'contain' => array(
				'MovieLanguage' => array(
					'fields' => array(
						
					),
					'conditions' => array(
						'language' => $data['language']
					)
				),
                'MovieTrailer' => array(
                    'fields' => array()
                )
			),
			'joins' => array(
				array(
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = '.$this->alias.'.id',
					),
                ),
                array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_type_id = MovieType.id',
					),
				),
                /*array(
                    'alias' => 'Schedule',
                    'table' => Environment::read('table_prefix') . 'schedules',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.movie_id = '.$this->alias.'.id',
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = Schedule.id',
                    ),
                ),*/
            ),
            // 'limit' => Environment::read('web.limit_record'),
            'order' => array('MoviesMovieType.start_date' => 'DESC'),
            'group' => array(
                $this->alias.'.id',
                'MoviesMovieType.movie_type_id'
            )
        );

        /*$now = date( 'Y-m-d' );
        if (isset($data['date']) && !empty($data['date'])) {
            $date_filter = $data['date'];
        } else {
            $date_filter = $now;
        }*/

        if ($data['type'] == "now_playing") {
            $option['conditions'] = $now_playing;
        }else {
            $option['conditions'] = $coming_soon;
        }

        if (isset($data['movie_type_id']) && !empty($data['movie_type_id'])) {
            $option['conditions']['MoviesMovieType.movie_type_id'] = $data['movie_type_id'];
        }

        if (isset($data['sort_by']) && !empty($data['sort_by'])
        && isset($data['sort_direction']) && !empty($data['sort_direction'])
        ) {
            $option['order'] = array(
                'MoviesMovieType.start_date' => $data['sort_direction']
            );
        }

        if (isset($data['limit']) && !empty($data['limit'])) {

            $option['limit'] = $data['limit'];
        }

        if (isset($data['offset']) && !empty($data['offset'])) {
            $option['offset'] = $data['offset'];
        }

        $list_result = array();
        $total_record = 0;

        $list_result = $this->find('all', $option);


        $option['fields'] = array('count(*) AS total_record');
        unset($option['limit']);
        unset($option['offset']);
        $total_record_list = $this->find('first', $option);
        if (isset($total_record_list[0]) && !empty ($total_record_list[0])) {
            $total_record = $total_record_list[0]['total_record'];
        }

        if (isset($data['get_schedule']) && $data['get_schedule'] == 1) {
            //$now_playing_result = $this->get_schedule_by_conditions($now_playing_result);
            foreach ($list_result as $k => $v) {
                $data['movie_type_id'] = $v['MoviesMovieType']['movie_type_id'];
                $list_schedule = $this->get_schedule_by_movie_id($v[$this->alias]['id'], $data);
                /*if (empty($list_schedule['date'])) {
                    unset($list_result[$k]);
                } else {
                    $list_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
                }*/
                $list_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
            }
        }
        $list_result = $this->append_domain_list_image_video($list_result);
        $result = array(
            'total' => $total_record,
            'list' => $list_result
        );

/*        if ($data['type'] == "now_playing") {
            $option['conditions'] = $now_playing;

            $now_playing_result = $this->find('all', $option);

            $option['fields'] = array('count(*) AS total_record');
            unset($option['limit']);
            unset($option['offset']);
            $total_record = $this->find('first', $option)[0]['total_record'];

            if (isset($data['get_schedule']) && $data['get_schedule'] == 1) {
                //$now_playing_result = $this->get_schedule_by_conditions($now_playing_result);
                foreach ($now_playing_result as $k => $v) {
                    $list_schedule = $this->get_schedule_by_movie_id($v[$this->alias]['id'], $data);
                    $now_playing_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
                }
            }
            $now_playing_result = $this->append_domain_list_image_video($now_playing_result);
            $result = array(
                'total' => $total_record,
                'list' => $now_playing_result
            );
        } else if ($data['type'] == "coming_soon") {
            if (isset($data['start_date_month']) && !empty($data['start_date_month'])) {
                $coming_soon['MONTH(MoviesMovieType.start_date)'] = $data['start_date_month'];
            }

            $option['conditions'] = $coming_soon;

            $coming_soon_result = $this->find('all', $option);

            $option['fields'] = array('count(*) AS total_record');
            unset($option['limit']);
            unset($option['offset']);
            $total_record = $this->find('first', $option)[0]['total_record'];

            if (isset($data['get_schedule']) && $data['get_schedule'] == 1) {
                //$coming_soon_result = $this->get_schedule_by_conditions($coming_soon_result);
                foreach ($coming_soon_result as $k => $v) {
                    $list_schedule = $this->get_schedule_by_movie_id($v[$this->alias]['id'], $data);
                    $coming_soon_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
                }
            }
            $coming_soon_result = $this->append_domain_list_image_video($coming_soon_result);
            $result = array(
                'total' => $total_record,
                'list' => $coming_soon_result,
            );
        } else if ($data['type'] == "both") {
            $option['conditions'] = $now_playing;
            $now_playing_result = $this->find('all', $option);

            $option_count = $option;
            $option_count['fields'] = array('count(*) AS total_record');
            unset($option_count['limit']);
            unset($option_count['offset']);
            $total_record_now_playing = $this->find('first', $option_count)[0]['total_record'];

            $option['conditions'] = $coming_soon;
            $coming_soon_result = $this->find('all', $option);

            $option_count = $option;
            $option_count['fields'] = array('count(*) AS total_record');
            unset($option_count['limit']);
            unset($option_count['offset']);
            $total_record_coming_soon = $this->find('first', $option_count)[0]['total_record'];


            if (isset($data['get_schedule']) && $data['get_schedule'] == 1) {
                //$coming_soon_result = $this->get_schedule_by_conditions($coming_soon_result);
                foreach ($coming_soon_result as $k => $v) {
                    $list_schedule = $this->get_schedule_by_movie_id($v[$this->alias]['id'], $data);
                    $coming_soon_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
                }
                //$now_playing_result = $this->get_schedule_by_conditions($now_playing_result);
                foreach ($now_playing_result as $k => $v) {
                    $list_schedule = $this->get_schedule_by_movie_id($v[$this->alias]['id'], $data);
                    $now_playing_result[$k]['Schedule']['DateSchedule'] = $list_schedule['date'];
                }
            }
            // append domain to image
            $coming_soon_result = $this->append_domain_list_image_video($coming_soon_result);
            $now_playing_result = $this->append_domain_list_image_video($now_playing_result);

            $result = array(
                'coming_soon' => array(
                    'total' => $total_record_coming_soon,
                    'list' => $coming_soon_result
                ),
                'now_playing' => array(
                    'total' => $total_record_now_playing,
                    'list' =>$now_playing_result
                )
            );
        }*/


        return array(
		    'status' => true,
            'message' => __('retrieve_data_successfully'),
			'params' => $result
        );
	}

    public function get_detail($data) {

        $message = __('retrieve_data_successfully');
		$conditions = array (
			"Movie.id" => $data['movie_id'],
        );

        if (isset($data['movie_type_id']) && !empty($data['movie_type_id'])) {
            $conditions['MoviesMovieType.movie_type_id'] = $data['movie_type_id'];
        }

		$option = array(
			'fields' => array(
				$this->alias.".*",
                'MoviesMovieType.*',
                'MovieType.*',
                "if(Movie.rating = 'III', true, false) as Movie__adult_only"
			),
			'contain' => array(
				'MovieLanguage' => array(
					'fields' => array(
                        'MovieLanguage.language',
						'MovieLanguage.name',
						'MovieLanguage.short_storyline',
						'MovieLanguage.storyline',
                        'MovieLanguage.lang_info',
                        'MovieLanguage.lang_movie',
                        'MovieLanguage.genre',
                        'MovieLanguage.subtitle',
                        'MovieLanguage.director',
					),
					'conditions' => array(
						'language' => $data['language']
					)
				),
//				'MovieType' => array(
//					'fields' => array(
//						'MovieType.name'
//					)
//				),
				'Star' => array(
					'fields' => array(
					    'Star.*'
                    ),
                    'StarLanguage' => array(
                        'conditions' => array (
                            'language' => $data['language']
                        )
                    )
				),
				'MovieTrailer' => array(
					'fields' => array()
				),
			),
			'joins' => array(
				array(
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = '.$this->alias.'.id',
					),
				),
				 array(
				 	'alias' => 'MovieType',
				 	'table' => Environment::read('table_prefix') . 'movie_types',
				 	'type' => 'left',
				 	'conditions' => array(
				 		'MoviesMovieType.movie_type_id = MovieType.id',
				 	),
				 ),
			),
			'conditions' => $conditions,
            'group' => array(
                $this->alias.'.id',
                'MoviesMovieType.movie_type_id'
            )
		);

        $this->virtualFields['adult_only'] = "if(Movie.rating = 'III', true, false)";
        $result = $this->find('first', $option);
        
        //$result = $this->get_schedule_by_conditions($result);

        if (empty($result)) {
            $message = __d('movie', 'movie_not_found');
            goto return_result;
        }

        /*$implode_movie_type = $implode_movie_language = array();

        $implode_movie_type = Hash::extract( $result['MovieType'], "{n}.name" );
        $implode_movie_type = implode(",", $implode_movie_type);

        $implode_movie_language = Hash::extract( $result['MovieLanguage'], "{n}.language" );
        $implode_movie_language = implode(",", $implode_movie_language);

        $result['MovieTypeTotal'] = $implode_movie_type;
        $result['MovieLanguageTotal'] = $implode_movie_language;*/

        $list_schedule = $this->get_schedule_by_movie_id($result[$this->alias]['id'], $data);
        $result['Schedule']['DateSchedule'] = $list_schedule['date'];

        /*foreach ($result['MovieLanguage'] as $kML => $vML) {
            if ($vML['language'] != $data['language']) {
                unset($result['MovieLanguage'][$kML]);
            }
        }*/

		$result = $this->append_domain_list_image_video(array($result));

		// add file star_director

        $star_director = array();
        $director_list = $result[0]['MovieLanguage'][0]['director'];
        $director_list = explode(',', $director_list);

        foreach ($director_list as $k => $v) {
            $temp = array();
            $temp['label'] = __d('movie','director');
            $temp['name'] = trim($v);
            $temp['image'] = '';
            $star_director[] = $temp;
        }

        $star_list = $result[0]['Star'];
        foreach ($star_list as $k => $v) {
            $temp = array();
            $temp['label'] = __d('movie','cast');
            $temp['name'] = '';
            if (isset($v['StarLanguage'][0])) {
                $temp['name'] = trim($v['StarLanguage'][0]['star_first_name']) . ' ' . trim($v['StarLanguage'][0]['star_surname']);
            }
            $temp['image'] = $v['image_url'];
            $star_director[] = $temp;
        }

        $result[0]['StarDirector'] = $star_director;

        return_result:
        return array(
            'status' => true,
            'message' => $message,
            'params' => $result
        );
	}

    public function get_list_feature_movie($data) {

        $conditions = array(
            "Movie.is_feature" => 1
        );

        $option = array(
            'fields' => array(
                $this->alias.".*"
            ),
            'contain' => array(
                'MovieLanguage' => array(
                    'fields' => array(
                        'MovieLanguage.name',
                        'MovieLanguage.short_storyline',
                        'MovieLanguage.storyline',
                        'MovieLanguage.lang_info',
                        'MovieLanguage.lang_movie',
                        'MovieLanguage.genre',
                        'MovieLanguage.subtitle',
                        'MovieLanguage.director',
                    ),
                    'conditions' => array(
                        'language' => $data['language']
                    )
                ),
                'MovieType' => array(
                    'fields' => array(
                        'MovieType.name'
                    )
                ),
                'Star' => array(
                    'fields' => array()
                ),
                'MovieTrailer' => array(
                    'fields' => array()
                ),
                /*'Schedule' => array(
                    'fields' => array(
                        "Schedule.movie_id",
                        "Schedule.movie_type_id"
                    ),
                    'ScheduleDetail' => array(
                        'fields' => array(
                            'ScheduleDetail.id',
                            'ScheduleDetail.schedule_id',
                            'ScheduleDetail.date',
                            'ScheduleDetail.time',
                            'ScheduleDetail.capacity',
                            'ScheduleDetail.attendance_rate',
                        ),
                        'ScheduleDetailLayout' => array(
                            'fields' => array(
                                'ScheduleDetailLayout.id',
                                'ScheduleDetailLayout.schedule_detail_id',
                                'ScheduleDetailLayout.column_number',
                                'ScheduleDetailLayout.row_number',
                                'ScheduleDetailLayout.status',
                                'ScheduleDetailLayout.order_time',
                            )
                        )
                    )
                )*/
            ),
            /*'joins' => array(
                array(
                    'alias' => 'MoviesMovieType',
                    'table' => Environment::read('table_prefix') . 'movies_movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MoviesMovieType.movie_id = '.$this->alias.'.id',
                    ),
                ),
                 array(
                 	'alias' => 'MovieType',
                 	'table' => Environment::read('table_prefix') . 'movie_types',
                 	'type' => 'left',
                 	'conditions' => array(
                 		'MoviesMovieType.movie_type_id = MovieType.id',
                 	),
                 ),
            ),*/
            'conditions' => $conditions,
        );

        $result = $this->find('all', $option);

        $result = $this->append_domain_list_image_video($result);

        return array(
            'status' => true,
            'message' => __('retrieve_data_successfully'),
            'params' => $result
        );
    }

    public function get_schedule_detail_layout($data) {
        $message = __('retrieve_data_successfully');
        $status = true;
        $data_result = array();
        $total_row = $total_column = 0;
        $hall_code = '';

        $conditions = array(
            "ScheduleDetailLayout.schedule_detail_id" => $data['schedule_detail_id']
        );
        $objScheduleDetailLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
        $option = array(
            'fields' => array(
                'ScheduleDetailLayout.*',
                'Hall.*'
            ),
            'contain' => array(
            ),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = ScheduleDetailLayout.schedule_detail_id',
                    ),
                ),
                array(
                    'alias' => 'Schedule',
                    'table' => Environment::read('table_prefix') . 'schedules',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.id = ScheduleDetail.schedule_id',
                    ),
                ),
                array(
                    'alias' => 'Hall',
                    'table' => Environment::read('table_prefix') . 'halls',
                    'type' => 'left',
                    'conditions' => array(
                        'Hall.id = Schedule.hall_id',
                    ),
                ),
            ),
            'conditions' => $conditions,
        );

        $result = $objScheduleDetailLayout->find('all', $option);

        $arr_label = array();
        if (empty($result)) {
            $status = false;
            $message = __d('movie','layout_not_found');
            goto return_result;
        }

        
        if ($result[0]['Hall']['code'] == 'House 1') {
            $arr_label = array(
                array(
                    'col' => -1,
                    'row' => -1,
                    'name' => 'Entrance',
                    'label_for' => 'entrance_way'
                ),
                array(
                    'col' => -1,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => -1,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 4,
                    'name' => 'E',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 5,
                    'name' => 'F',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 6,
                    'name' => 'Exit',
                    'label_for' => 'exit_way'
                ),

                array(
                    'col' => 8,
                    'row' => -1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => 8,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 8,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 8,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => 8,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 8,
                    'row' => 4,
                    'name' => 'E',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 8,
                    'row' => 5,
                    'name' => 'F',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 8,
                    'row' => 6,
                    'name' => null,
                    'label_for' => null
                ),
            );
        } else if ($result[0]['Hall']['code'] == 'House 2') {
            $arr_label = array(
                array(
                    'col' => -1,
                    'row' => -1,
                    'name' => 'Entrance',
                    'label_for' => 'entrance_way'
                ),
                array(
                    'col' => -1,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => -1,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 4,
                    'name' => 'Exit',
                    'label_for' => 'exit_way'
                ),

                array(
                    'col' => 6,
                    'row' => -1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => 6,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 6,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 6,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => 6,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 6,
                    'row' => 4,
                    'name' => null,
                    'label_for' => null
                ),
            );
        } else if ($result[0]['Hall']['code'] == 'House 3') {
            $arr_label = array(
                array(
                    'col' => -1,
                    'row' => -1,
                    'name' => 'Entrance',
                    'label_for' => 'entrance_way'
                ),
                array(
                    'col' => -1,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => -1,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 4,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => -1,
                    'row' => 5,
                    'name' => 'E',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 6,
                    'name' => 'F',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 7,
                    'name' => null,
                    'label_for' => null
                ),

                array(
                    'col' => 10,
                    'row' => -1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => 10,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 10,
                    'row' => 1,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 10,
                    'row' => 2,
                    'name' => 'C',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => 10,
                    'row' => 3,
                    'name' => 'D',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 10,
                    'row' => 4,
                    'name' => 'Exit',
                    'label_for' => 'exit_way'
                ),
                array(
                    'col' => 10,
                    'row' => 5,
                    'name' => 'E',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 10,
                    'row' => 6,
                    'name' => 'F',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 10,
                    'row' => 7,
                    'name' => null,
                    'label_for' => null
                ),
            );
        } else if ($result[0]['Hall']['code'] == 'VIP House') {
            $arr_label = array(
                array(
                    'col' => -1,
                    'row' => -1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => -1,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => -1,
                    'row' => 1,
                    'name' => 'Entrance',
                    'label_for' => 'entrance_way'
                ),
                array(
                    'col' => -1,
                    'row' => 2,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => -1,
                    'row' => 3,
                    'name' => null,
                    'label_for' => null
                ),

                array(
                    'col' => 6,
                    'row' => -1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => 6,
                    'row' => 0,
                    'name' => 'A',
                    'label_for' => 'seat'
                ),
                array(
                    'col' => 6,
                    'row' => 1,
                    'name' => null,
                    'label_for' => null
                ),
                array(
                    'col' => 6,
                    'row' => 2,
                    'name' => 'B',
                    'label_for' => 'seat'
                ),

                array(
                    'col' => 6,
                    'row' => 3,
                    'name' => null,
                    'label_for' => null
                ),
            );
        }

        $alphabet = range('A', 'Z');

        foreach ($result as $k => $v ) {
            if ($v['ScheduleDetailLayout']['is_blocked_seat']) {
                $result[$k]['ScheduleDetailLayout']['status'] = 2;
            }
        }

        foreach ($result as $k=>$v ) {
            if ($v['ScheduleDetailLayout']['column_number'] > $total_column) {
                $total_column = $v['ScheduleDetailLayout']['column_number'];
            }
            if ($v['ScheduleDetailLayout']['row_number'] > $total_row) {
                $total_row = $v['ScheduleDetailLayout']['row_number'];
            }
            $data_result[$v['ScheduleDetailLayout']['id']] = $v['ScheduleDetailLayout'];
            $data_result[$v['ScheduleDetailLayout']['id']]['hall_code'] = $v['Hall']['code'];

            $hall_code = $v['Hall']['code'];
        }

        $total_column = $total_column + 1;
        $total_row = $total_row + 1;

        return_result:
        return array(
            'status' => $status,
            'message' => $message,
            'params' => array(
                'info' => array(
                    'total_row' => $total_row,
                    'total_column' => $total_column,
                    'hall_code' => $hall_code
                ),
                'label' => $arr_label,
                'list' => array_values($data_result)
            )
        );
    }

    public function get_movie_by_schedule_date_time($data) {

        if (isset($data['date']) && ! empty($data['date'])) {
            $conditions['ScheduleDetail.date'] = $data['date'];
        }

        if (isset($data['time']) && ! empty($data['time'])) {
            $conditions['ScheduleDetail.time'] = $data['time'];
        }

        $conditions["Schedule.movie_id"] = $data['movie_id'];

        $objScheduleDetailLayout = ClassRegistry::init('Movie.Schedule');
        $option = array(
            'fields' => array(
                'Movie.*',
                'MovieType.*',
                'Schedule.*',
                'ScheduleDetail.*'
            ),
            'contain' => array(

            ),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = Schedule.id',
                    ),
                ),
                array(
                    'alias' => 'Movie',
                    'table' => Environment::read('table_prefix') . 'movies',
                    'type' => 'left',
                    'conditions' => array(
                        'Movie.id = Schedule.movie_id',
                    ),
                ),
                array(
                    'alias' => 'MovieType',
                    'table' => Environment::read('table_prefix') . 'movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieType.id = Schedule.movie_type_id',
                    ),
                )
            ),
            'conditions' => $conditions,
        );

        $result = $objScheduleDetailLayout->find('all', $option);
        return array(
            'status' => true,
            'message' => 'retrieve_data_successfully',
            'params' => $result
        );
    }

	public function append_domain_list_image_video($list_movie) {
        $url_img = Environment::read('web.url_img');
        foreach ($list_movie as $k=>$v) {
            $poster = $v[$this->alias]['poster'];
            $list_movie[$k][$this->alias]['poster'] = $url_img . $poster;

            if (isset($v[$this->movie_trailer_model]) && count($v[$this->movie_trailer_model]) > 0) {
                foreach ($v[$this->movie_trailer_model] as $i => $j) {
                    $trailer = $j['video_path'];
                    $list_movie[$k][$this->movie_trailer_model][$i]['video_path'] = $url_img . $trailer;
                    $trailer_poster = $j['poster_path'];
                    $list_movie[$k][$this->movie_trailer_model][$i]['poster_path'] = $url_img . $trailer_poster;
                }
            }

            if (isset($v[$this->star_model]) && count($v[$this->star_model]) > 0) {
                foreach ($v[$this->star_model] as $i => $j) {
                    $star_image = $j['image_url'];
                    $list_movie[$k][$this->star_model][$i]['image_url'] = $url_img . $star_image;
                }
            }
        }
        return $list_movie;
    }

    public function get_schedule_by_conditions($list_movie, $date = null) {
        $objSchedule = ClassRegistry::init('Movie.Schedule');

        foreach ($list_movie as $kM => $vM) {
            $option_schedule = array(
                'fields' => array(
                    'Schedule.*',
                    'ScheduleDetail.*',
                    'ScheduleDetailTicketType.*',
                    'TicketType.*'
                ),
                'joins' => array(
                    array(
                        'alias' => 'ScheduleDetail',
                        'table' => Environment::read('table_prefix') . 'schedule_details',
                        'type' => 'left',
                        'conditions' => array(
                            'ScheduleDetail.schedule_id = Schedule.id',
                        ),
                    ),
                    array(
                        'alias' => 'ScheduleDetailTicketType',
                        'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
                        'type' => 'left',
                        'conditions' => array(
                            'ScheduleDetailTicketType.schedule_detail_id = Schedule.id',
                        ),
                    ),
                    array(
                        'alias' => 'TicketType',
                        'table' => Environment::read('table_prefix') . 'ticket_types',
                        'type' => 'left',
                        'conditions' => array(
                            'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
                        ),
                    ),
                ),
//                'contain' => array(
//                    'ScheduleDetail' => array(
//                        'fields' => array(
//                            'ScheduleDetail.date',
//                            'ScheduleDetail.time'
//                        ),
//                        'conditions' => array(
//                            //'ScheduleDetail.date' => $date
//                        ),
//                        'ScheduleDetailTicketType' => array(
//                            'TicketType' => array(
//
//                            )
//                        )
//                    )
//                ),
                'conditions' => array(
                    'Schedule.movie_id' => $vM[$this->alias]['id'],
                    'TicketType.is_main' => 1
                ),
                'group' => array(
                    'ScheduleDetail.date',
                    'ScheduleDetail.time',
                    'ScheduleDetailTicketType.price'
                )
            );

            $schedule_list = $objSchedule->find('all', $option_schedule);
            $time = $time_list_temp = array();
            $date = $date_list_temp = array();

            foreach ($schedule_list as $kS => $vS) {
//                $time_temp = Hash::extract( $v['ScheduleDetail'], "{n}.time" );
//                $time_list_temp = array_merge($time_list_temp, $time_temp);
//                $time_list_temp = array_unique($time_list_temp);
//
//                $date_temp = Hash::extract( $v['ScheduleDetail'], "{n}.date" );
//                $date_list_temp = array_merge($date_list_temp, $date_temp);
//                $date_list_temp = array_unique($date_list_temp);

//                foreach ($v['ScheduleDetail'] as $i=>$j) {
//                    $time_list_temp[$j['time']] = null;
//
//                    foreach ($j['ScheduleDetailTicketType'] as $m => $n) {
//                        if ($n['TicketType']['is_main'] == 1) {
//                            $time_list_temp[$j['time']] = $n['price'];
//                        }
//                    }
//                }
                $key_time = $vS['ScheduleDetail']['time'] . '-' . $vS['ScheduleDetailTicketType']['price'];
                $time_list_temp[$key_time]['time'] = $vS['ScheduleDetail']['time'];
                $time_list_temp[$key_time]['price'] = $vS['ScheduleDetailTicketType']['price'];
//                $date_list_temp[$vS['ScheduleDetail']['date']] = date('Y-m-d',strtotime($vS['ScheduleDetail']['date']));
                $key_date = date('Y-m-d',strtotime($vS['ScheduleDetail']['date']));
                $date_list_temp[$key_date] = $time_list_temp;
            }

            //$time = array_values($time_list_temp);
            foreach ($date_list_temp as $k=>$v) {
                $date[$k] = array_values($v);
            }

//            foreach ($time_list_temp as $i => $j) {
//                $time[] = array(
//                    'time' => $j
//                );
//            }

//            foreach ($date_list_temp as $i => $j) {
//                $date[] = array(
//                    'date' => $j
//                );
//            }

            $list_movie[$kM]['Schedule']['TimeSchedule'] = $time;
            $list_movie[$kM]['Schedule']['DateSchedule'] = $date;
        }
        return $list_movie;
    }

    public function get_schedule_by_movie_id($movie_id, $data) {
        $time = date( 'H:i' );
        $now = date('Y-m-d');

        $objSchedule = ClassRegistry::init('Movie.Schedule');

        $option_schedule = array(
            'fields' => array(
                'Schedule.*',
                'ScheduleDetail.*',
                'ScheduleDetailTicketType.*',
                'TicketType.*',
                'group_concat(distinct Schedule.hall_id) as total_hall',
                'group_concat(distinct Schedule.movie_type_id) as total_movie_type'
            ),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = Schedule.id',
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetailTicketType',
                    'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetailTicketType.schedule_detail_id = ScheduleDetail.id',
                    ),
                ),
                array(
                    'alias' => 'TicketType',
                    'table' => Environment::read('table_prefix') . 'ticket_types',
                    'type' => 'left',
                    'conditions' => array(
                        'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
                    ),
                ),
            ),
            'conditions' => array(
                'Schedule.movie_id' => $movie_id,
                'TicketType.is_main' => 1
            ),
            'group' => array(
                /*'ScheduleDetail.date',
                'ScheduleDetail.time',*/
                'ScheduleDetail.id',
                'Schedule.hall_id',
                'Schedule.movie_type_id'
            ),
            'order' => array(
                'ScheduleDetail.date' => 'ASC',
                'ScheduleDetail.time' => 'ASC'
            )
        );

        if (isset($data['movie_type_id']) && !empty($data['movie_type_id'])) {
            $option_schedule['conditions']['Schedule.movie_type_id'] = $data['movie_type_id'];
        }

        if (isset($data['from_date_schedule']) && !empty($data['from_date_schedule'])) {
            $option_schedule['conditions']['ScheduleDetail.date >= '] = $data['from_date_schedule'];
            // $date_filter = date("Y-m-d", strtotime($data['from_date_schedule']));
            // $time_filter = date("H:i", strtotime($data['from_date_schedule']));
            // $option_schedule['conditions']['ScheduleDetail.date'] = $date_filter;
            // $option_schedule['conditions']['ScheduleDetail.time >='] = $time_filter;
        } else {
            $option_schedule['conditions']['ScheduleDetail.date >= '] = $now;
        }

        if (isset($data['date']) && !empty($data['date'])) {
            $option_schedule['conditions']['ScheduleDetail.date'] = $data['date'];
        } else {
            // get all date_schedule
            /*$default_number_of_day_schedule = Environment::read('web.amount_next_day_schedule');
            if (isset($data['amount_next_day_schedule']) && !empty($data['amount_next_day_schedule'])) {
                $default_number_of_day_schedule  = $data['amount_next_day_schedule'];
            }
            $option_schedule['conditions']['ScheduleDetail.date >= '] = date('Y-m-d');
            $option_schedule['conditions']['ScheduleDetail.date < '] = date('Y-m-d', strtotime( '+'. $default_number_of_day_schedule. ' day' ));*/
        }

        $schedule_list = $objSchedule->find('all', $option_schedule);
        $time_list = $time_list_temp = array();
        $date_list = $date_list_temp = array();

        foreach ($schedule_list as $kS => $vS) {

            $date = date('Y-m-d',strtotime($vS['ScheduleDetail']['date']));
            /*$key = $vS['ScheduleDetail']['date'] . '-' . $vS['ScheduleDetail']['time']
                        . '-' . $vS['Schedule']['hall_id'] . '-' .$vS['Schedule']['movie_type_id'];

            $time_list_temp[$key]['time'] = $vS['ScheduleDetail']['time'];
            $time_list_temp[$key]['price'] = $vS['ScheduleDetailTicketType']['price'];
            $time_list_temp[$key]['hall'] = $vS['Schedule']['hall_id'];
            $time_list_temp[$key]['movie_type_id'] = $vS['Schedule']['movie_type_id'];
            $time_list_temp[$key]['date'] = $date;*/

            $time_list_temp['time'] = date('H:i',strtotime($vS['ScheduleDetail']['time']));
            $time_list_temp['price'] = $vS['ScheduleDetailTicketType']['price'];
            $time_list_temp['hall'] = $vS['Schedule']['hall_id'];
            $time_list_temp['movie_type_id'] = $vS['Schedule']['movie_type_id'];
            $time_list_temp['schedule_detail_id'] = $vS['ScheduleDetail']['id'];

            $attendance_rate = $vS['ScheduleDetail']['attendance_rate'];
            if ($attendance_rate <= Environment::read('web.min_attendance_rate')) {
                $color_code = '#008000';
            } else if ($attendance_rate > Environment::read('web.min_attendance_rate') && $attendance_rate < Environment::read('web.max_attendance_rate')) {
                $color_code = '#FFFF00';
            } else if ($attendance_rate >= Environment::read('web.max_attendance_rate')) {
                $color_code = '#FF0000';
            }
            $time_list_temp['color_code'] = $color_code;
            //$time_list_temp[$key]['date'] = $date;

            $available_seat = true;
            if ($attendance_rate == 100) {
                $available_seat = false;
            }
            $time_list_temp['available_seat'] = $available_seat;

//            if (isset($data['date']) && !empty($data['date'])) {
//                if ($time_list_temp['time'] < $time) {
//                    // No need to display passed timeslots
//                    continue;
//                }
//            } else {
                if (
                    ($date == $now) && ($time_list_temp['time'] < $time)
                ) {
                    continue;
                }
//            }

            $date_list[$date][] = $time_list_temp;
        }


        /*foreach ($time_list_temp as $k=>$v) {
            $key_date = $v['date'];
            unset($v['date']);
            $date_list[$key_date][] = $v;
        }*/
        // get color code

        $objScheduleDetailLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');

        /*foreach ($date_list as $kDate=>$vDate) {
            foreach ($vDate as $kTime=>$vTime) {
                $conditions = array(
                    "ScheduleDetailLayout.schedule_detail_id" => $vTime['schedule_detail_id']
                );

                $option = array(
                    'fields' => array(
                        'ScheduleDetailLayout.*'
                    ),
                    'contain' => array(),
                    'joins' => array(),
                    'conditions' => $conditions,
                );

                $result_seat = $objScheduleDetailLayout->find('all', $option);

                $total_seat = count($result_seat);
                $seat_available = 0;
                foreach ($result_seat as $kSeat=>$vSeat) {
                    if($vSeat['ScheduleDetailLayout']['status'] == 1) {
                        $seat_available += 1;
                    }
                }
                $percent_seat_available = $seat_available / $total_seat;
                $color_code = '#008000';
                if ($percent_seat_available > 0.8) {
                    $color_code = '#008000';
                } else if ($percent_seat_available > 0.2 && $percent_seat_available < 0.8) {
                    $color_code = '#FFFF00';
                } else if ($percent_seat_available < 0.2) {
                    $color_code = '#FF0000';
                }
                $date_list[$kDate][$kTime]['color_code'] = $color_code;
            }
        }*/

        return array(
            'time' => $time_list,
            'date' => $date_list
        );
    }

    public function get_ticket_type_schedule_list($data){

        $objScheduleDetail = ClassRegistry::init('Movie.ScheduleDetail');

        $conditions = array(
            'ScheduleDetail.id' => $data['schedule_detail_id'],
            'TicketType.enabled' => true
        );
        $ticketTypeSchedule = $objScheduleDetail->find('all', array(
            'fields' => array(
                'ScheduleDetail.id',
                'TicketType.name',
                'TicketType.id',
                'TicketType.is_main',
                'TicketType.is_disability',
                'ScheduleDetailTicketType.id',
                'ScheduleDetailTicketType.price',
                'TicketTypeLanguage.*'
            ),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetailTicketType',
                    'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetailTicketType.schedule_detail_id = ScheduleDetail.id'
                    ),
                ),
                array(
                    'alias' => 'TicketType',
                    'table' => Environment::read('table_prefix') . 'ticket_types',
                    'type' => 'left',
                    'conditions' => array(
                        'TicketType.id = ScheduleDetailTicketType.ticket_type_id'
                    ),
                ),
                array(
                    'alias' => 'TicketTypeLanguage',
                    'table' => Environment::read('table_prefix') . 'ticket_type_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'TicketTypeLanguage.ticket_type_id = TicketType.id',
                        'TicketTypeLanguage.language' => $data['language']
                    ),
                ),
            ),
            'conditions' => $conditions,
        ));

        $result = array();
        foreach ($ticketTypeSchedule as $k=>$v) {
            $result[$v['ScheduleDetailTicketType']['id']] = array(
                'ticket_type_id' => $v['ScheduleDetailTicketType']['id'],
                'schedule_detail_id' => $v['ScheduleDetail']['id'],
                'name' => $v['TicketTypeLanguage']['name'],
                'price' => $v['ScheduleDetailTicketType']['price'],
                'is_main' => $v['TicketType']['is_main'],
                'is_disability' => $v['TicketType']['is_disability']
            );
        }
        return array_values($result);
    }

    public function get_list_day_has_schedule($data){
        $default_next_day = 7;
        $now = date( 'Y-m-d' );
        $time = date( 'H:i' );

        $objSchedule = ClassRegistry::init('Movie.Schedule');
        $conditions = array();

        if (isset($data['movie_id']) && !empty($data['movie_id']))
         {
            $conditions['Schedule.movie_id'] = $data['movie_id'];
        }
        if (isset($data['movie_type_id']) && !empty($data['movie_type_id']))
        {
            $conditions['Schedule.movie_type_id'] = $data['movie_type_id'];
        }

        $conditions['ScheduleDetail.date >= '] = $now;

        $list = $objSchedule->find('all', array(
            'fields' => array(
                'ScheduleDetail.date',
                'ScheduleDetail.time'
            ),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = Schedule.id'
                    ),
                )
            ),
            'conditions' => $conditions,
//            'group' => array(
//                'ScheduleDetail.date',
//            ),
            'order' => array(
                'ScheduleDetail.date' => 'ASC'
            ),
            //'limit' => $default_next_day
        ));

        $result = array();

        //$result = Hash::extract( $list, "{n}.ScheduleDetail.date" );
        foreach ($list as $k=>$v) {
            $date_temp = date('Y-m-d', strtotime($v['ScheduleDetail']['date']));
            $time_temp = date('H:i',strtotime($v['ScheduleDetail']['time']));
            if (
                ($date_temp == $now) && ($time_temp < $time)
            ) {
                continue;
            }
            $result[$date_temp] = $date_temp;
            /*$result[$k] = array(
                'date' => $date_temp,
            );*/
        }
        return array_values($result);
    }

    public function get_trailer_by_id($data){
        $result = array();
        $obj = ClassRegistry::init('Movie.MovieTrailer');
        $conditions = array();
        $conditions['MovieTrailer.id'] = $data['trailer_id'];

        $list = $obj->find('first', array(
            'fields' => array(
            ),
            'joins' => array(
            ),
            'conditions' => $conditions,
        ));

        if (isset($list['MovieTrailer'])) {
            $list['MovieTrailer']['poster_path'] = (isset($list['MovieTrailer']['poster_path']) && !empty($list['MovieTrailer']['poster_path'])) ? Environment::read('web.url_img') . $list['MovieTrailer']['poster_path'] : '';
            $list['MovieTrailer']['video_path'] = (isset($list['MovieTrailer']['video_path']) && !empty($list['MovieTrailer']['video_path'])) ? Environment::read('web.url_img') . $list['MovieTrailer']['video_path'] : '';
            $result = $list['MovieTrailer'];
        }

        return $result;
    }


    public function get_movie_name($list_id) {
        $objMovieLanguage = ClassRegistry::init('Movie.MovieLanguage');
        $option = array(
            'fields' => array(
                'id',
            ),
            'joins' => array(

            ),
            'contain' => array(
                'MovieLanguage' => array(
                    'fields' => array (
                        'name',
                        'language'
                    )
                )
            ),
            'conditions' => array(
                'Movie.id' => $list_id
            ),
        );
        $result = $this->find('all', $option);

        $result_format = array();

        foreach ($result as $k => $v) {
            foreach ($v['MovieLanguage'] as $i => $j) {
                $result_format[$v['Movie']['id']][$j['language']]['name'] = $j['name'];
            }
        }
        return $result_format;
    }
}
