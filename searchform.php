<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">

    <div class="m-t-40">

        <input type="text" value="<?php global $s; echo wp_specialchars($s, 1); ?>" name="s" id="s" size="18" />

        <input type="submit" id="searchsubmit" value="搜索" />

    </div>

</form>

