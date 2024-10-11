<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(session()->has('dir') ? session()->get('dir') : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="baseUrl" content="<?php echo e(env('APP_URL')); ?>" />

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <?php echo $__env->make('partials._head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="" id="app">
<?php echo $__env->make('partials._body', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/components/master-layout.blade.php ENDPATH**/ ?>