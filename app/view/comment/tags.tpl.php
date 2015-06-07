<h3><?= $title ?></h3>
<br/>
<!-- 
    This view show's all the comments stored in the database with a link that will open all comments with a specific tag.

    The for loop goes through all the tags and creates a link for all comments with the selected tag.
-->
<div class='center-tags'>
    <?php foreach ($tags as $tag) : ?>
        <a href="<?= $this->url->create('comment/tag-comments/' . $tag->name) ?>">
            <span class="tag">
                <i class="fa fa-tag"></i> <?= $tag->name ?>
            </span>
        </a><br/>

    <?php endforeach; ?>
</div>

