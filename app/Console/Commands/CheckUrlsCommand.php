<?php

namespace App\Console\Commands;


use App\Models\Indices\PartIndexDe;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Api\Category\CacheCategoryController;

class CheckUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkUrls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Url gives a 404';

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
        $i=1;

        $brokenLinks = [];
        foreach (PartIndexDe::all() as $item) {
            echo $i;
            echo "\n";
            $i++;

            if($item->table_detail_image_link) {
                $response = @get_headers($item->table_detail_image_link);

                if (is_array($response) && !strpos($response[0], "200")) {
                    $brokenLinks[$item->part_id] = $item->table_detail_image_link;
                    echo $item->part_id;
                    echo "\n";
                }
            }

            if(is_array(json_decode($item->image_links) )) {
                foreach (json_decode($item->image_links) as $link) {

                    $response = @get_headers($link);

                    if (is_array($response) && !strpos($response[0], "200")) {
                        $brokenLinks[$item->part_id] = $item->table_detail_image_link;
                        echo $item->part_id;
                        echo "\n";
                    }
                }
            }

            elseif($item->image_links) {
                $response = @get_headers($item->image_links);

                if (is_array($response) && !strpos($response[0], "200")) {
                    $brokenLinks[$item->part_id] = $item->image_links;
                    echo $item->part_id;
                    echo "\n";
                }
            }


        }

        var_dump($brokenLinks);

    }
}
