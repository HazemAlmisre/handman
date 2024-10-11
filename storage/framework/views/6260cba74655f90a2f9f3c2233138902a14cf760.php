<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<section class="login-content">
    <div class="container h-100">
       <div class="row align-items-center justify-content-center h-100">
          <div class="col-md-5">
             <div class="card p-3">
                <div class="card-body">
                   <div class="auth-logo">
                      <a href="<?php echo e(route('frontend.index')); ?>">
                        <img src="<?php echo e(getSingleMedia(imageSession('get'),'logo',null)); ?>" class="img-fluid rounded-normal" alt="logo">
                      </a>
                   </div>
                   <h3 class="mb-3 font-weight-bold text-center"><?php echo e(__('auth.sign_in')); ?></h3>
                   <p class="text-center text-secondary mb-4"><?php echo e(__('auth.login_continue')); ?></p>
                    <!-- Session Status -->
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.auth-session-status','data' => ['class' => 'mb-4','status' => session('status')]]); ?>
<?php $component->withName('auth-session-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['class' => 'mb-4','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(session('status'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

                    <!-- Validation Errors -->
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.auth-validation-errors','data' => ['class' => 'mb-4','errors' => $errors]]); ?>
<?php $component->withName('auth-validation-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['class' => 'mb-4','errors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                    <form method="POST" action="<?php echo e(route('login')); ?>" data-toggle="validator">
                        <?php echo e(csrf_field()); ?>

                      <div class="row">
                         <div class="col-lg-12">
                            <div class="form-group">
                               <label class="text-secondary"><?php echo e(__('auth.email')); ?> <span class="text-danger">*</span></label>
                               <input id="email" name="email" value="<?php echo e(request('email')); ?>" class="form-control" type="email" placeholder="<?php echo e(__('auth.enter_name',['name' => __('auth.email')])); ?>" required autofocus>
                               <small class="help-block with-errors text-danger"></small>
                            </div>
                         </div>
                         <div class="col-lg-12 mt-2">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="text-secondary"><?php echo e(__('auth.login_password')); ?> <span class="text-danger">*</span></label>
                                    <label><a href="<?php echo e(route('auth.recover-password')); ?>"><?php echo e(__('auth.forgot_password')); ?></a></label>
                                </div>                                    
                               <input class="form-control" type="password" value="<?php echo e(request('password')); ?>" placeholder="<?php echo e(__('auth.enter_name',['name' => __('auth.login_password') ])); ?>" name="password"  required autocomplete="current-password">
                               <small class="help-block with-errors text-danger"></small>
                            </div>
                         </div>                              
                      </div>
                      <button type="submit" class="btn btn-primary btn-block mt-2"><?php echo e(__('auth.login')); ?></button>
                      <div class="col-lg-12 mt-3">
                           <p class="mb-0 text-center"><?php echo e(__('auth.dont_have_account')); ?> <a href="<?php echo e(route('auth.register')); ?>"><?php echo e(__('auth.signup')); ?></a></p>
                      </div>
                   </form>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/auth/login.blade.php ENDPATH**/ ?>