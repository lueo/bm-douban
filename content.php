<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

    <header class="entry-header">
        <h1 class="green"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h1>
    </header><!-- .entry-header -->

    <div class="postdata_small">
        <?php the_date(); echo "&nbsp;&nbsp;"; the_time(); ?>    <?php the_category(', ') ?>      <?php post_comments_feed_link($link_text = 'RSS'); ?>
    </div>

    <div class="entry-content entry entrytext clearfix">

        <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

        <div class="entry-index-meta clearfix t_r">
            <?php comments_popup_link('暂无回应', '(1个回应)', '(%个回应)'); ?>
            <?php edit_post_link('(改)', '(', ')'); ?>
        </div>

        <footer class="clearfix">
            <?php the_tags('<div class="link_pages">标签： ', '&nbsp;&nbsp;&nbsp;&nbsp;', '</div>'); ?>
        </footer>

    </div>

</article>