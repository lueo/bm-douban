<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>


    <div class="entry-content entry entrytext clearfix">

        <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

    </div>

    <footer class="entry-meta postdata_small t_r">
        <?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>    <?php the_category(', ') ?>      <?php post_comments_feed_link($link_text = 'RSS'); ?>
    </footer>

        <div class="t_r">
            &nbsp;&nbsp;&nbsp;&nbsp;<?php comments_popup_link('暂无回应', '(1个回应)', '(%个回应)'); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php edit_post_link('改', '(', ')'); ?>
        </div>

        <footer class="clearfix">
            <?php the_tags('<div class="link_pages">标签： ', '&nbsp;&nbsp;&nbsp;&nbsp;', '</div>'); ?>
        </footer>

</article>