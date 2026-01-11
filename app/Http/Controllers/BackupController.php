<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Spatie\DbDumper\Databases\MySql;

class BackupController extends Controller
{

public function full()
{
    try {
        $connection = config('database.connections.mysql');

        $db   = $connection['database'];
        $host = $connection['host'];
        $port = $connection['port'] ?? 3306;
        $user = $connection['username'];
        $pass = $connection['password'];

        $date = now()->format('Y-m-d');
        $fileName = "backup_hojas_ruta_{$db}_{$date}.sql";
        $filePath = storage_path("app/{$fileName}");

        MySql::create()
            ->setHost($host)
            ->setPort($port)
            ->setDbName($db)
            ->setUserName($user)
            ->setPassword($pass)
            ->dumpToFile($filePath);

        if (!file_exists($filePath) || filesize($filePath) === 0) {
            return response()->json([
                'message' => 'El backup no se generó correctamente'
            ], 500);
        }

        return response()->download($filePath)->deleteFileAfterSend(true);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}


}
