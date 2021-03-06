<?php

// TODO: look into implementing something like this: https://www.sitepoint.com/flexible-view-manipulation-1/

// TODO: replace with composer ASAP, to autoload php files


require "src/Entity/Omen.php";
require "src/Entity/OmenCollection.php";

require "src/Taxonomy/Term.php";
require "src/Taxonomy/Taxonomy.php";
require "src/Taxonomy/Fault.php";
require "src/Taxonomy/FaultTaxonomy.php";
require "src/Taxonomy/Death.php";
require "src/Taxonomy/DeathTaxonomy.php";
require "src/Taxonomy/Aspect.php";
require "src/Taxonomy/AspectTaxonomy.php";

require "src/User/User.php";

require "src/Util/Tags.php";
require "src/Util/SelectInputs.php";
require "src/Util/TextParser.php";
require "src/Util/MemberHero.php";

require "src/Route/Route.php";

require "src/View/Page.php";
require "src/View/Partial.php";
require "src/View/JSON.php";

use Entity\Omen\Omen;
use Entity\Omen\OmenCollection;
use Taxonomy\FaultTaxonomy;
use Taxonomy\DeathTaxonomy;
use Taxonomy\AspectTaxonomy;

use Taxonomy\Fault;
use Taxonomy\Death;
use Taxonomy\Aspect;

use User\User;

use Util\MemberHero;

use Route\Route;
use View\Page;
use View\Partial;
use View\JSON;

$omens = new OmenCollection();


// start session so that we can store logged in cookie
session_start();

/*************************************************
 **  AJAX OMEN LIST ROUTE
 *  TODO: refactor so code isn't duplicated
 *
 *************************************************/
// Add omen list route
Route::get('/omen/ajax', function($query) {
    //print_r($query);

    $taxonomies = [];

    // TODO: REFACTOR & PUT ELSEWHERE https://gph.is/g/aN3YOMZ
    foreach ($query as $taxonomy => $term){
        switch ($taxonomy){
            case "aspect":
                if(!isset($taxonomies["aspect"]))  $taxonomies["aspect"] = new AspectTaxonomy();
                $taxonomies["aspect"]->addTerm(AspectTaxonomy::getTermBySlug($term));
                break;

            case "death":
                if(!isset($taxonomies["death"]))  $taxonomies["death"] = new DeathTaxonomy();
                $taxonomies["death"]->addTerm(DeathTaxonomy::getTermBySlug($term));
                break;

            case "fault":
                if(!isset($taxonomies["fault"]))  $taxonomies["fault"] = new FaultTaxonomy();
                $taxonomies["fault"]->addTerm(FaultTaxonomy::getTermBySlug($term));
                break;
        }
    }

    $omensCollection = OmenCollection::findOmensByFilter($query);

    // if the user is logged in
    if (isset($_SESSION['user'])){
        //Get user omens so that we can set the title to statement on only those omens
        $user = $_SESSION['user'];

        // update the omen's that the user has selected
        $omensCollection->setStatements($user->getUserOmens());
    }

    JSON::generate('omen-list', ["omenCollection" => $omensCollection->generateJSON()]);
}, true);


/*************************************************
 **  INDIVIDUAL OMEN ROUTE
 *************************************************/
// Add the individual omen route
Route::get('/omen/([a-z0-9]+(?:-[a-z0-9]+)*)', function($slug) {
    $omen = OmenCollection::getOmenBySlug($slug);

    Page::build('omen', ["omen" => $omen]);
});


/*************************************************
 **  SEARCH ROUTE [AJAX]
 *************************************************/
// Add search AJAX JSON result route
Route::get('/search/ajax', function ($query) {

    $omensCollection = OmenCollection::findOmensBySearch($query["query"])->generateJSON();

    JSON::generate('omen-list', ["omenCollection" => $omensCollection]);
});

/*************************************************
 **  SEARCH ROUTE
 *************************************************/
// Add the individual omen route
Route::get('/search', function($query) {

    if (empty($query)){
        $searchString = "";
        $omensCollection = OmenCollection::findAllOmens();
    } else {
        $searchString = $query["query"];
        $omensCollection = OmenCollection::findOmensBySearch($searchString);
    }

    Page::build('search', ["searchString" => $searchString, "omenCollection" => $omensCollection]);
});



/*************************************************
 **  OMEN LIST ROUTE
 *************************************************/

