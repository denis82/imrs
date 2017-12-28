<div class="row-fluid">        
    <div class="span12">
        <div class="portlet-body">            
            <div class="row-fluid">
                <div class="span12">
                    <div class="clearfix">
                        <h2>Результат анализа <?= $hostinfo->host; ?> от <?= $hostinfo->timestamp; ?></h2>
                        <table class="items table table-striped">    
                            <thead>
                                <tr>
                                    <th>Тип проверки</th>
                                    <th>Результат</th>
                                    <th>Источник</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>Домен зарегистрирован:</td>
                                <td><?= $hostinfo->created ?></td>
                                <td>nic.ru</td>
                            </tr>
                            <tr>
                                <td>Домен оплачен до:</td>
                                <td><?= $hostinfo->paid ?></td>
                                <td>nic.ru</td>
                            </tr>
                            <tr>
                                <td>Регистратор домена:</td>
                                <td><?= $hostinfo->registrar ?></td>
                                <td>nic.ru</td>
                            </tr>    
                            <tr>
                                <td>NS-серверы:</td>
                                <td>
                                    <?= $hostinfo->nsservers; ?>
                                </td>
                                <td>nic.ru</td>
                            </tr>
                            <tr>
                                <td>АйПи сайта:</td>
                                <td><?= $hostinfo->ip ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Хостинг сайта:</td>
                                <td><?= $hostinfo->hoster ?></td>
                                <td>http://ipinfodb.com</td>
                            </tr>
                            <tr>
                                <td>Время ответа главной страницы сайта:</td>
                                <td><?= $hostinfo->time ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Обработка ошибки 404:</td>
                                <td><?= ($hostinfo->error404) ? "да" : "нет" ?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Файл инструкций для поисковых систем robots.txt:</td>
                                <td><?= ($hostinfo->robots) ? "да" : "нет" ?></td>
                                <td></td>
                            </tr>    
                            <tr>
                                <td>Карта сайта:</td>
                                <td><?= ($hostinfo->sitemap) ? 'есть <a href="' . $hostinfo->sitemap . '">посмотреть</a>' : "нет"; ?></td>
                                <td></td>
                            </tr>    
                            <tr>
                                <td>Предположение о системе управления сайтом (CMS):</td>
                                <td><?= $hostinfo->cms ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Заголовок (Title):</td>
                                <td><?= $hostinfo->title ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Мета-тег description:</td>
                                <td><?= $hostinfo->description ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Мета-тег keywords:</td>
                                <td><?= $hostinfo->keywords ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Заголовки H1-H6 на главной странице:</td>
                                <td><?= ($hostinfo->h1h6) ? "присутствуют" : "нет" ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Alt и title картинок на главной странице:</td>
                                <td><?= ($hostinfo->alts) ? "присутствуют" : "нет" ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>ТИЦ:</td>
                                <td><?= $hostinfo->tic ?></td>
                                <td>http://bar-navig.yandex.ru/</td>
                            </tr>   
                            <tr>
                                <td>PageRank:</td>
                                <td><?= $hostinfo->pr ?></td>
                                <td>http://toolbarqueries.google.com</td>
                            </tr>   
                            <tr>
                                <td>Яндекс-каталог:</td>
                                <td><?= ($hostinfo->yac) ? "зарегистрирован" : "не зарегистрирован" ?></td>
                                <td>http://yaca.yandex.ru/</td>
                            </tr>   
                            <tr>
                                <td>Яндекс-метрика:</td>
                                <td><?= ($hostinfo->yam) ? "подключена" : "нет" ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Яндекс-вебмастер:</td>
                                <td><?= ($hostinfo->yaw) ? "подключена" : "код подтверждения не найден" ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Google-analytics:</td>
                                <td><?= ($hostinfo->ga) ? "подключена" : "код аналитики не установлен" ?></td>
                                <td></td>
                            </tr>   
                            <tr>
                                <td>Google-webmaster:</td>
                                <td><?= ($hostinfo->gw) ? "подключена" : "код подтверждения не найден" ?></td>
                                <td></td>
                            </tr>   

                            <tr>
                                <td>Google-webmaster:</td>
                                <td><?= ($hostinfo->gw) ? "подключена" : "код подтверждения не найден" ?></td>
                                <td></td>
                            </tr> 
                            <tr>
                                <td>Ежемесячно просмотров страниц:</td>            
                                <td><?= $hostinfo->limp; ?></td>
                                <td>http://counter.yadro.ru/logo;skipper.su?29.1</td>
                            </tr>
                            <tr>
                                <td>Ежемесячно уникальный посетителей:</td>            
                                <td><?= $hostinfo->limv; ?></td>
                                <td>http://counter.yadro.ru/logo;skipper.su?29.1</td>
                            </tr>
                            <tr>
                                <td>Ежедневно просмотров страниц:</td>            
                                <td><?= $hostinfo->lidp; ?></td>
                                <td>http://counter.yadro.ru/logo;skipper.su?29.1</td>
                            </tr>
                            <tr>
                                <td>Ежедневно уникальных посетителей:</td>            
                                <td><?= $hostinfo->lidv; ?></td>
                                <td>http://counter.yadro.ru/logo;skipper.su?29.1</td>
                            </tr>
                            <tr>
                                <td>Страницы в индексе:</td>
                                <td><?= $hostinfo->index_count; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>   
                            <tr>
                                <td>Дата индексации:</td>
                                <td><?= $hostinfo->index_date; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>   
                            <tr>
                                <td>Зеркала домена:</td>
                                <td><?= $hostinfo->mr_sites; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>    
                            <tr>
                                <td>Сайты на том же IP:</td>
                                <td><?= $hostinfo->ip_sites; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>    
                            <tr>
                                <td>Доноры:</td>
                                <td><?= $hostinfo->din; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>         
                            <tr>
                                <td>Доноры уровень 1:</td>
                                <td><?= $hostinfo->din_l1; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>         
                            <tr>
                                <td>Доноры уровень 2:</td>
                                <td><?= $hostinfo->din_l2; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>         
                            <tr>
                                <td>Доноры уровень 3:</td>
                                <td><?= $hostinfo->din_l3; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>         
                            <tr>
                                <td>Доноры уровень 4:</td>
                                <td><?= $hostinfo->din_l4; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>  
                            <tr>
                                <td>Внешние ссылки:</td>
                                <td><?= $hostinfo->hin; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Внешние ссылки уровень 1:</td>
                                <td><?= $hostinfo->hin_l1; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Внешние ссылки уровень 2:</td>
                                <td><?= $hostinfo->hin_l2; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Внешние ссылки уровень 3:</td>
                                <td><?= $hostinfo->hin_l3; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Внешние ссылки уровень 4:</td>
                                <td><?= $hostinfo->hin_l4; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Ссылки на сайте:</td>
                                <td><?= $hostinfo->hout; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Ссылки на сайте уровень 1:</td>
                                <td><?= $hostinfo->hout_l1; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Ссылки на сайте уровень 2:</td>
                                <td><?= $hostinfo->hout_l2; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Ссылки на сайте уровень 3:</td>
                                <td><?= $hostinfo->hout_l3; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Ссылки на сайте уровень 4:</td>
                                <td><?= $hostinfo->hout_l4; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>          
                            <tr>
                                <td>Получатели:</td>
                                <td><?= $hostinfo->dout; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>    
                            <tr>
                                <td>Анкоры:</td>
                                <td><?= $hostinfo->anchors; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>    
                            <tr>
                                <td>Исходящие анкоры:</td>
                                <td><?= $hostinfo->anchors_out; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>    
                            <tr>
                                <td>iGood доноров:</td>
                                <td><?= $hostinfo->igood; ?></td>
                                <td>http://xml.solomono.ru/</td>
                            </tr>
                        </table>

                        <? foreach ($hostinfo->checkkeywords as $key => $xml): ?>
                            Ключевое слово: <?= $key ?><br/>
                            <ul>
                                <?
                                $search = simplexml_load_string($xml);
                                if ($search) {
                                    foreach ($search->xpath("//group") as $p => $group) {
                                        $host = (string) $group->categ->attributes()->name;
                                        ?>
                                        <li>
                                            <? if ($host == $hostinfo->host): ?>                        
                                                <font color="red"><b><?= $p + 1 ?>.</b> <?= $host ?></font>
                                            <? else: ?>
                                                <b><?= $p + 1 ?>.</b> <?= $host ?>
                                            <? endif; ?>
                                        </li>
                                        <?
                                    }
                                }
                                ?>
                            </ul>
                        <? endforeach; ?>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>