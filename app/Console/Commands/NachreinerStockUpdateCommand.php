<?php

namespace App\Console\Commands;

use App\Mail\StockUpdateFailMail;
use App\Models\Indices\PartIndexDe;
use App\Models\Indices\PartIndexEn;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use SimpleXMLElement;

class NachreinerStockUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockUpdate:nachreiner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $parts = PartIndexDe::where('producer_id', 'nachreiner')->get();

            $client = new Client(['base_uri' => 'https://www.nachreiner-werkzeuge.de']);

            foreach ($parts as $part) {
                $response = $client->get('/erp/services/xtc/catalog/'.$part->stock_part_id.'/availability');

                $responseXml = new SimpleXMLElement($response->getBody()->getContents());

                $quantity = (string)$responseXml->availableQuantity;

                $part->update([
                    'stock'   =>  json_encode([ "1" =>  $quantity]),
                ]);
            }


            $parts = PartIndexEn::where('producer_id', 'nachreiner')->get();

            $client = new Client(['base_uri' => 'https://www.nachreiner-werkzeuge.de']);

            foreach ($parts as $part) {
                $response = $client->get('/erp/services/xtc/catalog/'.$part->stock_part_id.'/availability');

                $responseXml = new SimpleXMLElement($response->getBody()->getContents());

                $quantity = (string)$responseXml->availableQuantity;

                $part->update([
                    'stock'   =>  json_encode([ "1" =>  $quantity]),
                ]);
            }

        } catch (\Exception $e) {
            Mail::to('p.schaer@7industry.ch')->send(new StockUpdateFailMail('Nachreiner', $e->getMessage()));
        }

    }
}
