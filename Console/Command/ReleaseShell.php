<?php
    class ReleaseShell extends AppShell {
        public $uses = array(
            'Movie.ScheduleDetailLayout', 
            'Setting.Setting', 
        );

        private $defaultExpireTime = 15;
        private $settingSlug = 'seat-release';

        public function startup() {
            $option = array(
                'fields' => 'Setting.*',
                'conditions' => array(
                    'slug' => $this->settingSlug,
                    'enabled' => 1,
                )
            );

            $result = $this->Setting->find('first', $option);
            $this->expireTime = isset($result['Setting']['value']) ?: $this->defaultExpireTime;

            parent::startup();
        }
        
        public function main() {
            
            $option = array(
                'conditions' => array(
                    'status >' => 1,
                    'status <>' => 3,
                    'order_time <' => date('Y-m-d H:i:s', strtotime('-'.$this->expireTime.' minutes')),
                )
            );

            $rsvp = $this->ScheduleDetailLayout->find('all', $option);

            foreach($rsvp as $seat) {
                $seat['ScheduleDetailLayout']['status'] = 1;
                $this->ScheduleDetailLayout->save($seat['ScheduleDetailLayout']);
            }
        }
    }
?>