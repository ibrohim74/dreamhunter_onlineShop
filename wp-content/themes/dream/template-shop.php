<?php
/*
Template Name : shop Page
*/
get_header()
    ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (has_post_thumbnail()): ?>
            <img src="<?php the_post_thumbnail_url('post_image'); ?>" class="img-fluid mb-5" alt="">
            <?php endif; ?>



            <?php if (have_posts()):
                while (have_posts()):
                    the_post(); ?>
            <?php the_content(); ?>
            <?php endwhile; else: endif; ?>
        </div>
    </div>
</div>



<?php get_footer() ?>