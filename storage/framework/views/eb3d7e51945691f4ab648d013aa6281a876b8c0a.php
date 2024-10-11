    <?php
    // $app = \App\Models\AppSetting::first();
    $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
    $app = $sitesetup ? json_decode($sitesetup->value) : null;
    ?>
    <footer class="iq-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 ">
                    <span class="mr-1">
                <?php echo optional($app)->site_copyright; ?>

                    </span>
                </div>
            </div>
        </div>
    </footer><?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/partials/_body_footer.blade.php ENDPATH**/ ?>