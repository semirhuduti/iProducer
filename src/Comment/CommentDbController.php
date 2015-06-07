<?php

namespace Anax\Comment;

/**
 * Used to control comments on the page.
 */
class CommentDbController implements \Anax\DI\IInjectionAware {

    use \Anax\DI\TInjectable;

    /**
     * Initialize controllers and models that will be used in this controller.
     */
    public function initialize() {

        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
        $this->users->setTablePrefix('iproducer_');

        $this->comments = new \Anax\Comment\Comment();
        $this->comments->setDI($this->di);
        $this->comments->setTablePrefix('iproducer_');

        $this->tags = new \Anax\Tags\AssignTags();
        $this->tags->setDI($this->di);
        $this->tags->setTablePrefix('iproducer_');

        $this->assignTags = new \Anax\Tags\AssignTags();
        $this->assignTags->setDI($this->di);
        $this->assignTags->setTablePrefix('iproducer_');

        $this->commentanswer = new \Anax\Comment\CommentAnswer();
        $this->commentanswer->setDI($this->di);
        $this->commentanswer->setTablePrefix('iproducer_');
    }

    /**
     * Used to view  
     */
    public function viewAction() {
        $all = $this->comments->findAll();

        $all = $this->doAll($all);

        /**
         * Prepare the view.
         */
        $this->views->add('comment/commentsdb', [
            'comments' => $all,
            'content' => "",
        ]);
    }

    /**
     * View answers related to the original topic
     */
    public function viewQuestionsAction() {

        $all = $this->comments->findAll();

        $res = [];
        foreach ($all as $value) {
            if ($this->commentanswer->isAnswer($value->id) == null) {
                $res[] = $value;
            }
        }

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $res = $this->doAll($res);

        /**
         * Prepare the view.
         */
        $this->views->add('comment/commentsdb', [
            'comments' => $res,
            'title' => 'Browse Discussions',
            'content' => "",
        ]);
    }

    /**
     * This view is used to get the top 4 (new discussions, old discussions, active users and tags) for the index page.
     * 
     */
    public function viewTopFourAction() {

        /**
         * Select 4 comments from the database that do not contain "Reply" in the title columb in decending timestamp order.
         * This will give us the top 4 new discussions.
         */
        $this->db->select('*')
                ->from("comment WHERE title NOT LIKE '%Reply%' ORDER BY timestamp DESC LIMIT 4");
        $top_new = $this->db->executeFetchAll();

        /**
         * Count the number of idTags as "tc" in the assigntags table where idTag 
         * is grouped and ordered in decending order to get the top 4 results.
         */
        $this->db->select('idTag, COUNT(idTag) as tc')
                ->from('assigntags GROUP BY idTag ORDER BY tc DESC LIMIT 4');
        $top_tags = $this->db->executeFetchAll();

        /**
         * Get the name of the tages from the top_tags object.
         */
        foreach ($top_tags as $value) {
            $value->tag = $this->assignTags->getTagName($value->idTag)->name;
        }

        /**
         * Count the userId from comments that are groupd by userId in decending order 
         * to get the top 4 most active users.
         */
        $this->db->select('userId, COUNT(userId) as uc')
                ->from('comment GROUP BY userId ORDER BY uc DESC LIMIT 4');
        $top_users = $this->db->executeFetchAll();

        /**
         * Get the names of the users and save them in the top_users variable.
         */
        foreach ($top_users as $value) {
            $value->name = $this->users->find($value->userId)->name;
        }

        /**
         * Prepare the view.
         */
        $this->views->add('comment/topfour', [
            'new' => $top_new,
            'tags' => $top_tags,
            'users' => $top_users,
            'title' => 'stats',
        ]);
    }

    /**
     * Get comments that a user has contributed with.
     * @param type $userId id of the user.
     */
    public function viewByUserAction($userId) {
        /**
         * Find comments by user.
         */
        $res = $this->comments->findByUser($userId);

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $res = $this->doAll($res);

        /**
         * Find the in the database.
         */
        $user = $this->users->find($userId);

        /**
         * Used to construct a reply message for the user if the profile has not made any contributions so far.
         */
        if (isset($res[0]->name)) {
            $reply = "";
        } else {
            $reply = $user->name . " has not made any contribution yet!";
        }

        /**
         * Prepare the view.
         */
        $this->views->add('comment/commentsdb', [
            'comments' => $res,
            'title' => 'Contribution by ' . $user->name,
            'content' => $reply,
        ]);
    }

