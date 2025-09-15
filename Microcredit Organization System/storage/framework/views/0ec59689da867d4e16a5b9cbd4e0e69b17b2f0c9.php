<?php echo e(Form::open(['method' => 'get'])); ?>

    <div class="container p-0 m-0">
        <div class="row px-3">
            <div class="col-2 px-1">
                <?php echo e(Form::select('collateral_type', [0 => 'Все', 1 => 'Золото', 2 => 'Авто'], request()->get('collateral_type'), ['id' => 'filters', 'class' => 'form-control'])); ?>

            </div>
            <?php if($config['cashbox']): ?>
                <div class="col-3 px-1 m-0">
                    <?php echo e(Form::select('cashbox', $cashboxes, request()->get('cashbox'), ['id' => 'cashbox', 'class' => 'form-control'])); ?>

                </div>
            <?php endif; ?>

            <?php if($config['audit']): ?>
                <div class="col-3 px-1 m-0">
                    <?php echo e(Form::select('audit', ['no' => 'Все', 'yes' => 'Аудит'], request()->get('audit'), ['id' => 'filters', 'class' => 'form-control'])); ?>

                </div>
            <?php endif; ?>

            <?php if($config['closed']): ?>
                <div class="col-3 px-1 m-0">
                    <?php echo e(Form::select('closed', ['no' => 'Открытые', 'yes' => 'Закрытие'], request()->get('closed'), ['id' => 'filters', 'class' => 'form-control'])); ?>

                </div>
            <?php endif; ?>

            <?php if($config['from']): ?>
                <div class='col-2 px-1'>
                    <?php echo e(Form::text('from', request()->get('from'), ['id' => 'from', 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>'Дата от'])); ?>

                </div>
            <?php endif; ?>

            <?php if($config['to']): ?>
                <div class='col-2 px-1'>
                    <?php echo e(Form::text('to', request()->get('to'), ['id' => 'to', 'class' => 'form-control', 'autocomplete'=>'off', 'placeholder'=>'Дата до'])); ?>

                </div>
            <?php endif; ?>

            <div class="col px-1">
                <?php echo e(Form::submit('ПОИСК', ['class' => 'btn btn-primary'])); ?>

            </div>
        </div>
    </div>
<?php echo e(Form::close()); ?>


<style>
    .mobile-fix {

    }
</style>

<script type="text/javascript">
    $(function (){
        $('#from, #to').datepicker({
            language: 'ru',
            format: 'mm/dd/yyyy',
        });
    });
</script>
<?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/widgets/cashbox_filter.blade.php ENDPATH**/ ?>