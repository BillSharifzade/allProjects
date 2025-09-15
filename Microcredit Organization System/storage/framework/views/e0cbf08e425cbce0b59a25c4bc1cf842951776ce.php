<?php $__env->startSection('inc-content'); ?>
    <?php $deliverCount = $items->filter(function($r){ return $r->picked_by_incassator && !$r->delivered_by_incassator; })->count(); ?>

    <form id="form-pick" method="post" action="/inc/todeliver/pick" class="mb-3">
        <?php echo e(csrf_field()); ?>

        <div class="table-responsive">
            <table class="table table-striped table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:42px"></th>
                    <th>Договор</th><th>Клиент</th><th>Инфо</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($row->picked_by_incassator ? 'row-picked' : ''); ?>">
                        <td>
                            <?php if(!$row->picked_by_incassator): ?>
                                <input class="pick-cb" type="checkbox" name="ids[]" value="<?php echo e($row->id); ?>" />
                            <?php else: ?>
                                <i class="bi bi-check2-circle text-muted" title="Забрано"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($row->contract_no); ?></td>
                        <td><?php echo e($row->client_name); ?></td>
                        <td><?php echo e($row->loan_info); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </form>

    <form id="form-deliver" method="post" action="/inc/todeliver/deliver">
        <?php echo e(csrf_field()); ?>

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($row->picked_by_incassator && !$row->delivered_by_incassator): ?>
                <input type="hidden" name="ids[]" value="<?php echo e($row->id); ?>" />
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </form>

    <div class="mt-2"><?php echo e($items->links()); ?></div>

    <div class="inc-actions bg-white border-top" style="position:fixed;left:0;right:0;bottom:0;z-index:1030;">
        <div class="d-flex align-items-center p-2">
            <div class="small text-muted mr-2 flex-grow-1" id="selInfo">Выбрано: 0 • К доставке: <?php echo e($deliverCount); ?></div>
            <button type="button" class="btn btn-warning mr-2" style="min-width:140px" onclick="document.getElementById('form-pick').submit()">
                Забрать
            </button>
            <button type="button" class="btn btn-success" style="min-width:140px" onclick="document.getElementById('form-deliver').submit()" <?php echo e($deliverCount == 0 ? 'disabled' : ''); ?>>
                Доставлено
            </button>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
    <style>
        body{padding-bottom:80px;}
        .table td,.table th{vertical-align:middle;}
        .pick-cb{width:22px;height:22px;}
        .row-picked{background:#f1f3f5; color:#6c757d;}
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        (function(){
            function updateSel(){
                var n = document.querySelectorAll('.pick-cb:checked').length;
                var el = document.getElementById('selInfo');
                if(el){ el.textContent = 'Выбрано: ' + n + ' • К доставке: ' + <?php echo e($deliverCount); ?>; }
            }
            document.querySelectorAll('.pick-cb').forEach(function(cb){ cb.addEventListener('change', updateSel); });
            updateSel();
        })();
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('incassator.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/qwantum/Documents/ngn/nigin/nigin/html/resources/views/incassator/todeliver/index.blade.php ENDPATH**/ ?>