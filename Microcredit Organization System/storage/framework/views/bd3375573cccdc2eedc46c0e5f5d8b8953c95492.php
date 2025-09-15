<?php $__env->startSection('content'); ?>
    <?php echo e(Form::open(['url' => '/admin/gold-prices/create', 'method' => 'post'])); ?>

        <?php echo e(csrf_field()); ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo app('arrilot.widget')->run('Error'); ?>
                </div>
            </div>

            <div class="row my-2 mt-4">
                <div class="col">
                    <h5>Новая цена</h5>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="purity">Проба</label>
                        <?php echo e(Form::select('purity', [375=>375,500=>500,585=>585,750=>750,875=>875], '', ['id' => 'purity', 'class' => 'form-control', 'placeholder'=>''])); ?>

                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col">
                    <div class="form-group">
                        <label for="price">Цена</label>
                        <?php echo e(Form::number('price', '', ['id' => 'price', 'step' => '0', 'class' => 'form-control'])); ?>

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

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/goldPrice/create.blade.php ENDPATH**/ ?>