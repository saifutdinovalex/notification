<?
use \backend\modules\notifications\widgets\NotificationsWidget;

NotificationsWidget::widget([
    'theme' => NotificationsWidget::THEME_GROWL,
    'clientOptions' => [
        'location' => 'br',
        'id' => 'notif2',
    ],
    'counters' => [
        '.notifications-header-count',
        '.notifications-icon-count',
    ],
    //'markAllSeenSelector' => '#notification-seen-all',
    'listSelector' => '.notifications',
    'listItemTemplate' =>
        '<div class="row">'.
            '<div class="col-xs-10">'.
                '<div class="title">{title}</div>'.
                '<div class="description">{description}</div>'.
                '<div class="timeago">{timeago}</div>'.
            '</div>'.
            '<div class="col-xs-2">'.
                '<div class="actions pull-right">{seen}</div>'.
            '</div>'.
        '</div>',
    'pollInterval' => 300000,
    'delay' => 600000,
    'nameBellClass' => [],
]);
?>