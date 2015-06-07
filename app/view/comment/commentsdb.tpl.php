<!-- 
    Used in 3 views 
    1. Display all topic 
    2. Discuss a topic
    3. Discuss a comment
-->

<!-- 
        If all questions are being displayed show the "Want to discuss something button"
        else show the "Discuss this topic" to discuss the topic or a comment within the topic
-->
<div class="add-comment">

    <?php if (isset($question)) : ?>
        <a href="<?= $this->url->create('comment/add/null/' . $question[0]->id) ?>"><span class ='create-button-big'>Discuss</span></a>
    <?php else: ?>
        <a href="<?= $this->url->create('comment/add') ?>"><span class ='create-button-big'>New topic</span></a>
    <?php endif; ?>
</div>
<br/>

<!-- 
    Show the title of the page
-->
<h3><?= $title ?></h3>
<br/>


<?= $content ?>

<!-- 
    Post-wrapper is the wrapper containing a post information for a question.
    Information:    1. Avatar picture with the link to the users profile.
                    2. Body (User, Title, Tag, Comment)
-->
<?php if (isset($question)) : ?>
    <div class="post-wrapper">

        <div class="avatar">
            <a href="<?= $this->url->create('comment/view-by-user/' . $question[0]->userId) ?>">
                <img class="avatar" src="<?= $this->url->asset($question[0]->gravatar) ?>" alt="Avatar"></a>
        </div>

        <div class="post-body">
            <div class="post-header">
                <span class="post-title"><?= (strlen($question[0]->title) > 35) ? substr($question[0]->title, 0, 32) . '...' : $question[0]->title ?></span>
                <div class="post-tags">
                    <span class='who-posted'>By: <?= $question[0]->name ?></span>
                    <?php foreach ($question[0]->tags as $tag) : ?>
                        <span class="tag-button"><a href="<?= $this->url->create('comment/tag-comments/' . $tag) ?>"><i class="fa fa-tag"></i> <?= $tag ?></a></span>
                    <?php endforeach; ?>
                </div>  
            </div>

            <div class="post-content">
                <?= $question[0]->comment ?>
            </div>
            <div class="post-footer">

            </div>
        </div>
    </div>
    <br>
    <div class="strike">
        <span class='hranswer'>Replies</span>
    </div>
<?php endif; ?>

<!-- 
    Post-wrapper is the wrapper containing a post information for a answer.
    Information:    1. Avatar picture with the link to the users profile.
                    2. Body (User, Title, Tag, Comment, Answer-button, Edit-button)
-->
<div class="answers">
    <?php if (is_array($comments)) : ?>
        <?php foreach ($comments as $comment) : ?>
            <li class="post">
                <div class="post-wrapper">

                    <div class="avatar">
                        <a href="<?= $this->url->create('comment/view-by-user/' . $comment->userId) ?>">
                            <img class="avatar" src="<?= $this->url->asset($comment->gravatar) ?>" alt="Avatar"></a>
                    </div>
                    <div class="post-body">                 
                        <div class="post-header">
                            <span class="post-title"><a href="<?= $this->url->create('comment/answers/' . $comment->id) ?>"><?= (strlen($comment->title) > 35) ? substr($comment->title, 0, 32) . '...' : $comment->title ?></a></span>
                            <div class="post-tags">
                                <span class='who-posted'>By:  <?= $comment->name ?></span>
                                <?php foreach ($comment->tags as $tag) : ?>
                                    <span class="tag-button"><a href="<?= $this->url->create('comment/tag-comments/' . $tag) ?>"><i class="fa fa-tag"></i> <?= $tag ?></a></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="post-content">
                            <?= $comment->comment ?>
                        </div>
                        <div class="post-footer">
                            <span class ='create-button'><a href="<?= $this->url->create('comment/answers/' . $comment->id) ?>">Answers </span></a>
                            <?php if ($comment->userId == $_SESSION['authenticated']['user']->id) : ?>

                                <span class ='create-button'><a href="<?= $this->url->create('comment/add/' . $comment->id) ?>">Edit</span></a>


                            <?php endif; ?>  
                        </div>        
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
</div>

