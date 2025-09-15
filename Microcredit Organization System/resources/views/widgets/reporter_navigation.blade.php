<nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
    <span class="navbar-brand" >{{Auth::user()->company->name}}</span>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-loans" ? "active" : ""}}" href="/reporter/loans">Активные кредиты</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-closed-loans" ? "active" : ""}}" href="/reporter/loans/closed">Закрытые кредиты</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-payments" ? "active" : ""}}" href="/reporter/payments">Платежы</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-reports" ? "active" : ""}}" href="/reporter/reports">Отчет</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-monthly-report" ? "active" : ""}}" href="/reporter/reports/monthly">Месячные отчеты</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-daily-report" ? "active" : ""}}" href="/reporter/daily-report">Ежедневные отчеты</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-overdue65-form" ? "active" : ""}}" href="/reporter/reports/overdue65/form">Просрочка</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-transfers" ? "active" : ""}}" href="/reporter/transfers">Переводы</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-expenses" ? "active" : ""}}" href="/reporter/expenses">Расходы</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "reporter-sales" ? "active" : ""}}" href="/reporter/sales">Продажи</a>
        </div>
    </div>
    <div class="form-inline">
        <a href="/signout" class="btn btn-sm btn-outline-light my-2 my-sm-0">ВЫХОД</a>
    </div>
</nav>
