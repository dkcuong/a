<?php
    use SebastianBergmann\RecursionContext\Exception;
    /**
     * Custom Component for Email notification
     * 
     * @author Ricky Lam @ VTL
     */
	App::uses('Component', 'Controller');
	App::uses('CakeEmail', 'Network/Email');

	class EmailComponent extends Component {

        private $log_module = "Email";
        /**
         * Component callbacks
         */
		/**
		 * Is called before the controller’s beforeFilter method.
		 * 
		 */
		public function initialize(Controller $controller) {
			parent::initialize($controller);

		}

		/**
		 * Is called after the controller’s beforeFilter method 
		 * but before the controller executes the current action handler.
		 * 
		 */
		public function startup(Controller $controller) {
			parent::startup($controller);
		}

		/**
		 * Is called after the controller executes the requested action’s logic,
		 * but before the controller’s renders views and layout.
		 * 
		 */
		public function beforeRender(Controller $controller) {
			parent::beforeRender($controller);
		}

		/**
		 * Is called before output is sent to the browser.
		 * 
		 */
		public function shutdown(Controller $controller) {
			parent::shutdown($controller);
		}

		/**
		 * Is called before output is sent to the browser.
		 * 
		 */
		public function beforeRedirect(Controller $controller, $url = "", $status = null, $exit = true) {
			parent::beforeRedirect($controller, $url, $status, $exit);
        }

        /**
         * $to_emails, $subject, $template, $data
         */
        public function send($to_emails, $subject, $template, $data){
            try{
                $email = new CakeEmail('gmail');
                
                $email->template($template)
                    ->to($to_emails)
                    ->viewVars($data)
                    ->subject($subject);

                $email->send();

                return array(
                    'status' => true
                );
            } catch(SocketException $e) {
                CakeLog::write($this->log_module, 
                    "Send email to \"" . (is_array($to_emails) ? implode(', ', $to_emails) : $to_emails) . "\" with template \"" . 
                        $template . "\" Failed because " . $e->getMessage());
                return array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
            }
        }

        /**
         * $to_emails, $subject, $template, $data
         */
        public function send_report($data){
            try{
                $email = new CakeEmail('gmail');
                $data_email = array(
                    'report_name' => $data['name'],
                    'is_from_to' => $data['is_from_to'],
                    'from' => $data['from'],
                    'to' => $data['to'],
                );

                if($data['link']){
                    $data_email['link'] = $data['link'];
                    $email->template('report_auto')
                        ->to($data['emails'])
                        ->viewVars($data_email)
                        ->subject(__d('report', 'subject_report_auto'));
                }else{
                    $email->template('report_auto_attactment')
                        ->to($data['emails'])
                        ->viewVars($data_email)
                        ->subject(__d('report', 'subject_report_auto'));

                    $email->attachments(array(
                        $data['file_name'] => array(
                            'file' => WWW_ROOT . 'report' . DS . $data['file_name'],
                            'mimetype' => 'application/vnd.ms-excel',
                            'contentId' => $data['UID']
                        )
                    ));
                }

                $email->send();

                return array(
                    'status' => true
                );
            } catch(SocketException $e) {
                CakeLog::write($data['log_module'], 
                    "Send email to \"" . (is_array($data['emails']) ? implode(', ', $data['emails']) : $data['emails']) . "\" with template \"" . 
                        " 'report_auto' Failed because " . $e->getMessage());

                return array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
            }
        }
	}
