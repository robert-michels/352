<?php
use View\Partial;
?>



<?php if (isset($success) && !is_null($success)): ?>

	<?= Partial::build('forms/success', ["success" => $success]); ?>

<?php elseif (isset($error) && !is_null($error)): ?>
	
	<?= Partial::build('forms/error', ["error" => $error]); ?>
		
<?php endif; ?>