    /**
     * get linked answers to comment id
     */
    public function answersAction($id) {
        /**
         * Find all the comment answers to a comment in the commentanswer table.
         */
        $all_id = $this->commentanswer->find($id);

        /**
         * Save all id values of the comment answers in the $all vector.
         */
        $all = [];
        foreach ($all_id as $key => $value) {
            $all[] = $this->comments->find($value->idAnswer);
        }

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $all = $this->doAll($all);

        /**
         * Get the original question.
         */
        $question[0] = $this->comments->find($id);

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $question = $this->doAll($question);

        /**
         * Prepare the view.
         */
        $this->views->add('comment/commentsdb', [
            'question' => $question,
            'comments' => $all,
            'title' => 'Question',
            'content' => "",
        ]);
    }

    /**
     * Display tags stored in the database.
     */
    public function tagsAction() {
        /**
         * Find all tags in the database.
         */
        $res = $this->tags->findTags();

        /**
         * Prepare the view.
         */
        $this->views->add('comment/tags', [
            'tags' => $res,
            'title' => 'i | Producer tags',
        ]);
        $this->views->add('me/page', [
            'content' => '<br/><p> These tags are used in the discussions, if you are interested in seeing all discussions with a specific tag, you can do so by clicking on any of the tag links.</p>',
        ]);
    }

    /**
     * Get all comment with the assigned tag.
     * @param type $name the tag name
     */
    public function tagCommentsAction($name) {

        /**
         * Get all comments tied to a comment.
         */
        $this->db->select('C.*, T.name AS tag')
                ->from('comment AS C')
                ->leftOuterJoin('assigntags AS C2T', 'C.id = C2T.idComment')
                ->leftOuterJoin('tags AS T', 'C2T.idTag = T.id')
                ->where('T.name = "' . $name . '"')
        ;

        $res = $this->db->executeFetchAll();

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $res = $this->doAll($res);

        /**
         * Prepare the view.
         */
        $this->views->add('comment/commentsdb', [
            'comments' => $res,
            'title' => $this->theme->getVariable('title'),
            'content' => "",
        ]);
    }

