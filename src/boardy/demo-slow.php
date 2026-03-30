<?php
sleep(2);
echo json_encode([
    'result' => 'done',
    'pid'    => getmypid(),
    'time'   => date('H:i:s')
]);
