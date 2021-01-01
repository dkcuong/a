<?php
App::uses('DashboardAppModel', 'Dashboard.Model');

class Dashboard extends DashboardAppModel {
	public $useTable = false;
	public $actsAs = array('Containable');

	public function get_student_absent_rate($param_type, $param_value) {
		$objModel = ClassRegistry::init('Route.RouteSchedule');
		$model = $objModel->alias;

		// $param_type = 'M';
		switch($param_type) {
			case 'Y':
				//for yearly, report always started from this year and the data will be taken 12 year back
				$sql_qualifier = 'YEAR(a.school_date) ';
				$sql_select = $sql_qualifier;

				$sqlwhere = 'a.school_date < now()';
				$limit = 'limit 12';
			break;
			case 'M':
				//for monthly, user should provide the beginning date from that first month in that year, 
				//but by default, it will be this year
				$sql_qualifier = 'MONTH(a.school_date) ';
				$sql_select = "DATE_FORMAT(a.school_date, '%Y-%m') ";

				$date_start = date('Y', strtotime($param_value)).'-01-01';
				$date_end = date('Y', strtotime($param_value . " +1 years")).'-01-01';
				$sqlwhere = "a.school_date >= '" . $date_start . "' and a.school_date < '" . $date_end . "' ";
			break;
			case 'D':
				$sql_qualifier = 'DATE(a.school_date) ';
				$sql_select = $sql_qualifier;

				$date_start = date('Y-m', strtotime($param_value)).'-01';
				$date_end = date('Y-m', strtotime($param_value . " +1 months")).'-01';
				$sqlwhere = "a.school_date >= '" . $date_start . "' and a.school_date < '" . $date_end . "' ";
			break;
		}

		$prefix = Environment::read('database.prefix');

		$sqlstr = 	"select " . $sql_select . " as label, (sum(COALESCE(b.absent, 0))/coalesce(count(b.id), 1) * 100) value " .
					"from " . $prefix . "route_schedules a " .
							"left join " . $prefix . "trip_records b on b.route_schedule_id = a.id " .
					"where " . $sqlwhere . 
					"group by " . $sql_qualifier .
					"order by label";
		$result_data = $objModel->query($sqlstr);

		$result = array();

		foreach($result_data as $data) {
			$result['labels'][] = $data[0]['label'];
			$result['data'][] = $data[0]['value'];
		}

		return array(
            'render_datas' => $result,
            'updated' => __d('dashboard', 'as_of') . " " . date('Y-m-d H:i:s')
        );
	}
}
