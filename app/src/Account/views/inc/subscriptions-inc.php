<?php

/**
 * @var stdClass[] $list
 * @var stdClass[] $actives
 */

foreach ($list as $key => $item) {

    $paid = false;

    if($actives){
        foreach($actives as $active){
            if($active->certificate_id == $item->id){
                $paid = true;
            }
        }
    }

    ?>

    <!--suppress ALL -->
    <div class="col-sm-4">
        <article class="info-box">
            <div class="frame same-height-left" style="height: 450px;">
                <div class="heading">
                    <h3><?= ($key+1) ?>. <?= $item->name ?></h3>
                </div>
                <div class="text">
                    <p></p>

                    <p><?= $item->comments ?></p>

                    <p></p>
                </div>
                <div class="info-table">
                    <table>
                        <tbody>
                        <tr>
                            <td><?= trans('main.PricePerMonth') ?><br></td>
                            <td style="text-align: right;"><?= $item->price ?> <i class="fa fa-rub"></i></td>
                        </tr>
                        <?php foreach ($item->lines as $line) { ?>
                            <tr>
                                <td><?= $line->tov_name ?><br></td>
                                <td style="text-align: right;"><?= $line->price_after ?> x <?= $line->qty ?> <i class="fa fa-rub"></i></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php if(!$paid){ ?>
                    <div class="button-holder checkout subscription-button-holder" data-subscriptionid="<?= $item->id ?>">
                       <a href="#" class="btn btn-primary center-block" data-subid="<?= $item->id ?>" style="width: 80%;"><?= trans('main.Checkout') ?>
                            <i class="fa fa-bars"></i></a>
                    </div>
                <?php } else { ?>
                    <p style="font-size: 1.3em; margin: 20px 0;"><i class="fa fa-check-circle-o" style="color: #2dd982;"></i> Подписка оплачена</p>
                <?php } ?>
            </div>
        </article>
    </div>

<?php }


?>

<div class="clearfix clear"></div>