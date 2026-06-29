<?php

use App\Models\Archive;
use App\Models\Program;

it('shows archive impact details in the admin program delete confirmation', function () {
    $program = new Program();
    $program->forceFill([
        'id' => 1,
        'name' => 'DASDSA',
    ]);
    $program->exists = true;
    $program->created_at = now();
    $program->updated_at = now();
    $program->setRelation('archives', collect([new Archive(['id' => 101])]));

    $html = view('admin.admin_program', ['programs' => collect([$program])])->render();

    expect($html)
        ->toContain('data-program-name="DASDSA"')
        ->and($html)->toContain('data-archives-count="1"')
        ->and($html)->toContain('This program "${programName}" is assigned to')
        ->and($html)->toContain('Deleting it will affect these archives.');
});
