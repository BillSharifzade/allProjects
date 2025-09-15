<nav class="navbar navbar-expand-lg  navbar-dark bg-primary">
    <span class="navbar-brand"><?php echo e(Auth::user()->company->name); ?></span>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link <?php echo e(Request::route()->getName() == "loans" && request()->get('filter') != 'close_requests' ? "active" : ""); ?>" href="/loans">Кредиты</a>
            <a class="nav-item nav-link locked-hide <?php echo e(Request::route()->getName() == "create-loan" ? "active" : ""); ?>" href="/loans/create">Добавить кредит</a>
            <a class="nav-item nav-link <?php echo e(Request::route()->getName() == "payments" ? "active" : ""); ?>" href="/payments">Платежи</a>
            <a class="nav-item nav-link <?php echo e(Request::route()->getName() == "loans" && request()->get('filter') == 'close_requests' ? "active" : ""); ?>" href="/loans?filter=close_requests">Заявки на закрытие</a>
            <a class="nav-item nav-link locked-hide <?php echo e(Request::path() == 'transfer' ? 'active' : ''); ?>" href="/transfer">Перевод</a>
            <a class="nav-item nav-link <?php echo e(Request::route()->getName() == "cashier-transfers" ? "active" : ""); ?>" href="/transfers">Переводы</a>
            <a class="nav-item nav-link locked-hide <?php echo e(Request::route()->getName() == "cashier-expenses" ? "active" : ""); ?>" href="/expenses">Расходы</a>
            <a class="nav-item nav-link <?php echo e(Request::route()->getName() == "cashier-sales" ? "active" : ""); ?>" href="/sales">Продажи</a>
            <a class="nav-item nav-link <?php echo e(Request::path() == 'incassation/accept' ? 'active' : ''); ?>" href="/incassation/accept">
                Инкассация
                <?php if(!empty($hasDelivered) && $hasDelivered): ?>
                    <span class="badge badge-warning ml-1">!</span>
                <?php endif; ?>
            </a>
        </div>
    </div>
    <div class="form-inline">
        <?php if(isset($balance)): ?>
            <span class="badge badge-light badge-balance mr-2">Баланс: <?php echo e(number_format($balance, 2, '.', ' ')); ?></span>
        <?php endif; ?>
        <?php if(empty($shift)): ?>
            <form action="/shift/open" method="post" class="form-inline mr-2" onsubmit="return confirmAutoShift('open', <?php echo e(isset($nextOpening) ? number_format($nextOpening,2,'.','') : '0.00'); ?>)">
                <?php echo e(csrf_field()); ?>

                <input type="hidden" name="opening_balance" value="<?php echo e(isset($nextOpening) ? number_format($nextOpening,2,'.','') : '0.00'); ?>">
                <button class="btn btn-sm btn-outline-light">Открыть</button>
            </form>
        <?php elseif(!empty($shift) && isset($balance)): ?>
            <form action="/shift/close" method="post" class="form-inline mr-2" onsubmit="return confirmAutoShift('close', <?php echo e(number_format($balance,2,'.','')); ?>)">
                <?php echo e(csrf_field()); ?>

                <input type="hidden" name="counted_balance" value="<?php echo e(number_format($balance,2,'.','')); ?>">
                <button class="btn btn-sm btn-outline-light">Закрыть</button>
            </form>
        <?php endif; ?>
        <a href="/signout" class="btn btn-sm btn-outline-light my-2 my-sm-0">ВЫХОД</a>
    </div>
</nav>
<script>
    function confirmAutoShift(type, amount){
        var msg = type === 'open' ? ('Открыть смену с суммой: ' + Number(amount).toFixed(2) + ' ?') : ('Закрыть смену с суммой: ' + Number(amount).toFixed(2) + ' ?');
        return confirm(msg);
    }
    (function(){
        var url = <?php echo json_encode(session('shift_report_url'), 15, 512) ?>;
        if(url){
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url + (url.indexOf('?') === -1 ? '?t=' + Date.now() : '&t=' + Date.now());
            document.body.appendChild(iframe);
        }
    })();
</script>
<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/widgets/cashier_navigation.blade.php ENDPATH**/ ?>