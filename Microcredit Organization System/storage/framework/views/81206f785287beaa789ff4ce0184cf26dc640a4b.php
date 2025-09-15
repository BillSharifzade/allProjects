<?php $__env->startSection('content'); ?>
    <?php echo e(Form::open(['url' => '/admin/loans/' . $loan->id . '/update?redirect=' . request()->get('redirect'), 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

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

            <div class="row my-2">
                <div class="col align-self-center pt-2">
                    Дата выдачи кредита
                </div>
                <div class="col">
                    <div>
                        <div class="form-group">
                            <label for="passport_issued_day">День</label>
                            <?php ($day=(int)date("d", $loan->lend_date)); ?>
                            <?php if (isset($component)) { $__componentOriginale241ab293d10e7e4bb9a17e8a1c72d0890d63f27 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectDay::class, ['value' => ''.e($day).'','name' => 'lend_day']); ?>
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
                            <?php ($month=(int)date("m", $loan->lend_date)); ?>
                            <?php if (isset($component)) { $__componentOriginalfc4e4f0c6b93226699724e73fbc21c3713f8aa59 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectMonth::class, ['value' => ''.e($month).'','name' => 'lend_month']); ?>
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
                            <?php ($year=(int)date("Y", $loan->lend_date)); ?>
                            <?php if (isset($component)) { $__componentOriginal0b79684312f38dd04a4410d7c14c7e27f64b7d10 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\Forms\SelectYear::class, ['value' => ''.e($year).'}','name' => 'lend_year']); ?>
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

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="initial_sum">Сумма</label>
                        <?php echo e(Form::number('initial_sum', $loan->initial_sum, ['id' => 'initial_sum', 'step' => '0', 'class' => 'form-control'])); ?>

                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="grace_period">Лготный период</label>
                        <?php echo e(Form::number('grace_period', $loan->grace_period, ['id' => 'grace_period', 'class' => 'form-control'])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Залог</h5>
                </div>
            </div>

            <?php for($i = 1; $i <= 10; $i++): ?>
                <div class="row my-2">
                    <div class="col align-self-center" style="min-width:50px !important; max-width: 50px !important;">
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
                            <?php echo e(Form::select('item_'.$i.'_purity', [375=>375,500=>500,585=>585,750=>750,875=>875,958=>958,999=>999], isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->purity : '', ['id' => 'item_'.$i.'_purity', 'class' => 'form-control', 'placeholder'=>''])); ?>

                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            <?php echo e(Form::number('item_'.$i.'_count', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->count : '', ['id' => 'item_'.$i.'_count', 'class' => 'form-control tracker', 'placeholder'=>'количество'])); ?>

                        </div>
                    </div>
                    <div class="col p-0 ml-1">
                        <div class="form-group">
                            <?php echo e(Form::number('item_'.$i.'_price', isset($loan->jewelries[$i-1]) ? $loan->jewelries[$i-1]->price : '', ['id' => 'item_'.$i.'_price', 'class' => 'form-control', 'readonly' => 'readonly', 'placeholder'=>'цена'])); ?>

                        </div>
                    </div>
                </div>
            <?php endfor; ?>

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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/loan/edit.blade.php ENDPATH**/ ?>