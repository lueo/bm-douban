<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <h2 class="green"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?>    </a></h2>
    <small class="postdata_small"><?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>  <?php the_category(', ') ?>    </small>
    
    <div class="entry">
        <?php the_content(); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php comments_popup_link('暂无回应', '(1个回应)', '(%个回应)'); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link('(改)'); ?>
    </div>
</div>