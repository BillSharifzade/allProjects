<nav class="navbar navbar-expand-lg  navbar-dark bg-primary">
    <span class="navbar-brand">{{Auth::user()->company->name}}</span>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link {{Request::route()->getName() == "loans" && request()->get('filter') != 'close_requests' ? "active" : ""}}" href="/loans">Кредиты</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "create-loan" ? "active" : ""}}" href="/loans/create">Добавить кредит</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "payments" ? "active" : ""}}" href="/payments">Платежи</a>
            <a class="nav-item nav-link {{Request::route()->getName() == "loans" && request()->get('filter') == 'close_requests' ? "active" : ""}}" href="/loans?filter=close_requests">Заявки на закрытие</a>
        </div>
    </div>
    <div class="form-inline">
        <a href="/signout" class="btn btn-sm btn-outline-light my-2 my-sm-0">ВЫХОД</a>
    </div>
</nav>
