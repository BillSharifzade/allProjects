<?php $__env->startSection('content'); ?>
    <?php echo e(Form::open(['url' => '/admin/interest-rates/' . $interestRate->id . '/update', 'method' => 'post'])); ?>

    <?php echo e(csrf_field()); ?>

    <div class="container">
        <div class="row">
            <div class="col">
                <?php echo app('arrilot.widget')->run('Error'); ?>
            </div>
        </div>

        <div class="row my-2 mt-4">
            <div class="col">
                <h5>Редатирование процентовки</h5>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="sum_from">Сумма от</label>
                    <?php echo e(Form::number('sum_from', $interestRate->sum_from, ['id' => 'sum_from', 'step' => '0', 'class' => 'form-control'])); ?>

                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="sum_to">Сумма до</label>
                    <?php echo e(Form::number('sum_to', $interestRate->sum_to, ['id' => 'sum_to', 'step' => '0', 'class' => 'form-control'])); ?>

                </div>
            </div>
        </div>

        <div class="row my-2">
            <div class="col">
                <div class="form-group">
                    <label for="rate">Процент</label>
                    <?php echo e(Form::number('rate', $interestRate->rate, ['id' => 'rate', 'step' => 'any', 'class' => 'form-control'])); ?>

                </div>
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

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/interestRate/edit.blade.php ENDPATH**/ ?>