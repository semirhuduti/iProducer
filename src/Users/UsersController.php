<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware {

    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller.
     *
     * @return void
     */
    public function initialize() {

        /**
         * Initialize the Database and set the timezone
         * 
         */
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
        $this->users->setTablePrefix('iproducer_');
        date_default_timezone_set('Europe/London');

        $this->comments = new \Anax\Comment\Comment();
        $this->comments->setDI($this->di);
        $this->comments->setTablePrefix('iproducer_');

        $this->assignTags = new \Anax\Tags\AssignTags();
        $this->assignTags->setDI($this->di);
        $this->assignTags->setTablePrefix('iproducer_');
    }

    /**
     * List all users.
     *
     * @return void
     */
    public function listAction() {

        /**
         * $all has a list of all the users gethered from the database.
         */
        $all = $this->users->findAll();

        /**
         * Create a new view with the "List all users" title and add the users/list-all view. Send the $all variable that is to be used in the list-all view.
         */
        $this->theme->setTitle("List all users");
        $this->views->add('users/list-all', [

            'users' => $all,
            'title' => "All users",
        ]);

        /**
         * Add guidelines for the user at the bottom of the page.
         */
        $this->views->add('me/page', [
            'content' => '<p>Click on any user to display all the topics created by the user, as well as individual responses by the user. </p>',
        ]);
    }

    /**
     * List user with id.
     *
     * @param int $id of user to display
     *
     * @return void
     */
    public function idAction($id = null) {
        /**
         * Find a entity in the database with the specified ID and save in the $user variable.
         */
        $user = $this->users->find($id);


        /**
         * Find comments by user.
         */
        $res = $this->comments->findByUser($id);

        /**
         * Alter object by connecting comment tags, adding user information from the database and filtering markdown on the comment object.
         */
        $res = $this->doAll($res);

        /**
         * Find the in the database.
         */
        $user = $this->users->find($id);

        /**
         * Used to construct a reply message for the user if the profile has not made any contributions so far.
         */
        if (isset($res[0]->name)) {
            $reply = "";
        } else {
            $reply = $user->name . " has not made any contribution yet!";
        }

        /**
         * Create a view with the "Profile" title and the view users/profile.
         */
        $this->theme->setTitle("Profile");
        $this->views->add('users/profile', [
            'user' => $user,
            'comments' => $res,
            'content' => $reply,
        ]);
    }

    /**
     * Add new user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function addAction($acronym = null) {

        /**
         * Create a form and the form variables with 
         */
        if (!isset($acronym)) {
            $form = $this->form->create([], [
                'acronym' => [
                    'type' => 'text',
                    'label' => 'Acronym: ',
                    'placeholder' => 'Enter username',
                    'required' => true,
                    'validation' => ['not_empty'],
                ],
                'submit' => [
                    'type' => 'submit',
                    'callback' => function($form) {
                        $form->AddOutput("<p><i>Form submitted.</i><p>");
                        $form->AddOutput("<p>Welcome! " . $form->Value('acronym') . "</strong> has been added. Login by enter the name both as acrynom and password.</p>");
                        $form->saveInSession = true;
                        return true;
                    }
                ],
            ]);

            /**
             * Generate the HTML-code for the form and set the title for the view.
             */
            $this->views->add('users/view-default', [
                'title' => "Add a new user",
                'main' => $form->getHTML(),
            ]);

            /**
             * Check status for the form
             */
            $status = $form->check();

            /**
             * If registration is successful save the data from user and unset session.
             * Route to controller action to finalize the addition of the new user.
             * If registration was unsuccessful send output to the user.
             */
            if ($status === true) {

                $acronym = $_SESSION['form-save']['acronym']['value'];
                unset($_SESSION['form-save']);

                $url = $this->url->create('users/add/' . $acronym);
                $this->response->redirect($url);
            } else if ($status === false) {

                // What to do when form could not be processed?
                $form->AddOutput("<p><i>Form submitted but did not checkout.</i></p>");
            }
        };

        /**
         * If the acronye is present save the user to the database and redirect user back to the index.
         */
        if (isset($acronym)) {
            $now = date("Y-m-d H:i:s");
            $this->users->save([
                'acronym' => $acronym,
                'email' => $acronym . '@gmail.com',
                'gravatar' => getGravatar('', 65),
                'name' => $acronym,
                'password' => password_hash($acronym, PASSWORD_DEFAULT),
                'created' => $now,
                'active' => $now,
            ]);
            $url = $this->url->create('../index.php');
            $this->response->redirect($url);
        }
    }

    /**
     * Update user.
     *
     * @param integer $id of user to update.
     *
     * @return void
     */
    public function updateAction($id = null) {

        /**
         * If the ID of the profile is undefined inform the user that the ID is missing 
         */
        if (!isset($id)) {
            die("Missing id");
        }

        /**
         * If the ID of the profile that is to be updated is different from the Active user
         * inform user that he/she is only able to update their own profile.
         */
        if ($this->isActiveUser($id) == null) {
            die("You can only edit your own details.");
        }

        /**
         * After checking that the user is correct and a valid ID is given we find the user in the DB and 
         * save the user details in the $user variable.
         */
        $user = $this->users->find($id);

        /**
         * Create a form with 3 textareas (Acronym, Name, E-mail) and a submit button
         * and add the current values to the textareas.
         */
        $form = $this->form->create([], [
            'acronym' => [
                'type' => 'text',
                'label' => 'Acronym: ',
                'required' => true,
                'validation' => ['not_empty'],
                'value' => $user->acronym,
            ],
            'name' => [
                'type' => 'text',
                'label' => 'Name: ',
                'required' => true,
                'validation' => ['not_empty'],
                'value' => $user->name,
            ],
            'email' => [
                'type' => 'text',
                'label' => 'Email: ',
                'required' => true,
                'validation' => ['not_empty', 'email_adress'],
                'value' => $user->email,
            ],
            'submit' => [
                'type' => 'submit',
                'callback' => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        /**
         * Checking for the status of the form
         */
        $status = $form->check();

        /**
         * If the status is true and action has been executed successfully procede to create a 
         * user vector that will be used to update the user information to the database.
         */
        if ($status === true) {

            $updated_user['id'] = $id;
            $updated_user['acronym'] = $_SESSION['form-save']['acronym']['value'];
            $updated_user['name'] = $_SESSION['form-save']['name']['value'];
            $updated_user['email'] = $_SESSION['form-save']['email']['value'];
            $updated_user['gravatar'] = getGravatar($updated_user['email'], 65);
            unset($_SESSION['form-save']);

            /**
             * Save user to the database and save the outcome of the update in the $res variable.
             * If the outcome is successful save the user to the database
             */
            $res = $this->users->save($updated_user);
            if ($res) {
                $url = $this->url->create('users/id/' . $id);
                $this->response->redirect($url);
            }
        } else if ($status === false) {
            /**
             * If an error ocured with the form infrom user.
             */
            $form->AddOutput("<p><i>Form submitted but did not checkout.</i></p>");
        }

        /**
         * Generate HTML-code for the form and create the view to update a user.
         */
        $this->views->add('users/view-default', [
            'title' => "Update user",
            'main' => $form->getHTML(),
        ]);
    }

    /**
     * Login user.
     * 
     */
    public function loginAction() {

        /**
         * Create a from with 2 textareas (Acronym and Password) and a submit button.
         */
        $form = $this->form->create([], [
            'acronym' => [
                'type' => 'text',
                'placeholder' => 'Acronym',
            ],
            'password' => [
                'type' => 'password',
                'placeholder' => 'Password',
            ],
            'login' => [
                'type' => 'submit',
                'callback' => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        /**
         * Check status of form.
         */
        $status = $form->check();

        /**
         * If the status of the from is set get data from the acronym and password field
         * and unset the session for the forms.
         */
        if ($status === true) {

            $acronym = $_SESSION['form-save']['acronym']['value'];
            $password = $_SESSION['form-save']['password']['value'];
            unset($_SESSION['form-save']);

            /**
             * Try to find a user with the acronym that was previously saved to the $acronym variable.
             */
            $dbres = $this->users->findAcronym($acronym);

            /**
             * If the user was found in the database create a new session with a valid value and user value.
             * Valid value is set to true when the user is logged in succesfully.
             */
            if ($dbres) {
                /**
                 * Check if password and username is correct.
                 */
                if ($acronym == $dbres->acronym && password_verify($password, $dbres->password)) {
                    $form->AddOutput = "User logged in successfully.";
                    $_SESSION['authenticated']['valid'] = true;
                    $_SESSION['authenticated']['user'] = $dbres;

                    /**
                     * Route back to index.
                     */
                    $url = $this->url->create('');
                    $this->response->redirect($url);
                }
            } else {

                /**
                 * If the password is incorect or the user was not found in the database inform user.
                 */
                $form->AddOutput = "<p><i>Login not successful. Acronym or password might be invalid.</i></p>";
            }
        } else if ($status === false) {

            /**
             * If form was not processed succesfully inform user.
             */
            $form->AddOutput("<p><i>Acronym or password invalid.</i></p>");
        }


        /**
         * Generate HTML-code for the form and prepare the page.
         */
        $this->views->add('users/view-login', [
            'title' => "Welcome to iProducer, please log in.",
            'main' => $form->getHTML(),
        ]);

        /**
         * Add additional information for testing.
         */
        $this->views->add('me/page', [
            'content' => '<br/><p>If you dont want to create a user right now, just enter admin / admin </p>',
        ]);

        /**
         * Set page title.
         */
        $this->theme->setVariable('title', "Login user");
    }

    /**
     * Used to log users out.
     * 
     * @return string return the result of the logout function.
     */
    public function logoutAction() {
        /**
         * Unset the session used to validate loged in users. 
         */
        unset($_SESSION['authenticated']);
        $url = $this->url->create('');
        $this->response->redirect($url);
        return 'User logged out.';
    }

    /**
     * This function is used to check if a user is active in the session.
     * 
     * @param type $id of the user.
     * @return boolean status of the user.
     */
    private function isActiveUser($id) {
        /**
         * Check if session has a active user with the specified ID.
         */
        if ($_SESSION['authenticated']['user']->id == $id) {
            return true;
        } else {
            return false;
        }
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

}
