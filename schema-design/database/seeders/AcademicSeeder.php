<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // الصفوف
        $grades = collect([
            ['name' => 'Grade 10', 'level' => 10],
            ['name' => 'Grade 11', 'level' => 11],
            ['name' => 'Grade 12', 'level' => 12],
        ])->map(fn ($g) => Grade::updateOrCreate(['name' => $g['name']], ['level' => $g['level']]));

        // المواد
        $subjects = collect(['Chemistry', 'Physics', 'Mathematics', 'Biology', 'Arabic', 'English'])
            ->map(fn ($name) => Subject::updateOrCreate(['name' => $name], []));

        // ربط كل المواد بكل الصفوف
        $grades->each(fn (Grade $grade) => $grade->subjects()->sync($subjects->pluck('id')));

        // الفصول الدراسية
        Term::updateOrCreate(
            ['name' => 'First Term', 'academic_year' => '2024/2025'],
            ['starts_on' => '2024-09-01', 'ends_on' => '2025-01-15', 'is_current' => true]
        );
        Term::updateOrCreate(
            ['name' => 'Second Term', 'academic_year' => '2024/2025'],
            ['starts_on' => '2025-02-01', 'ends_on' => '2025-06-15', 'is_current' => false]
        );

        $this->command->info('✔ الهيكل الأكاديمي: الصفوف والمواد والفصول.');
    }
}
