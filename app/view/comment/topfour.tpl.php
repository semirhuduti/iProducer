<!-- 
    This view is used to show the top four tags, discussions, active users and topics to keep alive.
-->
<div class = 'top-wrapper'>


    <div class = 'left'>
        <h3> <i class="fa fa-tags"></i> Top Tags</h3>
        <p>
            <?php foreach ($tags as $value) : ?>
                <a href="<?= $this->url->create('comment/tag-comments/' . $value->tag) ?>">
                    <span class="tag">
                        <i class="fa fa-tag"></i>
                        <?= $value->tag ?> 
                    </span></a><br>

            <?php endforeach; ?>
        </p>
    </div>

    <div class = 'middle'>
        <h3><i class="fa fa-pencil"></i> Top Discussions</h3>
        <p>
            <?php foreach ($new as $value) : ?>
                <a href="<?= $this->url->create('comment/answers/' . $value->id) ?>">
                    <span class="top-stats">
                        <i class="fa fa-comment"></i>
                        <?= (strlen($value->title) > 35) ? substr($value->title, 0, 32) . '...' : $value->title ?>
                    </span></a><br>

            <?php endforeach; ?>
        </p>
    </div>

    <div class = 'right'>
        <h3><i class="fa fa-child"></i> Active users</h3>
        <p>
            <?php foreach ($users as $value) : ?>
                <a href="<?= $this->url->create('comment/view-by-user/' . $value->userId) ?>">
                    <span class="top-stats">
                        <i class="fa fa-user"></i>
                        <?= $value->name ?>
                    </span></a><br>

            <?php endforeach; ?>
        </p>
    </div>
</div>