    /**
     * Add a comment to the database.
     * @param type $id used if a comment is edited.
     * @param type $idFromQuestion  used if answer to a question.
     */
    public function addAction($id = null, $idFromQuestion = null) {

        if ($id == 'null') {
            $id = null;
        }

        /**
         * Create a comment object
         */
        $edit_comment = (object) [
                    'tags' => '',
                    'comment' => '',
                    'title' => '',
        ];

        if ($id) {
            /**
             * Get current version of the comment.
             */
            $edit_comment = $this->comments->find($id);

            /**
             * Check if user is authorized to edit this comment.
             */
            if ($edit_comment->userId != $_SESSION['authenticated']['user']->id) {
                die("Can only");
            }

            /**
             * Copy over the original tags
             */
            foreach ($this->assignTags->find($id) as $key => $value) {
                $edit_comment->tags[] = $this->assignTags->getTagName($value->idTag)->name;
            }
        }

        /**
         * Get a simple array with all tags.
         */
        $tags_array = $this->createSimpleTags();

        /**
         * Create a form to edit the comment.
         */
        $form_setup = [
            'comment' => [
                'type' => 'textarea',
                'label' => 'Comment: ',
                'required' => true,
                'validation' => ['not_empty'],
                'value' => $edit_comment->comment,
            ],
            'submit' => [
                'type' => 'submit',
                'callback' => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ];

        /**
         * Check if the edited comment is a comment answer.
         */
        $is_answer = $this->commentanswer->isAnswer($id);

        /**
         * If the edited comment is not a answer add additional form information.
         */
        if (!isset($idFromQuestion) && ($is_answer == null)) {

            $form_setup_add['title'] = [
                'type' => 'text',
                'required' => true,
                'validation' => ['not_empty'],
                'value' => $edit_comment->title,
            ];
            $form_setup_add['tags'] = [
                'type' => 'checkbox-multiple',
                'values' => $tags_array,
                'label' => 'Tags: ',
                'required' => true,
                'checked' => $edit_comment->tags,
            ];

            $form_setup = $form_setup_add + $form_setup;
        }

        /**
         * Create the form
         */
        $form = $this->form->create([], $form_setup);

        /**
         * Check the form status
         */
        $status = $form->check();

        if (!isset($idFromQuestion) && !isset($_SESSION['form-save']['tags']['values'])) {
            $status = false;
        }

        /**
         * If form submission has been sucessful procide with the following
         */
        if ($status === true) {



            /**
             * Get data from form storde them into $comment object and unset session variables
             */
            $comment['id'] = isset($id) ? $id : null;
            $comment['comment'] = $_SESSION['form-save']['comment']['value'];
            $comment['userId'] = $_SESSION['authenticated']['user']->id;
            $comment['timestamp'] = time();
            $comment['ip'] = $this->request->getServer('REMOTE_ADDR');
            $comment['title'] = !isset($idFromQuestion) ? $_SESSION['form-save']['title']['value'] : 'Reply: ' . $this->comments->find($idFromQuestion)->title;
            $tags = !isset($idFromQuestion) ? $_SESSION['form-save']['tags']['values'] : null;

            unset($_SESSION['form-save']);

            /**
             * Update the comment in the database.
             */
            $this->comments->save($comment);
            $row['idComment'] = isset($id) ? $id : $this->comments->findLastInsert();

            /**
             * Update the tags for the comment in the database.
             */
            if (!isset($idFromQuestion)) {
                if (isset($id)) {
                    $this->assignTags->delete($id);
                }
                if ($is_answer == null) {
                    foreach ($tags as $key => $value) {
                        $row['idTag'] = array_search($value, $tags_array);
                        $this->assignTags->save($row);
                    }
                }
            }

            /**
             * Update the comment answer.
             */
            if ($idFromQuestion) {
                $data = [
                    'idQuestion' => $idFromQuestion,
                    'idAnswer' => $row['idComment'],
                ];
                $this->commentanswer->save($data);

                $url = $this->url->create('comment/answers/' . $idFromQuestion);
            } else {

                $url = $this->url->create('comment/view-questions');
            }

            /**
             * Route to appropriate view.
             */
            $this->response->redirect($url);
        } else if ($status === false) {

            /**
             * If form submission was unsuccessful inform the user.
             */
            $form->AddOutput("<p><i>Form submitted but did not checkout, make sure that u have inserted a title and selected at least on tag.</i></p>");
        }

        /**
         * Generate the HTML-code for the form and prepare the view.
         */
        $this->views->add('comment/view-default', [
            'title' => "Discuss this topic",
            'main' => $form->getHTML(),
        ]);
        $this->theme->setVariable('title', "Add Comment");
    }

    /**
     * Used to remove a comment.
     * @param type $id
     */
    public function removeIdAction($id = null) {
        if (!isset($id)) {
            die("Missing id");
        }

        /**
         * Check if the user is authorized to remove a comment.
         */
        $comment = $this->comments->find($id);
        if ($comment->userId != $_SESSION['authenticated']['user']->id) {
            die("You can only edit your own posts.");
        }

        /**
         * Remove from database.
         */
        $this->comments->delete($id);
        $this->assignTags->delete($id);
        $this->commentanswer->delete($id);

        /**
         * Prepare the view
         */
        $url = $this->url->create('comment/view-questions');
        $this->response->redirect($url);
    }

    /**
     * Used to save the comment in markdown.
     */
    private function filterMarkdown($all) {
        foreach ($all as $value) {

            $value->comment = $this->textFilter->doFilter($value->comment, 'shortcode, markdown');
        }
        return $all;
    }

    /**
     * Add Tag string array to a comment object.
     *
     */
    private function tagsToComment($all) {
        foreach ($all as $value) {

            $tag_names = [];

            foreach ($this->assignTags->find($value->id) as $inner_value) {
                $tag_names[] = $this->assignTags->getTagName($inner_value->idTag)->name;
            }

            $value->tags = $tag_names;
        }
        return $all;
    }

    /**
     * Add data from the database to the user object.
     * 
     * @param type $all current user object.
     * @return type user object with all data from database.
     */
    private function userDataFromDB($all) {

        foreach ($all as $value) {

            $user_data = $this->users->find($value->userId);
            $value->name = $user_data->name;
            $value->mail = $user_data->email;
            $value->gravatar = $user_data->gravatar;
        }
        return $all;
    }

    /**
     * Alters a object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
     * @param type $all object to be altered.
     * @return type the altered object.
     */
    private function doAll($all) {

        /**
         * Add the connecting tags to the comment object.
         */
        $all = $this->tagsToComment($all);

        /**
         * Add user infromation for the comments to the $all object.
         */
        $all = $this->userDataFromDB($all);

        /**
         * Filter markdown on object.
         */
        $all = $this->filterMarkdown($all);
        return $all;
    }

    /**
     * Get a simple array with all tags from the database.
     * @return type simple arrat with all tags in database.
     */
    private function createSimpleTags() {

        $tags_array = [];

        foreach ($this->tags->findAllTags() as $key => $value) {
            $tags_array[$value->id] = $value->name;
        }

        return $tags_array;
    }

}
