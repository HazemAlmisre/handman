<script src="<?php echo e(asset('js/app.js')); ?>"></script>

<script>
      const currencyFormat = (amount) => {
        const DEFAULT_CURRENCY = JSON.parse(<?php echo json_encode(json_encode(Currency::getDefaultCurrency(true)), 15, 512) ?>)
         const noOfDecimal = 2
         const currencyPosition = DEFAULT_CURRENCY.defaultPosition
         const currencySymbol = DEFAULT_CURRENCY.defaultCurrency.symbol
        return formatCurrency(amount, noOfDecimal, currencyPosition, currencySymbol)
      }
      window.currencyFormat = currencyFormat
      window.defaultCurrencySymbol = <?php echo json_encode(Currency::defaultSymbol(), 15, 512) ?>

    </script>



<?php /**PATH C:\xampp\htdocs\appyourserbisyoph\resources\views/landing-page/partials/_currencyscripts.blade.php ENDPATH**/ ?>