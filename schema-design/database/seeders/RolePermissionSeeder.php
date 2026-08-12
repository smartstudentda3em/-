<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /* ------------------- الصلاحيات ------------------- */
        $permissions = [
            // المذكرات
            ['booklets.view',        'عرض المذكرات',              'booklets'],
            ['booklets.create',      'إضافة مذكرة',               'booklets'],
            ['booklets.update',      'تعديل مذكرة',               'booklets'],
            ['booklets.delete',      'حذف مذكرة',                 'booklets'],
            // أوامر الطباعة
            ['orders.view',          'عرض أوامر الطباعة',         'orders'],
            ['orders.create',        'إنشاء أمر طباعة',           'orders'],
            ['orders.update_status', 'تحديث حالة الأمر',          'orders'],
            ['orders.assign',        'إسناد أمر لمشغّل',          'orders'],
            ['orders.collect_cash',  'تحصيل النقدية',             'orders'],
            ['orders.delete',        'حذف أمر',                   'orders'],
            // المواد والمخزون
            ['materials.view',       'عرض المواد',                'materials'],
            ['materials.manage',     'إدارة المواد والمخزون',      'materials'],
            // الأكاديمي
            ['academic.manage',      'إدارة الهيكل الأكاديمي',     'academic'],
            // المستخدمون والتقارير
            ['users.manage',         'إدارة المستخدمين والأدوار',   'users'],
            ['reports.view',         'عرض التقارير',              'reports'],
        ];

        $permIds = [];
        foreach ($permissions as [$name, $display, $group]) {
            $permIds[$name] = Permission::updateOrCreate(
                ['name' => $name],
                ['display_name' => $display, 'group' => $group]
            )->id;
        }
        $all = array_values($permIds);

        /* ------------------- الأدوار + صلاحياتها ------------------- */
        $roles = [
            'admin' => [
                'display' => 'مدير النظام',
                'perms'   => $all,
            ],
            'manager' => [
                'display' => 'مدير المطبعة',
                'perms'   => $all, // المالك: صلاحية كاملة
            ],
            'teacher' => [
                'display' => 'مدرّس (رافع المذكرات)',
                'perms'   => ['booklets.view', 'booklets.create', 'booklets.update', 'orders.view'],
            ],
            'print_operator' => [
                'display' => 'مشغّل الطباعة',
                'perms'   => ['orders.view', 'orders.update_status', 'orders.assign', 'orders.collect_cash', 'materials.view'],
            ],
        ];

        $roleIds = [];
        foreach ($roles as $name => $def) {
            $role = Role::updateOrCreate(['name' => $name], ['display_name' => $def['display']]);
            $ids = is_array($def['perms']) && isset($def['perms'][0]) && is_int($def['perms'][0])
                ? $def['perms']
                : array_map(fn ($p) => $permIds[$p], $def['perms']);
            $role->permissions()->sync($ids);
            $roleIds[$name] = $role->id;
        }

        /* ------------------- مستخدمون افتراضيون ------------------- */
        $admin = User::updateOrCreate(
            ['phone' => '01000000000'],
            ['name' => 'System Admin', 'password' => 'password', 'is_active' => true]
        );
        $admin->roles()->sync([$roleIds['admin']]);

        // المدير — Makroum Reda
        $manager = User::updateOrCreate(
            ['phone' => '99970766'],
            ['name' => 'Makroum Reda', 'password' => 'password', 'is_active' => true]
        );
        $manager->roles()->sync([$roleIds['manager']]);

        $this->command->info('✔ الأدوار والصلاحيات + حساب المدير Makroum Reda.');
    }
}
