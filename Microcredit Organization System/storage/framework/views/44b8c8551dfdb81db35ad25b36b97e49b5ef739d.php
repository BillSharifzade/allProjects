<?php $__env->startSection('content'); ?>
    <div class="locked-hide">
    <?php echo e(Form::open(['url' => '/loans/create?collateral_type=' . $collateral_type, 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'loanForm', 'name' => 'loanForm'])); ?>

        <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Основные</h5>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="is_notifiable">Залог</label>
                        <?php echo e(Form::select('collateral_type', [1 => 'Золото', 2 => 'Авто', 3 => 'Смартфон'], $collateral_type, ['id' => 'collateral_type', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="passport_number">Серия паспорта <span class="text-danger pl-2" id="search-message"></span></label>
                        <div class="row">
                            <div class="col">
                                <?php echo e(Form::text('passport_number', '', ['id' => 'passport_number', 'class' => 'form-control'])); ?>

                            </div>
                            <div class="col p-0" style="max-width:110px;">
                                <?php echo e(Form::button('ПОИСК', ['id' => 'search', 'class' => 'btn btn-primary'])); ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="tin">ИНН</label>
                        <?php echo e(Form::number('tin', '', ['id' => 'tin', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="passport_issuer">Орган, выдавший паспорт</label>
                        <?php echo e(Form::text('passport_issuer', '', ['id' => 'passport_issuer', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col align-self-center pt-2">
                    Дата выдачи паспорта
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_day">День</label>
                            <?php if (isset($component)) { $__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectDay::class, ['name' => 'passport_issued_day']); ?>
<?php $component->withName('select-day'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27)): ?>
<?php $component = $__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27; ?>
<?php unset($__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_month">Месяц</label>
                            <?php if (isset($component)) { $__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectMonth::class, ['name' => 'passport_issued_month']); ?>
<?php $component->withName('select-month'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59)): ?>
<?php $component = $__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59; ?>
<?php unset($__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_year">Год</label>
                            <?php if (isset($component)) { $__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectYear::class, ['name' => 'passport_issued_year']); ?>
<?php $component->withName('select-year'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10)): ?>
<?php $component = $__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10; ?>
<?php unset($__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="fullname">ФИО</label>
                        <?php echo e(Form::text('fullname', '', ['id' => 'fullname', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col align-self-center pt-2">
                   День/Месяц/Год рождения
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="birth_day">День</label>
                            <?php if (isset($component)) { $__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectDay::class, ['name' => 'birth_day']); ?>
<?php $component->withName('select-day'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27)): ?>
<?php $component = $__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27; ?>
<?php unset($__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="birth_month">Месяц</label>
                            <?php if (isset($component)) { $__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectMonth::class, ['name' => 'birth_month']); ?>
<?php $component->withName('select-month'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59)): ?>
<?php $component = $__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59; ?>
<?php unset($__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="birth_year">Год</label>
                            <?php if (isset($component)) { $__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectYear::class, ['name' => 'birth_year']); ?>
<?php $component->withName('select-year'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10)): ?>
<?php $component = $__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10; ?>
<?php unset($__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-2 mt-5">
                <div class="col">
                    <div class="form-group">
                        <label for="phone1">Телефон1</label>
                        <?php echo e(Form::number('phone1', '', ['id' => 'phone1', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone2">Телефон2</label>
                        <?php echo e(Form::number('phone2', '', ['id' => 'phone2', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone3">Телефон3</label>
                        <?php echo e(Form::number('phone3', '', ['id' => 'phone3', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone4">Телефон4</label>
                        <?php echo e(Form::number('phone4', '', ['id' => 'phone4', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="is_notifiable">Оповещение</label>
                        <?php echo e(Form::select('is_notifiable', [1 => 'Да'], '', ['id' => 'is_notifiable', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="residence_address">Адрес проживания</label>
                        <?php echo e(Form::text('residence_address', '', ['id' => 'residence_address', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="initial_sum">Сумма</label>
                        <?php echo e(Form::number('initial_sum', '', ['id' => 'initial_sum', 'step' => '0', 'class' => 'form-control'])); ?>

                    </div>
                </div>

                <?php if(Auth::user()->company->isAuditable()): ?>
                    <div class="col">
                        <div class="form-group">
                            <?php if(Auth::user()->isCashier()): ?>
                                <label for="in_audit">В аудит</label>
                            <?php else: ?>
                                <label for="in_audit">&nbsp;</label>
                            <?php endif; ?>
                            <?php echo e(Form::select('in_audit', Auth::user()->isCashier() ? [0 => 'Нет', 1 => 'Да'] : [1 => 'Да'], '', ['id' => 'in_audit', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Залог</h5>
                </div>
            </div>

            <?php if($collateral_type == 1): ?>
                <div class="row my-2">
                    <div class="col align-self-center" style="min-width:50px !important; max-width: 50px !important;">
                        #
                    </div>
                    <div class="col p-0">
                        Название
                    </div>
                    <div class="col p-0 ml-1">
                        Весь
                    </div>
                    <div class="col p-0 ml-1">
                        Чистый весь
                    </div>
                    <div class="col p-0 ml-1">
                        Проба
                    </div>
                    <div class="col p-0 ml-1">
                        Количество
                    </div>
                    <div class="col p-0 ml-1">
                        Цена
                    </div>
                </div>

                <?php for($i = 1; $i <= 10; $i++): ?>
                    <div class="row my-2">
                        <div class="col pt-2" style="min-width:50px !important; max-width: 50px !important;">
                            <?php echo e($i); ?>.
                        </div>
                        <div class="col p-0">
                            <div class="form-group">
                                <?php echo e(Form::text('item_'.$i.'_name', '', ['id' => 'item_'.$i.'_name', 'class' => 'form-control', 'placeholder'=>'название'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_weight', '', ['id' => 'item_'.$i.'_weight', 'step' => 'any', 'class' => 'form-control', 'placeholder'=>'весь'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_pure_weight', '', ['id' => 'item_'.$i.'_pure_weight', 'step' => 'any', 'class' => 'form-control pure_weight tracker', 'placeholder'=>'чистый весь'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::select('item_'.$i.'_purity', [375=>375,500=>500,585=>585,750=>750,875=>875], '', ['id' => 'item_'.$i.'_purity', 'class' => 'form-control'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_count', '', ['id' => 'item_'.$i.'_count', 'class' => 'form-control tracker', 'placeholder'=>'количество'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_price', '', ['id' => 'item_'.$i.'_price', 'class' => 'form-control', 'placeholder'=>'цена', 'readonly' => 'readonly'])); ?>

                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>

            <?php if($collateral_type == 2): ?>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_brand">Марка</label>
                            <?php echo e(Form::text('vehicle_brand', '', ['id' => 'vehicle_brand',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_year">Год выпуска</label>
                            <?php echo e(Form::number('vehicle_year', '', ['id' => 'vehicle_year',  'max' => 9999, 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_color">Цвет</label>
                            <?php echo e(Form::select('vehicle_color', [1 => 'Белый', 2 => 'Черный', 3 => 'Мокрый асфальт', 4 => 'Серебристый', 5 => 'Красный', 6 => 'Зеленый', 7 => 'Синий'] , 0, ['id' => 'vehicle_color', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_plate_number">Гос. Номер</label>
                            <?php echo e(Form::text('vehicle_plate_number', '', ['id' => 'vehicle_plate_number',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_engine">Двигатель</label>
                            <?php echo e(Form::text('vehicle_engine', '', ['id' => 'vehicle_engine',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_location">Место хранения</label>
                            <?php echo e(Form::text('vehicle_location', '', ['id' => 'vehicle_location',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_mileage">Пробег</label>
                            <?php echo e(Form::number('vehicle_mileage', '', ['id' => 'vehicle_mileage',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_transmission">Коробка передач</label>
                            <?php echo e(Form::select('vehicle_transmission', [1 => 'Автомат', 2 => 'Механика', 3 => 'Вариатор', 4 => 'Другое'] , 0, ['id' => 'vehicle_transmission', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_gas">Топливо</label>
                            <?php echo e(Form::select('vehicle_gas', [1 => 'Бензин', 2 => 'Дизель', 3 => 'Газ', 4 => 'Другое'] , 0, ['id' => 'vehicle_gas', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_description">Описание</label>
                            <?php echo e(Form::textarea('vehicle_description', '', ['id' => 'vehicle_description', 'rows' => 4, 'cols' => 54, 'class' => 'form-control', 'style' => 'resize:none'])); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($collateral_type == 3): ?>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_brand">Бренд</label>
                            <?php echo e(Form::text('phone_brand', '', ['id' => 'phone_brand',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_model">Модель</label>
                            <?php echo e(Form::text('phone_model', '', ['id' => 'phone_model',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_storage_gb">Память (ГБ)</label>
                            <?php echo e(Form::number('phone_storage_gb', '', ['id' => 'phone_storage_gb',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_color">Цвет</label>
                            <?php echo e(Form::text('phone_color', '', ['id' => 'phone_color',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_condition">Состояние</label>
                            <?php echo e(Form::text('phone_condition', '', ['id' => 'phone_condition',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_imei">IMEI</label>
                            <?php echo e(Form::text('phone_imei', '', ['id' => 'phone_imei',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_description">Описание</label>
                            <?php echo e(Form::textarea('phone_description', '', ['id' => 'phone_description', 'rows' => 4, 'cols' => 54, 'class' => 'form-control', 'style' => 'resize:none'])); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row my-2 mt-4">
                <div class="col">
                    <div class="form-group">
                        <label for="image">Картинка</label>
                        <?php echo e(Form::file('image')); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <?php echo e(Form::button('СОХРАНИТЬ', ['id' => 'submitBtn', 'class' => 'btn btn-primary'])); ?>

                </div>
            </div>
        </div>

    <?php echo e(Form::close()); ?>

    </div>

    <script type="text/javascript">
        $(function() {
            var goldPrices = [];

            $.getJSON('/gold-prices/json', function (data) {
                if(data.isOk === true) {
                    goldPrices = data.goldPrices;

                    calculateInitialSum(goldPrices);
                }
            })

            $( ".tracker" ).keyup(function() {
                calculateInitialSum(goldPrices);
            });

            $('#search').click(function (){
                let passportNumber = $('#passport_number').val();
                $.getJSON( '/loaners/'+passportNumber, function( data ) {
                    if(data.isOk === false) {
                        $('#search-message').html('(не найден)');
                        $('#tin').val('');
                        $('#passport_issuer').val('');
                        $('#fullname').val('');
                        $('#phone1').val('');
                        $('#phone2').val('');
                        $('#phone3').val('');
                        $('#phone4').val('');
                        $('#residence_address').val('');
                        $('#passport_issued_day').val(1).change();
                        $('#passport_issued_month').val(1).change();
                        $('#passport_issued_year').val(<?php echo e(date('Y')); ?>).change();
                        $('#birth_day').val(1).change();
                        $('#birth_month').val(1).change();
                        $('#birth_year').val(<?php echo e(date('Y')); ?>).change();
                    } else {
                        $('#search-message').html('');
                        $('#tin').val(data.tin);
                        $('#passport_issuer').val(data.passport_issuer);
                        $('#fullname').val(data.fullname);
                        $('#phone1').val(data.phone1);
                        $('#phone2').val(data.phone2);
                        $('#phone3').val(data.phone3);
                        $('#phone4').val(data.phone4);
                        $('#residence_address').val(data.residence_address);
                        $('#passport_issued_day').val(data.passport_issued_day).change();
                        $('#passport_issued_month').val(data.passport_issued_month).change();
                        $('#passport_issued_year').val(data.passport_issued_year).change();
                        $('#birth_day').val(data.birth_day).change();
                        $('#birth_month').val(data.birth_month).change();
                        $('#birth_year').val(data.birth_year).change();
                        if(data.is_blacklisted === true) {
                            $('#search-message').html('— клиент в чёрном списке');
                        }
                    }
                });
            });

            $('#submitBtn').click(function (event){
                event.preventDefault()

                $(this).hide();
                $('#loanForm').trigger('submit');

                return false;
            });

            $('#collateral_type').change(function (event) {
                document.location.href='/loans/create?collateral_type=' + $(this).val();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/loan/create.blade.php ENDPATH**/ ?>