<div class="cq-block-wrapper">
  <?php /* if ($attributes) : ?>
      <pre>Output: <?= $attributes['cqb_block_description'] ?></pre>
  <?php endif */ ?>

    <?php if ($post_query && $post_query->have_posts()) :    ?>

        <div class="cq-block-container cq-block-container--3-grid">

            <?php foreach ($post_query->posts as $post) :
                $image = get_the_post_thumbnail($post->ID, 'large', array('class' => 'cq-block-item__image'));
                ?>
                <div class="cq-block-item">

                    <?php if ($image) : ?>
                        <a href="<?= get_permalink($post->ID) ?>"><?= $image ?></a>
                    <?php endif ?>

                    <h2 class="cq-block-item__title">
                        <a href="<?= get_permalink($post->ID) ?>"><?= $post->post_title ?></a>
                    </h2>

                    <?php if ($post->post_excerpt) : ?>
                        <p class="cq-block-item__intro"><?= $post->post_excerpt ?></p>
                    <?php endif ?>

                </div>
            <?php endforeach ?>

        </div>

    <?php else: ?>
        <p>There were no posts found matching your query</p>
    <?php endif ?>
</div>
