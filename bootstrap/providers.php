<?php

return [
    App\Providers\AppServiceProvider::class,
    // Aquí añadimos los motores para los reportes:
    Barryvdh\DomPDF\ServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
];