// Add omen list route
// TODO: confirm (it should be) that the routes are processed in order, so that this one shouldn't override the previous
Route::get('/omen', function($query) {
    $taxonomies = [];
    // Breakdown the query and create Term objects
    foreach ($query as $taxonomy => $term){
        switch ($taxonomy){
            case "aspect":
                if(!isset($taxonomies["aspect"]))  $taxonomies["aspect"] = new AspectTaxonomy();
                $taxonomies["aspect"]->addTerm(AspectTaxonomy::getTermBySlug($term));
                break;

            case "death":
                if(!isset($taxonomies["death"]))  $taxonomies["death"] = new DeathTaxonomy();
                $taxonomies["death"]->addTerm(DeathTaxonomy::getTermBySlug($term));
                break;

            case "fault":
                if(!isset($taxonomies["fault"]))  $taxonomies["fault"] = new FaultTaxonomy();
                $taxonomies["fault"]->addTerm(FaultTaxonomy::getTermBySlug($term));
                break;
        }
    }
    // TODO: some pagination stuff // is pagination being used /* if (array_key_exists("page", $query)){ }*/
    // create a list of omens, using the query
    $omenCollection = OmenCollection::findOmensByFilter($query);

    // if the user is logged in
    if (isset($_SESSION['user'])){
            //Get user omens so that we can set the title to statement on only those omens
            $user = $_SESSION['user'];

            // update the omen's that the user has selected
            $omenCollection->setStatements($user->getUserOmens());
    }

    Page::build('omen-list', ["taxonomies" => $taxonomies, "omenCollection" => $omenCollection, "breadcrumb" => $query]);
}, true);


/*************************************************
**  HOMEPAGE ROUTE
 *************************************************/
Route::get('/', function ($query){
    //echo print_r($query);
    if (isset($query["submit_logout"])) {
        unset($_SESSION);
        session_destroy();
    }

    $didEncounterOmens = FALSE;
    $heroText = "Are you going to die? Have you kicked your mother's bucket? Are your friends on the way out?";

    // if user us logged in disply their omens on the home page
    if (isset($_SESSION['user'])){
        //User omen collection
        $user = $_SESSION['user'];
        $omenCollection = $user->getUserOmens();

        // if the user hasn't selected any omens
        if(count($omenCollection->getOmens()) <= 0){
            // display default
            $omenCollection = OmenCollection::FindSomeOmens();
            $heroText = "Looks like you haven't encountered death yet. Lucky you! The gods must be on your side.";
        } else {
            // update wording of ones they have selected
            $omenCollection->setStatements($omenCollection);
            $didEncounterOmens = TRUE;
            $heroText = MemberHero::createMemberHero($omenCollection);
        }

    } else {
        $omenCollection = OmenCollection::FindSomeOmens();
    }

    Page::build('home', ["omenCollection" => $omenCollection, "heroText" => $heroText, "didEncounterOmens" => $didEncounterOmens]);
});


/*************************************************
 **  LOGOUT ROUTE
 *************************************************/
Route::get('/logout', function ($query){

    unset($_SESSION);
    session_destroy();

    $omenCollection = OmenCollection::FindSomeOmens();

    Page::build('home', ["omenCollection" => $omenCollection]);
});



/*************************************************
 **  CLEAR OMENS SUBMISSION ROUTE
 *************************************************/
// Handles register and login form submissions
Route::get('/clear', function (){

    $didEncounterOmens = FALSE;

    // clear omens & display appropriate text
    if (isset($_SESSION['user'])){
        //User omen collection
        $user = $_SESSION['user'];
        $user->clearUserOmens();
        $omenCollection = $user->getUserOmens();

        // if the user hasn't selected any omens
        if(count($omenCollection->getOmens()) <= 0){
            // display default
            $omenCollection = OmenCollection::FindSomeOmens();
            $heroText = "Looks like you haven't encountered death yet. Lucky you! The gods must be on your side.";
        } else {
            // update wording of ones they have selected
            $omenCollection->setStatements($omenCollection);
            $didEncounterOmens = TRUE;
            $heroText = MemberHero::createMemberHero($omenCollection);
        }

    } else {
        $omenCollection = OmenCollection::FindSomeOmens();
    }

    Page::build('home', ["omenCollection" => $omenCollection, "heroText" => $heroText, "didEncounterOmens" => $didEncounterOmens]);
});



/*************************************************
 **  REGISTER FORM SUBMISSION ROUTE
 *************************************************/
// Register user, add to database
Route::post('/register', function (){
    // TODO: move data processing to it's own place
    ////////////////////////////////// REGISTER
    if(isset($_POST['submit_register'])) {
        $user = (new User())
            ->setName($_POST['name'])
            ->setEmailAddress($_POST['emailAddress'])
            ->setPassword(sha1($_POST['password']))
            ->setBirthdayDay($_POST['birthday__day'])
            ->setBirthdayMonth($_POST['birthday__month'])
            ->setBirthdayYear($_POST['birthday__year'])
            ->setCountry($_POST['country'])
            ->writeToDB();
    }

    $omenCollection = OmenCollection::FindSomeOmens();
	Page::build('home', ["response" => $user, "omenCollection" => $omenCollection]);
});
    


