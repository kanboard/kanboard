<?= $this->calendar->render(
        $this->url->href('CalendarController', 'user', array('user_id' => $user['id'])),
        $this->url->href('CalendarController', 'save')
) ?>
