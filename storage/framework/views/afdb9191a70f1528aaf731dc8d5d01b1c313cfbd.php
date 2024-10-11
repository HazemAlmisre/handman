<!-- Horizontal Menu Start -->
<nav id="navbar_main" class="mobile-offcanvas nav navbar navbar-expand-xl hover-nav horizontal-nav py-xl-0">
    <div class="container-fluid p-lg-0">
        <div class="offcanvas-header px-0">
            <div class="navbar-brand ms-3">
                <?php echo $__env->make('landing-page.components.widgets.logo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <button class="btn-close float-end px-3"></button>
        </div>
        <?php
                $headerSection = App\Models\FrontendSetting::where('key', 'heder-menu-setting')->first();
                $sectionData = $headerSection ? json_decode($headerSection->value, true) : null;
                $settings = App\Models\Setting::whereIn('type', ['service-configurations','OTHER_SETTING'])
                ->whereIn('key', ['service-configurations', 'OTHER_SETTING'])
                ->get()
                ->keyBy('type');

                $serviceconfig = $settings->has('service-configurations') ? json_decode($settings['service-configurations']->value) : null;
                $othersetting = $settings->has('OTHER_SETTING') ? json_decode($settings['OTHER_SETTING']->value) : null;
        ?>
         <?php if($sectionData && isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1): ?>
        <ul class="navbar-nav iq-nav-menu list-unstyled" id="header-menu">
            <?php if($sectionData['home'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('frontend.index') ? 'active' : ''); ?>" href="<?php echo e(route('frontend.index')); ?>"><?php echo e(__('landingpage.home')); ?></a>
            </li>
            <?php endif; ?>
            <?php if($sectionData['categories'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('category.*') ? 'active' : ''); ?>" href="<?php echo e(route('category.list')); ?>"><?php echo e(__('landingpage.categories')); ?></a>
            </li>
            <?php endif; ?>
            <?php if($sectionData['service'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('service.*') ? 'active' : ''); ?>" href="<?php echo e(route('service.list')); ?>"><?php echo e(__('landingpage.services')); ?></a>
            </li>
            <?php endif; ?>
            <?php if(optional($othersetting)->blog  == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('blog.*') ? 'active' : ''); ?>" href="<?php echo e(route('blog.list')); ?>"><?php echo e(__('landingpage.blogs')); ?></a>
            </li>
            <?php endif; ?>
            <?php if($sectionData['provider'] == 1): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('frontend.provider.*') ? 'active' : ''); ?>" href="<?php echo e(route('frontend.provider')); ?>"><?php echo e(__('landingpage.providers')); ?></a>
            </li>
            <?php endif; ?>
            <?php if(auth()->check() && auth()->user()->user_type == 'user' && $sectionData['bookings'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('booking.*') ? 'active' : ''); ?>" href="<?php echo e(route('booking.list')); ?>"><?php echo e(__('landingpage.bookings')); ?></a>
                </li>
            <?php endif; ?>
            
        </ul>
    <?php endif; ?>
    </div>
    <!-- container-fluid.// -->
</nav>
<!-- Sidebar Menu End -->
<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/landing-page/partials/_horizontal-nav.blade.php ENDPATH**/ ?>