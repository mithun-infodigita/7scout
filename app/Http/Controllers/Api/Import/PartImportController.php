<?php
namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Imports\StandardImport\Jobs\StandardPartImportJob;
use App\Models\Import\Import;
use App\Producers\Amf\Jobs\AmfPartImportJob;
use App\Producers\Diametal\Jobs\DiametalPartImportJob;
use App\Producers\Diebold\Jobs\DieboldPartImportJob;
use App\Producers\Dixi\Jobs\DixiPartImportJob;
use App\Producers\Gloor\Jobs\GloorPartImportJob;
use App\Producers\Kaefer\Jobs\KaeferPartImportJob;
use App\Producers\Nachreiner\Jobs\NachreinerPartImportJob;
use App\Producers\Voelkel\Jobs\VoelkelPartImportJob;
use App\Producers\Parotec\Jobs\ParotecPartImportJob;
use App\Producers\Motorex\Jobs\MotorexPartImportJob;
use App\Producers\Tvb\Jobs\TvbPartImportJob;
use App\Producers\Botek\Jobs\BotekPartImportJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Artisan;

class PartImportController extends Controller
{
    public function index(Request $request, Import $import)
    {

        Artisan::call("cache:clear");

        $import->update([
            'status'        =>  'importing',
            'notification'  =>  'Import started '. Carbon::now()->format('d.m.Y H:i')
        ]);

        switch ($import->producer->unique_id) {
            case 'nachreiner':
                NachreinerPartImportJob::dispatch($import);
                break;
            case 'diebold':
                DieboldPartImportJob::dispatch($import);
                break;
            case 'dixi':
                DixiPartImportJob::dispatch($import);
                break;
            case 'gloor':
                GloorPartImportJob::dispatch($import);
                break;
            case 'amf':
                AmfPartImportJob::dispatch($import);
                break;
            case 'kaefer':
                KaeferPartImportJob::dispatch($import);
                break;
            case 'voelkel':
                VoelkelPartImportJob::dispatch($import);
                break;
            case 'diametal':
                DiametalPartImportJob::dispatch($import);
                break;
            case 'parotec':
                ParotecPartImportJob::dispatch($import);
                break;
            case 'motorex':
                MotorexPartImportJob::dispatch($import);
                break;
//            case 'tvb':
//                TvbPartImportJob::dispatch($import);
//                break;
            case 'botek':
                BotekPartImportJob::dispatch($import);
                break;
            default : StandardPartImportJob::dispatch($import);

            }

        $import->update([
            'status'        =>  'imported',
            'notification'  =>  'Imported at '. Carbon::now()->format('d.m.Y H:i')
        ]);

        return response($import->name.' done.');
    }
}

