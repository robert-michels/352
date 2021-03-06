<?php
use User\User;
use Util\HTML\Tags;
use Taxonomy\Term;
use Taxonomy\Taxonomy;

if (isset($_SESSION['user']) && is_object($_SESSION['user'])){
    $user = $_SESSION['user'];
    //echo "USER IS LOGGED IN BUT THE CODE DOES NOT WORK YET";
};
$search  = (isset($search)) ? TRUE : FALSE;

$breadcrumb  = (isset($breadcrumb)) ? $breadcrumb : null;

?>

<div data-js="content" class="content" <?php if($search) echo 'id="searchPage"' ?> >
<header>
    <nav class="nav layout g-flex">
        <ul data-js="breadcrumbs" class="nav__left">
            <li class="nav__link"><a href="/">Harbingers of Death</a></li>
            <li aria-hidden="true" class="nav__divider">//</li>

            <?php if(!is_null($breadcrumb)): ?>
                <?php if (is_array($breadcrumb)): ?>
                    <?php if (array_key_exists("aspect", $breadcrumb)): ?>
                        <li class="nav__link" data-js-breadcrumb="taxonomy">
                        [aspect : <?= $breadcrumb["aspect"] ?>]
                        </li>
                    <?php endif ?>
                    <?php if (array_key_exists("death", $breadcrumb)): ?>
                        <li class="nav__link" data-js-breadcrumb="taxonomy">
                            [aspect : <?= $breadcrumb["death"] ?>]
                        </li>
                    <?php endif ?>
                    <?php if (array_key_exists("fault", $breadcrumb)): ?>
                        <li class="nav__link" data-js-breadcrumb="taxonomy">
                            [aspect : <?= $breadcrumb["fault"] ?>]
                        </li>
                    <?php endif ?>
                <?php elseif(is_string($breadcrumb)): ?>
                    <li class="nav__link"><?= $breadcrumb ?></li>
                <?php endif ?>
            <?php endif; ?>
        </ul>
        <ul class="nav__right">
            <?php if (isset($user)): //MEMBERS ?>
                <li class="nav__text">Still alive, <?= Tags::tag('span', $user->getName(), ['class' => 'italics']); ?>? Let's change that.</li>
                <li aria-hidden="true" class="nav__divider">||</li>
                <!-- Deprecated Logout Button: <li class="nav__link"><a href="/logout/">Logout</a></li>-->
                <li class="nav__link"><a data-js-modal="accountButton" href="#0">Account</a></li>


            <?php else:               //VISITORS?>
                <li class="nav__link"><a data-js-modal="registerButton" href="#0">Register</a></li>
                <li aria-hidden="true" class="nav__divider">||</li>
                <!-- TODO: confirm whether the #0 is correct? -->
                <li class="nav__link"><a data-js-modal="loginButton" href="#0">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>