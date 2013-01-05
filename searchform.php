<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
<div><input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" size="18" />
<input type="submit" id="searchsubmit" value="我找" />
</div>
</form>
