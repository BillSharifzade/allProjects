<?php $__env->startSection('content'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <?php echo app('arrilot.widget')->run('Error'); ?>
                <?php if(session('message')): ?>
                    <div class="alert alert-success"><?php echo e(session('message')); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-8">
                <h5>Загрузка черного списка</h5>
                <p class="text-muted">Загрузите XLS/XLSX файл. Обязательно наличие колонки с заголовком "ID" (паспорт). Доп. поля (name, phone) необязательны.</p>
                <?php echo e(Form::open(['url' => route('admin-blacklist-upload'), 'method' => 'post', 'enctype' => 'multipart/form-data'])); ?>

                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                    <label for="file">Файл XLSX</label>
                    <?php echo e(Form::file('file', ['class' => 'form-control'])); ?>

                </div>
                <div class="form-group mt-3">
                    <?php echo e(Form::submit('Загрузить', ['class' => 'btn btn-primary'])); ?>

                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/admin/blacklist/index.blade.php ENDPATH**/ ?>