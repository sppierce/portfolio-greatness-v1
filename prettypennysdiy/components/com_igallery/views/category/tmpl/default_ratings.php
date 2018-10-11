<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="<?php echo $this->mode ?>_ratings_container<?php echo $this->uniqueid; ?>" class="<?php echo $this->mode ?>_ratings_container">

<div class="rating_stars">

    <div class="ratings_current"></div>
    <?php for($i=0; $i<=5; $i++): ?>
        <a href="#" class="rating_star" style="width:<?php echo ($i + 1) * 24; ?>px; z-index:<?php echo 7 - $i; ?>;"></a>
    <?php endfor; ?>
</div>

<div class="ratings_text"><?php echo JText::_( 'RATING' ) ?>: <span class="rating_number"></span>/5 (<span class="rating_count"></span> <span class="rating_vote_vote" style="display: none;"><?php echo JText::_( 'VOTE' ) ?></span><span class="rating_vote_votes"><?php echo JText::_( 'VOTES' ) ?></span>) <span class="rating_loading_gif"></span></div><span class="rating_message" style="display: block;"></span>

</div>