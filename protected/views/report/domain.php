Домен <?= $params['host']->value ?><? if ($params['host']->value != $domain->host()): 
?> (не введенный вами поддомен - <?= $domain->host() ?>)<? endif;
if ($params['created']->value): ?> зарегистрирован: <?= TxtHelper::DateFormat( $params['created']->value ) ?>, возраст домена: <?= TxtHelper::LivePeriod( $params['created']->value ) ?>,<? 
else: ?> зарегистрирован: дата отсутствует,<? endif; 
if ($params['expire']->value): ?> очередная оплата домена: до <?= TxtHelper::DateFormat( $params['expire']->value ) ?><? endif; ?>

Name Server (сервер имен): <?= $params['ns']->value ?>