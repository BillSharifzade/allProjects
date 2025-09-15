<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlacklistEntry;
use App\Helpers;
use Maatwebsite\Excel\Facades\Excel;

class BlacklistController extends Controller
{
    public function index()
    {
        return view('admin.blacklist.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,xls']
        ], [
            'file.required' => 'Загрузите XLSX файл',
            'file.mimes' => 'Поддерживаются только XLS/XLSX файлы',
        ]);

        $path = $request->file('file')->getRealPath();

        $rowsProcessed = 0;
        $companyId = Auth::user()->company_id;

        Excel::import(new class($companyId, $rowsProcessed) implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow, \Maatwebsite\Excel\Concerns\WithChunkReading, \Maatwebsite\Excel\Concerns\WithBatchInserts {
            private $companyId;
            public $rows;
            public function __construct($companyId, &$rows)
            {
                $this->companyId = $companyId;
                $this->rows = &$rows;
            }
            public function collection(\Illuminate\Support\Collection $collection)
            {
                $now = time();
                $upserts = [];
                foreach ($collection as $row) {
                    // Expecting a column named "ID"
                    if (!isset($row['id'])) { continue; }
                    $pid = Helpers::normalizePassportId($row['id']);
                    if ($pid === '') { continue; }
                    $upserts[] = [
                        'company_id' => $this->companyId,
                        'passport_id_norm' => $pid,
                        'full_name' => isset($row['name']) ? (string)$row['name'] : null,
                        'phone' => isset($row['phone']) ? (string)$row['phone'] : null,
                        'raw_json' => json_encode($row),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $this->rows++;
                }
                if (!empty($upserts)) {
                    BlacklistEntry::withoutEvents(function() use ($upserts) {
                        \DB::table('blacklist_entries')->upsert(
                            $upserts,
                            ['company_id', 'passport_id_norm'],
                            ['full_name','phone','raw_json','updated_at']
                        );
                    });
                }
            }
            public function chunkSize(): int { return 1000; }
            public function batchSize(): int { return 1000; }
        }, $request->file('file'));

        return redirect()->back()->with('message', 'Импортировано записей: ' . (int)$rowsProcessed);
    }
}


