<?php $__env->startSection('content'); ?>
    <div class="locked-hide">
    <?php echo e(Form::open(['url' => '/loans/' . $loan->id . '/update', 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

        <?php echo e(csrf_field()); ?>

    <?php echo e(Form::hidden('collateral_type', $loan->collateral_type)); ?>

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
                        <label for="fullname">ФИО</label>
                        <?php echo e(Form::text('fullname', $loan->loaner->full_name, ['id' => 'fullname', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 ">
                <div class="col">
                    <div class="form-group">
                        <label for="phone1">Телефон1</label>
                        <?php echo e(Form::number('phone1', $loan->loaner->phone1, ['id' => 'phone1', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone2">Телефон2</label>
                        <?php echo e(Form::number('phone2', $loan->loaner->phone2, ['id' => 'phone2', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone3">Телефон3</label>
                        <?php echo e(Form::number('phone3', $loan->loaner->phone3, ['id' => 'phone3', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone4">Телефон4</label>
                        <?php echo e(Form::number('phone4', $loan->loaner->phone4, ['id' => 'phone4', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="is_notifiable">Оповещение</label>
                        <?php echo e(Form::select('is_notifiable', [1 => 'Да', 2 => 'Нет'], $loan->loaner->is_notifiable, ['id' => 'is_notifiable', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="residence_address">Адрес проживания</label>
                        <?php echo e(Form::text('residence_address', $loan->loaner->residence_address, ['id' => 'residence_address', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Залог</h5>
                </div>
            </div>

            <?php if($loan->collateral_type == 1): ?>
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
                                <?php echo e(Form::text('item_'.$i.'_name', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->name : '', ['id' => 'item_'.$i.'_name', 'class' => 'form-control', 'placeholder'=>'название'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_weight', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->weight : '', ['id' => 'item_'.$i.'_weight', 'step' => 'any', 'class' => 'form-control', 'placeholder'=>'весь'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_pure_weight', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->pure_weight : '', ['id' => 'item_'.$i.'_pure_weight', 'step' => 'any', 'class' => 'form-control tracker', 'placeholder'=>'чистый весь'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::select('item_'.$i.'_purity', [375=>375,500=>500,585=>585,750=>750,875=>875], isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->purity : '', ['id' => 'item_'.$i.'_purity', 'class' => 'form-control', 'placeholder'=>''])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_count', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->count : '', ['id' => 'item_'.$i.'_count', 'class' => 'form-control tracker', 'placeholder'=>'количество'])); ?>

                            </div>
                        </div>
                        <div class="col p-0 ml-1">
                            <div class="form-group">
                                <?php echo e(Form::number('item_'.$i.'_price', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->price : '', ['id' => 'item_'.$i.'_price', 'class' => 'form-control', 'placeholder'=>'цена', 'readonly' => 'readonly'])); ?>

                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>

            <?php if($loan->collateral_type == 2): ?>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_brand">Марка</label>
                            <?php echo e(Form::text('vehicle_brand', $loan->auto->brand, ['id' => 'vehicle_brand',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_year">Год выпуска</label>
                            <?php echo e(Form::number('vehicle_year', $loan->auto->year, ['id' => 'vehicle_year',  'max' => 9999, 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_color">Цвет</label>
                            <?php echo e(Form::select('vehicle_color', [1 => 'Белый', 2 => 'Черный', 3 => 'Мокрый асфальт', 4 => 'Серебристый', 5 => 'Красный', 6 => 'Зеленый', 7 => 'Синий'] , $loan->auto->color, ['id' => 'vehicle_color', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_plate_number">Гос. Номер</label>
                            <?php echo e(Form::text('vehicle_plate_number', $loan->auto->plate_number, ['id' => 'vehicle_plate_number',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_engine">Двигатель</label>
                            <?php echo e(Form::text('vehicle_engine', $loan->auto->engine, ['id' => 'vehicle_engine',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_location">Место хранения</label>
                            <?php echo e(Form::text('vehicle_location', $loan->auto->location, ['id' => 'vehicle_location',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_mileage">Пробег</label>
                            <?php echo e(Form::number('vehicle_mileage', $loan->auto->mileage, ['id' => 'vehicle_mileage',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_transmission">Коробка передач</label>
                            <?php echo e(Form::select('vehicle_transmission', [1 => 'Автомат', 2 => 'Механика', 3 => 'Вариатор', 4 => 'Другое'] , $loan->auto->transmission, ['id' => 'vehicle_transmission', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_gas">Топливо</label>
                            <?php echo e(Form::select('vehicle_gas', [1 => 'Бензин', 2 => 'Дизель', 3 => 'Газ', 4 => 'Другое'] , $loan->auto->gas, ['id' => 'vehicle_gas', 'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="vehicle_description">Описание</label>
                            <?php echo e(Form::textarea('vehicle_description', $loan->auto->description, ['id' => 'vehicle_description', 'rows' => 4, 'cols' => 54, 'class' => 'form-control', 'style' => 'resize:none'])); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($loan->collateral_type == 3): ?>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_brand">Бренд</label>
                            <?php echo e(Form::text('phone_brand', optional($loan->phone)->brand, ['id' => 'phone_brand',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_model">Модель</label>
                            <?php echo e(Form::text('phone_model', optional($loan->phone)->model, ['id' => 'phone_model',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_storage_gb">Память (ГБ)</label>
                            <?php echo e(Form::number('phone_storage_gb', optional($loan->phone)->storage_gb, ['id' => 'phone_storage_gb',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_color">Цвет</label>
                            <?php echo e(Form::text('phone_color', optional($loan->phone)->color, ['id' => 'phone_color',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_condition">Состояние</label>
                            <?php echo e(Form::text('phone_condition', optional($loan->phone)->condition, ['id' => 'phone_condition',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_imei">IMEI</label>
                            <?php echo e(Form::text('phone_imei', optional($loan->phone)->imei, ['id' => 'phone_imei',  'class' => 'form-control'])); ?>

                        </div>
                    </div>
                </div>
                <div class="row my-2 mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone_description">Описание</label>
                            <?php echo e(Form::textarea('phone_description', optional($loan->phone)->description, ['id' => 'phone_description', 'rows' => 4, 'cols' => 54, 'class' => 'form-control', 'style' => 'resize:none'])); ?>

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
                <div class="col">
                    <img src="/<?php echo e($loan->image); ?>" style="height: 300px; width: 300px" />
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <?php echo e(Form::submit('СОХРАНИТЬ', ['class' => 'btn btn-primary'])); ?>

                </div>
            </div>
        </div>

    <?php echo e(Form::close()); ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/cashbox/loan/edit.blade.php ENDPATH**/ ?>