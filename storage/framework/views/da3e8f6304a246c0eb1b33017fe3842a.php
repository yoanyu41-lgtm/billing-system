<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="auth-card">

        <!-- Logo Section -->
        <div class="auth-logo-header">
            <div class="auth-logo-icon">
                <img src="<?php echo e($companyLogo); ?>" alt="CT">
            </div>
            <span class="auth-logo-text">City<span style="color: var(--secondary);">Tech</span></span>
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 32px; text-align: center;">
            <h2 style="font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px;"><?php echo e(__('app.create_account')); ?></h2>
            <p style="color: var(--text-muted); font-size: 15px; margin: 0; font-weight: 500;"><?php echo e(__('app.register_subtitle')); ?></p>
        </div>

        <!-- Validation Errors -->
        <?php if($errors->any()): ?>
            <div style="background: rgba(220, 38, 38, 0.05); border-left: 4px solid var(--danger); padding: 14px 16px; border-radius: var(--radius-sm); margin-bottom: 25px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="color: var(--danger); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <i class="fas fa-times-circle"></i> <?php echo e($error); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('register')); ?>">
            <?php echo csrf_field(); ?>

            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label"><?php echo e(__('app.full_name')); ?></label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-user form-input-icon"></i>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required autofocus placeholder="<?php echo e(__('app.full_name_placeholder')); ?>" class="form-input">
                </div>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label"><?php echo e(__('app.email_address')); ?></label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required placeholder="yourname@citytech.com" class="form-input">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label"><?php echo e(__('app.password')); ?></label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required placeholder="<?php echo e(__('app.password_placeholder')); ?>" class="form-input">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label"><?php echo e(__('app.confirm_password')); ?></label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-check-double form-input-icon"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="<?php echo e(__('app.confirm_password_placeholder')); ?>" class="form-input">
                </div>
            </div>

            <!-- Secret Register Code -->
            <div class="form-group" style="margin-bottom: 32px;">
                <label class="form-label"><?php echo e(__('app.secret_code')); ?></label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-key form-input-icon"></i>
                    <input id="registration_code" type="password" name="registration_code" required placeholder="<?php echo e(__('app.secret_code_placeholder')); ?>" class="form-input">
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn-submit">
                <?php echo e(__('app.create_account')); ?>

            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 32px 0;">
                <hr style="border:0; border-top:1.5px solid var(--border);">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background: var(--surface); padding:0 15px; color: var(--text-subtle); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
            </div>

            <!-- Link to Login -->
            <p style="text-align:center; font-size:14px; color: var(--text-muted); font-weight:600; margin: 0;">
                <?php echo e(__('app.already_member')); ?>

                <a href="<?php echo e(route('login')); ?>" style="color: var(--secondary); text-decoration:none; font-weight:700; border-bottom:1.5px solid rgba(37, 99, 235, 0.15); padding-bottom: 2px; transition: all 0.2s ease;" onmouseover="this.style.color='var(--brand)'; this.style.borderBottomColor='var(--brand)'" onmouseout="this.style.color='var(--secondary)'; this.style.borderBottomColor='rgba(37, 99, 235, 0.15)'"><?php echo e(__('app.sign_in')); ?></a>
            </p>
        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH E:\billing-system\resources\views/auth/register.blade.php ENDPATH**/ ?>