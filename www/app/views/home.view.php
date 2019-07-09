<?php
use Songfolio\Core\Helper;
use Songfolio\Core\Routing;
use Songfolio\Models\Users;
?>

<section id="home-slider">
    <picture>
        <!-- Mobile 600x450 -->
        <source srcset="<?php echo PUBLIC_DIR?>img/slider-1-600w.jpg" media="(max-width: <?php echo MOBILE_MAX_WIDTH ?>px)"/>
        <!-- Desktop 1920x720-->
        <img src="<?php echo PUBLIC_DIR?>img/slider-1-1920w.jpg" />
    </picture>
</section>

<section id="home-blog">
    <div class="container">
        <div class="row">
            <?php
            if(isset($articles)):
            ?>
            <div class="col-lg-6 col-12">
                <article class="event-full">
                    <a href="<?=$articles[0]['slug'] ?? ''?>" class="event">
                        <h1><?=$articles[0]['title'] ?? 'Bientôt ...'?></h1>

                        <?php if(isset($articles[0])): ?>
                            <p><?=$articles[0]['date_create']?> <span class="muted">par <?=(new Users($articles[0]['author']))->__get('username')?></span></p>
                            <?php if(isset($articles[0]['img_dir'])): ?>
                                <img src="<?=$articles[0]['img_dir']?>" />
                            <?php endif; ?>
                        <?php endif;?>

                    </a>
                </article>
            </div>
            <div class="col-lg-3 col-xs-6 col-12">
                <article class="event-half">
                    <a href="<?=$articles[1]['slug'] ?? ''?>" class="event">
                        <h1><?=$articles[1]['title'] ?? 'Bientôt ...'?></h1>
                        <?php if(isset($articles[1])): ?>
                            <p><?=$articles[1]['date_create']?> <span class="muted">par <?=(new Users($articles[1]['author']))->__get('username')?></span></p>
                            <?php if(isset($articles[1]['img_dir'])): ?>
                                <img src="<?=$articles[1]['img_dir']?>" />
                            <?php endif; ?>
                        <?php endif;?>
                    </a>
                    <a href="<?=$articles[2]['slug'] ?? ''?>" class="event">
                        <h1><?=$articles[2]['title'] ?? 'Bientôt ...'?></h1>
                        <?php if(isset($articles[2])): ?>
                            <p><?=$articles[2]['date_create']?> <span class="muted">par <?=(new Users($articles[2]['author']))->__get('username')?></span></p>
                            <?php if(isset($articles[2]['img_dir'])): ?>
                                <img src="<?=$articles[2]['img_dir']?>" />
                            <?php endif; ?>
                        <?php endif;?>
                    </a>
                </article>
            </div>
            <div class="col-lg-3 col-xs-6 col-12">
                <article class="event-other">
                    <h1>Autres nouveautés</h1>
                    <ul>
                        <?php if(count($articles) > 3): ?>
                            <?php for($i = 3; $i < count($articles) && $i < 9; $i++): ?>
                                <li>
                                    <a href="<?=$articles[$i]['slug'] ?? ''?>">
                                        <h2><?=$articles[$i]['title']?></h2>
                                        <p><?=$articles[$i]['date_create']?> par <?=$articles[$i]['author']?></p>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="<?=Routing::getSlug('pages','renderEventsPage')?>" class="chevron lexical_br">Voir tous les articles</a>
                </article>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!--
<section id="section-top">
    <div class="container">
        <div class="row center">

            <div class="col-lg-10 col-12 nav">
                <select id="top-select">
                    <option value="singles">Top singles</option>
                    <option value="albums">Top albums</option>
                </select>
                <a href="">Tout le temps</a>
                <a href="" class="active">Ce mois</a>
                <a href="">Aujourd'hui</a>
            </div>
            <table class="col-lg-10 col-12">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <tr>
                    <td class="rank">
                      <?php echo $i+1; ?>.
                    </td>
                    <td class="image">
                        <img src="public/img/cover_br.png" />
                    </td>
                    <td class="title">
                        Bohemian Rhapsody <br> <small>Queen</small>
                    </td>
                    <td class="info">
                        31 Octobre 1975
                    </td>
                    <td class="info">
                        1.2M
                    </td>
                    <td class="info">
                        3.8M
                    </td>
                </tr>
                <?php endfor; ?>
            </table>
            <div class="col-12 tac">
                <a href="#" class="chevron">Tous nos singles</a>
            </div>
        </div>
    </div>
</section>
-->
<section id="section-info">
    <div class="container">
        <div class="row center">
            <div class="col-lg-10 col-sm-10 col-12 events">
                <div class="nav">
                    <a href="<?= Routing::getSlug('Pages','renderEventsPage') ?>">
                        Prochains évènements
                    </a>
                </div>
                <ul style="list-style: none;padding: 0;">
                    <?php
                        if(isset($events)):
                            foreach ($events as $event):
                    ?>
                                <li>
                                    <?php  if(isset($event['img_dir'])): ?>   
                                        <img src="<?= BASE_URL.$event['img_dir'] ?>" alt="">
                                    <?php endif ?>
                                    <div class="info">
                                        <h2 style="margin: 0;display: inline;"> <a class="link" href="<?= BASE_URL.$event['slug'] ?>"><?= ucfirst($event['type']) ?> - <?= $event['displayName'] ?></a> </h2>
                                        <p>le <?= Helper::getFormatedDateWithTime($event['start_date']) ?></p>
                                    </div>
                                </li>
                           
                            <?php endforeach; else: ?>
                            <li> Aucun événement prévu </li>

                        <?php endif ?>
                </ul>
                <a href="<?=Routing::getSlug('pages','renderEventsPage')?>" class="chevron">Voir tous les évènements</a>
            </div>
            <!--<div class="col-sm-offset-1 col-lg-4 col-sm-5 col-12 group-info">
                    <img src="<?php echo PUBLIC_DIR?>img/photo_fm.jpg" />
                    <h1>Freddie Mercury</h1>
                    <p>Chanteur</p>
                    <a href="#">Biographie de Freddie</a>
                    <a href="#" class="chevron lexical">Tous les membres du groupe</a>
            </div>-->
        </div>
    </div>
</section>
