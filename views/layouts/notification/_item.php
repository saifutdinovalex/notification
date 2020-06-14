<li class="dropdown notification-dropdown <?=$data['li_class']?>" data-name="<?=$data['li_class']?>" data-array_key="<?=$data['array_key']?>">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="<?=$data['icon']?>"></i>
        <span class="label <?=$data['color_count']?> notifications-icon-count">0</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header"> <?= \Yii::t('admin','You have notifications')?> - <span class="notifications-header-count">0</span> </li>
        <li>
            <ul class="menu">
                <div class="notifications"></div>
            </ul>
        </li>
        <li class=""><a href="<?=$data['href_li']?>"> <?=Yii::t('admin', 'View all') ?></a>
            <?if($data['button_seen_all']):?>
                / <a href="#" class="notification-seen-all"><?=\Yii::t('admin','Mark all as seen')?></a>
            <?endif;?>
     </li>
    </ul>
</li>