/*************************************************
 **  LOGIN FORM SUBMISSION ROUTE
 *************************************************/
// Handles register and login form submissions
Route::post('/login', function (){
    ////////////////////////////////// LOGIN
    // create new User object from the text file and
    // compare it against the post values
    if (isset($_POST['submit_login'])){
        
        $formEmail = $_POST['emailAddress'];
        $formPassword = $_POST['password'];

        // Hero Text
        $didEncounterOmens = FALSE;
        $heroText = "Are you going to die? Have you kicked your mother's bucket? Are your friends on the way out?";

        //// Authenticate User, Print Response ////
        if (isset($formEmail) && isset($formPassword)){
            $user = User::authenticateUser($formEmail, $formPassword);

            if(isset($user)){
                $_SESSION['user'] = $user;
                $omenCollection = $user->getUserOmens();

                // if the user hasn't selected any omens
                if(count($omenCollection->getOmens()) <= 0){
                    // display default
                    $omenCollection = OmenCollection::FindSomeOmens();
                    $heroText = "Looks like you haven't encountered death yet. Lucky you! The gods must be on your side.";
                } else {
                    // update wording of ones they have selected
                    $omenCollection->setStatements($omenCollection);
                    $didEncounterOmens = TRUE;

                    $heroText = MemberHero::createMemberHero($omenCollection);
                }
				
                $responseMessage = "You've successfully signed in!";

				Page::build('home', ["response" => $responseMessage, "omenCollection" => $omenCollection, "heroText" => $heroText, "didEncounterOmens" => $didEncounterOmens]);
            } else {
                $responseMessage = "Wrong password. Please try again.";
                $omenCollection = OmenCollection::FindSomeOmens();

				Page::build('home', ["response" => $responseMessage, "omenCollection" => $omenCollection, "heroText" => $heroText, "didEncounterOmens" => $didEncounterOmens]);
                unset($_SERVER['user']);
            }
        } else {
            $responseMessage = "You're going to have to try harder.  Please fill in all the fields.";
            $omenCollection = OmenCollection::FindSomeOmens();
			Page::build('home', ["response" => $responseMessage, "omenCollection" => $omenCollection, "heroText" => $heroText, "didEncounterOmens" => $didEncounterOmens]);
        }
    }
});


/*************************************************
 **  ACCOUNT FORM SUBMISSION ROUTE
 *************************************************/
// Handles account form submissions (update user info)
Route::post('/account', function (){
    // TODO: move data processing to it's own place
    ////////////////////////////////// REGISTER
    if(isset($_POST['submit_account'])) {
        $user = $_SESSION['user'];
        $responseMessage = "You've successfully updated your account data.";
        
        if (isset($_POST['emailAddress'])) $user = $user->setEmailAddress($_POST['emailAddress']);
        if (isset($_POST['name'])) $user = $user->setName($_POST['name']);
        if (isset($_POST['password']) && isset($_POST['password-old']) && strcmp($_POST['password'], "") !== 0) {
            if (strcmp($user->getPassword(), $_POST['password-old']) !== 0) {
                $responseMessage = "Error: the old password wasn't correct, please repeat with the correct password, to prove your identity.";
            } else {
                if (strcmp($_POST['password'], $_POST['password-old']) === 0) {
                    $responseMessage = "Error: New password may not match the old password.";
                } else {
                    //Success with password change
                    $responseMessage = "Password has been successfully changed.";
                    $user = $user->setPassword($_POST['password']);
                }
            }
        }
        $user->updateUserData();
        $omenCollection = $user->getUserOmens();
    }
	Page::build('home', ["response" => $responseMessage, "omenCollection" => $omenCollection]);
});



/*************************************************
 **  OMEN MEMBER FORM SUBMISSION ROUTE
 *************************************************/
// Handles omen submissions: store whether the member added / removed the omen
Route::post('/omen/([a-z0-9]+(?:-[a-z0-9]+)*)', function($slug) {
    $omen = OmenCollection::getOmenBySlug($slug);

    // TODO: FIX!
    //var_dump($_POST);

    ////////////////////////////////// OMEN
    if (isset($_POST['submit_user_omen'])){
          //echo "user added omen";
          $user = $_SESSION['user']; 
          //echo get_class($user);
          //echo $user->get

          $user->addOmenToUser($omen);
    } else if (isset($_POST['submit_user_omen_remove'])) {
        $user = $_SESSION['user']; 
        $user->removeOmenFromUser($omen);
    }

    Page::build('omen', ["omen" => $omen]);
});



// Run the router
Route::run('/');
