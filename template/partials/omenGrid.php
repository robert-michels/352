<?php

use View\Partial;
use Entity\Omen\OmenCollection;

//Get omens for members
$userLoggedIn = isset($_SESSION['user']);
$user; 
$omenCollection;
$home = (isset($home)) ? TRUE : FALSE;

if ($userLoggedIn && $home){
    $user = $_SESSION['user'];
    $omenCollection = $user->getUserOmens();

    //echo print_r(get_object_vars($omenCollection));
} else if (!$userLoggedIn && $home){
    $omenCollection = OmenCollection::FindSomeOmens();
}

// do we spice this with oil paintings?
$oilPaintings  = (isset($oilPaintings) && $oilPaintings === TRUE) ? $oilPaintings : FALSE;


// if columns don't exist, set it to 3
$columns  = (isset($columns) && !is_null($columns)) ? $columns : 3;

// this assumes omen columns will always span two columns of the grid
?>

<div data-js="grid" class="tile__panel js-panel g-span<?= $columns * 2 ?>of9 g-last g-flex">
    <?php for($i = 0; $i < $columns; $i++): ?>
    <?php
        //generate grid class
        $columnClass = "g-span2of".$columns * 2;

        // if last column add class ".g-last" to remove right padding
        if ($i === $columns-1){
            $columnClass .= " g-last";
        }
        ?>
    <div data-js="column" class="tile__panel__column <?= $columnClass ?>"></div>

    <?php endfor; ?>

    <?php if(isset($omenCollection)): ?>

    <?= Partial::build('omens', ["omenCollection" => $omenCollection]); 
     //echo "OmenCollection is set"; ?>

    <?php else: ?>

    <?= Partial::build('omens', ["omenCollection" => OmenCollection::FindSomeOmens()]);  ?>

    <?php endif; ?>

</div>
