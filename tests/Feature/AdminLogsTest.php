<?php

it('renders the logs table with datatables pagination markup', function () {
    $logs = collect([
        (object) [
            'id' => 1,
            'description' => 'Test log entry',
            'created_at' => now(),
        ],
    ]);

    $html = view('admin.admin_logs', compact('logs'))->render();

    expect($html)
        ->toContain('id="basic-datatables"')
        ->and($html)->toContain("$('#basic-datatables').DataTable()");
});
