<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(session()->has('dir') ? session()->get('dir') : 'ltr' ,); ?>">
<head>
    <?php echo $__env->yieldContent('before_head'); ?>
    <?php echo $__env->make('landing-page.partials._head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
      <?php echo $__env->make('landing-page.partials._currencyscripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 

    <?php echo $__env->yieldContent('after_head'); ?>
</head>
<body class="body-bg">


    <span class="screen-darken"></span>

    <div id="loading">
        <?php echo $__env->make('landing-page.partials.loading', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>


    <main class="main-content" id="landing-app">
       
        <?php echo $__env->yieldContent('content'); ?>
    </main>

  

  <?php echo $__env->yieldContent('before_script'); ?>
    <?php echo $__env->make('landing-page.partials._scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('after_script'); ?>

   
</body>
</html>
<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/landing-page/layouts/headerremove.blade.php ENDPATH**/ ?>