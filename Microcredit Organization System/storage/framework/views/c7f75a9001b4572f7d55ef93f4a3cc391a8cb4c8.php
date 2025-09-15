<nav class="navbar navbar-expand-lg navbar-dark bg-dark mobile-fix">
    <span class="navbar-brand"><?php echo e(Auth::user()->company->name); ?></span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(in_array(Request::route()->getName(), ['admin-loans','admin-loans-close-requests','admin-archive']) ? 'active' : ''); ?>" href="#" id="navLoans" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Кредиты</a>
                <div class="dropdown-menu" aria-labelledby="navLoans">
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-loans' ? 'active' : ''); ?>" href="/admin/loans">Активные</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-loans-close-requests' ? 'active' : ''); ?>" href="/admin/loans/close-requests">Заявки на закрытие</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-archive' ? 'active' : ''); ?>" href="/admin/archive">Архив</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(in_array(Request::route()->getName(), ['admin-dashboard','admin-payments','admin-reports','admin-overdue65-form','admin-monthly-report']) ? 'active' : ''); ?>" href="#" id="navReports" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Отчеты</a>
                <div class="dropdown-menu" aria-labelledby="navReports">
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-dashboard' ? 'active' : ''); ?>" href="/admin/dashboard">Дашборд</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-daily-report' ? 'active' : ''); ?>" href="/admin/daily-report">Ежедневные отчеты</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-payments' ? 'active' : ''); ?>" href="/admin/payments">Платежи</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-reports' ? 'active' : ''); ?>" href="/admin/reports">Сводный отчет</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-overdue65-form' ? 'active' : ''); ?>" href="/admin/reports/overdue65/form">Просрочка 65+</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-monthly-report' ? 'active' : ''); ?>" href="/admin/reports/monthly">Месячные отчеты</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(in_array(Request::route()->getName(), ['cashboxes','cashbox-users','admin-transfers']) || Request::path()=='admin/transfer' ? 'active' : ''); ?>" href="#" id="navCash" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Кассы</a>
                <div class="dropdown-menu" aria-labelledby="navCash">
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'cashboxes' ? 'active' : ''); ?>" href="/admin/cashboxes">Кассы</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'cashbox-users' ? 'active' : ''); ?>" href="/admin/cashbox-users">Кассиры</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item <?php echo e(Request::path() == 'admin/transfer' ? 'active' : ''); ?>" href="/admin/transfer">Подкрепить кассира</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-transfers' ? 'active' : ''); ?>" href="/admin/transfers">История переводов</a>
                </div>
            </li>

            <li class="nav-item"><a class="nav-link <?php echo e(Request::route()->getName() == 'admin-expenses' ? 'active' : ''); ?>" href="/admin/expenses">Расходы</a></li>
            <li class="nav-item"><a class="nav-link <?php echo e(Request::route()->getName() == 'admin-sales' ? 'active' : ''); ?>" href="/admin/sales">Продажи</a></li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(in_array(Request::route()->getName(), ['admin-incassators','admin-incassation']) ? 'active' : ''); ?>" href="#" id="navIncass" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Инкассация</a>
                <div class="dropdown-menu" aria-labelledby="navIncass">
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-incassators' ? 'active' : ''); ?>" href="/admin/incassators">Инкассаторы</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-incassation' ? 'active' : ''); ?>" href="/admin/incassation">История</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?php echo e(in_array(Request::route()->getName(), ['admin-blacklist','interest-rates','gold-prices','passwords','admin-hr']) ? 'active' : ''); ?>" href="#" id="navSettings" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Настройки</a>
                <div class="dropdown-menu" aria-labelledby="navSettings">
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-hr' ? 'active' : ''); ?>" href="/admin/hr">Сотрудники</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'admin-blacklist' ? 'active' : ''); ?>" href="/admin/blacklist">Чёрный список</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'interest-rates' ? 'active' : ''); ?>" href="/admin/interest-rates">Процентовки</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'gold-prices' ? 'active' : ''); ?>" href="/admin/gold-prices">Ценообразование</a>
                    <a class="dropdown-item <?php echo e(Request::route()->getName() == 'passwords' ? 'active' : ''); ?>" href="/admin/passwords">Пароли</a>
                </div>
            </li>
        </ul>

        <div class="form-inline my-2 my-lg-0">
            <a href="/signout" class="btn btn-sm btn-outline-light">ВЫХОД</a>
        </div>
    </div>
</nav>

<style>
    .mobile-fix { overflow-x: auto; }
</style>
<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/widgets/admin_navigation.blade.php ENDPATH**/ ?>