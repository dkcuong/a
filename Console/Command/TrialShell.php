<?php
    class TrialShell extends AppShell {
        public $uses = array(
            'Pos.TicketType', 
        );

        public function startup() {
            $collection = new ComponentCollection();
            $this->Email = $collection->load('Email');
            $this->log_module = "DoScheduledJobs";

            parent::startup();
        }
        
        public function main() {
            CakeLog::write($this->log_module, "-------- Cronjob DoScheduledJobs START in " . date('Y-m-d H:i:s') . " -------- \r\n");
            
			// temporarily using the "development" DB config
			Environment::set('development'); // developmet/production

            $proc = 'do_trial_again';
            if ((method_exists($this, $proc)) && is_callable(array($this, $proc))) {
                $proc_result = call_user_func(array($this, $proc));
                if ($proc_result['valid']) {
                    pr('proc is completed successfully');
                } else {
                    pr('proc is failed to complete');
                }
                
            } else {
                pr(sprintf($this->method_not_found, $cronjob['Cronjob']['name'], $proc));
            }

            pr('job is finished');

            CakeLog::write($this->log_module, "-------- Cronjob END in " . date('Y-m-d H:i:s') . " -------- \r\n");
        }

        public function do_trial() {

            $valid = false;
            $message = '';

            $option = array(
                'conditions' => array('enabled' => 1)
            );

            $data_ticket_type = $this->TicketType->find('all', $option);

            foreach($data_ticket_type as $ticket_type) {
                pr($ticket_type['TicketType']['code']);
            }

            $valid = true;
            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }

        public function do_trial_again() {

            $valid = false;
            $message = '';

            for($i = 0; $i < count(1000); $i++) {
                sleep(10);
            }

            pr('looping is done');

            $valid = true;
            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }
 
    }
?>