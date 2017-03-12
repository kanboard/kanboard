<?= $this->calendar->render(
    $this->url->href('CalendarController', 'userEvents', array('user_id' => $user['id'])),
    $this->url->href('CalendarController', 'save')
) ?>
