<script>

	(function($) { 

	    "use strict"; 
	    
	    <?php if(Session::has('success')): ?>
	        Snackbar.show({text: '<?php echo e(Session::get('success')); ?>', pos: 'bottom-center'});
	    <?php endif; ?>

	    <?php if(Session::has('error')): ?>
	        Snackbar.show({text: '<?php echo e(Session::get("error")); ?>', pos: 'bottom-center',backgroundColor: '#dc3545',actionTextColor: 'white'});
	    <?php endif; ?>

	    <?php if(Session::has('errors') || ( isset($errors) && is_array($errors) && $errors->any())): ?>
	        Snackbar.show({text: '<?php echo e($errors->first()); ?>', pos: 'bottom-center',backgroundColor: '#dc3545',actionTextColor: 'white'});
	    <?php endif; ?>

	})(jQuery); 

</script>
<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/helper/app_message.blade.php ENDPATH**/ ?>